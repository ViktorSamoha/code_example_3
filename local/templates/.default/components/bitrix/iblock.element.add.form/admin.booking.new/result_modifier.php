<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */

/** @var array $arResult */

use \Bitrix\Main\Loader;

Loader::includeModule("iblock");
$dateCreate = CIBlockFormatProperties::DateFormat(
    'j.m.Y / h:m',
    MakeTimeStamp(
        new DateTime(),
        CSite::GetDateFormat()
    )
);
$arDateCreate = explode('/', $dateCreate);
$arResult['DATE_INSERT'] = $arDateCreate[0];

//достаем объекты для выбранной секции
$arSelect = ["ID", "NAME", "PROPERTY_TIME_UNLIMIT_OBJECT", "PROPERTY_CAR_POSSIBILITY", "PROPERTY_CAR_CAPACITY"];
$arFilter = ["IBLOCK_ID" => IB_LOCATIONS, "ACTIVE" => "Y", '=PROPERTY_CAN_BOOK' => 36];
$res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
while ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();
    $prop = CIBlockElement::GetProperty(IB_LOCATIONS, $arFields['ID'], array("sort" => "asc"), array("CODE" => "TIME_INTERVAL"));
    $arPropVal = [];
    while ($prop_ob = $prop->GetNext()) {
        if (isset($prop_ob['VALUE']) && isset($prop_ob['VALUE_ENUM'])) {
            $arPropVal[] = [
                'ID' => $prop_ob['VALUE'],
                'NAME' => $prop_ob['VALUE_ENUM'],
            ];
        }
    }
    $arResult['SECTION_OBJECTS'][$arFields['ID']] = [
        "ID" => $arFields['ID'],
        "NAME" => $arFields['NAME'],
    ];
    if (isset($arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'])) {
        if ($arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'] == 'Да') {
            $arResult['SECTION_OBJECTS'][$arFields['ID']]['TIME_LIMIT'] = 'Y';
        } else {
            $arResult['SECTION_OBJECTS'][$arFields['ID']]['TIME_LIMIT'] = 'N';
        }
    } else {
        $arResult['SECTION_OBJECTS'][$arFields['ID']]['TIME_LIMIT'] = 'N';
    }
    if (isset($arFields['PROPERTY_CAR_POSSIBILITY_VALUE'])) {
        if ($arFields['PROPERTY_CAR_POSSIBILITY_VALUE'] == 'Да') {
            $arResult['SECTION_OBJECTS'][$arFields['ID']]['CAR_POSSIBILITY'] = 'Y';
        } else {
            $arResult['SECTION_OBJECTS'][$arFields['ID']]['CAR_POSSIBILITY'] = 'N';
        }
    } else {
        $arResult['SECTION_OBJECTS'][$arFields['ID']]['CAR_POSSIBILITY'] = 'N';
    }
    if (isset($arFields['PROPERTY_CAR_CAPACITY_VALUE'])) {
        $arResult['SECTION_OBJECTS'][$arFields['ID']]['CAR_CAPACITY'] = $arFields['PROPERTY_CAR_CAPACITY_VALUE'];
    } else {
        $arResult['SECTION_OBJECTS'][$arFields['ID']]['CAR_CAPACITY'] = '';
    }
    if (!empty($arPropVal)) {
        if (count($arPropVal) == 2) {
            $arResult['SECTION_OBJECTS'][$arFields['ID']]["TIME_INTERVAL"]['TYPE'] = 'multi';
            $arResult['SECTION_OBJECTS'][$arFields['ID']]["TIME_INTERVAL"]['VALUE'] = 'null';
        } else {
            $arResult['SECTION_OBJECTS'][$arFields['ID']]["TIME_INTERVAL"]['TYPE'] = 'single';
            $arResult['SECTION_OBJECTS'][$arFields['ID']]["TIME_INTERVAL"]['VALUE'] = $arPropVal[0]['ID'];
        }
    }
}

//достаем данные менеджера
$user = getUserData();
$arResult['MANAGER_FIO'] = $user['LOGIN'];

$structure = getLocationStructure();
$arResult['LOCATIONS'] = $structure['LOCATION'];
$arResult['SECTIONS'] = $structure['TYPE'];
