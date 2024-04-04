<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */

$arSelect = array("ID", "NAME", "ACTIVE");
$arFilter = array("IBLOCK_ID" => IB_LOCATIONS);
$res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);

if ($res) {
    unset($arResult['ITEMS']);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arResult['ITEMS'][$arFields['ID']] = [
            'ID' => $arFields['ID'],
            'NAME' => $arFields['NAME'],
            'ACTIVE' => $arFields['ACTIVE'],
        ];
    }
}

