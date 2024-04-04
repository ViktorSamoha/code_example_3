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
    "bitrix:iblock.element.add.form",
    "admin.booking.new",
    array(
        "CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",    // * дата начала *
        "CUSTOM_TITLE_DATE_ACTIVE_TO" => "",    // * дата завершения *
        "CUSTOM_TITLE_DETAIL_PICTURE" => "",    // * подробная картинка *
        "CUSTOM_TITLE_DETAIL_TEXT" => "",    // * подробный текст *
        "CUSTOM_TITLE_IBLOCK_SECTION" => "",    // * раздел инфоблока *
        "CUSTOM_TITLE_NAME" => "",    // * наименование *
        "CUSTOM_TITLE_PREVIEW_PICTURE" => "",    // * картинка анонса *
        "CUSTOM_TITLE_PREVIEW_TEXT" => "",    // * текст анонса *
        "CUSTOM_TITLE_TAGS" => "",    // * теги *
        "DEFAULT_INPUT_SIZE" => "30",    // Размер полей ввода
        "DETAIL_TEXT_USE_HTML_EDITOR" => "N",    // Использовать визуальный редактор для редактирования подробного текста
        "ELEMENT_ASSOC" => "CREATED_BY",    // Привязка к пользователю
        "GROUPS" => array(    // Группы пользователей, имеющие право на добавление/редактирование
            0 => "2",
        ),
        "IBLOCK_ID" => IB_BOOKING_LIST,    // Инфоблок
        "IBLOCK_TYPE" => "booking_list",    // Тип инфоблока
        "LEVEL_LAST" => "Y",    // Разрешить добавление только на последний уровень рубрикатора
        "LIST_URL" => "",    // Страница со списком своих элементов
        "MAX_FILE_SIZE" => "0",    // Максимальный размер загружаемых файлов, байт (0 - не ограничивать)
        "MAX_LEVELS" => "100000",    // Ограничить кол-во рубрик, в которые можно добавлять элемент
        "MAX_USER_ENTRIES" => "100000",    // Ограничить кол-во элементов для одного пользователя
        "PREVIEW_TEXT_USE_HTML_EDITOR" => "N",    // Использовать визуальный редактор для редактирования текста анонса
        "PROPERTY_CODES" => array(    // Свойства, выводимые на редактирование
            0 => "9",
            1 => "10",
            2 => "11",
            3 => "12",
            4 => "13",
            5 => "14",
            6 => "15",
            7 => "16",
            8 => "17",
            9 => "19",
            10 => "20",
            11 => "NAME",
            12 => "21",
            13 => "22",
            14 => "32",
            15 => "39",
            16 => "43",
            17 => "44",
            18 => "TIME_LIMIT",
            19 => GUEST_CARS
        ),
        "PROPERTY_CODES_REQUIRED" => "",    // Свойства, обязательные для заполнения
        "RESIZE_IMAGES" => "N",    // Использовать настройки инфоблока для обработки изображений
        "SEF_MODE" => "N",    // Включить поддержку ЧПУ
        "STATUS" => "ANY",    // Редактирование возможно
        "STATUS_NEW" => "N",    // Деактивировать элемент
        "USER_MESSAGE_ADD" => "",    // Сообщение об успешном добавлении
        "USER_MESSAGE_EDIT" => "",    // Сообщение об успешном сохранении
        "USE_CAPTCHA" => "N",    // Использовать CAPTCHA
    ),
    false
);

?>