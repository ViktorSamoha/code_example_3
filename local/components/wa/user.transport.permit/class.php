<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\QROutputInterface;
use Bitrix\Main\Diag\Debug;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class userTransportPermit extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'setUserVehiclePermission' => [
                'prefilters' => [],
            ],
            'getRouteMap' => [
                'prefilters' => [],
            ],
        ];
    }

    public function getRouteMapAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if ($post['ID']) {
            if (Loader::includeModule("iblock")) {
                $coords = false;
                $name = false;
                $arSelect = ['ID', 'NAME', 'PROPERTY_ROUTE_COORDS'];
                $arFilter = ['IBLOCK_ID' => IB_LOCATIONS, 'ID' => $post['ID']];
                $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    if ($arFields['PROPERTY_ROUTE_COORDS_VALUE']) {
                        $coords = $arFields['PROPERTY_ROUTE_COORDS_VALUE'];
                    }
                    if ($arFields['NAME']) {
                        $name = $arFields['NAME'];
                    }
                }
                if ($coords) {
                    return AjaxJson::createSuccess([
                        'coords' => $coords,
                        'name' => $name,
                    ]);
                } else {
                    return AjaxJson::createError(null, 'нет значений!');
                }
            } else {
                return AjaxJson::createError(null, 'iblock');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function getUserVehicle($vehicleId)
    {
        if ($vehicleId) {
            $arUserVehicle = [];
            $arSelect = array("ID", "PROPERTY_VEHICLE_TYPE", "PROPERTY_MODEL");
            $arFilter = array("IBLOCK_ID" => IB_TRANSPORT, "ACTIVE" => "Y", 'ID' => $vehicleId);
            $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arUserVehicle = [
                    'ID' => $arFields['ID'],
                    'VEHICLE_TYPE' => $arFields['PROPERTY_VEHICLE_TYPE_VALUE'],
                    'MODEL' => $arFields['PROPERTY_MODEL_VALUE'],
                ];
            }
            if (!empty($arUserVehicle)) {
                $arUserVehicle['VEHICLE_TYPE'] = getUserVehicleType($arUserVehicle['VEHICLE_TYPE']);
                return $arUserVehicle;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function setUserVehiclePermissionAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (!empty($post)) {
            if ($post['USER_VEHICLE'] && $post['ARRIVAL_DATE'] && $post['DEPARTURE_DATE'] && $post['ROUTE']) {
                global $USER;
                $userId = $USER->GetID();
                $arUser = getUserData($userId);
                $userRecordId = checkUserRecord($userId);
                $now = time();
                if ($userRecordId) {
                    $elementName = 'Разрешение на транспортное средство[' . $userId . ']' . $now;
                    $hash_string = $elementName . $now;
                    $permission_unique_code = stringToHash($hash_string);
                    if (CModule::IncludeModule('iblock')) {
                        $el = new CIBlockElement;
                        $PROP = array();
                        $PROP['USER'] = $userRecordId;
                        $PROP['USER_FIO'] = $arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME'];
                        $PROP['USER_PHONE'] = $arUser['PHONE'];
                        $arUserVehicle = $this->getUserVehicle($post['USER_VEHICLE']);
                        if ($arUserVehicle) {
                            $PROP['USER_VEHICLE_NAME'] = $arUserVehicle['VEHICLE_TYPE'] . ' ' . $arUserVehicle['MODEL'];
                        }
                        if ($post['PERMISSION_CODE']) {
                            $PROP['USER_PERMISSION'] = $post['PERMISSION_CODE'];
                        }
                        $PROP['PERMISSION_STATUS'] = VEHICLE_PERMISSION_STATUS;
                        $PROP['USER_ARRIVAL_DATE'] = $post['ARRIVAL_DATE'];
                        $PROP['USER_DEPARTURE_DATE'] = $post['DEPARTURE_DATE'];
                        $PROP['ROUTE'] = $post['ROUTE'];
                        $PROP['USER_VEHICLE'] = $post['USER_VEHICLE'];
                        $PROP['QR_CODE'] = getQrCode($permission_unique_code, 'transport');
                        $arLoadProductArray = array(
                            "MODIFIED_BY" => $userId,
                            "IBLOCK_SECTION_ID" => false,
                            "IBLOCK_ID" => IB_TRANSPORT_PERMISSION,
                            "PROPERTY_VALUES" => $PROP,
                            "NAME" => $elementName,
                            "CODE" => $permission_unique_code,
                            "ACTIVE" => "Y",
                        );
                    }
                    if ($permissionId = $el->Add($arLoadProductArray)) {
                        return AjaxJson::createSuccess([
                            'data' => $permissionId,
                        ]);
                    } else {
                        return AjaxJson::createError([
                            'data' => $el->LAST_ERROR,
                        ]);
                    }
                } else {
                    return AjaxJson::createError(null, 'Пользователь не найден!');
                }
            } else {
                return AjaxJson::createError(null, 'нет значений!');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function getUserVehicles(&$arResult)
    {
        if (Loader::includeModule("iblock")) {
            global $USER;
            $userId = $USER->GetID();
            $userRecord = checkUserRecord($userId);
            if ($userRecord) {
                $userVehicles = array();
                $res = CIBlockElement::GetProperty(IB_USERS, $userRecord, "sort", "asc", array("CODE" => "USER_TRANSPORT"));
                while ($ob = $res->GetNext()) {
                    $userVehicles[] = $ob['VALUE'];
                }
                if (!empty($userVehicles)) {
                    $arVehicles = [];
                    $arSelect = array("ID", "PROPERTY_VEHICLE_TYPE", 'PROPERTY_MODEL', 'PROPERTY_VEHICLE_POWER');
                    $arFilter = array("IBLOCK_ID" => IB_TRANSPORT, "ACTIVE" => "Y", 'ID' => $userVehicles, '!PROPERTY_BLOCKED' => 30);
                    $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                    while ($ob = $res->GetNextElement()) {
                        $arFields = $ob->GetFields();
                        $arVehicles[] = [
                            'ID' => $arFields['ID'],
                            'VEHICLE_TYPE' => $arFields['PROPERTY_VEHICLE_TYPE_VALUE'],
                            'MODEL' => $arFields['PROPERTY_MODEL_VALUE'],
                            'POWER' => $arFields['PROPERTY_VEHICLE_POWER_VALUE'] == 'Да',
                        ];
                    }
                    if (!empty($arVehicles)) {
                        foreach ($arVehicles as &$vehicle) {
                            $vehicle['VEHICLE_TYPE'] = getUserVehicleType($vehicle['VEHICLE_TYPE']);
                        }
                        $arResult['USER_VEHICLES'] = $arVehicles;
                    }
                }
            }
        }
    }

    public function executeComponent()
    {
        $this->arResult['ROUTS'] = getRouts();
        $this->getUserVehicles($this->arResult);
        $this->IncludeComponentTemplate($this->componentPage);
    }
}