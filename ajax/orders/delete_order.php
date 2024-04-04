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

if ($request->get("order_id")) {
    $order_id = $request->get("order_id");
    $arSelect = array("ID", "PROPERTY_HLBLOCK_ORDER_ID");
    $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, "ID" => $order_id);
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $hl_record_id = $arFields['PROPERTY_HLBLOCK_ORDER_ID_VALUE'];
    }
    if ($hl_record_id) {
        $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        if ($entity_data_class::Delete($hl_record_id)) {
            echo 'true';
        }
    }
}
