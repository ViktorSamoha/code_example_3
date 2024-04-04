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

$filter_code = $request->get("code");
$filter_date = $request->get("date");
if ($request->get("object_id")) {
    $filter_object_id = $request->get("object_id");
}

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
    "PROPERTY_HLBLOCK_ORDER_ID"
);
$arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, "ID" => $filter_code);
$res = CIBlockElement::GetList(['ID' => 'DESC'], $arFilter, false, [], $arSelect);
while ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();
    if (isset($filter_date) && !empty($filter_date)) {
        if ($arFields['PROPERTY_ARRIVAL_DATE_VALUE'] == $filter_date) {
            $arRes[$arFields['ID']] = $arFields;
        }
    } else {
        $arRes[$arFields['ID']] = $arFields;
    }
}

foreach ($arRes as &$item) {
    if ($filter_object_id) {
        $res = CIBlockElement::GetByID($filter_object_id);
    } else {
        $res = CIBlockElement::GetByID($item["PROPERTY_BOOKING_OBJECT_VALUE"]);
    }
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
            'HLBLOCK_ORDER_ID' => $item["PROPERTY_HLBLOCK_ORDER_ID_VALUE"],
        ];
    }
}

$hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();
$now = new DateTime();
$data = $entity_data_class::getList(array(
    "select" => array("*"),
    "order" => array("ID" => "DESC"),
    "filter" => array("UF_OBJECT_ID" => $arElementId, ">UF_ARRIVAL_DATE" => $now->format('d.m.Y H:i:s'))
));

while ($arData = $data->Fetch()) {
    foreach ($arElementDates[$arData['UF_OBJECT_ID']] as &$order_object_data) {
        if ($order_object_data['HLBLOCK_ORDER_ID'] == $arData['ID']) {
            $order_object_data['RENT_STATUS'] = true;
        }
    }
}
foreach ($arRes as &$element) {
    if (isset($arElementDates[$element['OBJECT']['ID']]['RENT_STATUS'])) {
        if ($arElementDates[$element['OBJECT']['ID']]['ORDER_ID'] == $element['ID']) {
            $element['OBJECT']['RENT_STATUS'] = $arElementDates[$element['OBJECT']['ID']]['RENT_STATUS'];
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

$arResult['ELEMENTS'] = $arElements;

$user = getUserData();
?>
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
        <th></th>
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
        <? if (isset($element['OBJECT']['RENT_STATUS'])): ?>
            <? if ($element['OBJECT']['RENT_STATUS'] === false): ?>
                <td>
                    <span class="available-text">Освободилось</span>
                </td>
            <? else: ?>
                <td>
                    <button class="btn-available js-open-r-modal" data-name="modal-cancel-reservation"
                            data-object-id="<?= $element['OBJECT']['ID'] ?>"
                            data-object-arrival-date="<?= $element['ARRIVAL_DATE'] . ' ' . $element['CHECK_IN_TIME'] ?>"
                            data-object-departure-date="<?= $element['DEPARTURE_DATE'] . ' ' . $element['DEPARTURE_TIME'] ?>"
                    >
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M1.44375 12.9967L0 11.5529L5.05313 6.4998L0 1.44668L1.44375 0.00292969L6.49688 5.05606L11.55 0.00292969L12.9938 1.44668L7.94063 6.4998L12.9938 11.5529L11.55 12.9967L6.49688 7.94356L1.44375 12.9967Z"
                                    fill="#ED8C00"/>
                        </svg>
                        <span>Освободить</span>
                    </button>
                </td>
            <? endif; ?>
        <? else: ?>
            <td>
                <span class="available-text">Освободилось</span>
            </td>
        <? endif; ?>
        <td>
            <a href="<?= $arParams["EDIT_URL"] ?>?edit=Y&amp;CODE=<?= $element["ID"] ?>"
               class="btn-edit">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M1.5 17.9997V15.731H16.5V17.9997H1.5ZM3.0375 13.7435V11.2497L10.0312 4.25599L12.525 6.74974L5.53125 13.7435H3.0375ZM13.35 5.92474L10.8563 3.43099L12.4313 1.85599C12.5688 1.69349 12.725 1.60911 12.9 1.60286C13.075 1.59661 13.25 1.68099 13.425 1.85599L14.8875 3.31849C15.05 3.48099 15.1312 3.65286 15.1312 3.83411C15.1312 4.01536 15.0625 4.18724 14.925 4.34974L13.35 5.92474Z"
                            fill="#ED8C00"/>
                </svg>
                <span>Редактировать</span>
            </a>
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
