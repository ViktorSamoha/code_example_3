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
$post = $request->getPostList();

Loader::includeModule("iblock");
Loader::includeModule("highloadblock");

$item_id = $post['object_id'];
$arrival_date_time = $post['arr_date_time'];
$departure_date_time = $post['dep_date_time'];

if (isset($post['order_id'])) {
    $order_id = $post['order_id'];
    $arFilter = ["UF_OBJECT_ID" => $item_id, "!=ID" => $order_id];
} else {
    $arFilter = ["UF_OBJECT_ID" => $item_id,];
}
$book_permission = true;

if ($item_id) {
    $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    $data = $entity_data_class::getList(array(
        "select" => array("*"),
        "order" => array("ID" => "DESC"),
        "filter" => $arFilter,
    ));
    $booked_dates = [];
    while ($arData = $data->Fetch()) {
        $arDates = _get_dates($arData['UF_ARRIVAL_DATE'], $arData['UF_DEPARTURE_DATE']);
        foreach ($arDates as $date) {
            $booked_dates[] = $date;
        }
    }
    $db_booked_dates = createSelectedDateArr($booked_dates);
    $arUserDates = _get_dates($arrival_date_time, $departure_date_time);
    $user_booked_dates = createSelectedDateArr($arUserDates);
    if ($arUserDates[0]['ar_date']['date'] == date('d.m.Y')) {
        if ($arUserDates[0]['ar_date']['time'] <= date('H:i:s')) {
            $book_permission = false;
        }
    }
    if ($book_permission) {
        foreach ($db_booked_dates as $db_date => $db_date_time) {
            foreach ($user_booked_dates as $u_date => $u_date_time) {
                if ($db_date == $u_date) {
                    if (count(array_intersect($u_date_time['time'], $db_date_time['time'])) > 0) {
                        $book_permission = false;
                    }
                }
            }
        }
    }
    echo json_encode($book_permission);
}
