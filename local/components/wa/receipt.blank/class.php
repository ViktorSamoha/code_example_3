<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Bitrix\Main\Diag\Debug;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class WAReceiptBlank extends CBitrixComponent implements Controllerable
{

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'updateVehiclePermission' => [
                'prefilters' => [],
            ],
        ];
    }

    public function updateVehiclePermissionAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['PERMISSION_ID']) && isset($post['ACTION'])) {
            if (Loader::includeModule("iblock")) {
                if ($post['ACTION'] == 'DENY') {
                    if (isset($post['DENY_TEXT'])) {
                        CIBlockElement::SetPropertyValuesEx($post['PERMISSION_ID'], false, array('PERMISSION_STATUS' => 28, 'PERMISSION_DENY_TEXT' => $post['DENY_TEXT']));
                        return AjaxJson::createSuccess([
                            'PERMISSION_ID' => $post['PERMISSION_ID'],
                        ]);
                    } else {
                        return AjaxJson::createError(null, 'Отсутствует значение причины отказа!');
                    }
                } elseif ($post['ACTION'] == 'APPROVE') {
                    CIBlockElement::SetPropertyValuesEx($post['PERMISSION_ID'], false, array('PERMISSION_STATUS' => 27));
                    return AjaxJson::createSuccess([
                        'PERMISSION_ID' => $post['PERMISSION_ID'],
                    ]);
                } else {
                    return AjaxJson::createError(null, 'не заполнены обязательные поля!');
                }
            } else {
                return AjaxJson::createError(null, 'не удалось подключить модуль iblock!');
            }
        } else {
            return AjaxJson::createError(null, 'не заполнены обязательные поля!');
        }
    }

    public function getUserVehicleData($vehicleId)
    {
        if ($vehicleId) {
            if (Loader::includeModule("iblock")) {
                $arVehicle = [];
                $arSelect = array(
                    "ID",
                    "PROPERTY_DRIVING_LICENSE_SERIES",
                    "PROPERTY_DRIVING_LICENSE_NUMBER",
                    "PROPERTY_MODEL",
                    "PROPERTY_INSPECTION_DATE",
                    "PROPERTY_VEHICLE_POWER",
                    "PROPERTY_VEHICLE_TYPE",
                );
                $arFilter = array("IBLOCK_ID" => IB_TRANSPORT, "ACTIVE" => "Y", 'ID' => $vehicleId);
                $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arVehicle = [
                        'ID' => $arFields['ID'],
                        'DRIVING_LICENSE_SERIES' => $arFields['PROPERTY_DRIVING_LICENSE_SERIES_VALUE'],
                        'DRIVING_LICENSE_NUMBER' => $arFields['PROPERTY_DRIVING_LICENSE_NUMBER_VALUE'],
                        'MODEL' => $arFields['PROPERTY_MODEL_VALUE'],
                        'INSPECTION_DATE' => $arFields['PROPERTY_INSPECTION_DATE_VALUE'],
                        'VEHICLE_POWER' => $arFields['PROPERTY_VEHICLE_POWER_VALUE'],
                        'VEHICLE_TYPE' => $arFields['PROPERTY_VEHICLE_TYPE_VALUE'][0],
                    ];
                }
                if (!empty($arVehicle)) {
                    if ($arVehicle['VEHICLE_TYPE']) {
                        $arVehicle['VEHICLE_TYPE'] = getUserVehicleType($arVehicle['VEHICLE_TYPE']);
                        return $arVehicle;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getUserGroupData($arGroup)
    {
        if ($arGroup) {
            if (Loader::includeModule("iblock")) {
                $arGroupData = [];
                $arSelect = array(
                    "ID",
                    "PROPERTY_U_LAST_NAME",
                    "PROPERTY_U_NAME",
                    "PROPERTY_U_SECOND_NAME",
                    "PROPERTY_U_PREFERENTIAL_CATEGORY",
                    "PROPERTY_U_PREF_DOC_NUMBER",
                );
                $arFilter = array("IBLOCK_ID" => IB_VISITORS, "ACTIVE" => "Y", 'ID' => $arGroup);
                $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arGroupData[] = [
                        'ID' => $arFields['ID'],
                        'LAST_NAME' => $arFields['PROPERTY_U_LAST_NAME_VALUE'],
                        'NAME' => $arFields['PROPERTY_U_NAME_VALUE'],
                        'SECOND_NAME' => $arFields['PROPERTY_U_SECOND_NAME_VALUE'],
                        'PREFERENTIAL_CATEGORY' => $arFields['PROPERTY_U_PREFERENTIAL_CATEGORY_VALUE'],
                        'PREF_DOC_NUMBER' => $arFields['PROPERTY_U_PREF_DOC_NUMBER_VALUE'],
                    ];
                }
                if (!empty($arGroupData)) {
                    return $arGroupData;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    public function getRouteData($routeId)
    {
        if ($routeId) {
            if (Loader::includeModule("iblock")) {
                $sectionName = false;
                $res = CIBlockElement::GetByID($routeId);
                if ($ar_res = $res->GetNext()) {
                    $sectionName = $ar_res['NAME'];
                }
                if ($sectionName) {
                    return $sectionName;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getUserData($userRecordId)
    {
        if ($userRecordId) {
            if (Loader::includeModule("iblock")) {
                $userId = false;
                $arSelect = array("PROPERTY_USER_ID");
                $arFilter = array("IBLOCK_ID" => IB_USERS, "ACTIVE" => "Y", 'ID' => $userRecordId);
                $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $userId = $arFields['PROPERTY_USER_ID_VALUE'];
                }
                if ($userId) {
                    unset($res);
                    $arUser = [];
                    $arParams["FIELDS"] = array("ID", "NAME", "LAST_NAME", "SECOND_NAME");
                    $filter = array("ACTIVE" => "Y", 'ID' => $userId);
                    $rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), $filter, $arParams);
                    while ($res = $rsUsers->GetNext()) {
                        $arUser = [
                            'ID' => $res['ID'],
                            'NAME' => $res['NAME'],
                            'LAST_NAME' => $res['LAST_NAME'],
                            'SECOND_NAME' => $res['SECOND_NAME'],
                        ];
                    }
                    if (!empty($arUser)) {
                        return $arUser;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getTransportDocs($transportId)
    {
        if ($transportId) {
            if (Loader::includeModule("iblock")) {
                $arTransportDocs = [];
                $arSelect = array(
                    "ID",
                    "PROPERTY_DRIVING_LICENSE_FILES",
                    "PROPERTY_TECHNICAL_PASSPORT",
                    "PROPERTY_INSPECTION_FILES",
                );
                $arFilter = array("IBLOCK_ID" => IB_TRANSPORT, "ACTIVE" => "Y", 'ID' => $transportId);
                $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    if ($arFields['PROPERTY_DRIVING_LICENSE_FILES_VALUE']) {
                        foreach ($arFields['PROPERTY_DRIVING_LICENSE_FILES_VALUE'] as $drivingLicenseFile) {
                            $arTransportDocs[$arFields['ID']]['DRIVING_LICENSE_FILES'][] = CFile::GetFileArray($drivingLicenseFile);
                        }
                    }
                    if ($arFields['PROPERTY_TECHNICAL_PASSPORT_VALUE']) {
                        foreach ($arFields['PROPERTY_TECHNICAL_PASSPORT_VALUE'] as $technicalPassportFile) {
                            $arTransportDocs[$arFields['ID']]['TECHNICAL_PASSPORT'][] = CFile::GetFileArray($technicalPassportFile);
                        }
                    }
                    if ($arFields['PROPERTY_INSPECTION_FILES_VALUE']) {
                        foreach ($arFields['PROPERTY_INSPECTION_FILES_VALUE'] as $inspectionFile) {
                            $arTransportDocs[$arFields['ID']]['INSPECTION_FILES'][] = CFile::GetFileArray($inspectionFile);
                        }
                    }
                }
                if (!empty($arTransportDocs)) {
                    return $arTransportDocs;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getBlankData($params)
    {
        if ($params['ELEMENT_CODE'] && $params['IBLOCK_ID']) {
            if (Loader::includeModule("iblock")) {
                $arPermissionData = [];
                $arOrderData = [];
                if ($params['IBLOCK_ID'] == IB_PERMISSION) {
                    $arSelect = array(
                        "ID",
                        "NAME",
                        "PROPERTY_USER_RECORD_ID",
                        "PROPERTY_ARRIVAL_DATE",
                        "PROPERTY_DEPARTURE_DATE",
                        "PROPERTY_ROUTE",
                        "PROPERTY_QR_CODE",
                        "PROPERTY_PAYMENT_LINK",
                        "PROPERTY_PAYMENT_STATUS",
                        "PROPERTY_PAYMENT_DATE_TIME",
                        "PROPERTY_USER_GROUP",
                        "PROPERTY_PRICE",
                    );
                } elseif ($params['IBLOCK_ID'] == IB_TRANSPORT_PERMISSION) {
                    $arSelect = array(
                        "ID",
                        "NAME",
                        "PROPERTY_USER",
                        "PROPERTY_USER_ARRIVAL_DATE",
                        "PROPERTY_USER_DEPARTURE_DATE",
                        "PROPERTY_USER_VEHICLE_NAME",
                        "PROPERTY_QR_CODE",
                        "PROPERTY_PERMISSION_STATUS",
                        "PROPERTY_PERMISSION_DENY_TEXT",
                        "PROPERTY_ROUTE",
                        "PROPERTY_USER_VEHICLE",
                        "PROPERTY_USER_FIO",
                    );
                } elseif ($params['IBLOCK_ID'] == IB_BOOKING_LIST) {
                    $arSelect = array(
                        "ID",
                        "NAME",
                        "PROPERTY_NAME",
                        "PROPERTY_SURNAME",
                        "PROPERTY_ARRIVAL_DATE",
                        "PROPERTY_DEPARTURE_DATE",
                        "PROPERTY_CHECK_IN_TIME",
                        "PROPERTY_DEPARTURE_TIME",
                        "PROPERTY_PERMISSION",
                        "PROPERTY_ADULTS",
                        "PROPERTY_BENIFICIARIES",
                        "PROPERTY_EMAIL",
                        "PROPERTY_PHONE",
                        "PROPERTY_BOOKING_OBJECT",
                        "PROPERTY_BOOKING_TYPE",
                        "PROPERTY_BOOKING_COST",
                        "PROPERTY_QR_CODE",
                        "PROPERTY_IS_PAYED",
                        "PROPERTY_DATE_PAY",
                        "PROPERTY_OBJECT_RENT_COST",
                        "PROPERTY_VISIT_PERMISSION_COST",
                        "PROPERTY_GUEST_CARS",
                    );
                }
                $arFilter = array("IBLOCK_ID" => $params['IBLOCK_ID'], "ACTIVE" => "Y", 'CODE' => $params['ELEMENT_CODE']);
                $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    if ($params['IBLOCK_ID'] == IB_PERMISSION) {
                        $arPermissionData = [
                            'ID' => $arFields['ID'],
                            'NAME' => $arFields['NAME'],
                            'USER_RECORD_ID' => $arFields['PROPERTY_USER_RECORD_ID_VALUE'],
                            'ARRIVAL_DATE' => $arFields['PROPERTY_ARRIVAL_DATE_VALUE'],
                            'DEPARTURE_DATE' => $arFields['PROPERTY_DEPARTURE_DATE_VALUE'],
                            'ROUTE_ID' => $arFields['PROPERTY_ROUTE_VALUE'],
                            'QR_CODE' => CFile::GetPath($arFields['PROPERTY_QR_CODE_VALUE']),
                            'PAYMENT_LINK' => $arFields['PROPERTY_PAYMENT_LINK_VALUE'],
                            'PAYMENT_STATUS' => $arFields['PROPERTY_PAYMENT_STATUS_VALUE'],
                            'PAYMENT_DATE_TIME' => $arFields['PROPERTY_PAYMENT_DATE_TIME_VALUE'],
                            'USER_GROUP' => $arFields['PROPERTY_USER_GROUP_VALUE'],
                            'PRICE' => $arFields['PROPERTY_PRICE_VALUE'],
                        ];
                    } elseif ($params['IBLOCK_ID'] == IB_TRANSPORT_PERMISSION) {
                        $arPermissionData = [
                            'ID' => $arFields['ID'],
                            'NAME' => $arFields['NAME'],
                            'USER_RECORD_ID' => $arFields['PROPERTY_USER_VALUE'],
                            'ARRIVAL_DATE' => $arFields['PROPERTY_USER_ARRIVAL_DATE_VALUE'],
                            'DEPARTURE_DATE' => $arFields['PROPERTY_USER_DEPARTURE_DATE_VALUE'],
                            'ROUTE_ID' => $arFields['PROPERTY_ROUTE_VALUE'],
                            'QR_CODE' => CFile::GetPath($arFields['PROPERTY_QR_CODE_VALUE']),
                            'STATUS' => $arFields['PROPERTY_PERMISSION_STATUS_VALUE'],
                            'DENY_TEXT' => $arFields['PROPERTY_PERMISSION_DENY_TEXT_VALUE'],
                            'VEHICLE_NAME' => $arFields['PROPERTY_USER_VEHICLE_NAME_VALUE'],
                            'VEHICLE_ID' => $arFields['PROPERTY_USER_VEHICLE_VALUE'],
                            'USER_FIO' => $arFields['PROPERTY_USER_FIO_VALUE'],
                        ];
                    } elseif ($params['IBLOCK_ID'] == IB_BOOKING_LIST) {
                        $arOrderData = [
                            'ID' => $arFields['ID'],
                            'NAME' => $arFields['NAME'],
                            'USER_NAME' => $arFields['PROPERTY_NAME_VALUE'],
                            'USER_SECOND_NAME' => $arFields['PROPERTY_SURNAME_VALUE'],
                            'USER_EMAIL' => $arFields['PROPERTY_EMAIL_VALUE'],
                            'USER_PHONE' => $arFields['PROPERTY_PHONE_VALUE'],
                            'ARRIVAL_DATE' => $arFields['PROPERTY_ARRIVAL_DATE_VALUE'],
                            'DEPARTURE_DATE' => $arFields['PROPERTY_DEPARTURE_DATE_VALUE'],
                            'CHECK_IN_TIME' => $arFields['PROPERTY_CHECK_IN_TIME_VALUE'],
                            'DEPARTURE_TIME' => $arFields['PROPERTY_DEPARTURE_TIME_VALUE'],
                            'PERMISSION' => $arFields['PROPERTY_PERMISSION_VALUE'],
                            'GUEST_COUNT' => $arFields['PROPERTY_ADULTS_VALUE'],
                            'BENIFICIARIES_COUNT' => $arFields['PROPERTY_BENIFICIARIES_VALUE'],
                            'OBJECT_ID' => $arFields['PROPERTY_BOOKING_OBJECT_VALUE'],
                            'BOOKING_TYPE' => $arFields['PROPERTY_BOOKING_TYPE_VALUE'],
                            'BOOKING_COST' => $arFields['PROPERTY_BOOKING_COST_VALUE'],
                            'QR_CODE' => CFile::GetPath($arFields['PROPERTY_QR_CODE_VALUE']),
                            'IS_PAYED' => $arFields['PROPERTY_IS_PAYED_VALUE'],
                            'DATE_PAY' => $arFields['PROPERTY_DATE_PAY_VALUE'],
                            'OBJECT_RENT_COST' => $arFields['PROPERTY_OBJECT_RENT_COST_VALUE'],
                            'VISIT_PERMISSION_COST' => $arFields['PROPERTY_VISIT_PERMISSION_COST_VALUE'],
                            'GUEST_CARS' => $arFields['PROPERTY_GUEST_CARS_VALUE'],
                        ];
                    }
                }
                if (!empty($arPermissionData)) {
                    if ($params['ADMIN_MODE'] && $params['ADMIN_MODE'] == 'Y') {
                        $arPermissionData['VEHICLE_DOCS'] = $this->getTransportDocs($arPermissionData['VEHICLE_ID']);
                        return $arPermissionData;
                    } else {
                        return $arPermissionData;
                    }
                } elseif (!empty($arOrderData)) {
                    return $arOrderData;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function gatherBlankData($params, &$arResult)
    {
        if ($params['ELEMENT_CODE']) {
            $blankData = $this->getBlankData($params);
            if ($blankData) {
                if($blankData['USER_RECORD_ID']){
                    $userData = getUserData(getUserIdByRecordId($blankData['USER_RECORD_ID']));
                }
                $routeData = $this->getRouteData($blankData['ROUTE_ID']);
                $groupData = $this->getUserGroupData($blankData['USER_GROUP']);
                $vehicleData = $this->getUserVehicleData($blankData['VEHICLE_ID']);
                if ($userData) {
                    $arResult['USER_DATA'] = $userData;
                }
                if ($routeData) {
                    $arResult['ROUTE'] = $routeData;
                }
                if ($groupData) {
                    $arResult['USER_GROUP'] = $groupData;
                }
                if ($vehicleData) {
                    $arResult['USER_VEHICLE'] = $vehicleData;
                }
                $arResult['PERMISSION_DATA'] = $blankData;
            }
        }
    }

    public function executeComponent()
    {
        $this->gatherBlankData($this->arParams, $this->arResult);
        $this->includeComponentTemplate();
    }
}