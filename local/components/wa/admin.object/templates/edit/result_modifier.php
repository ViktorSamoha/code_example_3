<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */

if ($arResult['OBJECT_DATA']['SECTIONS']) {
    foreach ($arResult['OBJECT_DATA']['SECTIONS'] as $section) {
        switch ($section['DEPTH_LEVEL']) {
            case 1:
                $arResult['OBJECT_DATA']['SECTION'] = $section;
                break;
            case 2:
                $arResult['OBJECT_DATA']['CATEGORY'] = $section;
                break;
            case 3:
                $arResult['OBJECT_DATA']['LOCATION'] = $section;
                break;
        }
    }
}
if ($arResult['OBJECT_DATA']['DETAIL_GALLERY']) {
    foreach ($arResult['OBJECT_DATA']['DETAIL_GALLERY'] as &$img) {
        $img = CFile::GetFileArray($img);
    }
    unset($img);
}

if ($arResult['OBJECT_DATA']['GALLERY_VIDEO_PREVIEW']) {
    foreach ($arResult['OBJECT_DATA']['GALLERY_VIDEO_PREVIEW'] as &$img) {
        $img = CFile::GetFileArray($img);
    }
    unset($img);
}
if ($arResult['OBJECT_DATA']['GALLERY_VIDEO']) {
    foreach ($arResult['OBJECT_DATA']['GALLERY_VIDEO'] as &$file) {
        $file = CFile::GetFileArray($file);
    }
    unset($file);
}
if ($arResult['OBJECT_DATA']['OBJECT_TYPE']) {
    $arResult['OBJECT_DATA']['OBJECT_TYPE'] = getObjectType($arResult['OBJECT_DATA']['OBJECT_TYPE']);
}
$arResult['OBJECT_TYPES'] = getObjectType();
$arResult['OBJECT_FEATURES'] = getObjectCharacteristic();
if ($arResult['OBJECT_FEATURES'] && $arResult['OBJECT_DATA']['LOCATION_FEATURES']) {
    foreach ($arResult['OBJECT_DATA']['LOCATION_FEATURES'] as $locationFeature) {
        foreach ($arResult['OBJECT_FEATURES'] as &$feature) {
            if ($feature['VALUE'] == $locationFeature) {
                $feature['CHECKED'] = true;
            }
        }
    }
}
$arResult['OBJECT_PROPS'] = getIblockListProperties(IB_LOCATIONS);
if ($arResult['OBJECT_PROPS']) {
    foreach ($arResult['OBJECT_PROPS'] as $objectPropertyCode => &$objectProp) {
        foreach ($objectProp as &$objectPropValue) {
            if (isset($arResult['OBJECT_DATA'][$objectPropertyCode])) {
                if (is_array($arResult['OBJECT_DATA'][$objectPropertyCode])) {
                    foreach ($arResult['OBJECT_DATA'][$objectPropertyCode] as $objectDataPropValue) {
                        if ($objectDataPropValue == $objectPropValue['VALUE']) {
                            $objectPropValue['CHECKED'] = true;
                        }
                    }
                } else {
                    if ($arResult['OBJECT_DATA'][$objectPropertyCode] == $objectPropValue['VALUE']) {
                        $objectPropValue['CHECKED'] = true;
                    }
                }
            }
        }
    }
}
$arResult['PARTNERS_LIST'] = getPartnersList();
if ($arResult['PARTNERS_LIST']) {
    if (isset($arResult['OBJECT_DATA']['PARTNERS']) && !empty($arResult['OBJECT_DATA']['PARTNERS'])) {
        foreach ($arResult['OBJECT_DATA']['PARTNERS'] as $objectPartner) {
            foreach ($arResult['PARTNERS_LIST'] as &$partner) {
                if ($partner['ID'] == $objectPartner) {
                    $partner['CHECKED'] = true;
                }
            }
        }
    }
}
$galleryCounter = $videoPreviewCounter = $videoCounter = 0;
if (is_array($arResult['OBJECT_DATA']['DETAIL_GALLERY'])) {
    foreach ($arResult['OBJECT_DATA']['DETAIL_GALLERY'] as $file) {
        if (is_array($file)) {
            $galleryCounter++;
        }
    }
    unset($file);
}
if (is_array($arResult['OBJECT_DATA']['GALLERY_VIDEO_PREVIEW'])) {
    foreach ($arResult['OBJECT_DATA']['GALLERY_VIDEO_PREVIEW'] as $file) {
        if (is_array($file)) {
            $videoPreviewCounter++;
        }
    }
    unset($file);
}
if (is_array($arResult['OBJECT_DATA']['GALLERY_VIDEO'])) {
    foreach ($arResult['OBJECT_DATA']['GALLERY_VIDEO'] as $file) {
        if (is_array($file)) {
            $videoCounter++;
        }
    }
    unset($file);
}
$arResult['COUNTERS'] = [
    'GALLERY' => $galleryCounter,
    'VIDEO_PREVIEW' => $videoPreviewCounter,
    'VIDEO' => $videoCounter,
];
if ($arResult['OBJECT_DATA']['CATEGORY']) {
    if ($arResult['OBJECT_DATA']['CATEGORY']['ID'] == 98) {
        $arResult['OBJECT_DATA']['IS_ROUTE'] = true;
    }
}
