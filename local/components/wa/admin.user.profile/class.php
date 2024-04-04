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

class AdminUserProfile extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();
        return [
            '' => [
                'prefilters' => [],
            ],
        ];
    }

    public function getUserPermission($userRecordId)
    {
        if ($userRecordId) {
            $arUserPermissions = [];
            $arSelect = array(
                "ID",
                "DATE_CREATE",
                "PROPERTY_ARRIVAL_DATE",
                "PROPERTY_DEPARTURE_DATE",
                "PROPERTY_PAYMENT_LINK",
                "PROPERTY_PAYMENT_STATUS",
                "PROPERTY_BOOKING_TYPE",
                "CODE",
            );
            $arFilter = array("IBLOCK_ID" => IB_PERMISSION, "ACTIVE" => "Y", 'PROPERTY_USER_RECORD_ID' => $userRecordId);
            $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arUserPermissions[$arFields['ID']] = [
                    'ID' => $arFields['ID'],
                    'NAME' => 'Разрешение на посещение от ' . $arFields['DATE_CREATE'],
                    'DATE_CREATE' => $arFields['DATE_CREATE'],
                    'ARRIVAL_DATE' => $arFields['PROPERTY_ARRIVAL_DATE_VALUE'],
                    'DEPARTURE_DATE' => $arFields['PROPERTY_DEPARTURE_DATE_VALUE'],
                    'PAYMENT_LINK' => $arFields['PROPERTY_PAYMENT_LINK_VALUE'],
                    'PAYMENT_STATUS' => $arFields['PROPERTY_PAYMENT_STATUS_VALUE'],
                    'BOOKING_TYPE' => $arFields['PROPERTY_BOOKING_TYPE_VALUE'],
                    'BLANK_LINK' => '/receipt/user/' . $arFields['CODE'] . '/',
                ];
            }
            /*if (!empty($arUserPermissions)) {
                foreach ($arUserPermissions as &$arPermission) {
                    $arPermissionGroup = array();
                    $res = CIBlockElement::GetProperty(IB_PERMISSION, $arPermission['ID'], "sort", "asc", array("CODE" => "USER_GROUP"));
                    while ($ob = $res->GetNext()) {
                        $arPermissionGroup[] = $ob['VALUE'];
                    }
                    if (!empty($arPermissionGroup)) {
                        foreach ($arPermissionGroup as &$visitorId) {
                            $arSelect = array("ID", "PROPERTY_U_LAST_NAME", "PROPERTY_U_NAME", "PROPERTY_U_SECOND_NAME",);
                            $arFilter = array("IBLOCK_ID" => IB_VISITORS, "ACTIVE" => "Y", 'ID' => $visitorId);
                            $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                            while ($ob = $res->GetNextElement()) {
                                $arFields = $ob->GetFields();
                                $visitorId = [
                                    'ID' => $visitorId,
                                    'NAME' => $arFields['PROPERTY_U_NAME_VALUE'],
                                    'LAST_NAME' => $arFields['PROPERTY_U_LAST_NAME_VALUE'],
                                    'SECOND_NAME' => $arFields['PROPERTY_U_SECOND_NAME_VALUE'],
                                ];
                            }
                        }
                        $arPermission['USER_GROUP'] = $arPermissionGroup;
                    }
                }
            }*/
            return $arUserPermissions;
        } else {
            return false;
        }
    }

    public function getUserTransportPermission($userRecordId)
    {
        if ($userRecordId) {
            $arUserTransportPermission = [];
            $arSelect = array(
                "ID",
                'DATE_CREATE',
                "PROPERTY_USER_ARRIVAL_DATE",
                "PROPERTY_USER_DEPARTURE_DATE",
                "PROPERTY_USER_VEHICLE_NAME",
                "CODE",
            );
            $arFilter = array("IBLOCK_ID" => IB_TRANSPORT_PERMISSION, "ACTIVE" => "Y", 'PROPERTY_USER_RECORD_ID' => $userRecordId);
            $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arUserTransportPermission[$arFields['ID']] = [
                    'ID' => $arFields['ID'],
                    'NAME' => 'Разрешение на транспортное средство ' . $arFields['PROPERTY_USER_VEHICLE_NAME_VALUE'],
                    'DATE_CREATE' => $arFields['DATE_CREATE'],
                    'ARRIVAL_DATE' => $arFields['PROPERTY_USER_ARRIVAL_DATE_VALUE'],
                    'DEPARTURE_DATE' => $arFields['PROPERTY_USER_DEPARTURE_DATE_VALUE'],
                    'VEHICLE_NAME' => $arFields['PROPERTY_USER_VEHICLE_NAME_VALUE'],
                    'BLANK_LINK' => '/receipt/transport/' . $arFields['CODE'] . '/',
                ];
            }
            if (!empty($arUserTransportPermission)) {
                foreach ($arUserTransportPermission as &$arTransportPermission) {
                    $transportPermissionStatus = [];
                    $res = CIBlockElement::GetProperty(IB_TRANSPORT_PERMISSION, $arTransportPermission['ID'], "sort", "asc", array("CODE" => "PERMISSION_STATUS"));
                    while ($ob = $res->GetNext()) {
                        $transportPermissionStatus = [
                            'ID' => $ob['VALUE'],
                            'VALUE' => $ob['VALUE_ENUM'],
                        ];
                    }
                    if (!empty($transportPermissionStatus)) {
                        $arTransportPermission['PERMISSION_STATUS'] = $transportPermissionStatus;
                    }
                }
                return $arUserTransportPermission;
            }
        } else {
            return false;
        }
    }

    public function getOrderList(&$arResult, $params)
    {
        if ($params['USER_ID']) {
            $userRecordId = checkUserRecord($params['USER_ID']);
            if ($userRecordId) {
                $arResult['USER_PERMISSIONS'] = $this->getUserPermission($userRecordId);
                $arResult['USER_TRANSPORT_PERMISSIONS'] = $this->getUserTransportPermission($userRecordId);
            }
        }
    }

/*    public function getUserData(&$arResult, $params)
    {
        if ($params['USER_ID']) {
            $arUser = [];
            $arParams["FIELDS"] = array("ID", "NAME", "LAST_NAME", "SECOND_NAME", 'WORK_PHONE', 'EMAIL');
            $filter = array("ACTIVE" => "Y", "ID" => $params['USER_ID'], "GROUPS_ID" => VISITOR_PERMISSION_GROUP);
            $rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), $filter, $arParams);
            while ($res = $rsUsers->GetNext()) {
                $arUser = [
                    'ID' => $res['ID'],
                    'NAME' => $res['NAME'],
                    'LAST_NAME' => $res['LAST_NAME'],
                    'SECOND_NAME' => $res['SECOND_NAME'],
                    'WORK_PHONE' => $res['WORK_PHONE'],
                    'EMAIL' => $res['EMAIL'],
                ];
            }
            if (!empty($arUser)) {
                $arResult['USER_FIELDS'] = $arUser;
                if ($arUser['ID']) {
                    $arResult['USER_GROUP'] = getUserGroupDataByUserId($arUser['ID']);
                    $arResult['USER_TRANSPORT'] = getUserTransportByUserId($arUser['ID']);
                }
            }
        }
    }*/

    public function executeComponent()
    {
        $this->arResult['USER_FIELDS'] = getUserData($this->arParams['USER_ID']);
        $this->arResult['USER_GROUP'] = getUserGroupDataByUserId($this->arParams['USER_ID']);
        $this->arResult['USER_TRANSPORT'] = getUserTransportByUserId($this->arParams['USER_ID']);
        $this->getOrderList($this->arResult, $this->arParams);
        $this->IncludeComponentTemplate($this->componentPage);
    }
}