<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arResult */

use Bitrix\Main\Application,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;

$arResult['OBJECT_DATA']['OBJECT_TYPE'] = $arResult['DISPLAY_PROPERTIES']['OBJECT_TYPE']['DISPLAY_VALUE'];
$arResult['OBJECT_DATA']['COORDS'] = $arResult['DISPLAY_PROPERTIES']['NORTHERN_LATITUDE']['DISPLAY_VALUE'] . ', ' . $arResult['DISPLAY_PROPERTIES']['EASTERN_LONGITUDE']['DISPLAY_VALUE'];
if ($arResult['DISPLAY_PROPERTIES']['DETAIL_GALLERY']) {
    foreach ($arResult['DISPLAY_PROPERTIES']['DETAIL_GALLERY']['VALUE'] as $pictureId) {
        $picture = false;
        $picture = CFile::ResizeImageGet($pictureId, array('width' => 675, 'height' => 480), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        if ($picture) {
            $arResult['OBJECT_DATA']['GALLERY'][] = $picture;
        }
    }
}
if ($arResult['DISPLAY_PROPERTIES']['LOCATION_FEATURES']) {
    if (Loader::includeModule("highloadblock")) {
        $hlbl = HL_OBJECT_FEATURES;
        $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        foreach ($arResult['DISPLAY_PROPERTIES']['LOCATION_FEATURES']['DISPLAY_VALUE'] as $locationFeature) {
            $rsData = $entity_data_class::getList(array(
                "select" => array("*"),
                "order" => array("ID" => "ASC"),
                "filter" => array("UF_XML_ID" => $locationFeature)
            ));
            while ($arData = $rsData->Fetch()) {
                $arResult['OBJECT_DATA']['LOCATION_FEATURES'][] = [
                    'NAME' => $arData["UF_OF_NAME"],
                    'ICON' => CFile::GetPath($arData["UF_OF_ICON"]),
                ];
            }
        }
    }
}
if (
    $arResult['DISPLAY_PROPERTIES']['NORTHERN_LATITUDE']['DISPLAY_VALUE'] &&
    $arResult['DISPLAY_PROPERTIES']['EASTERN_LONGITUDE']['DISPLAY_VALUE']) {
    $arResult['MAP_DATA'] = [
        "id" => $arResult['ID'],
        "coordinates" => [$arResult['DISPLAY_PROPERTIES']['NORTHERN_LATITUDE']['DISPLAY_VALUE'], $arResult['DISPLAY_PROPERTIES']['EASTERN_LONGITUDE']['DISPLAY_VALUE']],
        "hintContent" => htmlentities($arResult['NAME'], ENT_SUBSTITUTE),
        "iconImageHref" => getMapPointIcon($arResult['DISPLAY_PROPERTIES']['ICON']["FILE_VALUE"]["SRC"], $arResult['ID'])
    ];
    $json = [
        "type" => 'FeatureCollection',
        "features" => createMapPoint([0 => $arResult['MAP_DATA']])
    ];
    $arResult["MAP_JSON"] = json_encode($json, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
}
$cp = $this->__component;

if (is_object($cp)) {
    $cp->arResult['PRICE'] = $arResult['DISPLAY_PROPERTIES']['PRICE']['DISPLAY_VALUE'];
    $cp->arResult['PRICE_TYPE'] = $arResult['DISPLAY_PROPERTIES']['PRICE_TYPE']['DISPLAY_VALUE'];
    $cp->SetResultCacheKeys(array('PRICE', 'PRICE_TYPE'));
    $arResult['PRICE'] = $cp->arResult['PRICE'];
    $arResult['PRICE_TYPE'] = $cp->arResult['PRICE_TYPE'];
}