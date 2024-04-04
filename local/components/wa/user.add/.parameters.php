<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!Loader::includeModule("iblock")) {
    throw new \Exception('Не загружены модули необходимые для работы компонента');
}

$arComponentParameters = [
    "GROUPS" => [
        "SETTINGS" => [
            "NAME" => "Выбор инфоблоков и разделов",
            "SORT" => 550,
        ],
    ],
    "PARAMETERS" => [
        // Настройки кэширования
        'CACHE_TIME' => ['DEFAULT' => 3600],
    ]
];