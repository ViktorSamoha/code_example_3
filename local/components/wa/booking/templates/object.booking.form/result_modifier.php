<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */

if ($arResult['LOCATION_DATA']) {
    if ($arResult['LOCATION_DATA']['PRICE_TYPE']) {
        if ($arResult['LOCATION_DATA']['PRICE_TYPE'] == "сутки") {
            $arResult['LOCATION_DATA']['BOOKING_PERIOD'] = 'couple';
            $arResult['LOCATION_DATA']['PRICE'] =  $arResult['LOCATION_DATA']['OBJECT_COST'];
        } else {
            $arResult['LOCATION_DATA']['BOOKING_PERIOD'] = 'day';
            $arResult['LOCATION_DATA']['PRICE'] =  $arResult['LOCATION_DATA']['FIXED_COST'];
        }
    }
    if ($arResult['LOCATION_DATA']['TIME_UNLIMIT_OBJECT']) {
        if ($arResult['LOCATION_DATA']['TIME_UNLIMIT_OBJECT'] == 'Нет') {
            $arResult['LOCATION_DATA']['TIME_UNLIMIT_OBJECT'] = 'N';
        } else {
            $arResult['LOCATION_DATA']['TIME_UNLIMIT_OBJECT'] = 'Y';
        }
    }
}
