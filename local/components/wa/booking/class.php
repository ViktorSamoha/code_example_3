<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Web\Cookie;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class WABooking extends CBitrixComponent implements Controllerable
{

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'drawCarNumberInput' => [
                'prefilters' => [],
            ],
            'add2Basket' => [
                'prefilters' => [],
            ],
        ];
    }

    public function addBasketToCookie($arBasket)
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $cookieBasket = $request->getCookie("basket");
        if ($cookieBasket) {
            $basket = unserialize($cookieBasket);
            if ($basket && is_array($basket)) {
                $basket = array_merge($basket, $arBasket);
            } else {
                $basket = $arBasket;
            }
        } else {
            $basket = $arBasket;
        }
        if ($basket) {
            $cookie = new \Bitrix\Main\Web\Cookie("basket", serialize($basket), time() + 60 * 60 * 24 * 60);
            $cookie->setPath("/");
            \Bitrix\Main\Application::getInstance()->getContext()->getResponse()->addCookie($cookie);
        }
    }

    public function add2BasketAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (!empty($post)) {
            //object fields
            /*
             * BOOKING_OBJECT
             * NAME
             * SURNAME
             * ARRIVAL_DATE
             * DEPARTURE_DATE
             * CHECK_IN_TIME
             * DEPARTURE_TIME
             * PERMISSION
             * ADULTS
             * BENIFICIARIES
             * GUEST_CARS
             * BOOKING_TYPE
             * OBJECT_RENT_COST
             * VISIT_PERMISSION_COST
             * BOOKING_COST
             * */
            //permission fields
            /*
             * USER_RECORD_ID
             * ARRIVAL_DATE
             * DEPARTURE_DATE
             * ROUTE
             * USER_GROUP
             * PRICE
             * USER_FIO
             * USER_PHONE
             * BOOKING_TYPE
             * */
            //transport permission fields
            /*
             * USER
             * USER_VEHICLE
             * USER_PERMISSION
             * USER_ARRIVAL_DATE
             * USER_DEPARTURE_DATE
             * USER_VEHICLE_NAME
             * PERMISSION_STATUS
             * ROUTE
             * USER_FIO
             * USER_PHONE
             * */

            //\Bitrix\Main\Diag\Debug::dumpToFile($post, $varName = '$post', $fileName = 'add2BasketAction_log.txt');

            $basket = [];
            $basketItem = [];
            if ($post["LOCATION_ID"]) {
                $object = getObjectById($post["LOCATION_ID"]);
                if ($object) {
                    $basketItem['OBJECT_ID'] = $object['ID'];
                    $basketItem['NAME'] = $object['NAME'];
                }
            }
            $basketItemId = 'O_' . $post["LOCATION_ID"] . '_' . $post["USER_ID"] . '_' . $post["ARRIVAL_DATE"];
            $basketItem['ID'] = $basketItemId;
            $basketItem['USER_ID'] = $post["USER_ID"];
            $basketItem['ARRIVAL_DATE'] = $post["ARRIVAL_DATE"];
            $basketItem['DEPARTURE_DATE'] = $post["DEPARTURE_DATE"];
            $basketItem['GUEST_QUANTITY'] = $post["GUEST_QUANTITY"];
            $basketItem['BENEFICIARIES_QUANTITY'] = $post["BENEFICIARIES_QUANTITY"];
            $basketItem['PRICE'] = (int)$post["PRICE"];
            if ($post["ADD_TRANSPORT_PERMISSION"]) {
                $transportPermissionId = 'TP_' . $post["LOCATION_ID"] . '_' . $post["USER_ID"] . '_' . $post["ARRIVAL_DATE"];
                $basketTransportPermissionItem = [
                    'ID' => $transportPermissionId,
                    'NAME' => 'Разрешение на транспортное средство',
                    'ARRIVAL_DATE' => $post["ARRIVAL_DATE"],
                    'DEPARTURE_DATE' => $post["DEPARTURE_DATE"],
                    'PRICE' => '0',
                ];
                $basket[] = $basketTransportPermissionItem;
            }
            if ($post["ADD_PERMISSION"]) {
                $permissionId = 'P_' . $post["LOCATION_ID"] . '_' . $post["USER_ID"] . '_' . $post["ARRIVAL_DATE"];
                $permissionPrice = 0;
                if ($post["BENEFICIARIES_QUANTITY"]) {
                    $permissionPrice = ($post["GUEST_QUANTITY"] - $post["BENEFICIARIES_QUANTITY"]) * VISIT_PERMISSION_COST;
                }
                $basketPermissionItem = [
                    'ID' => $permissionId,
                    'NAME' => 'Разрешение на посещение',
                    'ARRIVAL_DATE' => $post["ARRIVAL_DATE"],
                    'DEPARTURE_DATE' => $post["DEPARTURE_DATE"],
                    'PRICE' => $permissionPrice,
                ];
                $basket[] = $basketPermissionItem;
            }
            if (!empty($basketItem)) {
                $basket[] = $basketItem;
            }

            //\Bitrix\Main\Diag\Debug::dumpToFile($basket, $varName = '$basket', $fileName = 'add2BasketAction_log.txt');

            if (!empty($basket)) {
                $this->addBasketToCookie($basket);
                return AjaxJson::createSuccess();
            } else {
                return AjaxJson::createError(null, 'нет значений!');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public
    function drawCarNumberInputAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['COUNT'])) {
            $html = '';
            for ($i = 0; $i < $post['COUNT']; $i++) {
                $html .= '
                        <div class="input">
                            <label for="" class="input-label">Номер автомобиля ' . ($i + 1) . ' 
                                <span class="color-red">*</span>
                            </label>
                            <input type="text" name="CAR_ID_' . ($i + 1) . '" size="30">
                        </div>';
            }
            return AjaxJson::createSuccess([
                'html' => $html,
            ]);
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public
    function getLocationData($params, &$arResult)
    {
        if ($params['ELEMENT_ID']) {

            $arResult['LOCATION_DATA'] = getObjectById($params['ELEMENT_ID']);

            /*if (Loader::includeModule("iblock")) {
                $locationData = [];
                $arSelect = array(
                    "ID",
                    "NAME",
                    //'PROPERTY_PRICE',
                    'PROPERTY_PRICE_TYPE',
                    'PROPERTY_CAPACITY_MAXIMUM',
                    'PROPERTY_CAR_POSSIBILITY',
                    'PROPERTY_CAR_CAPACITY',
                    'PROPERTY_TIME_UNLIMIT_OBJECT',
                    'PROPERTY_CAPACITY_ESTIMATED',
                    'PROPERTY_FIXED_COST',
                    'PROPERTY_OBJECT_COST',
                    'PROPERTY_OBJECT_DAILY_COST',
                    'PROPERTY_COST_PER_PERSON_ONE_DAY',
                    'PROPERTY_COST_PER_PERSON',
                );
                $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "ID" => $params['ELEMENT_ID']);
                $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $locationData = [
                        'ID' => $arFields['ID'],
                        'NAME' => $arFields['NAME'],
                        //'PRICE' => $arFields['PROPERTY_PRICE_VALUE'],
                        'PRICE_TYPE' => $arFields['PROPERTY_PRICE_TYPE_VALUE'],
                        'CAPACITY_MAXIMUM' => $arFields['PROPERTY_CAPACITY_MAXIMUM_VALUE'],
                        'CAR_POSSIBILITY' => $arFields['PROPERTY_CAR_POSSIBILITY_VALUE'],
                        'CAR_CAPACITY' => $arFields['PROPERTY_CAR_CAPACITY_VALUE'],
                        'TIME_UNLIMIT_OBJECT' => $arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'],
                        'CAPACITY_ESTIMATED' => $arFields['PROPERTY_CAPACITY_ESTIMATED_VALUE'],
                        'FIXED_COST' => $arFields['PROPERTY_FIXED_COST_VALUE'],
                        'OBJECT_COST' => $arFields['PROPERTY_OBJECT_COST_VALUE'],
                        'OBJECT_DAILY_COST' => $arFields['PROPERTY_OBJECT_DAILY_COST_VALUE'],
                        'COST_PER_PERSON_ONE_DAY' => $arFields['PROPERTY_COST_PER_PERSON_ONE_DAY_VALUE'],
                        'COST_PER_PERSON' => $arFields['PROPERTY_COST_PER_PERSON_VALUE'],
                    ];
                }
                if (!empty($locationData)) {
                    $arResult['LOCATION_DATA'] = $locationData;
                }
            }*/
        }
    }

    public
    function getUserData(&$arResult)
    {
        global $USER;
        if ($USER->IsAuthorized()) {
            $userId = $USER->GetID();
            $userRecordId = checkUserRecord($userId);
            $arResult['USER_DATA'] = getUserData($userId);
            if (Loader::includeModule("iblock")) {
                if ($userRecordId) {
                    $arUserPermissions = [];
                    $arUserTransportPermissions = [];
                    $arSelect = array(
                        "ID",
                        "NAME",
                    );
                    $arFilter = array("IBLOCK_ID" => IB_PERMISSION, "=PROPERTY_USER_RECORD_ID" => $userRecordId, '>=PROPERTY_ARRIVAL_DATE' => date("Y-m-d"));
                    $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                    while ($ob = $res->GetNextElement()) {
                        $arFields = $ob->GetFields();
                        $arUserPermissions[] = $arFields;
                    }
                    unset($arFilter, $res, $ob, $arFields);
                    $arFilter = array("IBLOCK_ID" => IB_TRANSPORT_PERMISSION, "=PROPERTY_USER" => $userRecordId, '>=PROPERTY_USER_ARRIVAL_DATE' => date("Y-m-d"));
                    $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                    while ($ob = $res->GetNextElement()) {
                        $arFields = $ob->GetFields();
                        $arUserTransportPermissions[] = $arFields;
                    }
                    if (!empty($arUserPermissions)) {
                        $arResult['USER_DATA']['PERMISSIONS'] = $arUserPermissions;
                    }
                    if (!empty($arUserTransportPermissions)) {
                        $arResult['USER_DATA']['TRANSPORT_PERMISSIONS'] = $arUserTransportPermissions;
                    }
                }
            }
        }
    }

    public
    function executeComponent()
    {
        $this->getUserData($this->arResult);
        $this->getLocationData($this->arParams, $this->arResult);
        $this->includeComponentTemplate();
    }
}