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

if (isset($arResult) && !empty($arResult)) {
    if (isset($arResult['VARIABLES']) && !empty($arResult['VARIABLES'])) {
        if (isset($arResult['VARIABLES']['ELEMENT_CODE'])) {
            $APPLICATION->IncludeComponent(
                "wa:receipt.blank",
                "user.booking.blank",
                [
                    'IBLOCK_ID'=>IB_BOOKING_LIST,
                    'ELEMENT_CODE' => $arResult['VARIABLES']['ELEMENT_CODE'],
                ],
                false
            );
        }
    }
}
?>


