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

if ($user['IS_ADMIN']) {
    $APPLICATION->IncludeComponent(
        "wa:stat",
        ".default",
        [
            "OBJECTS_IBLOCK_TYPE" => "facility",
            "OBJECTS_IBLOCK_ID" => IB_OBJECT,
            "USER_GROUPS" => [
                0 => '7',
                1 => '8',
                2 => '9',
            ],
            "LOCATIONS_IBLOCK_TYPE" => "location",
            "LOCATIONS_IBLOCK_ID" => IB_LOCATIONS,
        ],
        false
    );
}

?>