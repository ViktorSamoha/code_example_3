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
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\QROutputInterface;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class adminPermissionAdd extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'recalculatePermissionPrice' => [
                'prefilters' => [],
            ],
            'registerUserPermission' => [
                'prefilters' => [],
            ],
        ];
    }

    public function getUserPropsData(&$arResult)
    {
        if (Loader::includeModule("iblock")) {
            global $USER;
            $arPrefCategories = [];
            $arUserLocations = [];
            $property_enums = CIBlockPropertyEnum::GetList(array("DEF" => "DESC", "SORT" => "ASC"), array("IBLOCK_ID" => IB_VISITORS, "CODE" => "U_PREFERENTIAL_CATEGORY"));
            while ($enum_fields = $property_enums->GetNext()) {
                if ($enum_fields) {
                    $arPrefCategories[] = [
                        'ID' => $enum_fields['ID'],
                        'VALUE' => $enum_fields['VALUE'],
                    ];
                }
            }
            unset($property_enums, $enum_fields);
            $property_enums = CIBlockPropertyEnum::GetList(array("DEF" => "DESC", "SORT" => "ASC"), array("IBLOCK_ID" => IB_VISITORS, "CODE" => "U_LOCATION"));
            while ($enum_fields = $property_enums->GetNext()) {
                if ($enum_fields) {
                    $arUserLocations[] = [
                        'ID' => $enum_fields['ID'],
                        'VALUE' => $enum_fields['VALUE'],
                    ];
                }
            }
            if ($arPrefCategories) {
                $arResult['PREF_CATEGORIES'] = $arPrefCategories;
            }
            if ($arPrefCategories) {
                $arResult['USER_LOCATIONS'] = $arUserLocations;
            }
        }
    }

    public function getUserGroups(&$arResult)
    {
        if ($arResult['USER_DATA']) {
            if ($arResult['USER_DATA']['ID']) {
                $arUserGroup = [];
                $arSelect = array("ID", "PROPERTY_USER_GROUP");
                $arFilter = array("IBLOCK_ID" => IB_USERS, "PROPERTY_USER_ID" => $arResult['USER_DATA']['ID']);
                $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    if ($arFields) {
                        $arUserGroup[$arFields['ID']] = $arFields['PROPERTY_USER_GROUP_VALUE'];
                    }
                }
                if (!empty($arUserGroup)) {
                    $temp = [];
                    foreach ($arUserGroup as $groupElements) {
                        $arSelect = array("ID", "PROPERTY_U_LAST_NAME", "PROPERTY_U_NAME", "PROPERTY_U_SECOND_NAME");
                        $arFilter = array("IBLOCK_ID" => IB_VISITORS, "ID" => $groupElements);
                        $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                        while ($ob = $res->GetNextElement()) {
                            $arFields = $ob->GetFields();
                            $groupElement = [
                                'ID' => $arFields['ID'],
                                'NAME' => $arFields['PROPERTY_U_LAST_NAME_VALUE'],
                                'LAST_NAME' => $arFields['PROPERTY_U_NAME_VALUE'],
                                'SECOND_NAME' => $arFields['PROPERTY_U_SECOND_NAME_VALUE'],
                            ];
                            $temp[] = $groupElement;
                        }
                    }
                    if (!empty($temp)) {
                        $arResult['USER_DATA']['USER_GROUP'] = $temp;
                    }
                }
            }
        }
    }

    public function getUserData(&$arResult, $params)
    {
        if ($params['USER_NUMBER']) {
            $arResult['USER_NUMBER'] = $params['USER_NUMBER'];
            if (Loader::includeModule("iblock")) {
                $arUsers = [];
                $arParams["FIELDS"] = array(
                    "ID",
                    "NAME",
                    "LAST_NAME",
                    "SECOND_NAME",
                );
                $filter = array("ACTIVE" => "Y", "WORK_PHONE" => $params['USER_NUMBER']);
                $rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), $filter, $arParams);
                while ($res = $rsUsers->GetNext()) {
                    $arUsers[$res['ID']] = [
                        'ID' => $res['ID'],
                        'LAST_NAME' => $res['LAST_NAME'],
                        'NAME' => $res['NAME'],
                        'SECOND_NAME' => $res['SECOND_NAME'],
                    ];
                }
                if (!empty($arUsers)) {
                    foreach ($arUsers as $userId => &$userFields) {
                        $arSelect = array("ID");
                        $arFilter = array("IBLOCK_ID" => IB_USERS, "ACTIVE" => "Y", 'PROPERTY_USER_ID' => $userId);
                        $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                        while ($ob = $res->GetNextElement()) {
                            $arFields = $ob->GetFields();
                            if ($arFields) {
                                $userFields['USER_RECORD_ID'] = $arFields['ID'];
                            }
                        }
                    }
                    $arResult['FOUND_USERS'] = $arUsers;
                }
            }
        }
        if ($params['USER_RECORD_ID']) {
            $arResult['USER_DATA'] = getUserData(getUserIdByRecordId($params['USER_RECORD_ID']));
            $this->getUserGroups($arResult);
        }
    }

    public function recalculatePermissionPriceAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (!empty($post)) {
            $count = ($post['count'] + 1) * VISIT_PERMISSION_COST;
            return AjaxJson::createSuccess([
                'price' => $count,
            ]);
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function registerUserPermissionAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (!empty($post)) {
            if ($post['USER_RECORD_ID'] && $post['ARRIVAL_DATE'] && $post['DEPARTURE_DATE']) {
                global $USER;
                $userId = getUserIdByRecordId($post['USER_RECORD_ID']);
                $arUser = getUserData($userId);
                $adminData = getUserData($USER->GetID());
                $now = time();
                $elementName = 'Разрешение на посещение [' . $userId . '] на период с ' . $post['ARRIVAL_DATE'] . ' по ' . $post['DEPARTURE_DATE'] . ' [' . $now . ']';
                $hash_string = $elementName . $now;
                $permission_unique_code = stringToHash($hash_string);
                Loader::includeModule("iblock");
                $el = new CIBlockElement;
                $PROP = array();
                $PROP['USER_RECORD_ID'] = $post['USER_RECORD_ID'];
                $PROP['USER_FIO'] = $arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME'];
                $PROP['USER_PHONE'] = $arUser['PHONE'];
                $PROP['ARRIVAL_DATE'] = $post['ARRIVAL_DATE'];
                $PROP['DEPARTURE_DATE'] = $post['DEPARTURE_DATE'];
                $PROP['ROUTE'] = '';
                $PROP['QR_CODE'] = getQrCode($permission_unique_code, 'user');
                //TODO:СДЕЛАТЬ ССЫЛКУ НА ОПЛАТУ
                $payment_link = '';
                $PROP['PRICE'] = $post['PRICE'];
                $PROP['BOOKING_TYPE'] = $adminData['LAST_NAME'] . ' ' . $adminData['NAME'] . ' ' . $adminData['SECOND_NAME'];
                if ($post['VISITORS']) {
                    if (is_array($post['VISITORS'])) {
                        foreach ($post['VISITORS'] as $arVisitorId) {
                            $PROP['USER_GROUP'][] = $arVisitorId;
                        }
                    } else {
                        $PROP['USER_GROUP'][] = $post['VISITORS'];
                    }
                }

                $arLoadProductArray = array(
                    "MODIFIED_BY" => $userId,
                    "IBLOCK_SECTION_ID" => false,
                    "IBLOCK_ID" => IB_PERMISSION,
                    'CODE' => $permission_unique_code,
                    "PROPERTY_VALUES" => $PROP,
                    "NAME" => $elementName,
                    "ACTIVE" => "Y",
                );
                if ($permissionId = $el->Add($arLoadProductArray)) {
                    return AjaxJson::createSuccess([
                        'permission_id' => $permissionId,
                        'payment_link' => $payment_link,
                        'blank_link' => 'https://' . $_SERVER['HTTP_HOST'] . '/receipt/user/' . $permission_unique_code . '/',
                    ]);
                } else {
                    return AjaxJson::createError([
                        'error_msg' => $el->LAST_ERROR,
                    ]);
                }
            } else {
                return AjaxJson::createError(null, 'Отсутствуют обязательные значения!');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function executeComponent()
    {
        $this->getUserData($this->arResult, $this->arParams);
        $this->getUserPropsData($this->arResult);
        $this->IncludeComponentTemplate($this->componentPage);
    }
}