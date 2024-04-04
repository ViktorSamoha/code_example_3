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

class AdminTransportPermissionList extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();
        return [
            'setUserVehicleBlockStatus' => [
                'prefilters' => [],
            ],
        ];
    }

    public function setUserVehicleBlockStatusAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['VEHICLE_ID']) && isset($post['ACTION'])) {
            if (Loader::includeModule("iblock")) {
                if ($post['ACTION'] == 'BLOCK') {
                    CIBlockElement::SetPropertyValuesEx($post['VEHICLE_ID'], false, array('BLOCKED' => 30));
                    return AjaxJson::createSuccess([
                        'VEHICLE_ID' => $post['VEHICLE_ID'],
                        'ACTION' => $post['ACTION'],
                    ]);
                } elseif ($post['ACTION'] == 'UNBLOCK') {
                    CIBlockElement::SetPropertyValuesEx($post['VEHICLE_ID'], false, array('BLOCKED' => 31));
                    return AjaxJson::createSuccess([
                        'VEHICLE_ID' => $post['VEHICLE_ID'],
                        'ACTION' => $post['ACTION'],
                    ]);
                } else {
                    return AjaxJson::createError(null, 'Не удалось выполнить действие!');
                }
            } else {
                return AjaxJson::createError(null, 'Не удалось подключить модуль iblock!');
            }
        } else {
            return AjaxJson::createError(null, 'Не заполнены обязательные поля!');
        }
    }

    public function getTransportPermissionList(&$arResult, $params)
    {
        $arSelect = array(
            "ID",
            'NAME',
            'CODE',
            "PROPERTY_USER",
            "PROPERTY_USER_VEHICLE",
            "PROPERTY_USER_ARRIVAL_DATE",
            "PROPERTY_USER_DEPARTURE_DATE",
            "PROPERTY_USER_VEHICLE_NAME",
            "PROPERTY_PERMISSION_STATUS",
            "PROPERTY_USER_FIO",
            "PROPERTY_USER_PHONE",
        );
        $now = date('Y-m-d');
        $arFilter = array("IBLOCK_ID" => IB_TRANSPORT_PERMISSION, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
        if ($params['FILTER_VALUE']) {
            $arFilter['ID'] = $params['FILTER_VALUE']['ID'];
            $arFilter['%PROPERTY_USER_FIO'] = $params['FILTER_VALUE']['USER_FIO'];
            $arFilter['>=DATE_CREATE'] = $params['FILTER_VALUE']['DATE'];
            if ($params['FILTER_VALUE']['ARCHIVE'] && $params['FILTER_VALUE']['ARCHIVE'] == 'Y') {
                $arFilter['<=PROPERTY_USER_DEPARTURE_DATE'] = $now;
            } else {
                $arFilter['>=PROPERTY_USER_DEPARTURE_DATE'] = $now;
            }
        } else {
            $arFilter['>=PROPERTY_USER_DEPARTURE_DATE'] = $now;
        }
        $res = CIBlockElement::GetList(["ID" => 'DESC'], $arFilter, false, [], $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arResult['TRANSPORT_PERMISSIONS'][] = [
                'ID' => $arFields['ID'],
                'NAME' => $arFields['NAME'],
                'USER_ID' => $arFields['PROPERTY_USER_VALUE'],
                'USER_FIO' => $arFields['PROPERTY_USER_FIO_VALUE'],
                'USER_PHONE' => $arFields['PROPERTY_USER_PHONE_VALUE'],
                'USER_VEHICLE' => $arFields['PROPERTY_USER_VEHICLE_VALUE'],
                'ARRIVAL_DATE' => $arFields['PROPERTY_USER_ARRIVAL_DATE_VALUE'],
                'DEPARTURE_DATE' => $arFields['PROPERTY_USER_DEPARTURE_DATE_VALUE'],
                'VEHICLE_NAME' => $arFields['PROPERTY_USER_VEHICLE_NAME_VALUE'],
                'PERMISSION_STATUS' => $arFields['PROPERTY_PERMISSION_STATUS_VALUE'],
                'LINK' => 'https://' . $_SERVER['HTTP_HOST'] . '/admin/transport_permission_edit/?CODE=' . $arFields['CODE'],
            ];
        }
    }

    public function executeComponent()
    {
        $this->getTransportPermissionList($this->arResult, $this->arParams);
        $this->IncludeComponentTemplate($this->componentPage);
    }
}