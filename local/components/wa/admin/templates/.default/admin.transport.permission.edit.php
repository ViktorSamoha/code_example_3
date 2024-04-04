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

$APPLICATION->SetTitle("Разрешение на ТС");

$context = Context::getCurrent();
$request = $context->getRequest();
$_get = $request->getQueryList();

if ($_get['CODE']) {
    $APPLICATION->IncludeComponent(
        "wa:receipt.blank",
        "admin.transport.permission.edit",
        [
            'IBLOCK_ID' => IB_TRANSPORT_PERMISSION,
            'ELEMENT_CODE' => $_get['CODE'],
            'ADMIN_MODE'=>'Y',
        ],
        false
    );
}

?>



