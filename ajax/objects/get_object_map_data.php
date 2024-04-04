<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

Loader::includeModule("iblock");

if ($request->get("object_id")) {

    $arSelect = array(
        "ID",
        "NAME",
        "PROPERTY_NORTHERN_LATITUDE",
        "PROPERTY_EASTERN_LONGITUDE",
        "PROPERTY_ICON"
    );
    $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "ID" => $request->get("object_id"));

    $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arResult['MAP_DATA'] = [
            "id" => $arFields['ID'],
            "coordinates" => [$arFields['PROPERTY_NORTHERN_LATITUDE_VALUE'], $arFields['PROPERTY_EASTERN_LONGITUDE_VALUE']],
            "hintContent" => htmlentities($arFields['NAME'], ENT_SUBSTITUTE),
            "iconImageHref" => getMapPointIcon(CFile::GetFileArray($arFields['PROPERTY_ICON_VALUE'])["SRC"], $arFields['ID'])
        ];
    }

    $json = [
        "type" => 'FeatureCollection',
        "features" => createMapPoint([0=>$arResult['MAP_DATA']])
    ];

    echo json_encode($json, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

}