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

class WAFilter extends CBitrixComponent implements Controllerable
{

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'initYaMap' => [
                'prefilters' => [],
            ],
            'setFilter' => [
                'prefilters' => [],
            ],
        ];
    }

    public function checkArrivalDate($arr_id, $arrival_date, $departure_date)
    {
        Loader::includeModule("highloadblock");
        $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $now = new DateTime();
        $first_half_day = [
            0 => '8:00:00',
            1 => '9:00:00',
            2 => '10:00:00',
            3 => '11:00:00',
            4 => '12:00:00',
            5 => '13:00:00',
            6 => '14:00:00',
        ];
        $second_half_day = [
            7 => '15:00:00',
            8 => '16:00:00',
            9 => '17:00:00',
            10 => '18:00:00',
            11 => '19:00:00',
            12 => '20:00:00',
            13 => '21:00:00',
            14 => '22:00:00',
        ];
        $arItems = $arr_id;
        $arItemsDates = [];
        $obj_in_hl = [];
        $_unset = [];
        $data = $entity_data_class::getList(array(
            "select" => array("*"),
            "order" => array("ID" => "DESC"),
            "filter" => array("UF_OBJECT_ID" => $arr_id)
        ));
        while ($arData = $data->Fetch()) {
            if (in_array($arData["UF_OBJECT_ID"], $arr_id)) {
                $obj_in_hl[] = $arData["UF_OBJECT_ID"];
            }
            $arDates = _get_dates($arData['UF_ARRIVAL_DATE'], $arData['UF_DEPARTURE_DATE']);
            foreach ($arDates as $date) {
                $booked_dates[] = $date;
            }
            if (isset($arItemsDates[$arData['UF_OBJECT_ID']])) {
                $arItemsDates[$arData['UF_OBJECT_ID']] = array_merge($arItemsDates[$arData['UF_OBJECT_ID']], createSelectedDateArr($booked_dates));
            } else {
                $arItemsDates[$arData['UF_OBJECT_ID']] = createSelectedDateArr($booked_dates);
            }
            unset($arDates, $booked_dates);
        }

        $filter_dates = _get_dates($arrival_date . ' 00:00:00', $departure_date . ' 00:00:00');
        $filter_dates = createSelectedDateArr($filter_dates);
        $filter_date_count = count($filter_dates);

        foreach ($arItemsDates as $item_id => $date_time) {
            foreach ($date_time as $date => $time) {
                foreach ($filter_dates as $f_date => $f_date_time) {
                    if (DateTime::createFromFormat('d.m.Y', $f_date)->format('d.m.Y') == DateTime::createFromFormat('d.m.Y', $date)->format('d.m.Y')) {
                        if (count(array_intersect($first_half_day, $time['time'])) == 7 || count(array_intersect($second_half_day, $time['time'])) == 8) {
                            $_unset[$item_id][] = $date;
                        }
                    }
                }
            }
        }

        foreach ($_unset as $id => $unset_date) {
            if (count($unset_date) != $filter_date_count) {
                unset($_unset[$id]);
            }
        }

        if (!empty($_unset)) {
            foreach ($_unset as $id => $unset_dates) {
                foreach ($arItems as $i => $item_id) {
                    if ($item_id == $id) {
                        unset($arItems[$i]);
                    }
                }
            }
        }

        if (!empty($arItems)) {
            return $arItems;
        } else {
            return false;
        }

    }

    public function setFilterAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        Loader::includeModule("iblock");
        $function_result = [];
        $locationId = $post['locationId'] ? $post['locationId'] : false;
        $objectTypeId = $post['objectTypeId'] ? $post['objectTypeId'] : false;
        $guestCount = $post['guestCount'] ? $post['guestCount'] : false;
        $arrivalDate = $post['arrivalDate'] ? $post['arrivalDate'] : false;
        $departureDate = $post['departureDate'] ? $post['departureDate'] : false;
        $arLocations = [];
        $arLocationElements = [];
        if ($locationId && $locationId != 'all') {
            $arLocations[] = $locationId;
        }
        $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "ACTIVE" => "Y");
        if ($locationId && $locationId != 'all') {
            $arFilter['=SECTION_ID'] = $locationId;
        }
        if ($objectTypeId && $objectTypeId != "undefined") {
            $arFilter['=PROPERTY_OBJECT_TYPE'] = $objectTypeId;
        }
        if ($guestCount) {
            $arFilter['>=PROPERTY_CAPACITY_MAXIMUM'] = $guestCount;
        }
        $arSelect = ["ID", "IBLOCK_ID", "NAME"];
        $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arLocationElements[] = $arFields['ID'];
        }
        if (($arrivalDate && $departureDate) && $arLocationElements) {
            if (!empty($arLocationElements)) {
                $arLocationElements = $this->checkArrivalDate($arLocationElements, $arrivalDate, $departureDate);
            }
        }
        if (!empty($locationId)) {
            $function_result['SECTIONS_ID'] = $arLocations;
        }
        if (!empty($arLocationElements)) {
            $function_result['ELEMENTS_ID'] = $arLocationElements;
        }
        return AjaxJson::createSuccess([
            'data' => $function_result,
        ]);
    }

    public function getFilterLocationList(&$arResult, $params)
    {
        if (Loader::includeModule("iblock")) {
            $arLocations = [];
            $arFilter = array('IBLOCK_ID' => IB_LOCATIONS, 'GLOBAL_ACTIVE' => 'Y', '>DEPTH_LEVEL' => 2);
            if (isset($params['PARENT_SECTION_ID'])) {
                $arFilter['IBLOCK_SECTION_ID'] = $params['PARENT_SECTION_ID'];
            }
            $arSelect = ['ID', 'NAME'];
            $db_list = CIBlockSection::GetList(array($by => $order), $arFilter, true, $arSelect);
            while ($ar_result = $db_list->GetNext()) {
                $arLocations[] = [
                    'ID' => $ar_result['ID'],
                    'NAME' => $ar_result['NAME'],
                ];
            }
            $arLocationElements = [];
            $arSelect = ["ID", "IBLOCK_ID", "NAME", "IBLOCK_SECTION", 'PROPERTY_ICON', 'PROPERTY_CAPACITY_MAXIMUM'];
            $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "ACTIVE" => "Y");
            if (isset($params['PARENT_SECTION_ID'])) {
                $arFilter['SECTION_ID'] = $params['PARENT_SECTION_ID'];
            }
            $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arLocationElements[] = [
                    'ID' => $arFields['ID'],
                    'NAME' => $arFields['NAME'],
                    'ICON' => CFile::GetPath($arFields['PROPERTY_ICON_VALUE']),
                    'CAPACITY' => $arFields['PROPERTY_CAPACITY_MAXIMUM_VALUE'],
                ];
            }
            unset($res, $ob);
            if (!empty($arLocations)) {
                foreach ($arLocationElements as &$location) {
                    $res = CIBlockElement::GetProperty(IB_LOCATIONS, $location['ID'], array("sort" => "asc"), array("CODE" => "OBJECT_TYPE"));
                    while ($ob = $res->GetNext()) {
                        $prop = $ob['VALUE'];
                        $location['OBJECT_TYPE'] = $prop;
                    }
                }
                unset($location);
                if (Loader::includeModule("highloadblock")) {
                    $hlbl = HL_OBJECT_TYPE;
                    $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
                    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                    $entity_data_class = $entity->getDataClass();
                    foreach ($arLocationElements as &$location) {
                        if ($location['OBJECT_TYPE']) {
                            $objectTypeXmlId = $location['OBJECT_TYPE'];
                            $rsData = $entity_data_class::getList(array(
                                "select" => array('ID', "UF_NAME"),
                                "order" => array("ID" => "ASC"),
                                "filter" => array("UF_XML_ID" => $location['OBJECT_TYPE'])
                            ));
                            while ($arData = $rsData->Fetch()) {
                                $location['OBJECT_TYPE_DATA'] = [
                                    'ID' => $arData["ID"],
                                    'NAME' => $arData["UF_NAME"],
                                    'XML_ID' => $objectTypeXmlId,
                                ];
                            }
                        }
                    }
                    unset($location, $hlbl, $hlblock, $entity, $entity_data_class, $rsData);
                    $arObjectTypes = [];
                    $arInArray = [];
                    foreach ($arLocationElements as $location) {
                        if ($location['OBJECT_TYPE_DATA']) {
                            if (!empty($arInArray)) {
                                if (!in_array($location['OBJECT_TYPE_DATA']["ID"], $arInArray)) {
                                    $arObjectTypes[] = [
                                        'LOCATION_ID' => $location['OBJECT_TYPE_DATA']['ID'],
                                        'XML_ID' => $location['OBJECT_TYPE_DATA']['XML_ID'],
                                        'NAME' => $location['OBJECT_TYPE_DATA']['NAME'],
                                        'ICON' => $location['ICON'] ? $location['ICON'] : '/local/templates/.default/assets/img/default_map_icon.svg',
                                    ];
                                    $arInArray[] = $location['OBJECT_TYPE_DATA']["ID"];
                                }
                            } else {
                                $arObjectTypes[] = [
                                    'LOCATION_ID' => $location['OBJECT_TYPE_DATA']['ID'],
                                    'XML_ID' => $location['OBJECT_TYPE_DATA']['XML_ID'],
                                    'NAME' => $location['OBJECT_TYPE_DATA']['NAME'],
                                    'ICON' => $location['ICON'] ? $location['ICON'] : '/local/templates/.default/assets/img/default_map_icon.svg',
                                ];
                                $arInArray[] = $location['OBJECT_TYPE_DATA']["ID"];
                            }
                        }
                    }
                    unset($arInArray);
                    if ($arObjectTypes) {
                        $arResult['OBJECT_TYPES'] = $arObjectTypes;
                    }
                    $arResult['LOCATION_ELEMENTS'] = $arLocationElements;
                    $arResult['LOCATIONS'] = $arLocations;
                }
            }
        }
    }

    public function executeComponent()
    {
        $this->getFilterLocationList($this->arResult, $this->arParams);

        $this->includeComponentTemplate();

    }
}