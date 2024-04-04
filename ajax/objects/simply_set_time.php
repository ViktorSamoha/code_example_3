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

$arResult = [];
$allowed_arrival_time = [];
$allowed_departure_time = [];

$ar_day_end = ["21:00:00", "22:00:00"];
$ar_full_day = ["8:00:00", "9:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00"];

if (!empty($ar_arrival_time)) {
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
            $allowed_arrival_time = array_diff($ar_full_day, $ar_arrival_time);
            $allowed_departure_time = $ar_departure_time;
        } else {
            $allowed_arrival_time = array_diff($ar_full_day, $ar_arrival_time);
            $allowed_departure_time = [];
        }
    }
} else {
    $arResult['ERROR'] = 'Бронирование на данный диапазон не возможно';
}

if (!isset($arResult['ERROR'])) {
    $arResult['ARRIVAL_TIME'] = $allowed_arrival_time;
    $arResult['DEPARTURE_TIME'] = $allowed_departure_time;
}

echo json_encode($arResult);
