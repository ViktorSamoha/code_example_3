<?php

use \Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity,
    Bitrix\Main\Mail\Event;
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\QROutputInterface;

// composer
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php';

require 'kint.phar'; //подключение дебаггера kint

include 'constants.php'; //подключение файла с константами
include 'classes.php'; //подключение файла с классами
include 'payment.php'; //подключение файла с классом оплаты
include 'functions.php'; //подключение файла с функциями
include 'agents.php'; //подключение файла с агентами

//AddEventHandler("iblock", "OnBeforeIBlockElementAdd", "insertObjectDataToHLBlock");
//AddEventHandler("iblock", "OnAfterIBlockElementAdd", "createQRCode");
//AddEventHandler("iblock", "OnBeforeIBlockElementDelete", "deleteObjectDataFromHLBlock");
//AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "updateObjectDataInHLBlock"); не актуально!
//AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "createNewHLBRecord");

//при редактирвоании заказа, добавляем новую запись о бронировании в хл блок и возвращаем ее id
function createNewHLBRecord(&$arFields)
{
    $ORDER_ID = intval($arFields['ID']);
    $OBJECT_ID = intval($arFields['PROPERTY_VALUES'][21]['VALUE']);
    $NEW_AR_DATE = $arFields['PROPERTY_VALUES'][11]['VALUE'] . ' ' . $arFields['PROPERTY_VALUES'][13];
    $NEW_DEP_DATE = $arFields['PROPERTY_VALUES'][12]['VALUE'] . ' ' . $arFields['PROPERTY_VALUES'][14];
    if ($OBJECT_ID) {
        Loader::includeModule("highloadblock");
        $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $data = array(
            "UF_OBJECT_ID" => $OBJECT_ID,
            "UF_ARRIVAL_DATE" => $NEW_AR_DATE,
            "UF_DEPARTURE_DATE" => $NEW_DEP_DATE
        );
        $result = $entity_data_class::add($data);
        if (!$result->isSuccess()) {
            \Bitrix\Main\Diag\Debug::dumpToFile(implode(', ', $result->getErrors()), 'createNewHLBRecord', 'init_error_log.txt');
        } else {
            $NEW_RECORD_ID = $result->getId();
            if ($NEW_RECORD_ID) {
                $arFields['PROPERTY_VALUES'][39] = $NEW_RECORD_ID;
            }
            if (!empty($arFields['PROPERTY_VALUES'][19])) {
                onBookingSendEmail($arFields);
            }
            if ($ORDER_ID) {
                Loader::includeModule("iblock");
                $arSelect = array("ID", "NAME", "ACTIVE", "PROPERTY_UNIQUE_ORDER_CODE");
                $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, "ID" => $ORDER_ID);
                $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arOrder = $ob->GetFields();
                    $order_hash = $arOrder['PROPERTY_UNIQUE_ORDER_CODE_VALUE'];
                    $order_link = "https://" . $_SERVER['HTTP_HOST'] . "/receipt/order/" . $order_hash . '/';
                    if (!empty($order_link)) {
                        onBookingSendQRToTelegram($order_link, $arFields);
                    }
                }
            }
            partnerNotification($arFields);
        }
    }
}

function updateObjectDataInHLBlock(&$arFields)
{
    //\Bitrix\Main\Diag\Debug::dumpToFile($arFields, 'updateObjectDataInHLBlock', 'init_log.txt');

    $ORDER_ID = intval($arFields['ID']);
    $OBJECT_ID = intval($arFields['PROPERTY_VALUES'][21]['VALUE']);
    $HLB_ORDER_ID = intval($arFields['PROPERTY_VALUES'][39]);
    $NEW_AR_DATE = $arFields['PROPERTY_VALUES'][11]['VALUE'] . ' ' . $arFields['PROPERTY_VALUES'][13];
    $NEW_DEP_DATE = $arFields['PROPERTY_VALUES'][12]['VALUE'] . ' ' . $arFields['PROPERTY_VALUES'][14];

    if ($ORDER_ID && $OBJECT_ID) {
        Loader::includeModule("iblock");
        $arSelect = array(
            "ID",
            "NAME",
            "PROPERTY_ARRIVAL_DATE",
            "PROPERTY_DEPARTURE_DATE",
            "PROPERTY_CHECK_IN_TIME",
            "PROPERTY_DEPARTURE_TIME",
        );
        $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, "ID" => $ORDER_ID);
        $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $_arFields = $ob->GetFields();
            $ARDATE = $_arFields['PROPERTY_ARRIVAL_DATE_VALUE'] . ' ' . $_arFields['PROPERTY_CHECK_IN_TIME_VALUE'];
            $DEPDATE = $_arFields['PROPERTY_DEPARTURE_DATE_VALUE'] . ' ' . $_arFields['PROPERTY_DEPARTURE_TIME_VALUE'];
        }
        if ($ARDATE && $DEPDATE) {
            Loader::includeModule("highloadblock");
            $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $arrival_date = DateTime::createFromFormat('d.m.Y H:i:s', \Bitrix\Main\Type\DateTime::createFromUserTime($ARDATE));
            $departure_date = DateTime::createFromFormat('d.m.Y H:i:s', \Bitrix\Main\Type\DateTime::createFromUserTime($DEPDATE));
            $data = $entity_data_class::getList(array(
                "select" => array("*"),
                "order" => array("ID" => "DESC"),
                "filter" => array(
                    "UF_OBJECT_ID" => $OBJECT_ID,
                )));
            while ($arData = $data->Fetch()) {
                $hl_arrival_date = DateTime::createFromFormat('d.m.Y H:i:s', $arData['UF_ARRIVAL_DATE']);
                $hl_departure_date = DateTime::createFromFormat('d.m.Y H:i:s', $arData['UF_DEPARTURE_DATE']);
                if ($hl_arrival_date == $arrival_date && $departure_date == $hl_departure_date) {
                    $data = array(
                        "UF_ARRIVAL_DATE" => $NEW_AR_DATE,
                        "UF_DEPARTURE_DATE" => $NEW_DEP_DATE,
                    );
                    $result = $entity_data_class::update($HLB_ORDER_ID, $data);
                    if (!$result->isSuccess()) {
                        \Bitrix\Main\Diag\Debug::dumpToFile(implode(', ', $result->getErrors()), 'updateObjectDataInHLBlock', 'init_error_log.txt');
                    }
                }
            }
        }
    }
}

//добавление записи в hl блок "Забронированные даты"
function insertObjectDataToHLBlock(&$arFields)
{
    if ($arFields['IBLOCK_ID'] === IB_BOOKING_LIST) {

        //\Bitrix\Main\Diag\Debug::dumpToFile($arFields, '$arFields', 'init_log.txt');

        Loader::includeModule("highloadblock");

        $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();

        if (isset($arFields['TIME_LIMIT']) && $arFields['TIME_LIMIT'] == 'Y') {
            $ARRIVAL_DATE = $arFields['PROPERTY_VALUES'][11]["VALUE"] . ' ' . $arFields['PROPERTY_VALUES'][13];
            $OBJECT_ID = intval($arFields['PROPERTY_VALUES'][21]["VALUE"]);
            $data = array(
                "UF_OBJECT_ID" => $OBJECT_ID,
                "UF_ARRIVAL_DATE" => $ARRIVAL_DATE,
                "UF_DEPARTURE_DATE" => $arFields['PROPERTY_VALUES'][11]["VALUE"] . '23:00:00',
            );
            $arFields['PROPERTY_VALUES'][12]["VALUE"] = $arFields['PROPERTY_VALUES'][11]["VALUE"];
            $arFields['PROPERTY_VALUES'][14] = '22:00';
        } else {
            $ARRIVAL_DATE = $arFields['PROPERTY_VALUES'][11]["VALUE"] . ' ' . $arFields['PROPERTY_VALUES'][13];
            $DEPARTURE_DATE = $arFields['PROPERTY_VALUES'][12]["VALUE"] . ' ' . $arFields['PROPERTY_VALUES'][14];
            $OBJECT_ID = intval($arFields['PROPERTY_VALUES'][21]["VALUE"]);
            $data = array(
                "UF_OBJECT_ID" => $OBJECT_ID,
                "UF_ARRIVAL_DATE" => $ARRIVAL_DATE,
                "UF_DEPARTURE_DATE" => $DEPARTURE_DATE
            );
        }
        $result = $entity_data_class::add($data);
        if (!$result->isSuccess()) {
            \Bitrix\Main\Diag\Debug::dumpToFile(implode(', ', $result->getErrors()), 'OnBeforeIBlockElementAddHandler', 'init_error_log.txt');
        } else {
            $arFields['PROPERTY_VALUES'][39] = $result->getId();
            //если объект без ограничения по времени бронирования, то убираем его активность
            /*if (isset($arFields['TIME_LIMIT']) && $arFields['TIME_LIMIT'] == 'Y') {
                CModule::IncludeModule("iblock");
                $res = CIBlockElement::SetPropertyValuesEx($arFields['PROPERTY_VALUES'][21]["VALUE"], false, array('CAN_BOOK' => 10));
            }*/
        }
    }
}

//функция добавляет уникальный номер(хеш) заказа, генерит куаркод и добавляет к заказу.
function createQRCode(&$arFields)
{
    if ($arFields['IBLOCK_ID'] === IB_BOOKING_LIST) {

        Loader::includeModule("iblock");

        //\Bitrix\Main\Diag\Debug::dumpToFile($arFields, '$arFields', 'init_log.txt');

        $timestamp = time();
        $hash_string = $arFields['NAME'] . $timestamp;
        $order_unique_code = stringToHash($hash_string);
        $order_id = $arFields['ID'];
        $order_link = "https://" . $_SERVER['HTTP_HOST'] . "/receipt/order/" . $order_unique_code . '/';
        $qrcode = new QRCode();
        $qrcode->render($order_link, $_SERVER["DOCUMENT_ROOT"] . "/qrCodes/qr" . $order_unique_code . ".png");

        if ($arFields['CHECK_PAYMENT'] == 'N') {
            $paymentLink = '';
        } else {

            //получаем название бронируемого объекта
            $ar_object = CIBlockElement::GetByID($arFields['PROPERTY_VALUES'][21]['VALUE']);
            $object = $ar_object->GetNext();

            $paymentData = [
                'PHONE' => $arFields['PROPERTY_VALUES'][20],
                'PRICE' => $arFields['PROPERTY_VALUES'][32],
                'OBJECT_NAME' => $object['NAME'],
                'PERMISSION' => $arFields['PROPERTY_VALUES'][15],
                'ADULTS_COUNT' => $arFields['PROPERTY_VALUES'][16],
                'BENEFIT_COUNT' => $arFields['PROPERTY_VALUES'][17],
                'ARRIVAL_DATE' => $arFields['PROPERTY_VALUES'][11]['VALUE'],
                'DEPARTURE_DATE' => $arFields['PROPERTY_VALUES'][12]['VALUE'],
                'CHECK_IN_TIME' => $arFields['PROPERTY_VALUES'][13],
                'DEPARTURE_TIME' => $arFields['PROPERTY_VALUES'][14],
                'ORDER_LINK' => $order_link];
            $paymentInfo = Payment::createPayment($paymentData);
            $paymentLink = $paymentInfo['confirmation']['confirmationUrl'];

        }

        $el = new CIBlockElement;
        $PROP = [
            9 => $arFields['PROPERTY_VALUES'][9],
            10 => $arFields['PROPERTY_VALUES'][10],
            11 => [
                'VALUE' => $arFields['PROPERTY_VALUES'][11]['VALUE'],
            ],
            12 => [
                'VALUE' => $arFields['PROPERTY_VALUES'][12]['VALUE'],
            ],
            13 => $arFields['PROPERTY_VALUES'][13],
            14 => $arFields['PROPERTY_VALUES'][14],
            15 => $arFields['PROPERTY_VALUES'][15],
            16 => $arFields['PROPERTY_VALUES'][16],
            17 => $arFields['PROPERTY_VALUES'][17],
            19 => $arFields['PROPERTY_VALUES'][19],
            20 => $arFields['PROPERTY_VALUES'][20],
            21 => intval($arFields['PROPERTY_VALUES'][21]['VALUE']),
            22 => $arFields['PROPERTY_VALUES'][22],
            32 => $arFields['PROPERTY_VALUES'][32],
            33 => $order_unique_code,
            34 => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"] . "/qrCodes/qr" . $order_unique_code . ".png"),
            39 => $arFields['PROPERTY_VALUES'][39],
            40 => '',
            41 => '',
            42 => $paymentLink,
            43 => $arFields['PROPERTY_VALUES'][43],
            44 => $arFields['PROPERTY_VALUES'][44],
        ];

        $arLoadOrderArray = array(
            "MODIFIED_BY" => $arFields['MODIFIED_BY'],
            'CODE' => $order_unique_code,
            "PROPERTY_VALUES" => $PROP,
        );

        $res = $el->Update($order_id, $arLoadOrderArray);

        if (!$res) {
            \Bitrix\Main\Diag\Debug::dumpToFile($res, 'createQRCode', 'init_error_log.txt');
        } else {
            //проверяем, если заказ оформлен не из админки - перенаправляем на форму оплаты
            if ($arFields['PROPERTY_VALUES'][22] == 'Онлайн') {
                if ($paymentLink) {
                    LocalRedirect($paymentLink);
                }
            } else {
                if (isset($arFields['PROPERTY_VALUES'][19]) && !empty($arFields['PROPERTY_VALUES'][19])) {
                    onBookingSendEmail($arFields);
                }
                if (!empty($order_link)) {
                    onBookingSendQRToTelegram($order_link, $arFields);
                }
                //собираем статистику по заказу
                addDataToStatTable($arFields);
                //отрпавляем оповещение партнерам
                partnerNotification($arFields);
            }
        }
    }
}

//функция удаляет актуальную запись о бронировании из хл блока при удалении заказа
function deleteObjectDataFromHLBlock(&$arFields)
{
    //\Bitrix\Main\Diag\Debug::dumpToFile($arFields, 'deleteObjectDataFromHLBlock', 'init_log.txt');

    Loader::includeModule("iblock");

    $OBJECT_ID = null;

    $arSelect = array(
        "ID",
        "NAME",
        "PROPERTY_NAME",
        "PROPERTY_ARRIVAL_DATE",
        "PROPERTY_DEPARTURE_DATE",
        "PROPERTY_CHECK_IN_TIME",
        "PROPERTY_DEPARTURE_TIME",
        "PROPERTY_BOOKING_OBJECT",
        "PROPERTY_HLBLOCK_ORDER_ID",
    );

    $arFilter = array(
        "IBLOCK_ID" => IB_BOOKING_LIST,
        "ID" => intval($arFields),
    );

    $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);

    if ($res) {
        $object = [];
        while ($ob = $res->GetNextElement()) {
            $obFields = $ob->GetFields();
            if ($obFields['PROPERTY_BOOKING_OBJECT_VALUE']) {
                $OBJECT_ID = $obFields['PROPERTY_BOOKING_OBJECT_VALUE'];
            }
            if (isset($obFields['PROPERTY_HLBLOCK_ORDER_ID_VALUE']) && !empty($obFields['PROPERTY_HLBLOCK_ORDER_ID_VALUE'])) {
                $hlb_id = $obFields['PROPERTY_HLBLOCK_ORDER_ID_VALUE'];
            } else {
                $object[$obFields['ID']] = [
                    'ID' => $obFields['PROPERTY_BOOKING_OBJECT_VALUE'],
                    'ORDER_ID' => $obFields['ID'],
                    'ARRIVAL_DATE' => $obFields['PROPERTY_ARRIVAL_DATE_VALUE'] . ' ' . $obFields['PROPERTY_CHECK_IN_TIME_VALUE'],
                    'DEPARTURE_DATE' => $obFields['PROPERTY_DEPARTURE_DATE_VALUE'] . ' ' . $obFields['PROPERTY_DEPARTURE_TIME_VALUE'],
                ];
            }
        }

        Loader::includeModule("highloadblock");

        $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $now = new DateTime();

        if (isset($hlb_id) && !empty($hlb_id)) {
            $entity_data_class::Delete($hlb_id);
        } else {
            $data = $entity_data_class::getList(array(
                "select" => array("*"),
                "order" => array("ID" => "DESC"),
                "filter" => array("UF_OBJECT_ID" => $object[intval($arFields)]['ID'])
            ));

            while ($arData = $data->Fetch()) {
                $hl_arrival_date = DateTime::createFromFormat('d.m.Y H:i:s', $arData['UF_ARRIVAL_DATE']);
                if (isset($arData['UF_DEPARTURE_DATE']) && $arData['UF_DEPARTURE_DATE'] != '') {
                    $hl_departure_date = DateTime::createFromFormat('d.m.Y H:i:s', $arData['UF_DEPARTURE_DATE']);
                } else {
                    $hl_departure_date = '';
                }
                $arrival_date = DateTime::createFromFormat('d.m.Y H:i:s', \Bitrix\Main\Type\DateTime::createFromUserTime($object[intval($arFields)]['ARRIVAL_DATE']));
                if (!empty($object[intval($arFields)]['ARRIVAL_DATE']) && !is_null($object[intval($arFields)]['ARRIVAL_DATE']) && $object[intval($arFields)]['ARRIVAL_DATE'] != " ") {
                    $departure_date = DateTime::createFromFormat('d.m.Y H:i:s', \Bitrix\Main\Type\DateTime::createFromUserTime($object[intval($arFields)]['ARRIVAL_DATE']));
                } else {
                    $departure_date = '';
                }
                if ($hl_arrival_date == $arrival_date && $departure_date == $hl_departure_date) {
                    $entity_data_class::Delete($arData['ID']);
                } else {
                    \Bitrix\Main\Diag\Debug::dumpToFile('delete error', 'deleteObjectDataFromHLBlock', 'init_error_log.txt');
                }
            }
        }
        //если объект без ограничения по времени бронирования, то возвращаем его активность
        if ($OBJECT_ID) {
            CModule::IncludeModule("iblock");
            global $USER;
            $res = CIBlockElement::GetByID($OBJECT_ID);
            if ($ar_res = $res->GetNext()) {
                $el = new CIBlockElement;
                $res = $el->Update($OBJECT_ID, array("ACTIVE" => "Y", "MODIFIED_BY" => $USER->GetID()));
            }
        }
    }
}

//функция отправляет почтовый шаблон NEW_ORDER
function onBookingSendEmail($arFields)
{
    //\Bitrix\Main\Diag\Debug::dumpToFile($arFields, 'onBookingSendEmail', 'init_log.txt');
    $USER_EMAIL = $arFields['PROPERTY_VALUES'][19];
    $object = CIBlockElement::GetByID(intval($arFields['PROPERTY_VALUES'][21]['VALUE']));
    $OBJECT_NAME = '';
    $OBJECT_CATEGORY = '';
    $PEOPLE_COUNT = '';
    $QR_SRC = '';
    $RECEIPT_LINK = '';
    if ($ar_object = $object->GetNext()) {
        $OBJECT_NAME = $ar_object['NAME'];
        if (!empty($ar_object['IBLOCK_SECTION_ID'])) {
            $section = CIBlockSection::GetByID($ar_object['IBLOCK_SECTION_ID']);
            if ($ar_section = $section->GetNext()) {
                $OBJECT_CATEGORY = $ar_section['NAME'];
            }
        }
    }
    $ARRIVAL_DATE = $arFields['PROPERTY_VALUES'][11]['VALUE'];
    $CHECK_IN_TIME = $arFields['PROPERTY_VALUES'][13];
    $DEPARTURE_DATE = $arFields['PROPERTY_VALUES'][12]['VALUE'];
    $DEPARTURE_TIME = $arFields['PROPERTY_VALUES'][14];
    $USER_FIO = $arFields['PROPERTY_VALUES'][9] . ' ' . $arFields['PROPERTY_VALUES'][10];
    if ($arFields['PROPERTY_VALUES'][17] == 0) {
        if (isset($arFields['PROPERTY_VALUES'][16])) {
            $PEOPLE_COUNT = "Общее число: " . $arFields['PROPERTY_VALUES'][16];
        }
    } else {
        $PEOPLE_COUNT = "Общее число: " . $arFields['PROPERTY_VALUES'][16] . ', из них льготников: ' . $arFields['PROPERTY_VALUES'][17];
    }
    $ORDER_ID = $arFields['ID'];
    $ORDER_MANAGER = $arFields['PROPERTY_VALUES'][22];
    $ORDER_COST = $arFields['PROPERTY_VALUES'][32];
    $arSelect = array(
        "ID",
        "PROPERTY_QR_CODE",
        "PROPERTY_UNIQUE_ORDER_CODE"
    );
    if ($ORDER_ID) {
        $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, "ID" => $ORDER_ID);
        $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arElementFields = $ob->GetFields();
            if (isset($arElementFields["PROPERTY_QR_CODE_VALUE"])) {
                $QR_SRC = CFile::GetPath($arElementFields["PROPERTY_QR_CODE_VALUE"]);
            }
            if (isset($arElementFields["PROPERTY_UNIQUE_ORDER_CODE_VALUE"])) {
                $RECEIPT_LINK = 'https:///receipt/?order=' . $arElementFields["ID"] . '&code=' . $arElementFields["PROPERTY_UNIQUE_ORDER_CODE_VALUE"];
            }
        }
        Event::send(array(
            "EVENT_NAME" => "NEW_ORDER",
            "LID" => "s1",
            "C_FIELDS" => array(
                //"DEFAULT_EMAIL_FROM" => "",
                //"SITE_NAME" => "",
                "EMAIL" => $USER_EMAIL,
                "OBJECT_NAME" => $OBJECT_NAME,
                "OBJECT_CATEGORY" => $OBJECT_CATEGORY,
                "ARRIVAL_DATE" => $ARRIVAL_DATE,
                "CHECK_IN_TIME" => $CHECK_IN_TIME,
                "DEPARTURE_DATE" => $DEPARTURE_DATE,
                "DEPARTURE_TIME" => $DEPARTURE_TIME,
                "USER_FIO" => $USER_FIO,
                "PEOPLE_COUNT" => $PEOPLE_COUNT,
                "ORDER_ID" => $ORDER_ID,
                "ORDER_MANAGER" => $ORDER_MANAGER,
                "ORDER_COST" => $ORDER_COST,
                "RECEIPT_LINK" => $RECEIPT_LINK,
                "QR_SRC" => $QR_SRC,
            ),
        ));
    } else {
        $errMsg = 'Отсутствует значение $ORDER_ID';
        \Bitrix\Main\Diag\Debug::dumpToFile($errMsg, $varName = 'onBookingSendEmail', $fileName = 'init_error_log.txt');
    }
    unset($USER_EMAIL, $OBJECT_NAME, $OBJECT_CATEGORY, $PEOPLE_COUNT, $RECEIPT_LINK, $QR_SRC);
}

//функция отправляет данные в телеграм
function onBookingSendQRToTelegram($order_link, $arFields)
{
    if (!empty($order_link) && !empty($arFields)) {
        //\Bitrix\Main\Diag\Debug::dumpToFile($arFields, 'onBookingSendQRToTelegram', 'init_log.txt');
        Loader::includeModule("iblock");
        $object = [];
        $arSelect = array(
            "NAME",
            "PROPERTY_LOCATION",
        );
        $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "ID" => intval($arFields['PROPERTY_VALUES'][21]['VALUE']));
        $object_data = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
        while ($object_values = $object_data->GetNextElement()) {
            $arObject = $object_values->GetFields();
            $object['name'] = $arObject['NAME'];
            $object['location'] = $arObject['PROPERTY_LOCATION_VALUE'];
        }
        if ($object['location']) {
            $res = CIBlockElement::GetByID($object['location']);
            if ($ar_res = $res->GetNext()) {
                $object['location'] = $ar_res['NAME'];
            }
        }
        $link = $order_link;
        $tlgrm = (object)[
            'object_name' => $object['name'],
            'object_location' => htmlspecialchars_decode($object['location']),
            'phone' => $arFields['PROPERTY_VALUES'][20],
            'fio' => $arFields['PROPERTY_VALUES'][9] . ' ' . $arFields['PROPERTY_VALUES'][10],
            'booking_period' => 'с ' . $arFields['PROPERTY_VALUES'][11]['VALUE'] . ' ' . $arFields['PROPERTY_VALUES'][13] . ' по ' . $arFields['PROPERTY_VALUES'][12]['VALUE'] . ' ' . $arFields['PROPERTY_VALUES'][14],
        ];
        $telegramQuery = [
            'chat_id' => '',
            'text' => "Оплачена услуга - локация \"$tlgrm->object_location\", объект \"$tlgrm->object_name\". \nФИО: $tlgrm->fio \nТел: $tlgrm->phone \nПериод бронирования: $tlgrm->booking_period \n$link"
        ];
        $resp = file_get_contents("https://api.telegram.org//sendMessage?" . http_build_query($telegramQuery));
    }
}