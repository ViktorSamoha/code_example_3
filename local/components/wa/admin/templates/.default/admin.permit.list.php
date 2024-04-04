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
$APPLICATION->SetTitle("Разрешение на посещение/ заказы");
$context = Context::getCurrent();
$request = $context->getRequest();
$_get = $request->getQueryList();
$APPLICATION->IncludeComponent(
    "wa:admin.permission.list",
    "filter",
    [
        'FILTER_USER_FIO_VALUE' => $_get['USER_FIO'],
        'FILTER_ID_VALUE' => $_get['ID'],
        'FILTER_DATE_VALUE' => $_get['DATE'],
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
    "wa:admin.permission.list",
    ".default",
    [
        'FILTER_VALUE' => $listFilter,
    ],
    false
);
?>
