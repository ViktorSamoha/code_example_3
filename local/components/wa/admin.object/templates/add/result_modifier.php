<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
$arResult['OBJECT_TYPES'] = getObjectType();
$arResult['OBJECT_PROPS'] = getIblockListProperties(IB_LOCATIONS);
$arResult['PARTNERS_LIST'] = getPartnersList();
$arResult['OBJECT_FEATURES'] = getObjectCharacteristic();