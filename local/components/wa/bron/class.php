<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

/*use Bitrix\Main\Context;
use Bitrix\Main\Request; */

use Bitrix\Main\Diag\Debug;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class WABron extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'sortContent' => [
                'prefilters' => [],
            ],
            'initYaMap' => [
                'prefilters' => [],
            ],
            'getDetailCardData' => [
                'prefilters' => [],
            ],
        ];
    }

    public function getDBData()
    {
        $params = [
            "IBLOCK_TYPE" => "facility",
            "IBLOCK_ID" => IB_OBJECT,
            "FIELD_CODE" => [
                0 => "NAME",
                1 => "PREVIEW_PICTURE",
                2 => "DETAIL_PAGE_URL",
            ],
            "PROPERTY_CODE" => [
                0 => "PROPERTY_LOCATION",
                1 => "PROPERTY_CAPACITY_MAXIMUM",
            ],
            "SECTION_PROPERTY_CODE" => [
                0 => "UF_CB_SVG_ICON",
                1 => "UF_CB_ICON_CLASS_COLOR",
            ],
            "LOCATIONS_IBLOCK_TYPE" => "location",
            "LOCATIONS_IBLOCK_ID" => IB_LOCATIONS,
        ];

        $arResult = $this->getFacilities($params);

        $this->getLocations($arResult, $params);

        foreach ($arResult['SECTIONS'] as $arSection) {
            foreach ($arSection['ITEMS'] as $arItem) {
                if (!empty($arItem['FIELDS']['PROPERTY_LOCATION_VALUE'])) {
                    $arResult['LOCATIONS'][$arItem['FIELDS']['PROPERTY_LOCATION_VALUE']]['ITEMS'][$arItem['FIELDS']['ID']] = $arItem;
                }
            }
        }

        foreach ($arResult['LOCATIONS'] as $locationId => &$ar_location) {
            if (!empty($ar_location['ITEMS'])) {
                foreach ($ar_location['ITEMS'] as &$ar_item) {
                    if (!empty($ar_item['PROPS']['DETAIL_GALLERY']['VALUE']) && count($ar_item['PROPS']['DETAIL_GALLERY']['VALUE']) > 0) {
                        $ar_item['FIELDS']['PREVIEW_PICTURE'] = CFile::ResizeImageGet(CFile::GetFileArray($ar_item['PROPS']['DETAIL_GALLERY']['VALUE'][0]), array('width' => 250, 'height' => 250), BX_RESIZE_IMAGE_PROPORTIONAL, false);
                        $ar_item['FIELDS']['PREVIEW_PICTURE']['SRC'] = $ar_item['FIELDS']['PREVIEW_PICTURE']['src'];
                    }
                }
                //запрещаем вывод неактивных локаций
                $res = CIBlockElement::GetByID($locationId);
                if ($ar_res = $res->GetNext()) {
                    if ($ar_res['ACTIVE'] == 'N') {
                        unset($arResult['LOCATIONS'][$locationId]);
                    }
                }
            }
        }

        return $arResult;
    }

    public function sortContentAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        $arResult = $this->getDBData();
        if (isset($arResult['LOCATIONS']) && isset($post["location_id"]) && $post["location_id"] != 'all') {
            $arResult['LOCATIONS'] = $this->sortByLocation($arResult['LOCATIONS'], $post["location_id"]);
        }
        if (isset($arResult['LOCATIONS']) && !empty($post["section_id"])) {
            $arResult['LOCATIONS'] = $this->sortBySectionID($arResult['LOCATIONS'], $post["section_id"]);
        }
        if (isset($arResult['LOCATIONS']) && !empty($post["guest_quantity"])) {
            $arResult['LOCATIONS'] = $this->checkGuestsCapacity($arResult['LOCATIONS'], $post["guest_quantity"]);
        }
        if (isset($arResult['LOCATIONS']) && !empty($post["arrival_date"]) && !empty($post["departure_date"])) {
            $arResult['LOCATIONS'] = $this->sortByDate($arResult['LOCATIONS'], $post["arrival_date"], $post["departure_date"]);
        }
        $arResult = $this->createMapData($arResult);
        return AjaxJson::createSuccess([
            'data' => $arResult,
        ]);
    }

    public function initYaMapAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post["arLocations"])) {
            $arResult['LOCATIONS'] = $post["arLocations"];
        }
        $arResult = $this->createMapData($arResult);
        return AjaxJson::createSuccess([
            'data' => $arResult,
        ]);
    }

    public function getDetailCardDataAction(): AjaxJson
    {
        Loader::includeModule("iblock");
        Loader::includeModule("highloadblock");
        $post = $this->request->getPostList()->toArray();
        if (isset($post["id"])) {
            $item_id = $post['id'];
            $item_data = []; //json массив
            $arSelect = array(
                "ID",
                "IBLOCK_ID",
                "NAME",
                "DETAIL_TEXT",
                "PROPERTY_OBJECT_COST",
                "PROPERTY_CAN_BOOK",
                "PROPERTY_OBJECT_DAILY_COST",
                "PROPERTY_COST_PER_PERSON",
                "PROPERTY_COST_PER_PERSON_ONE_DAY",
                "PROPERTY_CAPACITY_ESTIMATED",
                "PROPERTY_CAPACITY_MAXIMUM",
                "PROPERTY_FIXED_COST",
                "PROPERTY_TIME_UNLIMIT_OBJECT",
                "PROPERTY_CAR_POSSIBILITY",
                "PROPERTY_CAR_CAPACITY",
            );
            $arFilter = array("IBLOCK_ID" => IB_OBJECT, "ID" => $item_id);
            $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect); //достаем элементы из инфоблока
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields(); //массив полей
                $arProps = $ob->GetProperties(); //массив свойств
            }
            $item_data = [
                "ID" => $arFields['ID'],
                "NAME" => $arFields['NAME'],
                "DETAIL_TEXT" => $arFields['DETAIL_TEXT'],
                "OBJECT_COST" => $arFields['PROPERTY_OBJECT_COST_VALUE'] ? $arFields['PROPERTY_OBJECT_COST_VALUE'] : 0,
                "OBJECT_DAILY_COST" => $arFields['PROPERTY_OBJECT_DAILY_COST_VALUE'] ? $arFields['PROPERTY_OBJECT_DAILY_COST_VALUE'] : 0,
                "COST_PER_PERSON" => $arFields['PROPERTY_COST_PER_PERSON_VALUE'] ? $arFields['PROPERTY_COST_PER_PERSON_VALUE'] : 0,
                "COST_PER_PERSON_ONE_DAY" => $arFields['PROPERTY_COST_PER_PERSON_ONE_DAY_VALUE'] ? $arFields['PROPERTY_COST_PER_PERSON_ONE_DAY_VALUE'] : 0,
                "CAPACITY_ESTIMATED" => $arFields['PROPERTY_CAPACITY_ESTIMATED_VALUE'] ? $arFields['PROPERTY_CAPACITY_ESTIMATED_VALUE'] : 0,
                "CAPACITY_MAXIMUM" => $arFields['PROPERTY_CAPACITY_MAXIMUM_VALUE'] ? $arFields['PROPERTY_CAPACITY_MAXIMUM_VALUE'] : 0,
                "CAN_BOOK" => $arFields['PROPERTY_CAN_BOOK_VALUE'],
                "TIME_LIMIT" => $arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'] == 'Да' ? 'Y' : 'N',
                "FIXED_COST" => $arFields['PROPERTY_FIXED_COST_VALUE'] ? $arFields['PROPERTY_FIXED_COST_VALUE'] : 0,
                "CAR_POSSIBILITY" => $arFields['PROPERTY_CAR_POSSIBILITY_VALUE'] == 'Да' ? 'Y' : 'N',
                "CAR_CAPACITY" => $arFields['PROPERTY_CAR_CAPACITY_VALUE'] ? $arFields['PROPERTY_CAR_CAPACITY_VALUE'] : 0,
            ];
            if (!empty($arProps['DETAIL_GPS_N_L']['VALUE']) && !empty($arProps['DETAIL_GPS_E_L']['VALUE'])) {
                $item_data = array_merge($item_data, [
                    "GPS_N_L" => $arProps['DETAIL_GPS_N_L']['VALUE'],
                    "GPS_E_L" => $arProps['DETAIL_GPS_E_L']['VALUE'],
                ]);
            } else {
                $item_data = array_merge($item_data, [
                    "GPS_N_L" => $arProps['COORD_N_L']['VALUE'],
                    "GPS_E_L" => $arProps['COORD_E_L']['VALUE'],
                ]);
            }
            $item_pictures = [];
            $item_videos = [];
            foreach ($arProps['DETAIL_GALLERY']['VALUE'] as $picture) {
                $item_pictures[] = CFile::ResizeImageGet(CFile::GetFileArray($picture), array('width' => 450, 'height' => 450), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true)['src'];
            }
            foreach ($arProps['GALLERY_VIDEO_FILES']['VALUE'] as $video) {
                $file = CFile::GetFileArray($video);
                if ($file["CONTENT_TYPE"] == "video/mp4" || $file["CONTENT_TYPE"] == "video/webm") {
                    $item_videos[]['VIDEO'] = $file["SRC"];
                }
            }
            foreach ($arProps['GALLERY_VIDEO_PREVIEW']['VALUE'] as $preview) {
                $file = CFile::GetFileArray($preview);
                if ($file["CONTENT_TYPE"] == "image/jpeg" || $file["CONTENT_TYPE"] == "image/png") {
                    foreach ($item_videos as &$item_video) {
                        if (isset($item_video['VIDEO']) && !isset($item_video['PREVIEW'])) {
                            $item_video['PREVIEW'] = $file["SRC"];
                        }
                    }
                }
            }
            if (!empty($item_pictures)) {
                $item_data = array_merge($item_data, ["PICTURES" => $item_pictures]);
            }
            if (!empty($item_videos)) {
                $item_data = array_merge($item_data, ["VIDEOS" => $item_videos]);
            }
            $item_filter_props = [];
            foreach ($arProps['DETAIL_FEATURES']['VALUE'] as $feature) {
                $item_filter_props[] = $feature;
            }
            $service_cost_values = [];
            $res = CIBlockElement::GetProperty(IB_OBJECT, $item_id, array("sort" => "asc"), array("CODE" => "SERVICE_COST"));
            while ($ob = $res->GetNext()) {
                $service_cost_values[] = $ob['VALUE_ENUM'];
            }
            $item_data = array_merge($item_data, ["SERVICE_COST" => $service_cost_values]);
            $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_FEATURES)->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $data = $entity_data_class::getList(array(
                "select" => array("*"),
                "order" => array("ID" => "DESC"),
                "filter" => array("UF_XML_ID" => $item_filter_props)
            ));
            $item_features = [];
            while ($arData = $data->Fetch()) {
                $item_features[] = [
                    "ICON" => CFile::GetPath($arData["UF_OF_ICON"]),
                    "NAME" => $arData["UF_OF_NAME"],
                ];
            }
            $item_data = array_merge($item_data, ["FEATURES" => $item_features]);
            unset($hlblock, $entity, $data, $entity_data_class);
            $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $data = $entity_data_class::getList(array(
                "select" => array("*"),
                "order" => array("ID" => "DESC"),
                "filter" => array("UF_OBJECT_ID" => $item_id)
            ));
            $arPeriods = [];
            if ($item_data['TIME_LIMIT'] == 'N') {
                while ($arData = $data->Fetch()) {
                    $arDates = _get_dates($arData['UF_ARRIVAL_DATE'], $arData['UF_DEPARTURE_DATE']);
                    foreach ($arDates as $date) {
                        $arPeriods[] = $date;
                    }
                }
                $item_data = array_merge($item_data, ["BOOKED_DATES" => createCalendarDatesForUserForm($arPeriods)]);
            } else {
                while ($arData = $data->Fetch()) {
                    $arDates = _get_dates($arData['UF_ARRIVAL_DATE'], $arData['UF_DEPARTURE_DATE']);
                    foreach ($arDates as $date) {
                        $arPeriods[] = $date;
                    }
                }
                $result = [];
                $res = createSelectedDateArr($arPeriods);
                unset($date);
                foreach ($res as $date => $date_time) {
                    $result[$date] = [
                        'date' => $date,
                        'status' => 'disabled',
                        'class' => 'flatpickr-disabled',
                    ];
                }
                $item_data = array_merge($item_data, ["BOOKED_DATES" => $result]);
            }
            return AjaxJson::createSuccess([
                'data' => $item_data,
            ]);
        } else {
            return AjaxJson::createError([
                'data' => 'не заполнено обязательное поле id',
            ]);
        }
    }

    public function createMapData($arData)
    {

        $arPoints = [];
        $features = [];

        foreach ($arData['LOCATIONS'] as $arLocation) {
            foreach ($arLocation['ITEMS'] as $arItem) {
                if (!empty($arItem["FIELDS"]["ID"])
                    && !empty($arItem["PROPS"]["COORD_N_L"]["VALUE"])
                    && !empty($arItem["PROPS"]["COORD_E_L"]["VALUE"])
                    && !empty($arItem["FIELDS"]["NAME"])
                ) {
                    $arPoints[$arItem["FIELDS"]["ID"]] = [
                        "id" => $arItem["FIELDS"]["ID"],
                        "coordinates" => [$arItem["PROPS"]["COORD_N_L"]["VALUE"], $arItem["PROPS"]["COORD_E_L"]["VALUE"]],
                        "hintContent" => html_entity_decode($arItem["FIELDS"]["NAME"], ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8"),
                        "iconImageHref" => getMapPointIcon(CFile::GetFileArray($arItem["PROPS"]["MAP_ICON"]["VALUE"])["SRC"], $arItem["FIELDS"]["ID"])
                    ];
                }
            }
        }

        $features = createMapPoint($arPoints);

        $json = [
            "type" => 'FeatureCollection',
            "features" => $features
        ];

        $arResult = $arData;
        $arResult["MAP_JSON"] = json_encode($json, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

        return $arResult;

    }

    public function sortByLocation($locations, $location_id)
    {
        foreach ($locations as $lid => &$location) {
            if ($lid != $location_id) {
                unset($locations[$lid]);
            }
            /*if (isset($location['ITEMS'])) {
                foreach ($location['ITEMS'] as $iid => $item) {
                    if (isset($item['FIELDS']['PROPERTY_LOCATION_VALUE'])) {
                        if ($item['FIELDS']['PROPERTY_LOCATION_VALUE'] != $location_id) {
                            unset($location['ITEMS'][$iid]);
                        }
                    }

                }
            } else {
                unset($locations[$lid]);
            }*/
        }
        if (!empty($locations)) {
            return $locations;
        } else {
            return false;
        }
    }

    public function sortBySectionID($locations, $section_id)
    {
        //$arSections = [];
        foreach ($locations as &$location) {
            foreach ($location['ITEMS'] as $arItem) {
                if (!in_array($arItem['FIELDS']['IBLOCK_SECTION_ID'], $section_id)) {
                    unset($location['ITEMS'][$arItem['FIELDS']['ID']]);
                }
            }
        }
        /*foreach ($section_id as $id) {
            $arSections[] = $locations[intval($id)];
        }*/
        if (!empty($locations)) {
            return $locations;
        } else {
            return false;
        }
    }

    public function sortByDate($sections, $arrival_date, $departure_date)
    {

        $arSections = [];

        foreach ($sections as $arSect) {
            foreach ($arSect['ITEMS'] as $item_id => $arItem) {
                $arItems[] = $item_id;
            }
        }

        if (!empty($arItems)) {
            $arItems = $this->checkArrivalDate($arItems, $arrival_date, $departure_date);
        }

        foreach ($sections as &$arSect) {
            foreach ($arSect['ITEMS'] as $item_id => $arItem) {
                if (!in_array($item_id, $arItems)) {
                    unset($arSect['ITEMS'][$item_id]);
                }
            }
        }

        $arSections = $sections;

        if (!empty($arSections)) {
            return $arSections;
        } else {
            return false;
        }
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

    public function checkGuestsCapacity($locations, $quantity)
    {
        if ($quantity > 1) {
            foreach ($locations as &$location) {
                foreach ($location["ITEMS"] as $item_id => $location_item) {
                    if (isset($location_item['FIELDS']["PROPERTY_CAPACITY_MAXIMUM_VALUE"])) {
                        if ($location_item['FIELDS']["PROPERTY_CAPACITY_MAXIMUM_VALUE"] < $quantity) {
                            unset($location["ITEMS"][$item_id]);
                        }
                    }
                }
            }
        }

        if (!empty($locations)) {
            return $locations;
        } else {
            return false;
        }
    }

    //функция достает разделы и элементы заданного инфоблока
    /*public function getFacilities($params)
    {
        if (!Loader::includeModule("iblock")) {
            ShowError("Module \"iblock\" not found");
            return;
        }

        if (is_numeric($params["IBLOCK_ID"])) {
            $rsIBlock = CIBlock::GetList(array(), array(
                "ACTIVE" => "Y",
                "ID" => $params["IBLOCK_ID"],
            ));
        } else {
            $rsIBlock = CIBlock::GetList(array(), array(
                "ACTIVE" => "Y",
                "CODE" => $params["IBLOCK_ID"],
                "SITE_ID" => SITE_ID,
            ));
        }

        $arResult = $rsIBlock->GetNext();

        if ($arResult) {

            $arFilter = array('IBLOCK_ID' => $params["IBLOCK_ID"], 'ACTIVE' => 'Y');

            if (!empty($params["SECTION_PROPERTY_CODE"])) {
                $arSelect = $params["SECTION_PROPERTY_CODE"];
            }

            $db_list = CIBlockSection::GetList([], $arFilter, true, $arSelect);//достаем разделы

            while ($ar_result = $db_list->GetNext()) {

                $ar_result["ICON"] = CFile::GetPath($ar_result["UF_CB_SVG_ICON"]);
                unset($ar_result["UF_CB_SVG_ICON"]);

                $arResult["SECTIONS"][$ar_result['ID']] = $ar_result;
            }

            unset($arFilter, $db_list, $arSelect);

            $arSelect = ["ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"];

            if (!empty($params["FIELD_CODE"])) {
                $arSelect = array_merge($params["FIELD_CODE"], $arSelect);
            }

            if (!empty($params["PROPERTY_CODE"])) {
                $arSelect = array_merge($params["PROPERTY_CODE"], $arSelect);
            }

            $arFilter = array("IBLOCK_ID" => $params["IBLOCK_ID"], "ACTIVE" => "Y");

            $res = CIBlockElement::GetList(['SORT' => 'ASC'], $arFilter, false, [], $arSelect); //достаем элементы

            while ($ob = $res->GetNextElement()) {

                $arFields = $ob->GetFields();

                $arProps = $ob->GetProperties();

                foreach ($arFields as $field_name => $field_value) {
                    if ($field_name == "PREVIEW_PICTURE") {
                        $arFields[$field_name] = CFile::GetFileArray($field_value);//достаем картинку
                    }
                }

                $arResult["ITEMS"][] = [
                    "FIELDS" => $arFields,
                    "PROPS" => $arProps,
                ];

            }

            //проходим по всем элементам и записываем их в родительские разделы
            if (!empty($arResult["ITEMS"])) {
                foreach ($arResult["SECTIONS"] as $section_id => &$section) {
                    foreach ($arResult["ITEMS"] as $item) {
                        if ($item['FIELDS']['IBLOCK_SECTION_ID'] == $section_id) {
                            $section['ITEMS'][$item['FIELDS']['ID']] = $item;
                        }
                    }
                }
            }

            unset($arSelect, $arFilter, $res, $ob, $arFields, $arProps, $arResult["ITEMS"]);

        }

        return $arResult;
    }*/

    /*public function getLocations(&$arResult, $params)
    {

        $arSelect = ["ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME"];

        $arFilter = array("IBLOCK_ID" => $params["LOCATIONS_IBLOCK_ID"], "ACTIVE" => "Y");

        $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect); //достаем элементы

        while ($ob = $res->GetNextElement()) {

            $arFields = $ob->GetFields();

            $arResult["LOCATIONS"][$arFields['ID']] = $arFields;

        }

    }*/

    public function getLocationList(&$arResult, $params)
    {
        if ($params['PARENT_SECTION_ID']) {
            if (Loader::includeModule("iblock")) {
                $res = CIBlockSection::GetByID($params['PARENT_SECTION_ID']);
                if ($ar_res = $res->GetNext()) {
                    $arResult['PARENT_SECTION_NAME'] = $ar_res['NAME'];
                    $arResult['PARENT_SECTION_CODE'] = $ar_res['CODE'];
                }
                $arParentSections = [];
                $arFilter = array('IBLOCK_ID' => IB_LOCATIONS, 'GLOBAL_ACTIVE' => 'Y', 'SECTION_ID' => $params['PARENT_SECTION_ID']);
                $arSelect = ['ID', 'NAME', 'DEPTH_LEVEL'];
                $db_list = CIBlockSection::GetList(array($by => $order), $arFilter, true, $arSelect);
                while ($ar_result = $db_list->GetNext()) {
                    $arParentSections[] = $ar_result;
                }
                unset($arFilter, $arSelect, $db_list, $ar_result);
                if (!empty($arParentSections)) {
                    $arFilter = array('IBLOCK_ID' => IB_LOCATIONS, 'GLOBAL_ACTIVE' => 'Y');
                    if (isset($params['FILTER_SECTIONS_ID']) && !empty($params['FILTER_SECTIONS_ID'])) {
                        $arFilter['ID'] = $params['FILTER_SECTIONS_ID'];
                    }
                    $arSelect = ['ID', 'NAME', 'DESCRIPTION', 'PICTURE'];
                    foreach ($arParentSections as &$parentSection) {
                        $arFilter['SECTION_ID'] = $parentSection['ID'];
                        $db_list = CIBlockSection::GetList(array($by => $order), $arFilter, true, $arSelect);
                        while ($ar_result = $db_list->GetNext()) {
                            $picture = false;
                            if ($ar_result['PICTURE']) {
                                $picture = CFile::ResizeImageGet($ar_result['PICTURE'], array('width' => 450, 'height' => 310), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                            }
                            $parentSection['CHILDS'][] = [
                                'ID' => $ar_result['ID'],
                                'NAME' => $ar_result['NAME'],
                                'DESCRIPTION' => $ar_result['DESCRIPTION'],
                                'PICTURE' => $picture,
                            ];
                        }
                    }
                    unset($parentSection);
                    foreach ($arParentSections as $s_key => $parentSection) {
                        if (empty($parentSection['CHILDS'])) {
                            unset($arParentSections[$s_key]);
                        }
                    }
                    $arResult['PARENT_SECTIONS'] = $arParentSections;
                }
            }
        }
    }

    public function getSections(&$arResult, $params)
    {
        if (Loader::includeModule("iblock")) {
            $arFilter = ['IBLOCK_ID' => IB_LOCATIONS, 'GLOBAL_ACTIVE' => 'Y', '=DEPTH_LEVEL' => 1];
            $arSelect = ['ID', 'NAME', 'DESCRIPTION', 'UF_SECTION_SUBTITLE', 'PICTURE', 'CODE'];
            $db_list = CIBlockSection::GetList(array($by => $order), $arFilter, true, $arSelect);
            while ($ar_result = $db_list->GetNext()) {
                $picture = false;
                if ($ar_result['PICTURE']) {
                    $picture = CFile::ResizeImageGet($ar_result['PICTURE'], array('width' => 450, 'height' => 310), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                }
                $arResult["LOCATIONS"][$ar_result['ID']] = [
                    'ID' => $ar_result['ID'],
                    'CODE' => $ar_result['CODE'],
                    'NAME' => $ar_result['NAME'],
                    'SUBTITLE' => $ar_result['UF_SECTION_SUBTITLE'],
                    'DESCRIPTION' => $ar_result['DESCRIPTION'],
                    'PICTURE' => $picture,
                ];
            }
        }
    }

    public function executeComponent()
    {
        if ($this->arParams['GET_LOCATION_LIST'] && $this->arParams['GET_LOCATION_LIST'] == 'Y') {
            $this->getLocationList($this->arResult, $this->arParams);
        } else {
            $this->getSections($this->arResult, $this->arParams);
        }
        $this->includeComponentTemplate();
    }
}