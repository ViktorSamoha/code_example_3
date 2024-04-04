<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */

/** @var array $arResult */

use \Bitrix\Main\Loader;

Loader::includeModule("iblock");

//приводим дату создания к нужному формату
$dateCreate = CIBlockFormatProperties::DateFormat(
    'j.m.Y / h:m',
    MakeTimeStamp(
        $arResult['ELEMENT']["DATE_CREATE"],
        CSite::GetDateFormat()
    )
);
$arDateCreate = explode('/', $dateCreate);
$arResult['DATE_INSERT'] = $arDateCreate[0];

//достаем категории объектов
$arResult['SECTIONS'] = getLocationStructure();
$objectData = getObjectById($arResult['ELEMENT_PROPERTIES'][21][0]['VALUE']);
$arResult['ELEMENT_SECTION'] = $objectData['SECTIONS'][1];
$arResult['ELEMENT_LOCATION'] = $objectData['SECTIONS'][2];

//достаем объекты для выбранной секции
$arSelect = ["ID", "NAME", "PROPERTY_LOCATION", "PROPERTY_TIME_UNLIMIT_OBJECT", "PROPERTY_CAR_POSSIBILITY", "PROPERTY_CAR_CAPACITY"];
$arFilter = ["IBLOCK_ID" => IB_LOCATIONS, "SECTION_ID" => $arResult['ELEMENT_SECTION']['ID']];
$res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
while ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();
    if (isset($arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'])) {
        if ($arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'] == 'Да') {
            $arResult['SECTION_OBJECTS'][$arFields['ID']] = [
                "ID" => $arFields['ID'],
                "NAME" => $arFields['NAME'],
                "CHECKED" => ($arFields['ID'] == $arResult['ELEMENT_PROPERTIES'][21][0]['VALUE']) ? true : false,
                "TIME_LIMIT" => 'Y'
            ];
        } else {
            $arResult['SECTION_OBJECTS'][$arFields['ID']] = [
                "ID" => $arFields['ID'],
                "NAME" => $arFields['NAME'],
                "CHECKED" => ($arFields['ID'] == $arResult['ELEMENT_PROPERTIES'][21][0]['VALUE']) ? true : false,
                "TIME_LIMIT" => 'N'
            ];
        }
    } else {
        $arResult['SECTION_OBJECTS'][$arFields['ID']] = [
            "ID" => $arFields['ID'],
            "NAME" => $arFields['NAME'],
            "CHECKED" => ($arFields['ID'] == $arResult['ELEMENT_PROPERTIES'][21][0]['VALUE']) ? true : false,
            "TIME_LIMIT" => 'N'
        ];
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
    if ($arFields['ID'] == $arResult['ELEMENT_PROPERTIES'][21][0]['VALUE']) {
        $arResult['SELECTED_OBJECT'] = [
            "ID" => $arFields['ID'],
            "NAME" => $arFields['NAME'],
        ];
    }
}

//достаем данные менеджера

$user = getUserData();
$arResult['MANAGER_FIO'] = $user['LOGIN'];

unset($arFilter);
$arFilter = array('IBLOCK_ID' => IB_LOCATIONS);
$sections_list = CIBlockElement::GetList([], $arFilter, false, [], ["ID", "NAME"]);
while ($ar_sections = $sections_list->GetNext()) {
    $arResult['LOCATIONS'][$ar_sections['ID']] = $ar_sections;
}

/*костылик на проверку периода бронирования*/
if (isset($arResult['ELEMENT_PROPERTIES'][11][0]['VALUE']) && isset($arResult['ELEMENT_PROPERTIES'][12][0]['VALUE'])) {
    if ($arResult['ELEMENT_PROPERTIES'][11][0]['VALUE'] != '' && $arResult['ELEMENT_PROPERTIES'][12][0]['VALUE'] != '') {
        if (DateTime::createFromFormat('d.m.Y', $arResult['ELEMENT_PROPERTIES'][11][0]['VALUE'])->format('d.m.Y') == DateTime::createFromFormat('d.m.Y', $arResult['ELEMENT_PROPERTIES'][12][0]['VALUE'])->format('d.m.Y')) {
            $arResult['RENT_PERIOD'] = 'day';
        } else {
            $arResult['RENT_PERIOD'] = 'couple';
        }
    }
}

$prop = CIBlockElement::GetProperty(IB_LOCATIONS, $arResult['ELEMENT_PROPERTIES'][21][0]['VALUE'], "sort", "asc", array("CODE" => "TIME_UNLIMIT_OBJECT"));
while ($prop_ob = $prop->GetNext()) {
    if (isset($prop_ob['VALUE']) && isset($prop_ob['VALUE_ENUM'])) {
        if ($prop_ob['VALUE_ENUM'] == 'Да') {
            $arResult['TIME_UNLIMIT_OBJECT'] = 'Y';
        } else {
            $arResult['TIME_UNLIMIT_OBJECT'] = 'N';
        }

    }
}
$prop = CIBlockElement::GetProperty(IB_LOCATIONS, $arResult['ELEMENT_PROPERTIES'][21][0]['VALUE'], "sort", "asc", array("CODE" => "CAR_POSSIBILITY"));
while ($prop_ob = $prop->GetNext()) {
    if (isset($prop_ob['VALUE']) && isset($prop_ob['VALUE_ENUM'])) {
        if ($prop_ob['VALUE_ENUM'] == 'Да') {
            $arResult['CAR_POSSIBILITY'] = 'Y';
        } else {
            $arResult['CAR_POSSIBILITY'] = 'N';
        }
    }
}
unset($prop, $prop_ob);
$prop = CIBlockElement::GetProperty(IB_LOCATIONS, $arResult['ELEMENT_PROPERTIES'][21][0]['VALUE'], "sort", "asc", array("CODE" => "CAR_CAPACITY"));
while ($prop_ob = $prop->GetNext()) {
    $arResult['CAR_CAPACITY'] = $prop_ob['VALUE'];
}

if (!empty($arResult['ELEMENT_PROPERTIES'][GUEST_CARS][0]['VALUE'])) {
    $arResult['SHOW_CAR_BLOCK'] = true;
} else {
    $arResult['SHOW_CAR_BLOCK'] = false;
}

d($arResult);
