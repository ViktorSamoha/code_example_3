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

$context = Context::getCurrent();
$request = $context->getRequest();
$_get = $request->getQueryList();

$template = '.default';
if (isset($_get['EDIT']) && $_get['EDIT'] == 'Y') {
    $template = 'edit';
} elseif (isset($_get['FAST']) && $_get['FAST'] == 'Y') {
    $template = 'fast';
}

$APPLICATION->IncludeComponent(
    "wa:admin.booking",
    $template,
    [
        'OBJECT_ID' => $_get['OBJECT_ID'] ? $_get['OBJECT_ID'] : false,
        'ORDER_ID' => $_get['ORDER_ID'] ? $_get['ORDER_ID'] : false,
    ],
    false
);
?>