<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */

if ($arResult['ORDER_DATA']) {
    if (getDateDiff($arResult['ORDER_DATA']['ARRIVAL_DATE'], $arResult['ORDER_DATA']['DEPARTURE_DATE']) > 0) {
        $arResult['ORDER_DATA']['RENT_PERIOD'] = 'couple';
    } else {
        $arResult['ORDER_DATA']['RENT_PERIOD'] = 'day';
    }
    if ($arResult['ORDER_DATA']["DATE_INSERT"]) {
        $arResult['ORDER_DATA']["DATE_INSERT"] = date('d.m.Y', strtotime($arResult['ORDER_DATA']["DATE_INSERT"]));
    }
    if ($arResult['ORDER_DATA']['BOOKING_OBJECT']) {
        if ($arResult['ORDER_DATA']['BOOKING_OBJECT']['TIME_INTERVAL'] && is_array($arResult['ORDER_DATA']['BOOKING_OBJECT']['TIME_INTERVAL']) && count($arResult['ORDER_DATA']['BOOKING_OBJECT']['TIME_INTERVAL']) > 0) {
            if (isset($arResult['ORDER_DATA']['BOOKING_OBJECT']['TIME_INTERVAL'][PROPERTY_TIME_INTERVAL_COUPLE])) {
                if ($arResult['ORDER_DATA']['BOOKING_OBJECT']['OBJECT_COST']) {
                    $arResult['ORDER_DATA']['BOOKING_OBJECT']['BOOKING_OBJECT_PRICE'] = $arResult['ORDER_DATA']['BOOKING_OBJECT']['OBJECT_COST'];
                }
                $arResult['ORDER_DATA']['BOOKING_OBJECT']['BOOKING_OBJECT_PERIOD'] = 'couple';
            } else {
                if ($arResult['ORDER_DATA']['BOOKING_OBJECT']['OBJECT_DAILY_COST']) {
                    $arResult['ORDER_DATA']['BOOKING_OBJECT']['BOOKING_OBJECT_PRICE'] = $arResult['ORDER_DATA']['BOOKING_OBJECT']['OBJECT_DAILY_COST'];
                }
                $arResult['ORDER_DATA']['BOOKING_OBJECT']['BOOKING_OBJECT_PERIOD'] = 'day';
            }
        }
        if(isset($arResult['ORDER_DATA']['BOOKING_OBJECT']['IS_ROUTE'])){
            if($arResult['ORDER_DATA']['BOOKING_OBJECT']['IS_ROUTE']){
                $arResult['ORDER_DATA']['IS_ROUTE'] = 'true';
            }else{
                $arResult['ORDER_DATA']['IS_ROUTE'] = 'false';
            }
        }
    }
}
