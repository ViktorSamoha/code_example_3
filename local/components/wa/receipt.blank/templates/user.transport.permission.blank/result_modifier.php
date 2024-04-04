<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
if ($arResult['PERMISSION_DATA']) {
    $arResult['BLANK']['NAME'] = $arResult['PERMISSION_DATA']['NAME'];
    if ($arResult['PERMISSION_DATA']['STATUS']) {
        $arResult['BLANK']['STATUS_STRING'] = 'Статус: ' . $arResult['PERMISSION_DATA']['STATUS'];
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
}else{
    if($arResult['PERMISSION_DATA']['USER_FIO']){
        $arResult['BLANK']['FIO'] = $arResult['PERMISSION_DATA']['USER_FIO'];
    }
}
if ($arResult['ROUTE']) {
    $arResult['BLANK']['ROUTE'] = $arResult['ROUTE'];
}
if ($arResult['USER_VEHICLE']) {
    if ($arResult['USER_VEHICLE']['DRIVING_LICENSE_SERIES'] && $arResult['USER_VEHICLE']['DRIVING_LICENSE_NUMBER']) {
        if ($arResult['USER_VEHICLE']['VEHICLE_POWER'] == 'Да') {
            $arResult['BLANK']['USER_DRIVING_LICENSE'] = 'серия ' . $arResult['USER_VEHICLE']['DRIVING_LICENSE_SERIES'] . ', номер ' . $arResult['USER_VEHICLE']['DRIVING_LICENSE_NUMBER'] . ', менее 10 л.с.';
        } else {
            $arResult['BLANK']['USER_DRIVING_LICENSE'] = 'серия ' . $arResult['USER_VEHICLE']['DRIVING_LICENSE_SERIES'] . ', номер ' . $arResult['USER_VEHICLE']['DRIVING_LICENSE_NUMBER'];
        }
    }
    if ($arResult['USER_VEHICLE']['VEHICLE_TYPE'] && $arResult['USER_VEHICLE']['MODEL']) {
        $arResult['BLANK']['VEHICLE_MARK_MODEL'] = $arResult['USER_VEHICLE']['VEHICLE_TYPE'] . ' ' . $arResult['USER_VEHICLE']['MODEL'];
    }
    if ($arResult['USER_VEHICLE']['INSPECTION_DATE']) {
        $arResult['BLANK']['INSPECTION_DATE'] = $arResult['USER_VEHICLE']['INSPECTION_DATE'];
    }
}
