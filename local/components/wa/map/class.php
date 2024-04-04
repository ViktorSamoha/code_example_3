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

class WAMap extends CBitrixComponent implements Controllerable
{

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'initYaMap' => [
                'prefilters' => [],
            ],
        ];
    }

    public function getObjectsList(&$arResult, $params)
    {
        if (Loader::includeModule("iblock")) {
            $arLocations = [];
            $locationData = [];
            $parentSectionCode = $parentSectionId = null;
            $arSelect = ["ID", "IBLOCK_ID", "NAME", 'PREVIEW_PICTURE', 'PROPERTY_PRICE', 'PROPERTY_NORTHERN_LATITUDE', 'PROPERTY_EASTERN_LONGITUDE', 'PROPERTY_ICON', 'CODE', 'IBLOCK_SECTION_ID'];
            $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "ACTIVE" => "Y");
            if ($params['FILTER_SECTIONS_ID']) {
                $arFilter['SECTION_ID'] = $params['FILTER_SECTIONS_ID'];
            }
            if ($params['FILTER_ELEMENTS_ID']) {
                $arFilter['ID'] = $params['FILTER_ELEMENTS_ID'];
            }
            $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $picture = false;
                if ($arFields['PREVIEW_PICTURE']) {
                    $picture = CFile::ResizeImageGet($arFields['PREVIEW_PICTURE'], array('width' => 194, 'height' => 151), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                }
                $parentSectionId = getParent($arFields['IBLOCK_SECTION_ID']);
                $parentSectionData = CIBlockSection::GetByID($parentSectionId);
                if ($ar_res = $parentSectionData->GetNext())
                    $parentSectionCode = $ar_res['CODE'];
                $locationData =
                    [
                        'ID' => $arFields['ID'],
                        'NAME' => $arFields['NAME'],
                        'PICTURE' => $picture,
                        'PRICE' => $arFields['PROPERTY_PRICE_VALUE'],
                        'NORTHERN_LATITUDE' => $arFields['PROPERTY_NORTHERN_LATITUDE_VALUE'],
                        'EASTERN_LONGITUDE' => $arFields['PROPERTY_EASTERN_LONGITUDE_VALUE'],
                        'ICON' => CFile::GetPath($arFields['PROPERTY_ICON_VALUE']),
                    ];
                if ($parentSectionCode) {
                    $locationData['LINK'] = '/catalog/' . $parentSectionCode . '/' . $arFields['CODE'] . '/';
                }
                $arLocations[] = $locationData;
            }
            unset($res, $ob);
            if (!empty($arLocations)) {
                foreach ($arLocations as &$location) {
                    $res = CIBlockElement::GetProperty(IB_LOCATIONS, $location['ID'], array("sort" => "asc"), array("CODE" => "LOCATION_FEATURES"));
                    while ($ob = $res->GetNext()) {
                        $prop = $ob['VALUE'];
                        $location['FEATURES'][] = $prop;
                    }
                    unset($res, $ob, $prop);
                    $res = CIBlockElement::GetProperty(IB_LOCATIONS, $location['ID'], array("sort" => "asc"), array("CODE" => "PRICE_TYPE"));
                    while ($ob = $res->GetNext()) {
                        $prop = $ob['VALUE_ENUM'];
                        $location['PRICE_TYPE'] = $prop;
                    }
                    unset($res, $ob, $prop);
                    $res = CIBlockElement::GetProperty(IB_LOCATIONS, $location['ID'], array("sort" => "asc"), array("CODE" => "OBJECT_TYPE"));
                    while ($ob = $res->GetNext()) {
                        $prop = $ob['VALUE'];
                        $location['OBJECT_TYPE'] = $prop;
                    }
                }
                unset($location);
                if (Loader::includeModule("highloadblock")) {
                    $hlbl = HL_OBJECT_FEATURES;
                    $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
                    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                    $entity_data_class = $entity->getDataClass();
                    foreach ($arLocations as &$location) {
                        if ($location['FEATURES']) {
                            foreach ($location['FEATURES'] as &$xmlId) {
                                $rsData = $entity_data_class::getList(array(
                                    "select" => array("UF_OF_NAME"),
                                    "order" => array("ID" => "ASC"),
                                    "filter" => array("UF_XML_ID" => $xmlId)
                                ));
                                while ($arData = $rsData->Fetch()) {
                                    $xmlId = $arData["UF_OF_NAME"];
                                }
                            }
                        }
                    }
                    unset($location, $hlbl, $hlblock, $entity, $entity_data_class, $rsData);
                    $hlbl = HL_OBJECT_TYPE;
                    $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
                    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                    $entity_data_class = $entity->getDataClass();
                    foreach ($arLocations as &$location) {
                        if ($location['OBJECT_TYPE']) {
                            $rsData = $entity_data_class::getList(array(
                                "select" => array("UF_NAME"),
                                "order" => array("ID" => "ASC"),
                                "filter" => array("UF_XML_ID" => $location['OBJECT_TYPE'])
                            ));
                            while ($arData = $rsData->Fetch()) {
                                $location['OBJECT_TYPE'] = $arData["UF_NAME"];
                            }
                        }
                    }
                    unset($location, $hlbl, $hlblock, $entity, $entity_data_class, $rsData);

                    $arResult['LOCATIONS'] = $arLocations;

                }
            }
        }
    }

    public function createMapPointsArray(&$arResult)
    {
        if ($arResult['LOCATIONS']) {
            $arPoints = [];
            $features = [];

            foreach ($arResult['LOCATIONS'] as $arLocation) {
                if (!empty($arLocation["ID"])
                    && !empty($arLocation["NORTHERN_LATITUDE"])
                    && !empty($arLocation["EASTERN_LONGITUDE"])
                    && !empty($arLocation["NAME"])
                ) {
                    $arPoints[$arLocation["ID"]] = [
                        "id" => $arLocation["ID"],
                        "coordinates" => [$arLocation["NORTHERN_LATITUDE"], $arLocation["EASTERN_LONGITUDE"]],
                        "hintContent" => html_entity_decode($arLocation["NAME"], ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8"),
                        "iconImageHref" => $arLocation['ICON'] ? $arLocation['ICON'] : '/local/templates/.default/assets/img/default_map_icon.svg',
                    ];
                }
            }

            $features = createMapPoint($arPoints);

            $json = [
                "type" => 'FeatureCollection',
                "features" => $features
            ];

            $arResult["MAP_JSON"] = json_encode($json, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
        }
    }

    public function executeComponent()
    {
        $this->getObjectsList($this->arResult, $this->arParams);

        $this->createMapPointsArray($this->arResult);

        $this->includeComponentTemplate();

    }
}