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

$APPLICATION->IncludeComponent(
    "wa:admin.object",
    'add',
    [],
    false
);
/*
$APPLICATION->IncludeComponent(
    "bitrix:iblock.element.add.form",
    "admin.objects.new",
    array(
        "CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
        "CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
        "CUSTOM_TITLE_DETAIL_PICTURE" => "",
        "CUSTOM_TITLE_DETAIL_TEXT" => "",
        "CUSTOM_TITLE_IBLOCK_SECTION" => "",
        "CUSTOM_TITLE_NAME" => "",
        "CUSTOM_TITLE_PREVIEW_PICTURE" => "",
        "CUSTOM_TITLE_PREVIEW_TEXT" => "",
        "CUSTOM_TITLE_TAGS" => "",
        "DEFAULT_INPUT_SIZE" => "30",
        "DETAIL_TEXT_USE_HTML_EDITOR" => "N",
        "ELEMENT_ASSOC" => "CREATED_BY",
        "GROUPS" => array(
            0 => "2",
        ),
        "IBLOCK_ID" => $arParams['OBJECTS_IBLOCK_ID'],
        "IBLOCK_TYPE" => $arParams['OBJECTS_IBLOCK_TYPE'],
        "LEVEL_LAST" => "N",
        "LIST_URL" => "",
        "MAX_FILE_SIZE" => "0",
        "MAX_LEVELS" => "100000",
        "MAX_USER_ENTRIES" => "100000",
        "PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
        "PROPERTY_CODES" => array(
            0 => "2",
            1 => "3",
            2 => "4",
            3 => "5",
            4 => "6",
            5 => "7",
            6 => "8",
            7 => "24",
            8 => "26",
            9 => "27",
            10 => "28",
            11 => "29",
            12 => "NAME",
            13 => "IBLOCK_SECTION",
            14 => "DETAIL_TEXT",
            15 => "1",
            16 => "30",
            17 => "35",
            18 => "36",
            19 => "37",
            20 => "38",
            21 => "66",
            22 => "67",
            23 => "68",
            24 => "69",
            25 => OBJECT_PROPERTY_PARTNERS,
            26 => "72",
            27 => "73",
            28 => "SORT",
            29 => CAR_POSSIBILITY,
            30 => CAR_CAPACITY,
        ),
        "PROPERTY_CODES_REQUIRED" => array(),
        "RESIZE_IMAGES" => "N",
        "SEF_MODE" => "N",
        "STATUS" => "ANY",
        "STATUS_NEW" => "N",
        "USER_MESSAGE_ADD" => "",
        "USER_MESSAGE_EDIT" => "",
        "USE_CAPTCHA" => "N",
        "USER_DATA" => $arParams['USER'],
    )
);*/
?>