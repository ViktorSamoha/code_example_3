<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!Loader::includeModule("iblock")) {
    throw new \Exception('Не загружены модули необходимые для работы компонента');
}

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = [];
$iblockFilter = !empty($arCurrentValues['IBLOCK_TYPE'])
    ? ['TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y']
    : ['ACTIVE' => 'Y'];

$rsIBlock = CIBlock::GetList(['SORT' => 'ASC'], $iblockFilter);
while ($arr = $rsIBlock->Fetch()) {
    $arIBlock[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
}
unset($arr, $rsIBlock, $iblockFilter);


$arComponentParameters = [
    "GROUPS" => [
        "SETTINGS" => [
            "NAME" => "Выбор инфоблоков и разделов",
            "SORT" => 550,
        ],
    ],
    "PARAMETERS" => [

        "ORDERS_IBLOCK_TYPE" => [
            "PARENT" => "SETTINGS",
            "NAME" => "Тип инфоблока заказов",
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlockType,
            "REFRESH" => "Y"
        ],
        "ORDERS_IBLOCK_ID" => [
            "PARENT" => "SETTINGS",
            "NAME" => "Инфоблок заказов",
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y"
        ],
        "OBJECTS_IBLOCK_TYPE" => [
            "PARENT" => "SETTINGS",
            "NAME" => "Тип инфоблока Объектов",
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlockType,
            "REFRESH" => "Y"
        ],
        "OBJECTS_IBLOCK_ID" => [
            "PARENT" => "SETTINGS",
            "NAME" => "Инфоблок Объектов",
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y"
        ],
        'SEF_MODE' => array( // это для работы в режиме ЧПУ
            'orders' => array(
                'NAME' => 'Страница бронирования',
                'DEFAULT' => 'orders/',
            ),
            'objects.list' => array(
                'NAME' => 'Страница списка объектов',
                'DEFAULT' => 'objects_list/',
            ),
            'objects.new' => array(
                'NAME' => 'Страница добавления нового объекта',
                'DEFAULT' => 'objects_new/',
            ),
            'booking.new' => array(
                'NAME' => 'Страница бронирования',
                'DEFAULT' => 'booking/',
            ),
            'create.user' => array(
                'NAME' => 'Страница создания нового кабинета пользователя',
                'DEFAULT' => 'create_user/',
            ),
            'booking.fast' => array(
                'NAME' => 'Страница быстрого бронирования',
                'DEFAULT' => 'booking_fast/',
            ),
            'users.list' => array(
                'NAME' => 'Страница списка зарегистрированных пользователей',
                'DEFAULT' => 'users/',
            ),
            'users.edit' => array(
                'NAME' => 'Страница редактирования пользователя',
                'DEFAULT' => 'users/edit/?id=#ID#',
            ),
        ),
        // Настройки кэширования
        'CACHE_TIME' => ['DEFAULT' => 3600],
    ]
];