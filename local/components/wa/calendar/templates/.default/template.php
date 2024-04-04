<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
global $USER;
?>
<div style="display: none" id="element-id"><?= $arResult['ELEMENT_ID'] ?></div>
<div class="calendar-aside">
    <h3 class="calendar-aside_title">Свободные даты</h3>
    <? if ($arResult['CALENDAR_HTML']): ?>
        <div id="ajax-calendar">
            <?= $arResult['CALENDAR_HTML'] ?>
        </div>
    <? endif; ?>
    <? if ($USER->IsAuthorized()): ?>
        <button type="button" class="primary-btn primary-btn--lg primary-btn--center"
                data-name="modal-booking" onclick="callBookingModal(<?= $arResult['ELEMENT_ID'] ?>)">
            Бронировать
        </button>
    <? else: ?>
        <button type="button" class="primary-btn primary-btn--lg primary-btn--center js-open-modal"
                data-name="modal-auth">
            Бронировать
        </button>
    <? endif; ?>
</div>