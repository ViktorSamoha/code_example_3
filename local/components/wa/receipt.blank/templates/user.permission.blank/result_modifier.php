<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
if ($arResult['PERMISSION_DATA']) {
    $arResult['BLANK']['NAME'] = $arResult['PERMISSION_DATA']['NAME'];
    if ($arResult['PERMISSION_DATA']['PAYMENT_STATUS'] && $arResult['PERMISSION_DATA']['PAYMENT_DATE_TIME'] && $arResult['PERMISSION_DATA']['PRICE']) {
        $arResult['BLANK']['PAYMENT_STRING'] = $arResult['PERMISSION_DATA']['PAYMENT_STATUS'] . ': ' . $arResult['PERMISSION_DATA']['PRICE'] . 'р - ' . $arResult['PERMISSION_DATA']['PAYMENT_DATE_TIME'];
    }
    if ($arResult['PERMISSION_DATA']['PAYMENT_LINK']) {
        $arResult['BLANK']['PAYMENT_LINK'] = $arResult['PERMISSION_DATA']['PAYMENT_LINK'];
    }
    if ($arResult['PERMISSION_DATA']['ARRIVAL_DATE'] && $arResult['PERMISSION_DATA']['DEPARTURE_DATE']) {
        $arResult['BLANK']['DATE_INTERVAL'] = $arResult['PERMISSION_DATA']['ARRIVAL_DATE'] . ' - ' . $arResult['PERMISSION_DATA']['DEPARTURE_DATE'];
    }
    if ($arResult['PERMISSION_DATA']['QR_CODE']) {
        $arResult['BLANK']['QR_CODE'] = $arResult['PERMISSION_DATA']['QR_CODE'];
    }
}
if ($arResult['USER_DATA']) {
    $arResult['BLANK']['FIO'] = $arResult['USER_DATA']['LAST_NAME'] . ' ' . $arResult['USER_DATA']['NAME'] . ' ' . $arResult['USER_DATA']['SECOND_NAME'];
}
if ($arResult['ROUTE']) {
    $arResult['BLANK']['ROUTE'] = $arResult['ROUTE'];
}
if ($arResult['USER_GROUP']) {
    $arResult['BLANK']['VISITORS']['COUNT'] = count($arResult['USER_GROUP']) + 1;
    foreach ($arResult['USER_GROUP'] as $visitor) {
        if ($visitor['PREFERENTIAL_CATEGORY']) {
            $arResult['BLANK']['VISITORS']['CATEGORIES'][$visitor['PREFERENTIAL_CATEGORY']][] = $visitor;
        } else {
            $arResult['BLANK']['VISITORS']['LIST'][] = $visitor;
        }
    }
}