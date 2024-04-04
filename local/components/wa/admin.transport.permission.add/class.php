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

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class AdminTransportPermissionAdd extends CBitrixComponent implements Controllerable
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
            $userVehicles = array();
            $res = CIBlockElement::GetProperty(IB_USERS, $params['USER_RECORD_ID'], "sort", "asc", array("CODE" => "USER_TRANSPORT"));
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
            $db_props = CIBlockElement::GetProperty(IB_USERS, $params['USER_RECORD_ID'], array("sort" => "asc"), array("CODE" => "USER_ID"));
            if ($ar_props = $db_props->Fetch()) {
                $userId = $ar_props["VALUE"];
            } else {
                $userId = false;
            }
            if ($userId) {
                $arResult['USER_DATA'] = getUserData($userId);
            }
        }

        if (Loader::includeModule("iblock")) {
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

    public function setUserVehiclePermissionAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (!empty($post)) {
            if ($post['ARRIVAL_DATE'] && $post['DEPARTURE_DATE'] && $post['ROUTE']) {
                $userId = $userRecordId = $arUserVehicle = $arUserVehicleType = $arUser = false;
                if ($post['USER_VEHICLE_ID'] && $post['USER_RECORD_ID']) {
                    global $USER;
                    $userId = getUserIdByRecordId($post['USER_RECORD_ID']);
                    $arUser = getUserData($userId);
                    $userRecordId = $post['USER_RECORD_ID'];
                    $arUserVehicle = getUserVehicle($post['USER_VEHICLE_ID']);
                    if ($arUserVehicle) {
                        $arUserVehicleType = getUserVehicleType($arUserVehicle['VEHICLE_TYPE'][0]);
                    }
                } else {
                    if ($post['NAME'] && $post['LAST_NAME'] && $post['EMAIL']) {
                        $newUserData = registerNewUser([
                            'NAME' => $post['NAME'],
                            'LAST_NAME' => $post['LAST_NAME'],
                            'EMAIL' => $post['EMAIL'],
                            'SECOND_NAME' => $post['SECOND_NAME'],
                            'PHONE' => $post['PHONE'],
                        ]);
                        //TODO:СДЕЛАТЬ ИНФОРМИРОВАНИЕ ПОЛЬЗОВАТЕЛЯ О РЕГИСТРАЦИИ
                        if ($newUserData && is_array($newUserData)) {
                            $arUser = $newUserData;
                            $userId = $newUserData['ID'];
                        }
                    }
                    if ($post['USER_VEHICLE_TYPE']) {
                        $arUserVehicleType = getUserVehicleType($post['USER_VEHICLE_TYPE']);
                    }
                    if ($post['MODEL']) {
                        $arUserVehicle['MODEL'] = $post['MODEL'];
                    }
                }
                if ($userId) {
                    $now = time();
                    $elementName = 'Разрешение на транспортное средство[' . $userId . ']' . $now;
                    $hash_string = $elementName . $now;
                    $permission_unique_code = stringToHash($hash_string);
                    if (CModule::IncludeModule('iblock')) {
                        $el = new CIBlockElement;
                        $PROP = array();
                        $PROP['USER'] = $userRecordId ? $userRecordId : '';
                        $PROP['USER_FIO'] = $arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME'];
                        $PROP['USER_PHONE'] = $arUser['PHONE'];
                        if ($arUserVehicle) {
                            $PROP['USER_VEHICLE_NAME'] = $arUserVehicleType . ' ' . $arUserVehicle['MODEL'];
                        }
                        if ($post['PERMISSION_CODE']) {
                            $PROP['USER_PERMISSION'] = $post['PERMISSION_CODE'];
                        }
                        $PROP['PERMISSION_STATUS'] = VEHICLE_PERMISSION_STATUS;
                        $PROP['USER_ARRIVAL_DATE'] = $post['ARRIVAL_DATE'];
                        $PROP['USER_DEPARTURE_DATE'] = $post['DEPARTURE_DATE'];
                        $PROP['ROUTE'] = $post['ROUTE'];
                        $PROP['USER_VEHICLE'] = $post['USER_VEHICLE_ID'];
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
                            'permission_id' => $permissionId,
                            'blank_link' => 'https://' . $_SERVER['HTTP_HOST'] . '/receipt/transport/' . $permission_unique_code . '/',
                        ]);
                    } else {
                        return AjaxJson::createError([
                            'error' => $el->LAST_ERROR,
                        ]);
                    }
                } else {
                    return AjaxJson::createError(null, 'Пользователь отсутствует!');
                }
            } else {
                return AjaxJson::createError(null, 'нет значений!');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function executeComponent()
    {
        $this->arResult['ROUTS'] = getRouts();
        $this->arResult['VEHICLE_TYPES'] = getVehicleTypes();

        $this->getUserData($this->arResult, $this->arParams);
        $this->IncludeComponentTemplate($this->componentPage);
    }
}