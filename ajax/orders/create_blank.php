<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$values = $request->getPostList();

Loader::includeModule("iblock");

$arProperties = $values['PROPERTY']; //свойства заказа
$arBlankFields = [];//свойства бланка
$arBufer = [];
if (isset($values['order_id'])) {
    $arSelect = array(
        "ID",
        "NAME",
        "PROPERTY_ARRIVAL_DATE",
        "PROPERTY_DEPARTURE_DATE",
        "PROPERTY_QR_CODE",
        "PROPERTY_OBJECT_RENT_COST",
    );
    $arFilter = array(
        "IBLOCK_ID" => IB_BOOKING_LIST,
        "ID" => $values['order_id'],
    );
    $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arBufer[$arFields["ID"]] = $arFields;
    }
}

foreach ($arBufer as $order) {
    if ($order['PROPERTY_ARRIVAL_DATE_VALUE'] == $arProperties[11][0]["VALUE"] &&
        $order['PROPERTY_DEPARTURE_DATE_VALUE'] == $arProperties[12][0]["VALUE"]) {
        $arBlankFields['QRCODE'] = CFile::GetPath($order["PROPERTY_QR_CODE_VALUE"]);
        $arBlankFields['OBJECT_COST'] = $order['PROPERTY_OBJECT_RENT_COST_VALUE'];
    }
}

//достаем свойства объекта оп его id
$arSelect = array("ID", "NAME", "IBLOCK_SECTION_ID", "PROPERTY_OBJECT_COST", "PROPERTY_COST_PER_PERSON", "PROPERTY_CAPACITY_MAXIMUM", "PROPERTY_CAPACITY_ESTIMATED", "PROPERTY_LOCATION");
$arFilter = array("IBLOCK_ID" => IB_OBJECT, "ID" => $arProperties[21][0]['VALUE']);
$res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
if ($ar_res = $res->GetNextElement()) {
    $arFields = $ar_res->GetFields();
    $arBlankFields['OBJECT_NAME'] = $arFields['NAME'];
    //$arBlankFields['OBJECT_COST'] = $arFields['PROPERTY_OBJECT_COST_VALUE'];

    $arBufer['OBJECT_IBLOCK_SECTION_ID'] = $arFields['IBLOCK_SECTION_ID'];
    $arBufer['OBJECT_COST_PER_PERSON'] = $arFields['PROPERTY_COST_PER_PERSON_VALUE'];
    $arBufer['OBJECT_CAPACITY_MAXIMUM'] = $arFields['PROPERTY_CAPACITY_MAXIMUM_VALUE'];
    $arBufer['OBJECT_CAPACITY_ESTIMATED'] = $arFields['PROPERTY_CAPACITY_ESTIMATED_VALUE'];
    $arBufer['OBJECT_LOCATION'] = $arFields['PROPERTY_LOCATION_VALUE'];
}

//достаем свойство раздела
$arFilter = array('IBLOCK_ID' => IB_OBJECT, "ID" => $arBufer['OBJECT_IBLOCK_SECTION_ID']);
$db_list = CIBlockSection::GetList([], $arFilter, true, ["ID", "NAME"]);
while ($ar_result = $db_list->GetNext()) {
    $arBlankFields['OBJECT_CATEGORY'] = $ar_result['NAME'];
}

$res = CIBlockElement::GetByID($arBufer['OBJECT_LOCATION']);
if ($ar_res = $res->GetNext()) {
    $arBlankFields['OBJECT_LOCATION'] = $ar_res['NAME'];
}

//записываем свойства бланка
$arBufer['PERMISSION'] = $arProperties[15] ? $arProperties[15] : "2";
$arBlankFields['RENTAL_DATE'] = $arProperties[11][0]['VALUE'] ? $arProperties[11][0]['VALUE'] : "";
$arBlankFields['CHECK_IN_TIME'] = $arProperties[13][0] ? $arProperties[13][0] : "";
$arBlankFields['DEPARTURE_DATE'] = $arProperties[12][0]['VALUE'] ? $arProperties[12][0]['VALUE'] : "";
$arBlankFields['DEPARTURE_TIME'] = $arProperties[14][0] ? $arProperties[14][0] : "";
if (isset($arProperties[9][0]) && isset($arProperties[10][0])) {
    $arBlankFields['TENANT'] = $arProperties[9][0] . ' ' . $arProperties[10][0];
} else {
    $arBlankFields['TENANT'] = '';
}
$arBlankFields['ADULTS'] = $arProperties[16][0] ? $arProperties[16][0] : "";
$arBlankFields['BENIFICIARIES'] = $arProperties[17][0] ? $arProperties[17][0] : "";
/*$arBlankFields['KIDS'] = $arProperties[18][0] ? $arProperties[18][0] : "";*/
$arBlankFields['ORDER_ID'] = $values['order_id'] ? $values['order_id'] : "";
$arBlankFields['MANAGER'] = $arProperties[22][0] != 'Онлайн' ? $arProperties[22][0] : "";
$arBlankFields['COST'] = $arProperties[32][0] ? $arProperties[32][0] : "0";

if ($arBufer['PERMISSION'] == 2) {
    //льготники не платят за разрешение
    $arBufer['PERMISSION_COUNT'] = $arBlankFields['ADULTS'] - $arBlankFields['BENIFICIARIES'];
    $arBufer['PERMISSION_COST'] = $arBufer['PERMISSION_COUNT'] * VISIT_PERMISSION_COST;
}

if ($arBlankFields['ADULTS'] > $arBufer['OBJECT_CAPACITY_ESTIMATED']) {
    $arBufer['PERSON_OVER'] = $arBlankFields['ADULTS'] - $arBufer['OBJECT_CAPACITY_ESTIMATED'];
    $arBufer['PERSON_OVER_COST'] = $arBufer['PERSON_OVER'] * $arBufer['OBJECT_COST_PER_PERSON'];
}
if (!empty($arProperties[75][0])) {
    $do = true;
    $i = 0;
    while ($do) {
        if (isset($arProperties[75][$i])) {
            $arBlankFields['GUEST_CARS'][] = $arProperties[75][$i];
            $i++;
        } else {
            $do = false;
        }
    }
}
?>
<table class="blank-table">
    <tr>
        <th>Наименование объекта</th>
        <td><?= $arBlankFields['OBJECT_NAME'] ?></td>
    </tr>
    <tr>
        <th>Категория</th>
        <td><?= $arBlankFields['OBJECT_CATEGORY'] ?></td>
    </tr>
    <tr>
        <th>Локация</th>
        <td><?= $arBlankFields['OBJECT_LOCATION'] ?></td>
    </tr>
    <tr>
        <th>Дата и время заезда</th>
        <td><?= $arBlankFields['RENTAL_DATE'] . ' ' . $arBlankFields['CHECK_IN_TIME'] ?></td>
    </tr>
    <tr>
        <th>Дата и время выезда</th>
        <td><?= $arBlankFields['DEPARTURE_DATE'] . ' ' . $arBlankFields['DEPARTURE_TIME'] ?></td>
    </tr>
    <? if ($arBlankFields['TENANT'] != ''): ?>
        <tr>
            <th>ФИО арендатора</th>
            <td><?= $arBlankFields['TENANT'] ?></td>
        </tr>
    <? endif; ?>
    <tr>
        <th>Состав</th>
        <td>
            <?= $arBlankFields['ADULTS'] > 0 ? "Общее число: " . $arBlankFields['ADULTS'] . "," : "" ?>
            <?= $arBlankFields['BENIFICIARIES'] > 0 ? "из них льготников: " . $arBlankFields['BENIFICIARIES'] : "" ?>
        </td>
    </tr>
    <tr>
        <th>Уникальный номер</th>
        <td id="order-id"><?= $arBlankFields['ORDER_ID'] ?></td>
    </tr>
    <tr>
        <th>Тип бронирования</th>
        <td><?= $arBlankFields['MANAGER'] ?></td>
    </tr>
    <? if ($arBufer['PERMISSION'] == 1): ?>
        <tr>
            <th>Разрешение</th>
            <td>Есть</td>
        </tr>
    <? endif; ?>
    <? if (!empty($arBlankFields['GUEST_CARS'])): ?>
        <? foreach ($arBlankFields['GUEST_CARS'] as $guest_car): ?>
            <tr>
                <th>Номер автомобиля</th>
                <td><?= $guest_car ?></td>
            </tr>
        <? endforeach; ?>
    <? endif; ?>
    <tr>
        <th>Стоимость</th>
        <td></td>
    </tr>
    <tr>
        <td colspan="2" class="td-w-padding">
            <table>
                <tr>
                    <th>Стоимость аренды</th>
                    <td><?= $arBlankFields['OBJECT_COST'] ?> ₽</td>
                </tr>
                <? if ($arBufer['PERMISSION'] == 2 && $arBufer['PERMISSION_COST']): ?>
                    <tr>
                        <th>Разрешение на посещение</th>
                        <td><?= $arBufer['PERMISSION_COUNT'] ?> чел - <?= $arBufer['PERMISSION_COST'] ?> ₽</td>
                    </tr>
                <? else: ?>
                    <tr>
                        <th>Разрешение на посещение</th>
                        <td>Имеется</td>
                    </tr>
                <? endif; ?>
                <? if ($arBufer['PERSON_OVER'] && $arBufer['PERSON_OVER_COST']): ?>
                    <tr>
                        <th>Дополнительные места</th>
                        <td><?= $arBufer['PERSON_OVER'] ?> чел - <?= $arBufer['PERSON_OVER_COST'] ?> ₽</td>
                    </tr>
                <? endif; ?>
                <tr>
                    <th>Итого</th>
                    <td><?= $arBlankFields['COST'] ?> ₽</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="blank_right">
    <img src="<?= ASSETS ?>images/f-logo.svg" alt="" class="blank_logo">
    <? if ($arBlankFields['QRCODE']): ?>
        <div class="qr">
            <img src="<?= $arBlankFields['QRCODE'] ?>" alt="">
        </div>
    <? endif; ?>
</div>
