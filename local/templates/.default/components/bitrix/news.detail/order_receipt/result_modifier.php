<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arResult */

use Bitrix\Main\Loader;

if ($arResult['DISPLAY_PROPERTIES']['BOOKING_OBJECT']['LINK_ELEMENT_VALUE']) {
    $res = CIBlockSection::GetByID($arResult['DISPLAY_PROPERTIES']['BOOKING_OBJECT']['LINK_ELEMENT_VALUE'][$arResult['DISPLAY_PROPERTIES']['BOOKING_OBJECT']['VALUE']]['IBLOCK_SECTION_ID']);
    if ($ar_res = $res->GetNext())
        $object_category = $ar_res['NAME'];
    $object_name = $arResult['DISPLAY_PROPERTIES']['BOOKING_OBJECT']['LINK_ELEMENT_VALUE'][$arResult['DISPLAY_PROPERTIES']['BOOKING_OBJECT']['VALUE']]['NAME'];
    unset($ar_res);
} else {
    Loader::includeModule("iblock");
    $arSelect = array(
        "ID",
        "NAME",
        "IBLOCK_SECTION_ID",

    );
    $arFilter = array("IBLOCK_ID" => IB_OBJECT, "ID" => $arResult['DISPLAY_PROPERTIES']['BOOKING_OBJECT']['VALUE']);
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arElementFields = $ob->GetFields();
        $object_name = $arElementFields['NAME'];
        $result = CIBlockSection::GetByID($arElementFields['IBLOCK_SECTION_ID']);
        if ($ar_res = $result->GetNext()) {
            $object_category = $ar_res['NAME'];
        }
    }
    unset($arFilter, $arSelect, $arElementFields, $res, $result);
}

$datetimeFrom = DateTime::createFromFormat('d.m.Y H:i:s', $arResult['DISPLAY_PROPERTIES']['ARRIVAL_DATE']['VALUE'] . ' ' . $arResult['DISPLAY_PROPERTIES']['CHECK_IN_TIME']['VALUE'] . ':00');
$datetimeTo = DateTime::createFromFormat('d.m.Y H:i:s', $arResult['DISPLAY_PROPERTIES']['DEPARTURE_DATE']['VALUE'] . ' ' . $arResult['DISPLAY_PROPERTIES']['DEPARTURE_TIME']['VALUE'] . ':00');

/*if (intval($datetimeFrom->diff($datetimeTo)->format('%a')) > 1) {
    $arSelect = ["CODE" => "OBJECT_DAILY_COST"];
} else {
    $arSelect = ["CODE" => "OBJECT_COST"];
}

$db_props = CIBlockElement::GetProperty(IB_OBJECT, $arResult['DISPLAY_PROPERTIES']['BOOKING_OBJECT']['VALUE'], [], []);
if ($ar_props = $db_props->Fetch()) {
    $object_cost = $ar_props['VALUE'];
}
unset($arSelect);
*/

if (isset($arResult['DISPLAY_PROPERTIES']['OBJECT_RENT_COST'])) {
    $object_cost = $arResult['DISPLAY_PROPERTIES']['OBJECT_RENT_COST']['VALUE'];
} elseif (isset($arResult['PROPERTIES']['OBJECT_RENT_COST'])) {
    $object_cost = $arResult['PROPERTIES']['OBJECT_RENT_COST']['VALUE'];
}

$arSelect = array(
    "ID",
    "NAME",
    "PROPERTY_CAPACITY_ESTIMATED",
    "PROPERTY_COST_PER_PERSON",
    "PROPERTY_OBJECT_COST",
    "PROPERTY_LOCATION",
    "PROPERTY_COORD_N_L",
    "PROPERTY_COORD_E_L",
    "PROPERTY_MAP_ICON"
);
$arFilter = array(
    "IBLOCK_ID" => IB_OBJECT,
    "ID" => $arResult['DISPLAY_PROPERTIES']['BOOKING_OBJECT']['VALUE'],
);
$res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
while ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();
    //$object_cost = $arFields['PROPERTY_OBJECT_COST_VALUE'];
    $object_capacity_estimated = $arFields['PROPERTY_CAPACITY_ESTIMATED_VALUE'];
    $object_cost_per_person = $arFields['PROPERTY_COST_PER_PERSON_VALUE'];
    $object_location = $arFields['PROPERTY_LOCATION_VALUE'];
    $arResult['MAP_DATA'] = [
        "id" => $arFields['ID'],
        "coordinates" => [$arFields['PROPERTY_COORD_N_L_VALUE'], $arFields['PROPERTY_COORD_E_L_VALUE']],
        "hintContent" => htmlentities($arFields['NAME'], ENT_SUBSTITUTE),
        "iconImageHref" => getMapPointIcon(CFile::GetFileArray($arFields['PROPERTY_MAP_ICON_VALUE'])["SRC"], $arFields['ID'])
    ];
}

$json = [
    "type" => 'FeatureCollection',
    "features" => createMapPoint([0 => $arResult['MAP_DATA']])
];

$arResult["MAP_JSON"] = json_encode($json, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

$arResult['BLANK_FIELDS'] = [
    'OBJECT_NAME' => $object_name,
    'OBJECT_CATEGORY' => $object_category,
    'RENTAL_DATE' => $arResult['DISPLAY_PROPERTIES']['ARRIVAL_DATE']['VALUE'],
    'CHECK_IN_TIME' => $arResult['DISPLAY_PROPERTIES']['CHECK_IN_TIME']['VALUE'],
    'DEPARTURE_DATE' => $arResult['DISPLAY_PROPERTIES']['DEPARTURE_DATE']['VALUE'],
    'DEPARTURE_TIME' => $arResult['DISPLAY_PROPERTIES']['DEPARTURE_TIME']['VALUE'],
    'ADULTS' => $arResult['DISPLAY_PROPERTIES']['ADULTS']['VALUE'],
    'BENIFICIARIES' => $arResult['DISPLAY_PROPERTIES']['BENIFICIARIES']['VALUE'],
    'ORDER_ID' => $arResult['ID'],
    'MANAGER' => $arResult['DISPLAY_PROPERTIES']['BOOKING_TYPE']['VALUE'],
    'TENANT' => $arResult['DISPLAY_PROPERTIES']['SURNAME']['VALUE'] . ' ' . $arResult['DISPLAY_PROPERTIES']['NAME']['VALUE'],
    'OBJECT_COST' => $object_cost,
    'COST' => $arResult['DISPLAY_PROPERTIES']['BOOKING_COST']['VALUE'],
    'QRCODE' => $arResult['DISPLAY_PROPERTIES']['QR_CODE']['FILE_VALUE']['SRC']
];

$rsProps = CIBlockElement::GetProperty(IB_BOOKING_LIST, $arResult['ID'], [], ['CODE' => 'GUEST_CARS']);
while ($arrProps = $rsProps->Fetch()) {
    if ($arrProps['VALUE']) {
        $arResult['BLANK_FIELDS']['GUEST_CARS'][] = $arrProps['VALUE'];
    }
}

$res = CIBlockElement::GetByID($object_location);
if ($ar_res = $res->GetNext()) {
    $arResult['BLANK_FIELDS']['OBJECT_LOCATION'] = $ar_res['NAME'];
}

if ($arResult['DISPLAY_PROPERTIES']['PERMISSION']['VALUE_ENUM_ID'] == "2") {
    $permission_count = $arResult['BLANK_FIELDS']['ADULTS'] - $arResult['BLANK_FIELDS']['BENIFICIARIES'];
    $permission_cost = $permission_count * VISIT_PERMISSION_COST;
    $arResult['BLANK_FIELDS']['PERMISSION_COUNT'] = $permission_count;
    $arResult['BLANK_FIELDS']['PERMISSION_COST'] = $permission_cost;
}

if ($arResult['BLANK_FIELDS']['ADULTS'] > $object_capacity_estimated) {
    $person_over = $arResult['BLANK_FIELDS']['ADULTS'] - $object_capacity_estimated;
    $person_over_cost = $person_over * $object_cost_per_person;
    $arResult['BLANK_FIELDS']['PERSON_OVER'] = $person_over;
    $arResult['BLANK_FIELDS']['PERSON_OVER_COST'] = $person_over_cost;
}

if ($arResult['DISPLAY_PROPERTIES']['IS_PAYED']['DISPLAY_VALUE'] == 'Y') {
    $arResult['BLANK_FIELDS']['PAYMENT_STATUS'] = $arResult['DISPLAY_PROPERTIES']['DATE_PAY']['DISPLAY_VALUE'];
} else {
    $arResult['BLANK_FIELDS']['PAYMENT_STATUS']['SHOW_PAY_BTN'] = 'Y';
    $arResult['BLANK_FIELDS']['PAYMENT_STATUS']['PAY_BTN_LINK'] = $arResult['DISPLAY_PROPERTIES']['PAYMENT']['VALUE'];
}
