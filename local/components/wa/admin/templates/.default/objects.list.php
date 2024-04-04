<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server;

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
$APPLICATION->SetTitle("Объекты");
$context = Context::getCurrent();
$request = $context->getRequest();
$_get = $request->getQueryList();
$listFilter = [];
if (!empty($_get)) {
    foreach ($_get as $getName => $getValue) {
        if ($getValue && $getName != 'clear_cache' && $getName != 'EDIT' && $getName != 'OBJECT_ID') {
            $listFilter[$getName] = $getValue;
        }
    }
}
$template = 'list';
if (isset($_get['EDIT']) && $_get['EDIT'] == 'Y') {
    $template = 'edit';
}
if ($template == 'list') {
    $APPLICATION->IncludeComponent(
        "wa:admin.object",
        "filter",
        [],
        false
    );
}
$APPLICATION->IncludeComponent(
    "wa:admin.object",
    $template,
    [
        'FILTER_VALUE' => $listFilter,
    ],
    false
);

/*
$APPLICATION->IncludeComponent(
    "bitrix:iblock.element.add",
    "admin.objects.list",
    array(
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "ALLOW_DELETE" => "Y",
        "ALLOW_EDIT" => "Y",
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
        "GROUPS" => array("1", "7"),
        "IBLOCK_ID" => $arParams['OBJECTS_IBLOCK_ID'],
        "IBLOCK_TYPE" => $arParams['OBJECTS_IBLOCK_TYPE'],
        "LEVEL_LAST" => "N",//!!! не менять!
        "MAX_FILE_SIZE" => "0",
        "MAX_LEVELS" => "100000",
        "MAX_USER_ENTRIES" => "100000",
        "NAV_ON_PAGE" => "10",
        "PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
        "PROPERTY_CODES" => array("1", "2", "3", "4", "5", "6", "7", "8", "24", "26", "27", "28", "29", "30", "NAME", "PREVIEW_PICTURE", "DETAIL_TEXT", "IBLOCK_SECTION", "35", "36", "37", "38", "66", "67", "68", "69", OBJECT_PROPERTY_PARTNERS, "72", "73", "SORT", CAR_POSSIBILITY, CAR_CAPACITY),
        "PROPERTY_CODES_REQUIRED" => array(),
        "RESIZE_IMAGES" => "N",
        "SEF_MODE" => "N",
        "STATUS" => "ANY",
        "STATUS_NEW" => "N",
        "USER_MESSAGE_ADD" => "",
        "USER_MESSAGE_EDIT" => "",
        "USE_CAPTCHA" => "N"
    )
);*/
?>
