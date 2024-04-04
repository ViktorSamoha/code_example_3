<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
if (Loader::includeModule("iblock")) {
    if ($request->get("object_id")) {
        $arData = [];
        $arSelect = array(
            "ID",
            "PROPERTY_OBJECT_COST",
            "PROPERTY_OBJECT_DAILY_COST",
            "PROPERTY_COST_PER_PERSON",
            "PROPERTY_COST_PER_PERSON_ONE_DAY",
            "PROPERTY_CAPACITY_ESTIMATED",
            "PROPERTY_CAPACITY_MAXIMUM",
            "PROPERTY_FIXED_COST",
            "PROPERTY_TIME_UNLIMIT_OBJECT",
        );
        $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "ID" => $request->get("object_id"));
        $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arData = [
                "ID" => $arFields['ID'],
                "OBJECT_COST" => $arFields['PROPERTY_OBJECT_COST_VALUE'],
                "OBJECT_DAILY_COST" => $arFields['PROPERTY_OBJECT_DAILY_COST_VALUE'],
                "COST_PER_PERSON" => $arFields['PROPERTY_COST_PER_PERSON_VALUE'],
                "COST_PER_PERSON_ONE_DAY" => $arFields['PROPERTY_COST_PER_PERSON_ONE_DAY_VALUE'],
                "CAPACITY_ESTIMATED" => $arFields['PROPERTY_CAPACITY_ESTIMATED_VALUE'],
                "CAPACITY_MAXIMUM" => $arFields['PROPERTY_CAPACITY_MAXIMUM_VALUE'],
                "VISIT_PERMISSION_COST" => VISIT_PERMISSION_COST,
                "TIME_LIMIT" => $arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'] == 'Да' ? 'Y' : 'N',
                "FIXED_COST" => $arFields['PROPERTY_FIXED_COST_VALUE'] ? $arFields['PROPERTY_FIXED_COST_VALUE'] : 0,
            ];
        }
        if (!empty($arData)) {
            echo json_encode($arData);
        }
    }
}