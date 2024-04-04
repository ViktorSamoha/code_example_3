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

$user = getUserData();

if ($user['IS_RESERV']) {
    LocalRedirect("/admin/booking/");
}

/*$APPLICATION->IncludeComponent(
    "bitrix:iblock.element.add",
    "admin.orders",
    array(
        "AJAX_MODE" => "N",    // Включить режим AJAX
        "AJAX_OPTION_ADDITIONAL" => "",    // Дополнительный идентификатор
        "AJAX_OPTION_HISTORY" => "N",    // Включить эмуляцию навигации браузера
        "AJAX_OPTION_JUMP" => "N",    // Включить прокрутку к началу компонента
        "AJAX_OPTION_STYLE" => "N",    // Включить подгрузку стилей
        "ALLOW_DELETE" => "Y",    // Разрешать удаление
        "ALLOW_EDIT" => "Y",    // Разрешать редактирование
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
        "DETAIL_TEXT_USE_HTML_EDITOR" => "N",    // Использовать упрощенный визуальный редактор для редактирования подробного текста
        "ELEMENT_ASSOC" => "CREATED_BY",    // Привязка к пользователю
        "GROUPS" => "",    // Группы пользователей, имеющие право на добавление/редактирование
        "IBLOCK_ID" => $arParams['ORDERS_IBLOCK_ID'],    // Инфоблок
        "IBLOCK_TYPE" => $arParams['ORDERS_IBLOCK_TYPE'],    // Тип инфоблока
        "LEVEL_LAST" => "Y",    // Разрешить добавление только на последний уровень рубрикатора
        "MAX_FILE_SIZE" => "0",    // Максимальный размер загружаемых файлов, байт (0 - не ограничивать)
        "MAX_LEVELS" => "100000",    // Ограничить кол-во рубрик, в которые можно добавлять элемент
        "MAX_USER_ENTRIES" => "100000",    // Ограничить кол-во элементов для одного пользователя
        "NAV_ON_PAGE" => "10",    // Количество элементов на странице
        "PREVIEW_TEXT_USE_HTML_EDITOR" => "N",    // Использовать упрощенный визуальный редактор для редактирования текста анонса
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
            10 => "19",
            11 => "20",
            12 => "22",
            13 => "NAME",
            14 => "21",
            15 => "32",
            16 => "39",
            17 => '33',
            18 => '40',
            19 => 'TIME_LIMIT',
            20 => GUEST_CARS
        ),
        "PROPERTY_CODES_REQUIRED" => "",    // Свойства, обязательные для заполнения
        "RESIZE_IMAGES" => "N",    // Использовать настройки инфоблока для обработки изображений
        "SEF_MODE" => "N",    // Включить поддержку ЧПУ
        "STATUS" => "ANY",    // Редактирование возможно
        "STATUS_NEW" => "N",    // Деактивировать элемент после сохранения
        "USER_MESSAGE_ADD" => "",    // Сообщение об успешном добавлении
        "USER_MESSAGE_EDIT" => "",    // Сообщение об успешном сохранении
        "USE_CAPTCHA" => "N",    // Использовать CAPTCHA
        "USER_DATA" => $arParams['USER'],
    ),
    false
);*/


$APPLICATION->SetTitle("Заказы");
$context = Context::getCurrent();
$request = $context->getRequest();
$_get = $request->getQueryList();

$APPLICATION->IncludeComponent(
    "wa:admin.booking.list",
    "filter",
    [
        'FILTER_USER_FIO_VALUE' => $_get['USER_FIO'],
        'FILTER_ID_VALUE' => $_get['ID'],
        'FILTER_DATE_VALUE' => $_get['DATE'],
        'FILTER_VEHICLE_ID_VALUE' => $_get['VEHICLE_ID'],
        'FILTER_OBJECT_NAME_VALUE' => $_get['OBJECT_NAME'],
        'FILTER_LOCATION_ID_VALUE' => $_get['LOCATION_ID'],
    ],
    false
);
$listFilter = [];
if (!empty($_get)) {
    foreach ($_get as $getName => $getValue) {
        if ($getValue && $getName != 'clear_cache') {
            $listFilter[$getName] = $getValue;
        }
    }
}
$APPLICATION->IncludeComponent(
    "wa:admin.booking.list",
    "list",
    [
        'FILTER_VALUE' => $listFilter,
    ],
    false
);

?>