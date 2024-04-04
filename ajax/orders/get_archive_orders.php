<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader,
    Bitrix\Main\Entity;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$values = $request->getPostList();

$order_id = $request->get("order_id");
$order_date = $request->get("order_date");

Loader::includeModule("iblock");
Loader::includeModule("highloadblock");


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
);
$arFilter = array("IBLOCK_ID" => IB_ORDERS_ARCHIVE, "ID" => $order_id);


$res = CIBlockElement::GetList(['ID' => 'DESC'], $arFilter, false, [], $arSelect);
$arResult["ELEMENTS_COUNT"] = $res->SelectedRowsCount();
$arParams["NAV_ON_PAGE"] = $arParams["NAV_ON_PAGE"] > 0 ? $arParams["NAV_ON_PAGE"] : 10;
$res->NavStart($arParams["NAV_ON_PAGE"]);
if ($arParams["NAV_ON_PAGE"] < $arResult["ELEMENTS_COUNT"]) {
    $arResult["NAV_STRING"] = $res->GetPageNavString(GetMessage("IBLOCK_LIST_PAGES_TITLE"), "", true);
}

while ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();

    $PREVIOUS_ID = $arFields['ID'];
    $LAST_ID = CIBlockElement::WF_GetLast($arFields['ID']);

    if ($LAST_ID != $arFields["ID"]) {
        $rsElement = CIBlockElement::GetByID($LAST_ID);
        $arFields = $rsElement->GetNext();
    }

    $arFields["ID"] = $PREVIOUS_ID;

    if (isset($order_date) && !empty($order_date)) {
        if ($arFields['PROPERTY_ARRIVAL_DATE_VALUE'] == $order_date) {
            $arRes[$arFields['ID']] = $arFields;
        }
    } else {
        $arRes[$arFields['ID']] = $arFields;
    }
}

foreach ($arRes as &$item) {

    $res = CIBlockElement::GetByID($item["PROPERTY_BOOKING_OBJECT_VALUE"]);

    if ($ar_res = $res->GetNext()) {
        $item["OBJECT"] = [
            "ID" => $ar_res["ID"],
            "NAME" => $ar_res["NAME"],
        ];
        $arElementId[] = $ar_res['ID'];
        $arElementDates[$ar_res['ID']] = [
            'ORDER_ID' => $item['ID'],
            'ID' => $ar_res['ID'],
            'ARRIVAL_DATE' => $item['PROPERTY_ARRIVAL_DATE_VALUE'] . ' ' . $item['PROPERTY_CHECK_IN_TIME_VALUE'],
            'DEPARTURE_DATE' => $item['PROPERTY_DEPARTURE_DATE_VALUE'] . ' ' . $item['PROPERTY_DEPARTURE_TIME_VALUE'],
        ];
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
        "GROUP" => [
            "ADULTS_COUNT" => $element['PROPERTY_ADULTS_VALUE'],
            "BENIFICIARIES_COUNT" => $element['PROPERTY_BENIFICIARIES_VALUE'],
        ],
        "OBJECT" => $element['OBJECT'],
    ];

}
unset($arRes, $element);

$arResult['ELEMENTS'] = $arElements;

$user = getUserData();
?>
<? if (!empty($arResult['ELEMENTS'])): ?>
    <table class="table">
        <tr>
            <th>Код</th>
            <th>Название объекта</th>
            <th>Кто забронировал</th>
            <th>Даты брони</th>
            <th>Время заезда</th>
            <th>Состав группы</th>
            <th>Дата и время выезда</th>
            <th></th>
        </tr>
        <? foreach ($arResult['ELEMENTS'] as $element): ?>
            <? if (isset($element['RED']) && $element['RED'] == 'true'): ?>
                <tr class="tr-red">
            <? else: ?>
                <tr>
            <? endif; ?>
            <td><?= $element['ID'] ?></td>
            <td><?= $element['OBJECT']['NAME'] ?></td>
            <td><?= $element['USER_FIO'] ?>
                <? if ($element['BOOKING_TYPE'] == 'Онлайн'): ?>
                    <div class="online"><?= $element['BOOKING_TYPE'] ?></div>
                <? else: ?>
                    <div class="operator">
                        <span class="operator_title">оператор</span>
                        <span class="operator_value"><?= $element['BOOKING_TYPE'] ?></span>
                    </div>
                <? endif; ?>
            </td>
            <td>с <?= $element['ARRIVAL_DATE'] ?><br>
                по <?= $element['DEPARTURE_DATE'] ?>
            </td>
            <td><?= $element['CHECK_IN_TIME'] ?></td>
            <td>
                всего - <?= $element['GROUP']['ADULTS_COUNT'] ?> чел <br>
                <? if ($element['GROUP']['BENIFICIARIES_COUNT'] > 0): ?>
                    льготников - <?= $element['GROUP']['BENIFICIARIES_COUNT'] ?> чел
                <? endif; ?>
            </td>
            <td><?= $element['DEPARTURE_DATE'] ?> <br>
                <?= $element['DEPARTURE_TIME'] ?>
            </td>
            <? if ($user['IS_ADMIN']): ?>
                <td>
                    <a class="btn-remove"
                       href="?delete=Y&amp;CODE=<?= $element["ID"] ?>&amp;<?= bitrix_sessid_get() ?>"
                       onClick="return confirm('<? echo CUtil::JSEscape(str_replace("#ELEMENT_NAME#", $element["NAME"], GetMessage("IBLOCK_ADD_LIST_DELETE_CONFIRM"))) ?>')"
                    >
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5.79167 17.083C5.47222 17.083 5.19444 16.965 4.95833 16.7288C4.72222 16.4927 4.60417 16.208 4.60417 15.8747V4.54134H3.75V3.60384H7.3125V3.02051H12.6875V3.60384H16.25V4.54134H15.3958V15.8747C15.3958 16.208 15.2778 16.4927 15.0417 16.7288C14.8056 16.965 14.5278 17.083 14.2083 17.083H5.79167ZM7.9375 14.3747H8.89583V6.29134H7.9375V14.3747ZM11.1042 14.3747H12.0625V6.29134H11.1042V14.3747Z"
                                    fill="#ED8C00"/>
                        </svg>
                    </a>
                </td>
            <? else: ?>
                <td></td>
            <? endif; ?>
            </tr>
        <? endforeach; ?>
    </table>
<? else: ?>
    <p>Архив пуст</p>
<? endif; ?>
