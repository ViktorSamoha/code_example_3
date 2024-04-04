<?php

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;

//гент, который проверяет статус оплаты
function checkOrdersAgent()
{
    CModule::IncludeModule('iblock');

    $minutes = new DateTime("15 minutes ago");
    $time_limit = $minutes->format("d.m.Y H:i:s");

    $res = CIBlockElement::GetList(['ID' => 'DESC'], ['IBLOCK_ID' => IB_BOOKING_LIST, '!PROPERTY_PAYMENT_VALUE' => false, 'PROPERTY_BOOKING_TYPE' => 'Онлайн', 'PROPERTY_IS_PAYED' => false, '<=DATE_ACTIVE_FROM' => $time_limit], false, ["nTopCount" => 5], ['ID', 'DATE_CREATE', 'PROPERTY_IS_PAYED', 'PROPERTY_PAYMENT', 'PROPERTY_DATE_PAY', 'PROPERTY_UNIQUE_ORDER_CODE']);
    $arIdOrder = [];
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arIdOrder[$arFields['ID']] = $arFields;
    }
    if (!empty($arIdOrder)) {
        foreach ($arIdOrder as $idOrder => $orderInfo) {
            if (isset($orderInfo['PROPERTY_PAYMENT_VALUE']) && !empty($orderInfo['PROPERTY_PAYMENT_VALUE'])) {
                $isPayid = Payment::checkPayment($orderInfo);
                if ($isPayid) {
                    CIBlockElement::SetPropertyValuesEx($idOrder, false, array('IS_PAYED' => ['VALUE' => 11], 'DATE_PAY' => $isPayid));

                    $order_props = getOrderDate($idOrder);

                    if ($_SERVER['HTTP_HOST']) {
                        $site_url = $_SERVER['HTTP_HOST'];
                    } else {
                        $site_url = '';
                    }
                    $order_link = "https://" . $site_url . "/receipt/order/" . $order_props['PROPERTY_VALUES'][33];

                    //отправка письма на почту пользователю
                    if (!empty($order_props['PROPERTY_VALUES'][19])) {
                        onBookingSendEmail($order_props);
                    }
                    //отправка сообщения в тг канал для администратора
                    if (!empty($order_link)) {
                        onBookingSendQRToTelegram($order_link, $order_props);
                    }
                    if (!empty($order_props['PROPERTY_VALUES'][21]["VALUE"])) {
                        partnerNotification($order_props);
                    }
                    addDataToStatTable($order_props);
                } else {
                    deleteObjectDataFromHLBlock($idOrder);
                    global $DB;
                    $DB->StartTransaction();
                    if (!CIBlockElement::Delete($idOrder)) {
                        $strWarning .= 'Error!';
                        $DB->Rollback();
                    } else
                        $DB->Commit();
                }
            }
        }
    }

    return 'checkOrdersAgent();';
}

//агент, который проверяет заказы и меняет статус объекта на "свободный" если время выезда уже прошло
/*function checkOrderObjectRentStatusAgent()
{
    Loader::includeModule("highloadblock");

    $currentDateTime = new DateTime();
    $currentDate = $currentDateTime->format("d.m.Y");

    $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    $now = new DateTime();

    $data = $entity_data_class::getList(array(
        "select" => array("*"),
        "order" => array("ID" => "DESC"),
        "filter" => array(">UF_ARRIVAL_DATE" => $now->format('d.m.Y H:i:s'))
    ));

    while ($arData = $data->Fetch()) {
        if (DateTime::createFromFormat('d.m.Y', $arData['UF_DEPARTURE_DATE'])->format('d.m.Y') < DateTime::createFromFormat('d.m.Y', $currentDate)->format('d.m.Y')) {
            $entity_data_class::Delete($arData['ID']);
        }
    }

    return "checkOrderObjectRentStatusAgent();";
}*/

function sendOrderToArchiveAgent()
{
    Loader::includeModule("highloadblock");
    Loader::includeModule("iblock");

    global $USER;
    global $DB;

    $now = new DateTime();
    $currentDateTime = new DateTime();
    $currentDate = $currentDateTime->format("d.m.Y H:i:s");

    //Достаем id заказов из HL блока у которых дата выезда < текущей
    $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

    $data = $entity_data_class::getList(array(
        "select" => array("*"),
        "order" => array("ID" => "DESC"),
        "filter" => array("<UF_DEPARTURE_DATE" => $now->format('d.m.Y H:i:s'))
    ));

    $arArchiveOrders = [];

    while ($arData = $data->Fetch()) {
        if (strtotime($arData['UF_DEPARTURE_DATE']->format("d.m.Y H:i:s")) < strtotime($currentDate)) {
            $arArchiveOrders[] = $arData['ID'];
        }
    }

    //Достаем из инфоблока заказы по полю PROPERTY_HLBLOCK_ORDER_ID
    $arSelect = array("ID");
    $arOrders = [];
    foreach ($arArchiveOrders as $hlb_record_id) {
        $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, "PROPERTY_HLBLOCK_ORDER_ID" => $hlb_record_id);
        $res = CIBlockElement::GetList(array(), $arFilter, false, array("nTopCount" => 1), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arOrders[] = $arFields['ID'];
        }
    }

    unset($arSelect, $arFilter, $res, $ob);

    if (!empty($arOrders)) {
        //достаем поля нужных нам заказов
        $arSelect = array(
            "ID",
            "NAME",
            "PROPERTY_NAME",
            "PROPERTY_ARRIVAL_DATE",
            "PROPERTY_DEPARTURE_DATE",
            "PROPERTY_CHECK_IN_TIME",
            "PROPERTY_DEPARTURE_TIME",
            "PROPERTY_PERMISSION",
            "PROPERTY_ADULTS",
            "PROPERTY_BENIFICIARIES",
            "PROPERTY_EMAIL",
            "PROPERTY_SURNAME",
            "PROPERTY_PHONE",
            "PROPERTY_BOOKING_OBJECT",
            "PROPERTY_BOOKING_TYPE",
            "PROPERTY_BOOKING_COST",
            "PROPERTY_OBJECT_RENT_COST",
            "PROPERTY_VISIT_PERMISSION_COST",
            "PROPERTY_HLBLOCK_ORDER_ID",
            "PROPERTY_QR_CODE",
            "PROPERTY_IS_PAYED",
            "PROPERTY_DATE_PAY",
            "PROPERTY_PAYMENT",
            "PROPERTY_UNIQUE_ORDER_CODE",
        );
        $result = [];
        $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, "ID" => $arOrders);
        $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $result[$arFields['ID']] = $arFields;
            $result[$arFields['ID']]['NAME'] = 'Архивный заказ №' . $arFields['ID'] . ' ' . $arFields['NAME'];
        }

        foreach ($result as $arElement) {

            //формируем элементы для переноса в архив
            $el = new CIBlockElement;

            $PROP = [
                45 => $arElement['PROPERTY_NAME_VALUE'],
                46 => $arElement['PROPERTY_ARRIVAL_DATE_VALUE'],
                47 => $arElement['PROPERTY_DEPARTURE_DATE_VALUE'],
                48 => $arElement['PROPERTY_CHECK_IN_TIME_VALUE'],
                49 => $arElement['PROPERTY_DEPARTURE_TIME_VALUE'],
                50 => $arElement['PROPERTY_PERMISSION_VALUE'],
                51 => $arElement['PROPERTY_ADULTS_VALUE'],
                52 => $arElement['PROPERTY_BENIFICIARIES_VALUE'],
                53 => $arElement['PROPERTY_EMAIL_VALUE'],
                54 => $arElement['PROPERTY_SURNAME_VALUE'],
                55 => $arElement['PROPERTY_PHONE_VALUE'],
                56 => $arElement['PROPERTY_BOOKING_OBJECT_VALUE'],
                57 => $arElement['PROPERTY_BOOKING_TYPE_VALUE'],
                58 => $arElement['PROPERTY_BOOKING_COST_VALUE'],
                59 => $arElement['PROPERTY_OBJECT_RENT_COST_VALUE'],
                60 => $arElement['PROPERTY_VISIT_PERMISSION_COST_VALUE'],
                63 => $arElement['PROPERTY_IS_PAYED_VALUE'],
                64 => $arElement['PROPERTY_DATE_PAY_VALUE'],
                65 => $arElement['PROPERTY_PAYMENT_VALUE'],
            ];

            $arLoadProductArray = array(
                "MODIFIED_BY" => $USER->GetID(),
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID" => IB_ORDERS_ARCHIVE,
                "PROPERTY_VALUES" => $PROP,
                "NAME" => $arElement['NAME'],
                "ACTIVE" => "Y",
            );

            //добавляем элементы в архив
            if ($ARCHIVE_RECORD_ID = $el->Add($arLoadProductArray)) {
                $DB->StartTransaction();
                //если элемент успешно добавился, удаляем его из заказов
                if (!CFile::Delete($arElement["PROPERTY_QR_CODE_VALUE"])) {
                    $errMsg = 'Ошибка удаления QR-кода id=' . $arElement["PROPERTY_QR_CODE_VALUE"] . ' time= ' . $now->format('d.m.Y H:i:s');
                    \Bitrix\Main\Diag\Debug::dumpToFile($errMsg, $varName = 'sendOrderToArchiveAgent', $fileName = 'agents_error_log.txt');
                }
                if (!CIBlockElement::Delete($arElement['ID'])) {
                    $errMsg = 'Ошибка удаления элемента id=' . $arElement["ID"] . ' time= ' . $now->format('d.m.Y H:i:s');
                    \Bitrix\Main\Diag\Debug::dumpToFile($errMsg, $varName = 'sendOrderToArchiveAgent', $fileName = 'agents_error_log.txt');
                    $DB->Rollback();
                } else {
                    $DB->Commit();
                }
                if (!$entity_data_class::Delete($arElement['PROPERTY_HLBLOCK_ORDER_ID_VALUE'])) {
                    $errMsg = 'Ошибка удаления элемента из hl блока id=' . $arElement["PROPERTY_HLBLOCK_ORDER_ID_VALUE"] . ' time= ' . $now->format('d.m.Y H:i:s');
                    \Bitrix\Main\Diag\Debug::dumpToFile($errMsg, $varName = 'sendOrderToArchiveAgent', $fileName = 'agents_error_log.txt');
                }
            }
        }
    }

    return "sendOrderToArchiveAgent();";
}
