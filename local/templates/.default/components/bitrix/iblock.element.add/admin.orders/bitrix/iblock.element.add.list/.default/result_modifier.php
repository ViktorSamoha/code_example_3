<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */

/** @var array $arResult */

use Bitrix\Main\Application,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity,
    Bitrix\Main\Context,
    Bitrix\Main\Request;

Loader::includeModule("iblock");
Loader::includeModule("highloadblock");

$user = getUserData();
$request = Context::getCurrent()->getRequest();
$get = $request->getQueryList();
$arOrders = $request->getQuery("orders");
$arLocations = $request->getQuery("locations");
$arch = $request->get("arch");
$search_fio = $request->get("search_fio");
$search_car_id = $request->get("search_car_id");
$search_object_name = $request->getQuery("search_object_name");
$filter_code = $request->get("code");

if (isset($arch) && $arch == 'Y') {
    $arFilter = array("IBLOCK_ID" => IB_ORDERS_ARCHIVE);
} else {
    $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST);
}
//поиск по заказам
if (isset($search_object_name) && !empty($search_object_name)) {
    $arSelect = array("ID");
    $arObjectsId = [];
    $arSearchObjectName = [];
    $arObjectFilter = array("IBLOCK_ID" => IB_OBJECT, "%NAME" => $search_object_name);
    $res = CIBlockElement::GetList([], $arObjectFilter, false, [], $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arObjectsId[] = $arFields['ID'];
    }
    unset($res, $ob, $arFields);
    if (!empty($arObjectsId)) {
        $Filter = array("IBLOCK_ID" => IB_BOOKING_LIST, "PROPERTY_BOOKING_OBJECT" => $arObjectsId);
        $res = CIBlockElement::GetList([], $Filter, false, [], $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arSearchObjectName[] = $arFields['ID'];
        }
    }
    if (isset($arFilter["ID"])) {
        $arFilter["ID"] = selectFromArr($arFilter["ID"], $arSearchObjectName);
    } else {
        $arFilter["ID"] = $arSearchObjectName;
    }
    unset($arSelect, $Filter, $res, $ob, $ob, $arFields);
}
if (isset($search_fio) && !empty($search_fio)) {
    $arSearchFioResult = [];
    $arSelect = array("ID");
    $Filter = array("IBLOCK_ID" => IB_BOOKING_LIST, "%PROPERTY_SURNAME" => $search_fio);
    $res = CIBlockElement::GetList([], $Filter, false, [], $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arSearchFioResult[] = $arFields['ID'];
    }
    if (isset($arFilter["ID"])) {
        $arFilter["ID"] = selectFromArr($arFilter["ID"], $arSearchFioResult);
    } else {
        $arFilter["ID"] = $arSearchFioResult;
    }
    unset($arSelect, $Filter, $res, $ob, $ob, $arFields);
}
if (isset($search_car_id) && !empty($search_car_id)) {
    $arSearchCarIdResult = [];
    $arSelect = array("ID");
    $Filter = array("IBLOCK_ID" => IB_BOOKING_LIST, "%PROPERTY_GUEST_CARS" => $search_car_id);
    $res = CIBlockElement::GetList([], $Filter, false, [], $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arSearchCarIdResult[] = $arFields['ID'];
    }
    if (isset($arFilter["ID"])) {
        $arFilter["ID"] = selectFromArr($arFilter["ID"], $arSearchCarIdResult);
    } else {
        $arFilter["ID"] = $arSearchCarIdResult;
    }
    unset($arSelect, $Filter, $res, $ob, $ob, $arFields);
}
if (isset($filter_code) && !empty($filter_code) && $filter_code != '') {
    if (isset($arFilter["ID"])) {
        $arFilter["ID"] = selectFromArr($arFilter["ID"], $filter_code);
    } else {
        $arFilter["ID"] = $filter_code;
    }
}

$filter_date = $request->get("date");

$arRes = [];
$arElementId = [];
$arElementDates = [];
$arElements = [];

$arSelect = array(
    "ID",
    "NAME",
    "DETAIL_TEXT",
    "PROPERTY_NAME",
    "PROPERTY_ARRIVAL_DATE",
    "PROPERTY_DEPARTURE_DATE",
    "PROPERTY_CHECK_IN_TIME",
    "PROPERTY_DEPARTURE_TIME",
    "PROPERTY_PERMISSION",
    "PROPERTY_ADULTS",
    "PROPERTY_BENIFICIARIES",
    "PROPERTY_KIDS",
    "PROPERTY_EMAIL",
    "PROPERTY_SURNAME",
    "PROPERTY_PHONE",
    "PROPERTY_BOOKING_OBJECT",
    "PROPERTY_BOOKING_TYPE",
    "PROPERTY_HLBLOCK_ORDER_ID"
);

/*if (isset($filter_date) && $filter_date != '') {
    $rsIBlockElements = CIBlockElement::GetList(['ID' => 'DESC'], $arFilter, false, [], $arSelect);
    $arResult["NAV_STRING"] = '';
} else {
    $rsIBlockElements = CIBlockElement::GetList(['ID' => 'DESC'], $arFilter, false, [], $arSelect);
    $arResult["ELEMENTS_COUNT"] = $rsIBlockElements->SelectedRowsCount();
    $arParams["NAV_ON_PAGE"] = intval($arParams["NAV_ON_PAGE"]);
    $arParams["NAV_ON_PAGE"] = $arParams["NAV_ON_PAGE"] > 0 ? $arParams["NAV_ON_PAGE"] : 10;
    $rsIBlockElements->NavStart($arParams["NAV_ON_PAGE"]);
    if ($arParams["NAV_ON_PAGE"] < $arResult["ELEMENTS_COUNT"]) {
        $arResult["NAV_STRING"] = $rsIBlockElements->GetPageNavString(GetMessage("IBLOCK_LIST_PAGES_TITLE"), "", true);
    }
}*/

$rsIBlockElements = CIBlockElement::GetList(['ID' => 'DESC'], $arFilter, false, [], $arSelect);
$arResult["NAV_STRING"] = '';

while ($arElement = $rsIBlockElements->NavNext(false)) {
    $arElement = htmlspecialcharsex($arElement);
    $PREVIOUS_ID = $arElement['ID'];
    $LAST_ID = CIBlockElement::WF_GetLast($arElement['ID']);
    if ($LAST_ID != $arElement["ID"]) {
        $rsElement = CIBlockElement::GetByID($LAST_ID);
        $arElement = $rsElement->GetNext();
    }
    $arElement["ID"] = $PREVIOUS_ID;
    if (isset($filter_date) && !empty($filter_date)) {
        if (isset($arElement['PROPERTY_DEPARTURE_DATE_VALUE']) && $arElement['PROPERTY_DEPARTURE_DATE_VALUE'] != '') {
            if (DateTime::createFromFormat('d.m.Y', $arElement['PROPERTY_ARRIVAL_DATE_VALUE'])->format('d.m.Y') ==
                DateTime::createFromFormat('d.m.Y', $filter_date)->format('d.m.Y') ||
                DateTime::createFromFormat('d.m.Y', $arElement['PROPERTY_DEPARTURE_DATE_VALUE'])->format('d.m.Y') ==
                DateTime::createFromFormat('d.m.Y', $filter_date)->format('d.m.Y')) {
                $arRes[$arElement['ID']] = $arElement;
            }
        }
    } else {
        $arRes[$arElement['ID']] = $arElement;
    }
}

foreach ($arRes as &$item) {
    //$rsIBlockElements = CIBlockElement::GetByID($item["PROPERTY_BOOKING_OBJECT_VALUE"]);
    $rsIBlockElements = CIBlockElement::GetList([], ['IBLOCK_ID' => IB_LOCATIONS, 'ID' => $item["PROPERTY_BOOKING_OBJECT_VALUE"]], false, [], ["ID", "NAME", "PROPERTY_LOCATION", "PROPERTY_TIME_UNLIMIT_OBJECT"]);
    if ($ar_res = $rsIBlockElements->GetNext()) {
        $item["OBJECT"] = [
            "ID" => $ar_res["ID"],
            "NAME" => $ar_res["NAME"],
            "LOCATION" => $ar_res["PROPERTY_LOCATION_VALUE"],
        ];
        if (isset($ar_res['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'])) {
            if ($ar_res['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'] == 'Да') {
                $item["OBJECT"]['TIME_LIMIT'] = 'Y';
            } else {
                $item["OBJECT"]['TIME_LIMIT'] = 'N';
            }
        } else {
            $item["OBJECT"]['TIME_LIMIT'] = 'N';
        }
        $arElementId[] = $ar_res['ID'];
        $arElementDates[$ar_res['ID']][] = [
            'ORDER_ID' => $item['ID'],
            'ID' => $ar_res['ID'],
            'ARRIVAL_DATE' => $item['PROPERTY_ARRIVAL_DATE_VALUE'] . ' ' . $item['PROPERTY_CHECK_IN_TIME_VALUE'],
            'DEPARTURE_DATE' => $item['PROPERTY_DEPARTURE_DATE_VALUE'] . ' ' . $item['PROPERTY_DEPARTURE_TIME_VALUE'],
            'TIME_LIMIT' => $item["OBJECT"]['TIME_LIMIT'],
            'HLBLOCK_ORDER_ID' => $item["PROPERTY_HLBLOCK_ORDER_ID_VALUE"],
        ];
    }
}

$hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();
$now = date('d.m.Y H:i:s');
$data = $entity_data_class::getList(array(
    "select" => array("*"),
    "order" => array("ID" => "DESC"),
    "filter" => array("UF_OBJECT_ID" => $arElementId)
));
while ($arData = $data->Fetch()) {
    foreach ($arElementDates[$arData['UF_OBJECT_ID']] as &$order_object_data) {
        if ($order_object_data['HLBLOCK_ORDER_ID'] == $arData['ID']) {
            $order_object_data['RENT_STATUS'] = true;
        }
    }
}
foreach ($arRes as &$element) {
    foreach ($arElementDates[$element['OBJECT']['ID']] as $object_data) {
        if (isset($object_data['RENT_STATUS'])) {
            if ($object_data['ORDER_ID'] == $element['ID']) {
                $element['OBJECT']['RENT_STATUS'] = $object_data['RENT_STATUS'];
            }
        }
    }
}

foreach ($arRes as &$element) {

    $arElements[$element['ID']] = [
        "ID" => $element['ID'],
        "NAME" => $element['NAME'],
        "USER_FIO" => $element['PROPERTY_NAME_VALUE'] . ' ' . $element['PROPERTY_SURNAME_VALUE'],
        "BOOKING_TYPE" => $element['PROPERTY_BOOKING_TYPE_VALUE'],
        "ARRIVAL_DATE" => $element['PROPERTY_ARRIVAL_DATE_VALUE'],
        "DEPARTURE_DATE" => $element['PROPERTY_DEPARTURE_DATE_VALUE'],
        "CHECK_IN_TIME" => $element['PROPERTY_CHECK_IN_TIME_VALUE'],
        "DEPARTURE_TIME" => $element['PROPERTY_DEPARTURE_TIME_VALUE'],
        "HLBLOCK_ORDER_ID" => $element['PROPERTY_HLBLOCK_ORDER_ID_VALUE'],
        "GROUP" => [
            "ADULTS_COUNT" => $element['PROPERTY_ADULTS_VALUE'],
            "BENIFICIARIES_COUNT" => $element['PROPERTY_BENIFICIARIES_VALUE'],
        ],
        "OBJECT" => $element['OBJECT'],
    ];

}
unset($arRes, $element);
$currentDateTime = new DateTime();
$currentDate = $currentDateTime->format("d.m.Y");
$currentTime = $currentDateTime->format("H:i:s");
foreach ($arElements as &$element) {
    if ($element['DEPARTURE_DATE'] == $currentDate) {
        if (getTimeDiff($element['DEPARTURE_TIME'], $currentTime) <= 2) {
            $element['RED'] = true;
        }
    }
}

if (isset($arOrders)) {
    $arUserOrders = [];
    $arSelect = array("ID");
    $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, '=PROPERTY_BOOKING_OBJECT' => json_decode($arOrders));
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arUserOrders[] = $arFields['ID'];
    }
}

if (isset($arLocations) && !empty($arLocations)) {
    $arLocationElements = [];
    $arSelect = array("ID");
    $res = CIBlockElement::GetList(array(), ['IBLOCK_ID' => IB_LOCATIONS, 'PROPERTY_LOCATION' => $arLocations], false, array(), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arLocationElements[] = $arFields['ID'];
    }
    foreach ($arElements as $id => $_element) {
        if (!in_array($_element['OBJECT']['ID'], $arLocationElements)) {
            if (isset($arOrders) && isset($arUserOrders)) {
                if (!in_array($id, $arUserOrders)) {
                    unset($arElements[$id]);
                }
            } else {
                unset($arElements[$id]);
            }
        }
    }
    unset($arLocationElements, $arSelect, $res, $ob, $arFields, $_element, $id);
} else {

//фильтруем заказы по локации пользователя
    $filter['IBLOCK_ID'] = IB_LOCATIONS;
    $arLocationElements = [];
    if ($user['IS_OPERATOR'] || $user['IS_RESERV'] || $user['IS_ADMIN']) {
        if (isset($user['USER_LOCATIONS']) && !empty($user['USER_LOCATIONS'])) {
            $arLocationsId = [];
            foreach ($user['USER_LOCATIONS'] as $userLocation) {
                $arLocationsId[] = $userLocation['ID'];
            }
            if (!empty($arLocationsId)) {
                $filter['=PROPERTY_LOCATION'] = $arLocationsId;
            }
        }
    }
    $arSelect = array("ID");
    $res = CIBlockElement::GetList(array(), $filter, false, array(), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arLocationElements[] = $arFields['ID'];
    }
    foreach ($arElements as $id => $_element) {
        if (!in_array($_element['OBJECT']['ID'], $arLocationElements)) {
            if (isset($arOrders) && isset($arUserOrders)) {
                if (!in_array($id, $arUserOrders)) {
                    unset($arElements[$id]);
                }
            } else {
                unset($arElements[$id]);
            }
        }
    }
}
foreach ($arElements as &$element) {
    if (is_numeric(strpos($element['NAME'], 'Быстрая'))) {
        $element['IS_FAST'] = true;
    }
    $object_location = CIBlockElement::GetByID($element['OBJECT']['LOCATION']);
    if ($location = $object_location->GetNext()) {
        $element['OBJECT']['LOCATION'] = htmlspecialchars_decode($location['NAME']);
    }
}

//$arResult['ELEMENTS'] = $arElements;

$arResult['ELEMENTS'] = [];
$rs_ObjectList = new CDBResult;
$rs_ObjectList->InitFromArray($arElements);
$rs_ObjectList->NavStart(10, false);
$arResult["NAV_STRING"] = $rs_ObjectList->GetPageNavString("", '');
$arResult["PAGE_START"] = $rs_ObjectList->SelectedRowsCount() - ($rs_ObjectList->NavPageNomer - 1) * $rs_ObjectList->NavPageSize;
while ($ar_Field = $rs_ObjectList->Fetch()) {

    $arResult['ELEMENTS'][] = $ar_Field;
}

//для каждого заказа достаем значение свойства GUEST_CARS (Автомобили постояльцев)
foreach ($arResult['ELEMENTS'] as &$order) {
    $VALUES = array();
    $res = CIBlockElement::GetProperty(IB_BOOKING_LIST, $order['ID'], "sort", "asc", array("CODE" => "GUEST_CARS"));
    while ($ob = $res->GetNext()) {
        $VALUES[] = $ob['VALUE'];
    }
    if (!empty($VALUES)) {
        $order['ORDER_TRANSPORT'] = $VALUES;
    }
}