<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

Loader::includeModule("iblock");

/*
LEGACY USELESS CODE
 */

if ($request->get("object_id")) {
    /*$ELEMENT_ID = $request->get("object_id");
    $PROPERTY_CODE = "OBJECT_RENTED";
    $PROPERTY_VALUE = "7";
    $dbr = CIBlockElement::GetList(array(), array("=ID" => $ELEMENT_ID), false, false, array("ID", "IBLOCK_ID"));
    if ($dbr_arr = $dbr->Fetch()) {
        $IBLOCK_ID = $dbr_arr["IBLOCK_ID"];
        CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
    }*/
}