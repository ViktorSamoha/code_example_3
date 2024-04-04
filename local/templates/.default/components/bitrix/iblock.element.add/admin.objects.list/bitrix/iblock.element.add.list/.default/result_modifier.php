<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */

$arItems = [];

foreach ($arResult['ELEMENTS'] as $item) {

    $arItems[$item['ID']]["NAME"] = $item['NAME'];

    $ar_parent_section = CIBlockSection::GetByID($item['IBLOCK_SECTION_ID']);

    if ($parent_section = $ar_parent_section->GetNext()) {
        $arItems[$item['ID']]["CATEGORY"] = $parent_section['NAME'];
    }

    $db_capacity = CIBlockElement::GetProperty(IB_OBJECT, $item['ID'], [], ["CODE" => "CAPACITY_MAXIMUM"]);
    if ($ar_capacity = $db_capacity->Fetch()) {
        $arItems[$item['ID']]["CAPACITY"] = $ar_capacity['VALUE'];
    }

    $db_time_interval = CIBlockElement::GetProperty(IB_OBJECT, $item['ID'], [], ["CODE" => "TIME_INTERVAL"]);
    while ($ar_time_interval = $db_time_interval->GetNext()) {
        $arItems[$item['ID']]["TIME_INTERVAL"][$ar_time_interval['VALUE']] = $ar_time_interval['VALUE_ENUM'];
    }
}

$arResult['ELEMENTS'] = $arItems;
