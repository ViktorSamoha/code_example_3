<?php
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
Loader::includeModule("highloadblock");

$object_id = $values['object_id'];
$user_date = $values['user_date'];
$cur_day = false;
if ($user_date == date('d.m.Y')) {
    $cur_day = true;
}
$day = [
    0 => '8:00:00',
    1 => '9:00:00',
    2 => '10:00:00',
    3 => '11:00:00',
    4 => '12:00:00',
    5 => '13:00:00',
    6 => '14:00:00',
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

//получаем забронированные даты объекта
$hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();
$data = $entity_data_class::getList(array(
    "select" => array("*"),
    "order" => array("ID" => "DESC"),
    "filter" => array("UF_OBJECT_ID" => $object_id)
));

$booked_dates = [];
while ($arData = $data->Fetch()) {
    if($arData['UF_ARRIVAL_DATE'] && $arData['UF_DEPARTURE_DATE']){
        $arDates = _get_dates($arData['UF_ARRIVAL_DATE'], $arData['UF_DEPARTURE_DATE']);
        foreach ($arDates as $date) {
            $booked_dates[] = $date;
        }
    }
}
$arr = createSelectedDateArr($booked_dates);
foreach ($arr as $date => $date_props) {
    if ($date == $user_date) {
        $diff_arr = array_diff($day, $date_props['time']);
        if ($cur_day) {
            $free_time = [];
            foreach ($diff_arr as $time) {
                if (DateTime::createFromFormat('H:i:s', $time)->format('H:i:s') > date('H:i:s')) {
                    $free_time[] = $time;
                }
            }

        }
    }
}
if (isset($free_time) && !empty($free_time)) {
    echo json_encode($free_time);
} elseif (isset($diff_arr) && !empty($diff_arr)) {
    echo json_encode($diff_arr);
} else {
    if ($cur_day) {
        $clear_time = [];
        foreach ($day as $time) {
            if (explode(':', $time)[0] > date('H')) {
                $clear_time[] = $time;
            }
        }
        echo json_encode($clear_time);
    } else {
        echo json_encode($day);
    }
}