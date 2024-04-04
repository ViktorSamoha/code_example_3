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

$user = getUserData();

if ($user['IS_RESERV']) {
    LocalRedirect("/admin/booking/");
}

if ($user['IS_ADMIN']){
    $APPLICATION->IncludeComponent(
        "wa:partners",
        ".default",
        [],
        false
    );
}?>
