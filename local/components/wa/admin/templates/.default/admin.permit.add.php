<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server;

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);

$APPLICATION->SetTitle("Разрешение на посещение");
$context = Context::getCurrent();
$request = $context->getRequest();
$_get = $request->getQueryList();
if (!empty($_get)) {
    $APPLICATION->IncludeComponent(
        "wa:admin.permission.add",
        "",
        [
            'USER_NUMBER' => $_get['USER_NUMBER'],
            'USER_RECORD_ID' => $_get['USER_RECORD_ID'],
        ],
        false
    );
} else {
    $APPLICATION->IncludeComponent(
        "wa:admin.permission.add",
        ".default",
        [],
        false
    );
}

?>

