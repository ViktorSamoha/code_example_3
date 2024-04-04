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

if ($request->get("object_id")) {
    $db_props = CIBlockElement::GetProperty(IB_OBJECT, $request->get("object_id"), array("sort" => "asc"), array("CODE" => "BOOKING_ALERT_MSG"));
    if ($ar_props = $db_props->Fetch()) {
        echo $ar_props['VALUE']['TEXT'];
    }
}
