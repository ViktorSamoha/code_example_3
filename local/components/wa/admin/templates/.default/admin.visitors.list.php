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
$APPLICATION->SetTitle("Карточки посетителей");
$context = Context::getCurrent();
$request = $context->getRequest();
$_get = $request->getQueryList();
?>
<?
$APPLICATION->IncludeComponent(
    "wa:admin.visitors.list",
    "filter",
    [
        'FILTER_LAST_NAME_VALUE' => $_get['LAST_NAME'],
        'FILTER_PHONE_VALUE' => $_get['WORK_PHONE'],
        'FILTER_EMAIL_VALUE' => $_get['EMAIL'],
    ],
    false
);

$listFilter = [];
if (!empty($_get)) {
    foreach ($_get as $getName => $getValue) {
        if ($getValue && $getName != 'clear_cache') {
            $listFilter[$getName] = $getValue;
        }
    }
}

$APPLICATION->IncludeComponent(
    "wa:admin.visitors.list",
    ".default",
    [
        'FILTER_VALUE' => $listFilter,
    ],
    false
);
?>
