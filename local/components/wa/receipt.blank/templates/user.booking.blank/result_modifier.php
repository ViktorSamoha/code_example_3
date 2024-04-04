<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
if ($arResult['PERMISSION_DATA']) {
    $arResult['BLANK']['NAME'] = $arResult['PERMISSION_DATA']['NAME'];
    if ($arResult['PERMISSION_DATA']['OBJECT_ID']) {
        $arResult['OBJECT_DATA'] = getObjectById($arResult['PERMISSION_DATA']['OBJECT_ID']);
        if ($arResult['OBJECT_DATA']) {
            $arResult['BLANK']['OBJECT_NAME'] = $arResult['OBJECT_DATA']['NAME'];
            if ($arResult['OBJECT_DATA']['SECTIONS']) {
                $arResult['BLANK']['OBJECT_CATEGORY'] = $arResult['OBJECT_DATA']['SECTIONS'][1]['NAME'];
                $arResult['BLANK']['OBJECT_LOCATION'] = $arResult['OBJECT_DATA']['SECTIONS'][2]['NAME'];
            }
            if ($arResult['OBJECT_DATA']['CAPACITY_ESTIMATED']) {
                if ($arResult['PERMISSION_DATA']['GUEST_COUNT'] > $arResult['OBJECT_DATA']['CAPACITY_ESTIMATED']) {
                    $arResult['BLANK']['PERSON_OVER'] = $arResult['PERMISSION_DATA']['GUEST_COUNT'] - $arResult['OBJECT_DATA']['CAPACITY_ESTIMATED'];
                    $arResult['BLANK']['PERSON_OVER_COST'] = $arResult['BLANK']['PERSON_OVER'] * $arResult['OBJECT_DATA']['COST_PER_PERSON'];
                }
            }
        }
    }
    if ($arResult['PERMISSION_DATA']['ARRIVAL_DATE'] && $arResult['PERMISSION_DATA']['CHECK_IN_TIME']) {
        $arResult['BLANK']['ARRIVAL_DATE_TIME'] = $arResult['PERMISSION_DATA']['ARRIVAL_DATE'] . ' - ' . $arResult['PERMISSION_DATA']['CHECK_IN_TIME'];
    }
    if ($arResult['PERMISSION_DATA']['DEPARTURE_DATE'] && $arResult['PERMISSION_DATA']['DEPARTURE_TIME']) {
        $arResult['BLANK']['DEPARTURE_DATE_TIME'] = $arResult['PERMISSION_DATA']['DEPARTURE_DATE'] . ' - ' . $arResult['PERMISSION_DATA']['DEPARTURE_TIME'];
    }
    if ($arResult['PERMISSION_DATA']['USER_NAME'] && $arResult['PERMISSION_DATA']['USER_SECOND_NAME']) {
        $arResult['BLANK']['USER_FIO'] = $arResult['PERMISSION_DATA']['USER_NAME'] . ' ' . $arResult['PERMISSION_DATA']['USER_SECOND_NAME'];
    }
    if ($arResult['PERMISSION_DATA']['GUEST_COUNT']) {
        $arResult['BLANK']['GUEST_COUNT'] = $arResult['PERMISSION_DATA']['GUEST_COUNT'];
    }
    if ($arResult['PERMISSION_DATA']['ID']) {
        $arResult['BLANK']['ID'] = $arResult['PERMISSION_DATA']['ID'];
    }
    if ($arResult['PERMISSION_DATA']['BOOKING_TYPE']) {
        $arResult['BLANK']['BOOKING_TYPE'] = $arResult['PERMISSION_DATA']['BOOKING_TYPE'];
    }
    if ($arResult['PERMISSION_DATA']['GUEST_CARS']) {
        if (is_array($arResult['PERMISSION_DATA']['GUEST_CARS'])) {
            $arResult['BLANK']['GUEST_CARS'] = $arResult['PERMISSION_DATA']['GUEST_CARS'];
        }

    }
    if ($arResult['PERMISSION_DATA']['IS_PAYED'] && $arResult['PERMISSION_DATA']['DATE_PAY'] && $arResult['PERMISSION_DATA']['BOOKING_COST']) {
        if ($arResult['PERMISSION_DATA']['IS_PAYED'] == 'Y') {
            $arResult['BLANK']['PAYMENT_STRING'] = 'Оплачено: ' . $arResult['PERMISSION_DATA']['BOOKING_COST'] . 'р - ' . $arResult['PERMISSION_DATA']['DATE_PAY'];
        } else {
            $arResult['BLANK']['PAYMENT_STRING'] = 'Не оплачено';
        }
    } else {
        $arResult['BLANK']['PAYMENT_STRING'] = 'Не оплачено';
    }
    if ($arResult['PERMISSION_DATA']['QR_CODE']) {
        $arResult['BLANK']['QR_CODE'] = $arResult['PERMISSION_DATA']['QR_CODE'];
    }
    if ($arResult['PERMISSION_DATA']['PERMISSION'] == 'Нет') {
        //льготники не платят за разрешение
        $arResult['BLANK']['PERMISSION_COUNT'] = $arResult['PERMISSION_DATA']['GUEST_COUNT'] - $arResult['PERMISSION_DATA']['BENIFICIARIES'];
        $arResult['BLANK']['PERMISSION_COST'] = $arResult['BLANK']['PERMISSION_COUNT'] * VISIT_PERMISSION_COST;
    }
    $arResult['BLANK']['PERMISSION'] = $arResult['PERMISSION_DATA']['PERMISSION'];
    if ($arResult['PERMISSION_DATA']['BOOKING_COST']) {
        $arResult['BLANK']['BOOKING_COST'] = $arResult['PERMISSION_DATA']['BOOKING_COST'];
    }
    if ($arResult['PERMISSION_DATA']['OBJECT_RENT_COST']) {
        $arResult['BLANK']['OBJECT_RENT_COST'] = $arResult['PERMISSION_DATA']['OBJECT_RENT_COST'];
    }
}