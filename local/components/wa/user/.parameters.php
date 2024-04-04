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
        'SEF_MODE' => array( // это для работы в режиме ЧПУ
            'personal' => array(
                'NAME' => 'Страница личного кабинета пользователя',
                'DEFAULT' => 'personal/',
            ),
            'visiting.permit' => array(
                'NAME' => 'Страница оформления разрешения на посещения',
                'DEFAULT' => 'visiting_permit/',
            ),
            'transport.permit' => array(
                'NAME' => 'Страница оформления разрешения для транспортного средства',
                'DEFAULT' => 'transport_permit/',
            ),
            'add.transport' => array(
                'NAME' => 'Страница регистрации транспортного средства',
                'DEFAULT' => 'add_transport/',
            ),
            'add.visitor' => array(
                'NAME' => 'Страница добавления посетителя',
                'DEFAULT' => 'add_visitor/',
            ),
            'order.history' => array(
                'NAME' => 'Страница истории заказов пользователя',
                'DEFAULT' => 'order_history/',
            ),
        ),
        // Настройки кэширования
        'CACHE_TIME' => ['DEFAULT' => 3600],
    ]
];