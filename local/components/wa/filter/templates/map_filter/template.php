<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<form class="booking-form">
    <div class="select-block">
        <span class="input-label">Объект бронирования</span>
        <div class="custom-select">
            <div class="custom-select_head">
                <span class="custom-select_title">Выбрать локацию</span>
                <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L7 7L13 1" stroke="#000"/>
                </svg>
            </div>
            <div class="custom-select_body">
                <div class="custom-select_item" data-id="all">Все локации</div>
                <? foreach ($arResult['LOCATIONS'] as $location): ?>
                    <div class="custom-select_item"
                         data-id="<?= $location['ID'] ?>"><?= $location['NAME'] ?></div>
                <? endforeach; ?>
            </div>
        </div>
    </div>
    <div class="m-input-dates js-input-date-group">
        <div class="m-input-date-block">
            <label for="">Заезд</label>
            <input type="text" class="input-date" id="map-filter-arrival-date">
        </div>
        <div class="m-input-date-block">
            <label for="">Выезд</label>
            <input type="text" class="input-date second-range-input" id="map-filter-departure-date">
        </div>
    </div>
    <div class="input-counter-item">
        <span class="input-label">Кол-во гостей</span>
        <div class="input-counter">
            <button class="input-counter_btn" type="button" data-input-id="guest-quantity"
                    data-action="minus">
                <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                </svg>
            </button>
            <input type="text" class="input-counter_input" value="1" id="guest-quantity">
            <button class="input-counter_btn" type="button" data-input-id="guest-quantity"
                    data-action="plus">
                <svg width="13" height="13" viewBox="0 0 13 13" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 6H13V7H0V6Z" fill="#313131"/>
                    <path d="M6 13L6 4.37112e-08L7 0L7 13H6Z" fill="#313131"/>
                </svg>
            </button>
        </div>
    </div>
    <button class="primary-btn primary-btn--md" id="apply-map-filter">Найти</button>
</form>