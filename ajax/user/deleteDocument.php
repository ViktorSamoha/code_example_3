<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$file_id = $request->get("file_id");

if (isset($file_id)) {
    CFile::Delete($file_id);
}