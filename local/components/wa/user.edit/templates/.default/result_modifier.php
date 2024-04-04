<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arResult */

$arFilter = array('IBLOCK_ID' => IB_LOCATIONS);
$sections_list = CIBlockElement::GetList([], $arFilter, false, [], ["ID", "NAME"]);
while ($ar_sections = $sections_list->GetNext()) {
    $arResult['LOCATIONS'][$ar_sections['ID']] = $ar_sections;
}
foreach ($arResult['USER']['LOCATIONS'] as $user_location) {
    $arResult['LOCATIONS'][$user_location['ID']]['CONDITION'] = 'checked';
    $propertyLocationfilter[] = $user_location['ID'];
}
unset($arFilter);
$arSelect = ["ID", "NAME"];
$arFilter = ["IBLOCK_ID" => IB_OBJECT, 'PROPERTY_LOCATION'=>$propertyLocationfilter];
$res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
while ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();
    $arResult['OBJECTS'][$arFields['ID']] = [
        "ID" => $arFields['ID'],
        "NAME" => $arFields['NAME'],
    ];
}
