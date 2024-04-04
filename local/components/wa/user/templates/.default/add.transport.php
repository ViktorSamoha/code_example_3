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

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$values = $request->getQueryList();

if ($values['ID']) {
    $APPLICATION->IncludeComponent(
        "wa:user.add.transport",
        '.default',
        [
            'EDIT_VEHICLE' => 'Y',
            'VEHICLE_ID' => $values['ID']
        ],
        false
    );
} else {
    $APPLICATION->IncludeComponent(
        "wa:user.add.transport",
        ".default",
        [],
        false
    );
}
?>

