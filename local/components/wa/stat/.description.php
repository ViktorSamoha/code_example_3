<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    "NAME" => Loc::getMessage("WA_BRON_COMPONENT"),
    "DESCRIPTION" => Loc::getMessage("WA_BRON_COMPONENT_DESCRIPTION"),
    "COMPLEX" => "N",
    "PATH" => [
        "ID" => Loc::getMessage("WA_BRON_COMPONENT_PATH_ID"),
        "NAME" => Loc::getMessage("WA_BRON_COMPONENT_PATH_NAME"),
    ],
];