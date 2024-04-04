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
        'VARIABLE_ALIASES' => array(
            'ELEMENT_CODE' => array('NAME' => 'Символьный код элемента'),
        ),
        'SEF_MODE' => array(
            'transport' => array(
                'NAME' => 'Страница разрешения для транспортного средства',
                'DEFAULT' => 'transport/#ELEMENT_CODE#/',
            ),
            'user' => array(
                'NAME' => 'Страница разрешения на посещение',
                'DEFAULT' => 'user/#ELEMENT_CODE#/',
            ),
        ),
        // Настройки кэширования
        'CACHE_TIME' => ['DEFAULT' => 3600],
    ]
];

CIBlockParameters::Add404Settings($arComponentParameters, $arCurrentValues);