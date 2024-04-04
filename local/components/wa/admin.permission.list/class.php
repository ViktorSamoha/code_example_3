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

class AdminPermissionList extends CBitrixComponent implements Controllerable
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

    public function getPermissionList(&$arResult, $params)
    {
        $arPermissions = [];
        $now = date('Y-m-d');
        $arSelect = array(
            "ID",
            'NAME',
            'PROPERTY_USER_RECORD_ID',
            'PROPERTY_ARRIVAL_DATE',
            'PROPERTY_DEPARTURE_DATE',
            'CODE',
            'PROPERTY_USER_FIO',
            'PROPERTY_USER_PHONE',
            'PROPERTY_BOOKING_TYPE',
        );
        $arFilter = array("IBLOCK_ID" => IB_PERMISSION, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
        if ($params['FILTER_VALUE']) {
            $arFilter['ID'] = $params['FILTER_VALUE']['ID'];
            $arFilter['%PROPERTY_USER_FIO'] = $params['FILTER_VALUE']['USER_FIO'];
            $arFilter['>=DATE_CREATE'] = $params['FILTER_VALUE']['DATE'];
            if ($params['FILTER_VALUE']['ARCHIVE'] && $params['FILTER_VALUE']['ARCHIVE'] == 'Y') {
                $arFilter['<=PROPERTY_DEPARTURE_DATE'] = $now;
            } else {
                $arFilter['>=PROPERTY_DEPARTURE_DATE'] = $now;
            }
        } else {
            $arFilter['>=PROPERTY_DEPARTURE_DATE'] = $now;
        }
        $res = CIBlockElement::GetList(["ID" => 'DESC'], $arFilter, false, [], $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arPermissions[] = [
                'ID' => $arFields['ID'],
                'NAME' => $arFields['NAME'],
                'USER_FIO' => $arFields['PROPERTY_USER_FIO_VALUE'],
                'USER_PHONE' => $arFields['PROPERTY_USER_PHONE_VALUE'],
                'USER_RECORD_ID' => $arFields['PROPERTY_USER_RECORD_ID_VALUE'],
                'BOOKING_TYPE' => $arFields['PROPERTY_BOOKING_TYPE_VALUE'],
                'ARRIVAL_DATE' => $arFields['PROPERTY_ARRIVAL_DATE_VALUE'],
                'DEPARTURE_DATE' => $arFields['PROPERTY_DEPARTURE_DATE_VALUE'],
                'LINK' => 'https://' . $_SERVER['HTTP_HOST'] . '/receipt/user/' . $arFields['CODE'] . '/',
            ];
        }
        if (!empty($arPermissions)) {

            $arResult['PERMISSION_LIST'] = $arPermissions;
        }
    }

    public function executeComponent()
    {
        $this->getPermissionList($this->arResult, $this->arParams);
        $this->IncludeComponentTemplate($this->componentPage);
    }
}