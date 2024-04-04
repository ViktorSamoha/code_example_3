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
    if ($arResult['PERMISSION_DATA']['VEHICLE_DOCS']) {
        $arDocs = $arResult['PERMISSION_DATA']['VEHICLE_DOCS'][$arResult['PERMISSION_DATA']['VEHICLE_ID']];
        if ($arDocs['DRIVING_LICENSE_FILES']) {
            foreach ($arDocs['DRIVING_LICENSE_FILES'] as $drivingLicenseFile) {
                $arResult['BLANK']['DRIVING_LICENSE_FILES'][] = [
                    'CONTENT_TYPE' => $drivingLicenseFile['CONTENT_TYPE'],
                    'SRC' => $drivingLicenseFile['SRC'],
                ];
            }
        }
        if ($arDocs['TECHNICAL_PASSPORT']) {
            foreach ($arDocs['TECHNICAL_PASSPORT'] as $techPassFile) {
                $arResult['BLANK']['TECHNICAL_PASSPORT'][] = [
                    'CONTENT_TYPE' => $techPassFile['CONTENT_TYPE'],
                    'SRC' => $techPassFile['SRC'],
                ];
            }
        }
        if ($arDocs['INSPECTION_FILES']) {
            foreach ($arDocs['INSPECTION_FILES'] as $inspectionFile) {
                $arResult['BLANK']['INSPECTION_FILES'][] = [
                    'CONTENT_TYPE' => $inspectionFile['CONTENT_TYPE'],
                    'SRC' => $inspectionFile['SRC'],
                ];
            }
        }
    }
    if($arResult['PERMISSION_DATA']['DENY_TEXT']){
        $arResult['BLANK']['DENY_TEXT'] = $arResult['PERMISSION_DATA']['DENY_TEXT']['TEXT'];
    }
    if($arResult['PERMISSION_DATA']['STATUS']){
        $arResult['BLANK']['STATUS'] = $arResult['PERMISSION_DATA']['STATUS'];
    }
}
if ($arResult['USER_DATA']) {
    $arResult['BLANK']['FIO'] = $arResult['USER_DATA']['LAST_NAME'] . ' ' . $arResult['USER_DATA']['NAME'] . ' ' . $arResult['USER_DATA']['SECOND_NAME'];
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
