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
$APPLICATION->SetTitle("Карточка пользователя");
$context = Context::getCurrent();
$request = $context->getRequest();
$_get = $request->getQueryList();

if ($_get['ID']) {
    $APPLICATION->IncludeComponent(
        "wa:admin.user.profile",
        "",
        [
            'USER_ID' => $_get['ID'],
        ],
        false
    );
    $APPLICATION->IncludeComponent(
        "wa:admin.user.profile",
        "order_list",
        [
            'USER_ID' => $_get['ID'],
        ],
        false
    );
} else {
    LocalRedirect("/admin/visitors/");
}
?>




