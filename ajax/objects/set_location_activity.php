<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;

Loader::includeModule("iblock");

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$action = $request->get("action");
$location = $request->get("location");

if (isset($action) && isset($location)) {
    if ($action == 'deactivate') {
        $action_value = 'N';
    } else {
        $action_value = 'Y';
    }

    $loc = new CIBlockElement();
    $locActivationResult = $loc->Update(intval($location), array('ACTIVE' => $action_value));
}
?>
