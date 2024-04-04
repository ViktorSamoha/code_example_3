<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */

//скрипт пробегает по секциям и переносит items в соответствующие локации
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
                //$ar_item['FIELDS']['PREVIEW_PICTURE']['SRC'] = CFile::GetPath($ar_item['PROPS']['DETAIL_GALLERY']['VALUE'][0]);
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
