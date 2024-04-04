<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

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

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request;

$request = Context::getCurrent()->getRequest();
$user_id = $request["id"];

$APPLICATION->IncludeComponent(
    "wa:user.edit",
    "",
    [
        'USER_ID' => $user_id,
    ],
    false
);
?>
