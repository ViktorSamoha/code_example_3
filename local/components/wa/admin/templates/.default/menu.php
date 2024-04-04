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

$APPLICATION->SetTitle("Быстрое меню");

?>
<nav class="p-menu">
    <a href="/admin/permission_add/" class="p-menu_item">Разрешение на посещение</a>
    <a href="/admin/visitor_add/" class="p-menu_item">Карточка посетителя</a>
    <a href="/admin/transport_permission_add/" class="p-menu_item">разрешение на тс</a>
</nav>