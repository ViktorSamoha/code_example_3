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

if ($request->get("object_id")) {

    $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

    $data = $entity_data_class::getList(array(
        "select" => array("*"),
        "order" => array("ID" => "DESC"),
        "filter" => array("UF_OBJECT_ID" => $request->get("object_id"), 'ID' => $request->get("record_id"))
    ));

    while ($arData = $data->Fetch()) {
        $hl_arrival_date = DateTime::createFromFormat('d.m.Y H:i:s', $arData['UF_ARRIVAL_DATE']);
        if (isset($arData['UF_DEPARTURE_DATE']) && $arData['UF_DEPARTURE_DATE'] != '') {
            $hl_departure_date = DateTime::createFromFormat('d.m.Y H:i:s', $arData['UF_DEPARTURE_DATE']);
        } else {
            $hl_departure_date = '';
        }
        $arrival_date = DateTime::createFromFormat('d.m.Y H:i:s', \Bitrix\Main\Type\DateTime::createFromUserTime($request->get("object_arrival_date")));
        if (!empty($request->get("object_departure_date")) && !is_null($request->get("object_departure_date")) && $request->get("object_departure_date") != " ") {
            $departure_date = DateTime::createFromFormat('d.m.Y H:i:s', \Bitrix\Main\Type\DateTime::createFromUserTime($request->get("object_departure_date")));
        } else {
            $departure_date = '';
        }
        if ($hl_arrival_date == $arrival_date && $departure_date == $hl_departure_date) {
            $hlb_record_id = $arData['ID'];
        }
    }

    if ($hlb_record_id) {
        $entity_data_class::Delete($hlb_record_id);
        if (!isset($arData['UF_DEPARTURE_DATE']) || $arData['UF_DEPARTURE_DATE'] == '') {
            CModule::IncludeModule("iblock");
            $res = CIBlockElement::SetPropertyValuesEx($request->get("object_id"), false, array('CAN_BOOK' => 9));
        }
    }
}
