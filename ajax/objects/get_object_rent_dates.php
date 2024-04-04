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

Loader::includeModule("iblock");
Loader::includeModule("highloadblock");

$user_form = $request->get("user_form");
$user_period = $request->get("period");
$unset_a_date = $request->get("unset_arrival_date");
$unset_d_date = $request->get("unset_departure_date");
$unset_date = $request->get("unset_date");
$is_route = $request->get("is_route");
$daily_traffic = $request->get("daily_traffic");

if ($request->get("object_id")) {
    if ($is_route) {
        $hlblock = HL\HighloadBlockTable::getById(HL_ROUTE_BOOKING_ID)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $hlbElements = [];
        $arBookedDay = [];
        $arBookedDates = [];
        $booked_dates = [];
        $data = $entity_data_class::getList(array(
            "select" => array("*"),
            "order" => array("ID" => "DESC"),
            "filter" => array("UF_OBJECT_ID" => $request->get("object_id"))
        ));
        while ($arData = $data->Fetch()) {
            $hlbElements[] = [
                'ID' => $arData['ID'],
                'OBJECT_ID' => $arData['UF_OBJECT_ID'],
                'ARRIVAL_DATE' => $arData['UF_ARRIVAL_DATE']->format('d.m.Y'),
                'DEPARTURE_DATE' => $arData['UF_DEPARTURE_DATE']->format('d.m.Y'),
                'PEOPLE_COUNT' => $arData['UF_PEOPLE_COUNT'],
            ];
        }
        if (!empty($hlbElements)) {
            foreach ($hlbElements as $element) {
                $period = getDatePeriod($element['ARRIVAL_DATE'], $element['DEPARTURE_DATE']);
                if ($period) {
                    foreach ($period as $date) {
                        if (isset($arBookedDay[$date]) && !empty($arBookedDay[$date])) {
                            $arBookedDay[$date] += $element['PEOPLE_COUNT'];
                        } else {
                            $arBookedDay[$date] = $element['PEOPLE_COUNT'];
                        }
                    }
                }
            }
            if (!empty($arBookedDay)) {
                foreach ($arBookedDay as $day => $count) {
                    if ($daily_traffic == $count) {
                        $arBookedDates[] = _get_dates($day . ' 08:00:00', $day . ' 23:00:00');
                    }
                }
                if (!empty($arBookedDates)) {
                    foreach ($arBookedDates as $date) {
                        $booked_dates[] = $date;
                    }
                    if (isset($user_form) && $user_form == "true") {
                        echo json_encode(createCalendarDatesForUserForm($booked_dates));
                    } else {
                        echo json_encode(createCalendarDates($booked_dates, $user_period));
                    }
                }
            }
        }else{
            if (isset($user_form) && $user_form == "true") {
                echo json_encode(createCalendarDatesForUserForm($booked_dates));
            } else {
                echo json_encode(createCalendarDates($booked_dates, $user_period));
            }
        }
    } else {
        $item_id = $request->get("object_id");
        $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $data = $entity_data_class::getList(array(
            "select" => array("*"),
            "order" => array("ID" => "DESC"),
            "filter" => array("UF_OBJECT_ID" => $item_id)
        ));
        $booked_dates = [];
        if ($unset_date) {
            while ($arData = $data->Fetch()) {
                $arDates = _get_dates($arData['UF_ARRIVAL_DATE'], $arData['UF_DEPARTURE_DATE']);
                foreach ($arDates as $date) {
                    $booked_dates[] = $date;
                }
            }
            $result = [];
            $res = createSelectedDateArr($booked_dates);
            unset($date);
            foreach ($res as $date => $date_time) {
                $result[$date] = [
                    'date' => $date,
                    'status' => 'disabled',
                    'class' => 'flatpickr-disabled',
                ];
            }
            echo json_encode($result);
        } else {
            while ($arData = $data->Fetch()) {
                if (isset($unset_a_date) && isset($unset_d_date)) {
                    if (($arData["UF_ARRIVAL_DATE"]->format("d.m.Y") == $unset_a_date)
                        && ($arData["UF_DEPARTURE_DATE"]->format('d.m.Y') == $unset_d_date)) {
                        continue;
                    } else {
                        $arDates = _get_dates($arData['UF_ARRIVAL_DATE'], $arData['UF_DEPARTURE_DATE']);
                        foreach ($arDates as $date) {
                            $booked_dates[] = $date;
                        }
                    }
                } else {
                    $arDates = _get_dates($arData['UF_ARRIVAL_DATE'], $arData['UF_DEPARTURE_DATE']);
                    foreach ($arDates as $date) {
                        $booked_dates[] = $date;
                    }
                }
            }
            if (isset($user_form) && $user_form == "true") {
                echo json_encode(createCalendarDatesForUserForm($booked_dates));
            } else {
                echo json_encode(createCalendarDates($booked_dates, $user_period));
            }
        }
    }
}