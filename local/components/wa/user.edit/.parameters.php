<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = [
    "GROUPS" => [
        "SETTINGS" => [
            "NAME" => "Выбор инфоблоков и разделов",
            "SORT" => 550,
        ],
    ],
    "PARAMETERS" => [
        "USER_ID" => array(
            "PARENT" => "PARAMS",
            "TYPE" => "TEXT",
            "NAME" => 'ID пользователя',
        ),
        // Настройки кэширования
        'CACHE_TIME' => ['DEFAULT' => 3600],
    ]
];