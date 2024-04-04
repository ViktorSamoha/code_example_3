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
Loader::includeModule("highloadblock");

/*
 * category_id
 * location_id
 * period
 * arrival_date
 * departure_date
 * arrival_time
 * departure_time
 */

if (isset($values['category_id']) && !empty($values['category_id']) && $values['category_id'] != '') {
    $CATEGORY_ID = $values['category_id'];
}
if (isset($values['location_id']) && !empty($values['location_id']) && $values['location_id'] != '') {
    $LOCATION_ID = $values['location_id'];
}
if (isset($values['period']) && !empty($values['period']) && $values['period'] != '') {
    $PERIOD = $values['period'];
    if ($PERIOD == 'day') {
        $filter_time_interval_value = 4;
        $period_filter = 'day';
    } else {
        $filter_time_interval_value = 3;
        $period_filter = 'couple';
    }
}
if (isset($values['arrival_date']) && !empty($values['arrival_date']) && $values['arrival_date'] != '') {
    $ARRIVAL_DATE = $values['arrival_date'];
}
if (isset($values['departure_date']) && !empty($values['departure_date']) && $values['departure_date'] != '') {
    $DEPARTURE_DATE = $values['departure_date'];
}
if (isset($values['arrival_time']) && !empty($values['arrival_time']) && $values['arrival_time'] != '') {
    $ARRIVAL_TIME = $values['arrival_time'];
}
if (isset($values['departure_time']) && !empty($values['departure_time']) && $values['departure_time'] != '') {
    $DEPARTURE_TIME = $values['departure_time'];
}
$arObjects = [];
$arObjId = [];
//$CATEGORY_ID > $LOCATION_ID
if (isset($CATEGORY_ID) || isset($LOCATION_ID)) {
    $section = '';
    if (isset($CATEGORY_ID)) {
        $section = $CATEGORY_ID;
    } elseif (isset($LOCATION_ID)) {
        $section = $LOCATION_ID;
    }
    $arSelect = array("ID", "NAME", "PROPERTY_TIME_UNLIMIT_OBJECT", "PROPERTY_CAR_POSSIBILITY", "PROPERTY_CAR_CAPACITY");
    if (isset($filter_time_interval_value)) {
        $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "ACTIVE" => "Y", "SECTION_ID" => $section, "=PROPERTY_TIME_INTERVAL" => $filter_time_interval_value, '=PROPERTY_CAN_BOOK' => 36);
    } else {
        $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "ACTIVE" => "Y", "SECTION_ID" => $section, '=PROPERTY_CAN_BOOK' => 36);
    }
    $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $prop = CIBlockElement::GetProperty(IB_LOCATIONS, $arFields['ID'], array("sort" => "asc"), array("CODE" => "TIME_INTERVAL"));
        $arPropVal = [];
        while ($prop_ob = $prop->GetNext()) {
            if (isset($prop_ob['VALUE']) && isset($prop_ob['VALUE_ENUM'])) {
                $arPropVal[] = [
                    'ID' => $prop_ob['VALUE'],
                    'NAME' => $prop_ob['VALUE_ENUM'],
                ];
            }
        }
        $arObjects[$arFields['ID']] = [
            'ID' => $arFields['ID'],
            'NAME' => $arFields['NAME']
        ];
        if (!empty($arPropVal)) {
            if (count($arPropVal) == 2) {
                $arObjects[$arFields['ID']]["TIME_INTERVAL"]['TYPE'] = 'multi';
                $arObjects[$arFields['ID']]["TIME_INTERVAL"]['VALUE'] = 'null';
            } else {
                $arObjects[$arFields['ID']]["TIME_INTERVAL"]['TYPE'] = 'single';
                $arObjects[$arFields['ID']]["TIME_INTERVAL"]['VALUE'] = $arPropVal[0]['ID'];
            }
        }
        if (isset($arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'])) {
            if ($arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'] == 'Да') {
                $arObjects[$arFields['ID']]['TIME_LIMIT'] = 'Y';
            } else {
                $arObjects[$arFields['ID']]['TIME_LIMIT'] = 'N';
            }
        } else {
            $arObjects[$arFields['ID']]['TIME_LIMIT'] = 'N';
        }
        if (isset($arFields['PROPERTY_CAR_POSSIBILITY_VALUE'])) {
            if ($arFields['PROPERTY_CAR_POSSIBILITY_VALUE'] == 'Да') {
                $arObjects[$arFields['ID']]['CAR_POSSIBILITY'] = 'Y';
            } else {
                $arObjects[$arFields['ID']]['CAR_POSSIBILITY'] = 'N';
            }
        } else {
            $arObjects[$arFields['ID']]['CAR_POSSIBILITY'] = 'N';
        }
        if (isset($arFields['PROPERTY_CAR_CAPACITY_VALUE'])) {
            $arObjects[$arFields['ID']]['CAR_CAPACITY'] = $arFields['PROPERTY_CAR_CAPACITY_VALUE'];
        } else {
            $arObjects[$arFields['ID']]['CAR_CAPACITY'] = '';
        }
        $arObjId[] = $arFields['ID'];
    }
}
if (isset($ARRIVAL_DATE) && isset($DEPARTURE_DATE)) {
    Loader::includeModule("highloadblock");
    $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    $now = new DateTime();
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
    ];
    $arItems = $arObjId;
    $arItemsDates = [];
    $obj_in_hl = [];
    $_unset = [];
    $data = $entity_data_class::getList(array(
        "select" => array("*"),
        "order" => array("ID" => "DESC"),
        "filter" => array("UF_OBJECT_ID" => $arObjId)
    ));
    while ($arData = $data->Fetch()) {
        if (in_array($arData["UF_OBJECT_ID"], $arObjId)) {
            $obj_in_hl[] = $arData["UF_OBJECT_ID"];
        }
        $arDates = _get_dates($arData['UF_ARRIVAL_DATE'], $arData['UF_DEPARTURE_DATE']);
        foreach ($arDates as $date) {
            $booked_dates[] = $date;
        }
        if (isset($arItemsDates[$arData['UF_OBJECT_ID']])) {
            $arItemsDates[$arData['UF_OBJECT_ID']] = array_merge($arItemsDates[$arData['UF_OBJECT_ID']], createSelectedDateArr($booked_dates));
        } else {
            $arItemsDates[$arData['UF_OBJECT_ID']] = createSelectedDateArr($booked_dates);
        }
        unset($arDates, $booked_dates);
    }
    if (isset($ARRIVAL_TIME)) {
        $filter_dates = _get_dates($ARRIVAL_DATE . ' ' . $ARRIVAL_TIME . ':00', $DEPARTURE_DATE . ' 00:00:00');
    } elseif (isset($ARRIVAL_TIME) && isset($DEPARTURE_TIME)) {
        $filter_dates = _get_dates($ARRIVAL_DATE . ' ' . $ARRIVAL_TIME . ':00', $DEPARTURE_DATE . ' ' . $DEPARTURE_TIME . ':00');
    } else {
        $filter_dates = _get_dates($ARRIVAL_DATE . ' 00:00:00', $DEPARTURE_DATE . ' 00:00:00');
    }
    $filter_dates = createSelectedDateArr($filter_dates);
    $filter_date_count = count($filter_dates);
    $un_unset = [];
    foreach ($arItemsDates as $item_id => $date_time) {
        foreach ($date_time as $date => $time) {
            foreach ($filter_dates as $f_date => $f_date_time) {
                if (DateTime::createFromFormat('d.m.Y', $f_date)->format('d.m.Y') == DateTime::createFromFormat('d.m.Y', $date)->format('d.m.Y')) {
                    $_unset[$item_id][] = $date;
                }
            }
        }
    }
    foreach ($un_unset as $id) {
        foreach ($_unset as $u_id => $unset_date) {
            if ($u_id == $id) {
                unset($_unset[$u_id]);
            }
        }
    }
    foreach ($_unset as $id => $unset_date) {
        if (isset($period_filter)) {
            continue;
        } else {
            if (count($unset_date) != $filter_date_count) {
                unset($_unset[$id]);
            }
        }
    }

    if (!empty($_unset)) {
        foreach ($_unset as $id => $unset_dates) {
            foreach ($arItems as $i => $item_id) {
                if ($item_id == $id) {
                    unset($arItems[$i]);
                }
            }
        }
    }
    foreach ($arObjects as &$obj) {
        if (!in_array($obj['ID'], $arItems)) {
            unset($arObjects[$obj['ID']]);
        }
    }
}
if (!empty($arObjects)) {
    echo json_encode($arObjects);
} else {
    return false;
}
?>