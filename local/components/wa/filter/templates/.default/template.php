<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="page-w-aside_aside">
    <div class="aside-top">
        <? if ($USER->IsAuthorized()): ?>
            <a href="/user/visiting_permit/" class="aside-top-btn rounded-btn">Оформить
                разрешение на посещение</a>
        <? else: ?>
            <a href="/permission/" class="aside-top-btn rounded-btn">Оформить
                разрешение на посещение</a>
        <? endif; ?>
        <h2 class="aside-title">Объекты на территории </h2>
        <div class="vue-btns">
            <div class="vue-btn active">
                <input type="radio" id="vue-list" value="list" name="vue" checked>
                <label for="vue-list">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M4 11.125V4H11.125V11.125H4ZM4 20V12.875H11.125V20H4ZM12.875 11.125V4H20V11.125H12.875ZM12.875 20V12.875H20V20H12.875Z"
                                fill="#BABABA"/>
                    </svg>
                    <span>Список</span>
                </label>
            </div>
            <div class="vue-btn">
                <input type="radio" id="vue-map" value="map" name="vue" onclick="window.location.href = '/map/';">
                <label for="vue-map">
                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M15.7786 20.755L9.24219 18.4634L5.20573 20.0519C4.96267 20.1561 4.7283 20.1387 4.5026 19.9998C4.27691 19.8609 4.16406 19.6526 4.16406 19.3748V6.6665C4.16406 6.47553 4.20747 6.30626 4.29427 6.15869C4.38108 6.01112 4.51128 5.90262 4.6849 5.83317L9.24219 4.24463L15.7786 6.51025L19.7891 4.94775C20.0321 4.84359 20.2665 4.84793 20.4922 4.96077C20.7179 5.07362 20.8307 5.26893 20.8307 5.54671V18.4894C20.8307 18.663 20.7743 18.8193 20.6615 18.9582C20.5486 19.0971 20.4141 19.1925 20.2578 19.2446L15.7786 20.755ZM15.3099 19.7655V7.13525L9.6849 5.18213V17.8123L15.3099 19.7655Z"
                                fill="#BABABA"/>
                    </svg>
                    <span>На карте</span>
                </label>
            </div>
        </div>
    </div>
    <form class="filter" action="">
        <input type="hidden" id="parent-section-id" value="<?= $arParams['PARENT_SECTION_ID'] ?>">
        <button class="close-filter-btn" type="button">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                        d="M20 2.01429L17.9857 0L10 7.98571L2.01429 0L0 2.01429L7.98571 10L0 17.9857L2.01429 20L10 12.0143L17.9857 20L20 17.9857L12.0143 10L20 2.01429Z"
                        fill="#313131"/>
            </svg>
        </button>
        <div class="filter-block-group">
            <div class="filter_block">
                <div class="select-block">
                    <span class="input-label input-label--mb">Выбрать локацию</span>
                    <div class="custom-select">
                        <div class="custom-select_head">
                            <? if (isset($arResult['SELECTED_LOCATION'])): ?>
                                <span class="custom-select_title"
                                      data-selected-id="<?= $arResult['SELECTED_LOCATION']['ID'] ?>"><?= $arResult['SELECTED_LOCATION']['NAME'] ?></span>
                            <? else: ?>
                                <span class="custom-select_title" data-selected-id="all">Все локации</span>
                            <? endif; ?>

                            <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                 fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L7 7L13 1" stroke="#ED8C00"/>
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
            </div>
            <div class="filter_block">
                <div class="checkbox-block">
                    <span class="input-label input-label--mb">Тип объекта</span>
                    <div class="checkbox-w-icon-group">
                        <? foreach ($arResult['OBJECT_TYPES'] as $i => $objectType): ?>
                            <div class="checkbox-w-icon checkbox-w-icon">
                                <input type="checkbox" id="checkbox_<?= $i ?>"
                                       data-object-type-id="<?= $objectType['XML_ID'] ?>" <?= $objectType['CHECKED'] ? 'checked' : '' ?>>
                                <label for="checkbox_<?= $i ?>">
                                    <div class="checkbox-w-icon_text">
                                        <? if ($objectType['ICON']): ?>
                                            <div class="checkbox-w-icon_icon">
                                                <img src="<?= $objectType['ICON'] ?>"
                                                     alt="<?= $objectType['NAME'] ?>">
                                            </div>
                                        <? endif; ?>
                                        <span class="checkbox-w-icon_title"><?= $objectType['NAME'] ?></span>
                                    </div>
                                </label>
                            </div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="filter_block">
                <div class="input-counter-block">
                    <span class="input-label">Количество гостей</span>
                    <div class="input-counter">
                        <button class="input-counter_btn" type="button" data-input-id="guest-quantity"
                                data-action="minus">
                            <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                            </svg>
                        </button>
                        <input type="text" class="input-counter_input"
                               value="<?= $arResult['FILTER_DATA']['GUEST_COUNT'] ? $arResult['FILTER_DATA']['GUEST_COUNT'] : '1' ?>"
                               id="guest-quantity">
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
            </div>
            <div class="filter_block">
                <div class="input-date-block">
                    <span class="input-label input-label--mb">Доступные даты (заезд-выезд)</span>
                    <div class="input-dates js-input-date-group">
                        <input type="text" class="input-date"
                               id="arrival-date" <?= $arResult['FILTER_DATA']['A_DATE'] ? 'value="' . $arResult['FILTER_DATA']['A_DATE'] . '"' : '' ?>>
                        <input type="text" class="input-date second-range-input"
                               id="departure-date" <?= $arResult['FILTER_DATA']['D_DATE'] ? 'value="' . $arResult['FILTER_DATA']['D_DATE'] . '"' : '' ?>>
                    </div>
                </div>
            </div>
        </div>
        <button class="primary-btn" name="apply-filter">Применить фильтр</button>
        <button class="btn-reset" type="reset" id="reset-filter-button">
            <span>Сбросить</span>
            <svg width="11" height="13" viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="1.94727" width="1.34353" height="14.1313" transform="rotate(-45 0 1.94727)"
                      fill="#ED8C00"/>
                <rect x="1.00781" y="12.0244" width="1.34353" height="14.1313"
                      transform="rotate(-135 1.00781 12.0244)"
                      fill="#ED8C00"/>
            </svg>
        </button>
    </form>
</div>
