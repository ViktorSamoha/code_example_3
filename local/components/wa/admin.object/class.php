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

class WAAdminObject extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();
        return [
            'deleteObject' => [
                'prefilters' => [],
            ],
            'saveObject' => [
                'prefilters' => [],
            ],
        ];
    }

    public function saveObjectAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        $files = $this->request->getFileList()->toArray();
        if (!empty($post)) {
            if (Loader::IncludeModule("iblock")) {
                /*
                 * TODO:ПРОВЕРИТЬ НЕОБХОДИМОСТЬ СЛЕДУЮЩИХ СВОЙСТВ ОБЪЕКТА В ФОРМЕ!
                'PROPERTY_PRICE',
                'PROPERTY_PRICE_TYPE',
                'PROPERTY_OBJECT_TYPE',
                'PROPERTY_DETAIL_GALLERY',
                 * */
                global $USER;
                $el = new CIBlockElement;
                $sectionValue = false;
                $PROP = [
                    'TIME_INTERVAL' => $post["TIME_INTERVAL"] ? $post["TIME_INTERVAL"] : '',
                    'BOOKING_ALERT_MSG' => $post["BOOKING_ALERT_MSG"] ? $post["BOOKING_ALERT_MSG"] : '',
                    'CAR_POSSIBILITY' => $post["CAR_POSSIBILITY"] ? $post["CAR_POSSIBILITY"] : '',
                    'CAR_CAPACITY' => $post["CAR_CAPACITY"] ? $post["CAR_CAPACITY"] : '',
                    'CAN_BOOK' => $post["CAN_BOOK"] ? $post["CAN_BOOK"] : '',
                    'TIME_UNLIMIT_OBJECT' => $post["TIME_UNLIMIT_OBJECT"] ? $post["TIME_UNLIMIT_OBJECT"] : '',
                    'CAPACITY_ESTIMATED' => $post["CAPACITY_ESTIMATED"] ? $post["CAPACITY_ESTIMATED"] : '',
                    'CAPACITY_MAXIMUM' => $post["CAPACITY_MAXIMUM"] ? $post["CAPACITY_MAXIMUM"] : '',
                    'OBJECT_COST' => $post["OBJECT_COST"] ? $post["OBJECT_COST"] : '',
                    'COST_PER_PERSON' => $post["COST_PER_PERSON"] ? $post["COST_PER_PERSON"] : '',
                    'OBJECT_DAILY_COST' => $post["OBJECT_DAILY_COST"] ? $post["OBJECT_DAILY_COST"] : '',
                    'COST_PER_PERSON_ONE_DAY' => $post["COST_PER_PERSON_ONE_DAY"] ? $post["COST_PER_PERSON_ONE_DAY"] : '',
                    'FIXED_COST' => $post["FIXED_COST"] ? $post["FIXED_COST"] : '',
                    'NORTHERN_LATITUDE' => $post["NORTHERN_LATITUDE"] ? $post["NORTHERN_LATITUDE"] : '',
                    'EASTERN_LONGITUDE' => $post["EASTERN_LONGITUDE"] ? $post["EASTERN_LONGITUDE"] : '',
                    'START_TIME' => $post["START_TIME"] ? $post["START_TIME"] : '',
                    'END_TIME' => $post["END_TIME"] ? $post["END_TIME"] : '',
                    'DAILY_TRAFFIC' => $post["DAILY_TRAFFIC"] ? $post["DAILY_TRAFFIC"] : '',
                    'ROUTE_COORDS' => $post["ROUTE_COORDS"] ? $post["ROUTE_COORDS"] : '',
                ];
                if ($post["SECTION_ID"]) {
                    $sectionValue[] = $post["SECTION_ID"];
                }
                if ($post["CATEGORY_ID"]) {
                    $sectionValue[] = $post["CATEGORY_ID"];
                }
                if ($post["LOCATION_ID"]) {
                    $sectionValue[] = $post["LOCATION_ID"];
                }
                if (isset($post["LOCATION_FEATURES"]) && !empty($post["LOCATION_FEATURES"])) {
                    $PROP['LOCATION_FEATURES'] = $post["LOCATION_FEATURES"];
                }
                if (isset($post["PARTNERS"]) && !empty($post["PARTNERS"])) {
                    if (is_array($post["PARTNERS"])) {
                        foreach ($post["PARTNERS"] as $partnerName) {
                            $PROP['PARTNERS'][] = getObjectPartnerByName($partnerName)['ID'];
                        }
                    }
                }
                foreach ($files as $propName => $arFile) {
                    list($propertyName, $filePos) = explode('_K_', $propName);
                    $PROP[$propertyName]['n' . $filePos] = ['VALUE' => $arFile];
                }
                $arLoadProductArray = array(
                    "MODIFIED_BY" => $USER->GetID(),
                    "IBLOCK_SECTION" => $sectionValue,
                    "IBLOCK_ID" => IB_LOCATIONS,
                    "SORT" => $post["SORT"],
                    "PROPERTY_VALUES" => $PROP,
                    "NAME" => $post["NAME"],
                    "CODE" => Cutil::translit($post["NAME"], "ru", ["replace_space" => "_", "replace_other" => "_"]),
                    "ACTIVE" => "Y",
                    "DETAIL_TEXT" => $post["DETAIL_TEXT"] ? $post["DETAIL_TEXT"] : '',
                );
                if (isset($post["OBJECT_ID"]) && !empty($post["OBJECT_ID"])) {
                    if ($el->Update($post["OBJECT_ID"], $arLoadProductArray)) {
                        return AjaxJson::createSuccess([
                            'data' => 'Информация об объекте обновлена успешно!',
                        ]);
                    } else {
                        return AjaxJson::createError(null, 'Ошибка обновления объекта');
                    }
                } else {
                    if ($newObjectId = $el->Add($arLoadProductArray)) {
                        return AjaxJson::createSuccess([
                            'id' => $newObjectId,
                        ]);
                    } else {
                        return AjaxJson::createError(null, $el->LAST_ERROR);
                    }
                }
            } else {
                return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function deleteObjectAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['OBJECT_ID'])) {
            if (Loader::IncludeModule("iblock")) {
                global $DB;
                $DB->StartTransaction();
                if (!CIBlockElement::Delete($post['OBJECT_ID'])) {
                    $DB->Rollback();
                    return AjaxJson::createError(null, 'Ошибка удаления элемента!');
                } else {
                    $DB->Commit();
                    return AjaxJson::createSuccess();
                }
            } else {
                return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function getObjectsList(&$arResult, $params)
    {
        $arObjects = [];
        if (Loader::includeModule("iblock")) {
            $arSelect = array(
                "ID",
                "NAME",
                'PROPERTY_TIME_INTERVAL',
                'PROPERTY_CAPACITY_MAXIMUM',
            );
            $arFilter = array("IBLOCK_ID" => IB_LOCATIONS);
            if (isset($params['FILTER_VALUE']) && !empty($params['FILTER_VALUE'])) {
                $arFilter = array_merge($arFilter, $params['FILTER_VALUE']);
            }
            $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arObjects[] = [
                    'ID' => $arFields['ID'],
                    'NAME' => $arFields['NAME'],
                    'TIME_INTERVAL' => $arFields['PROPERTY_TIME_INTERVAL_VALUE'],
                    'CAPACITY_MAXIMUM' => $arFields['PROPERTY_CAPACITY_MAXIMUM_VALUE'],
                ];
            }
            if (!empty($arObjects)) {
                foreach ($arObjects as &$arObject) {
                    $objectGroups = CIBlockElement::GetElementGroups($arObject['ID'], true);
                    while ($arGroup = $objectGroups->Fetch()) {
                        $arObject['SECTIONS'][] = [
                            'ID' => $arGroup['ID'],
                            'NAME' => $arGroup['NAME'],
                            'DEPTH_LEVEL' => $arGroup['DEPTH_LEVEL'],
                        ];
                    }
                }
                $arResult['OBJECTS'] = $arObjects;
            }
        }
    }

    public function executeComponent()
    {
        $this->getObjectsList($this->arResult, $this->arParams);
        $this->arResult['LOCATIONS'] = getLocationStructure();
        if ($_GET['OBJECT_ID']) {
            $this->arResult['OBJECT_DATA'] = getObjectById($_GET['OBJECT_ID']);
        }
        $this->IncludeComponentTemplate($this->componentPage);
    }
}