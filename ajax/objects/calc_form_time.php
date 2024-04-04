<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$values = $request->getPostList();

Loader::includeModule("iblock");

$ar_arrival_time = $values['arrival_time'];
$ar_departure_time = $values['departure_time'];
$period_value = $values['period_value'];
$time_limit_value = $values['time_limit_value'];
$time_limit_start_day_border = $values['tl_am'];
$time_limit_end_day_border = $values['tl_pm'];
$user_form = $values['user_form'];

$arResult = [];
$allowed_arrival_time = [];
$allowed_departure_time = [];

$ar_day_end = ["21:00:00", "22:00:00", "23:00:00"];
$ar_full_day = ["8:00:00", "9:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00"];
$first_half_day = [
    0 => '8:00:00',
    1 => '9:00:00',
    2 => '10:00:00',
    3 => '11:00:00',
    4 => '12:00:00',
    5 => '13:00:00',
    6 => '14:00:00',
];
$second_half_day = [
    7 => '15:00:00',
    8 => '16:00:00',
    9 => '17:00:00',
    10 => '18:00:00',
    11 => '19:00:00',
    12 => '20:00:00',
    13 => '21:00:00',
    14 => '22:00:00',
    15 => '23:00:00',
];
if (!empty($ar_arrival_time)) {
    $can_book = true;
    /*$can_book = false;
    if ($period_value == 'couple') {
        if (in_array("20:00:00", $ar_arrival_time)) {
            foreach ($ar_arrival_time as $time) {
                $hour = explode(":", $time)[0];
                if ($hour <= 20) {
                    $can_book = false;
                } else {
                    $can_book = true;
                }
            }
        }
    } else {
        if (count($ar_arrival_time) > 1) {
            $can_book = true;
        }
    }*/
    if ($can_book) {
        if ($user_form == 'true') {
            if ($period_value == 'day') {
                if (!empty($ar_departure_time)) {
                    $allowed_arrival_time = array_diff(array_intersect($ar_departure_time, $ar_arrival_time), $ar_day_end);
                    $allowed_departure_time = $allowed_arrival_time;
                } else {
                    $allowed_arrival_time = array_diff($ar_arrival_time, $ar_day_end);
                    $allowed_departure_time = array_diff($allowed_arrival_time, $ar_day_end);
                }
            } else {
                if (!empty($ar_departure_time)) {
                    if (count(array_intersect($ar_departure_time, $first_half_day)) != 7) {
                        if (count(array_intersect($ar_departure_time, $second_half_day)) != 8) {
                            if (count(array_intersect($ar_departure_time, $ar_arrival_time)) > 0) {
                                $allowed_departure_time = array_intersect($ar_departure_time, $first_half_day);
                                $allowed_arrival_time = array_intersect($allowed_departure_time, $ar_arrival_time);
                            } else {
                                $arResult['ERROR'] = 'Бронирование на данный диапазон не возможно';
                            }
                        } else {
                            $arResult['ERROR'] = 'Бронирование на данный диапазон не возможно';
                        }
                    } else {
                        if (count(array_intersect($ar_departure_time, $first_half_day)) > 0) {
                            if (count(array_intersect($ar_departure_time, $second_half_day)) > 0) {
                                if (count(array_intersect($ar_departure_time, $ar_arrival_time)) > 0) {
                                    $allowed_departure_time = array_intersect($ar_departure_time, $ar_arrival_time);
                                    $allowed_arrival_time = array_intersect($allowed_departure_time, $ar_arrival_time);
                                } else {
                                    $arResult['ERROR'] = 'Бронирование на данный диапазон не возможно';
                                }
                            } else {
                                if (array_intersect($ar_departure_time, $ar_arrival_time)) {
                                    $allowed_departure_time = array_intersect($ar_departure_time, $ar_arrival_time);
                                    $allowed_arrival_time = array_intersect($allowed_departure_time, $ar_arrival_time);
                                } else {
                                    $arResult['ERROR'] = 'Бронирование на данный диапазон не возможно';
                                }
                            }
                        } else {
                            $arResult['ERROR'] = 'Бронирование на данный диапазон не возможно';
                        }
                    }
                } else {
                    $allowed_arrival_time = $ar_arrival_time;
                    $allowed_departure_time = $ar_full_day;
                }
            }
        } else {
            if ($period_value == 'day') {
                if (!empty($ar_departure_time)) {
                    $allowed_arrival_time = array_diff(array_intersect($ar_departure_time, $ar_arrival_time), $ar_day_end);
                    $allowed_departure_time = $allowed_arrival_time;
                } else {
                    $allowed_arrival_time = array_diff($ar_arrival_time, $ar_day_end);
                    $allowed_departure_time = array_diff($allowed_arrival_time, $ar_day_end);
                }
            } else {
                $allowed_arrival_time = $ar_arrival_time;
                $allowed_departure_time = $ar_departure_time;
            }
        }
    } else {
        $arResult['ERROR'] = 'Бронирование на данный диапазон не возможно';
    }
} else {
    $arResult['ERROR'] = 'Бронирование на данный диапазон не возможно';
}

if (!isset($arResult['ERROR'])) {
    if (isset($time_limit_value) && $time_limit_value != '') {
        if ($time_limit_value == 'Y') {
            if ((isset($time_limit_start_day_border) && $time_limit_start_day_border != '') && (isset($time_limit_end_day_border) && $time_limit_end_day_border != '')) {
                $a = array_slice($allowed_arrival_time, array_keys($allowed_arrival_time, $time_limit_start_day_border . ':00')[0]);
                $arResult['ARRIVAL_TIME'] = array_slice($a, 0, array_keys($a, $time_limit_end_day_border . ':00')[0] + 1);
            }
        }
    } else {
        /*$arResult['ARRIVAL_TIME'] = array_diff($allowed_arrival_time, $ar_day_end);
        $arResult['DEPARTURE_TIME'] = array_diff($allowed_departure_time, $ar_day_end);*/
        $arResult['ARRIVAL_TIME'] = $allowed_arrival_time;
        $arResult['DEPARTURE_TIME'] = $allowed_departure_time;
    }
}

echo json_encode($arResult);
