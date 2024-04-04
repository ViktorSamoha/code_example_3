<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if ($arResult['USER_VEHICLE'] && $arResult['VEHICLE_TYPES']) {
    foreach ($arResult['VEHICLE_TYPES'] as $type) {
        if ($arResult['USER_VEHICLE']['VEHICLE_TYPE'][0] == $type['UF_XML_ID']) {
            $arResult['USER_VEHICLE']['VEHICLE_TYPE'] = [
                'XML_ID' => $type['UF_XML_ID'],
                'TYPE' => $type['UF_TYPE'],
            ];
        }
    }
}

?>