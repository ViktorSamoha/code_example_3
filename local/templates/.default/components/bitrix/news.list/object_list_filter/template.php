<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
?>
<div class="custom-select" id="object-list-filter">
    <div class="custom-select_head">
        <span class="custom-select_title" data-selected-id="">Фильтр</span>
        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1L7 7L13 1" stroke="#000"/>
        </svg>
    </div>
    <div class="custom-select_body">
        <? foreach ($arResult["ITEMS"] as $item): ?>
            <div class="custom-select_item" data-id="<?= $item['ID'] ?>"><?= $item['NAME'] ?></div>
        <? endforeach; ?>
    </div>
</div>
<div id="reset-btn" style="display: none">
    <input type="button" value="Сбросить" class="primary-btn" onclick="location.reload()">
</div>