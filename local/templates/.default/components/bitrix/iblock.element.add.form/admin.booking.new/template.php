<?
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
$this->setFrameMode(false);

$APPLICATION->SetTitle("Бронирование");

if (!empty($arResult["ERRORS"])):?>
    <? ShowError(implode("<br />", $arResult["ERRORS"])) ?>
<?endif;
if ($arResult["MESSAGE"] <> ''):?>
    <? ShowNote($arResult["MESSAGE"]) ?>
<? endif ?>
<style>
    @media print {
        #panel, .lk_aside, .lk-head, .tabs, .group-btn {
            display: none;
        }
    }
</style>
<div class="tabs-block">
    <div class="tabs">
        <button class="tab active" type="button" data-id="2">Параметры бронирования</button>
        <button class="tab" type="button" data-id="3">Квитанция</button>
    </div>
    <div class="tabs-content">
        <div class="tabs-content_item active" data-id="2">
            <div class="preloader preloader--fixed">
                <div class="preloader_text">
                    <img src="<?= ASSETS ?>images/preloader.svg" alt="" class="preloader_icon">
                    <span class="preloader_title">Подождите, идет загрузка</span>
                </div>
            </div>
            <form name="iblock_add" action="<?= POST_FORM_ACTION_URI ?>" class="lk-booking-form" method="post"
                  enctype="multipart/form-data">
                <?= bitrix_sessid_post() ?>
                <? if ($arParams["MAX_FILE_SIZE"] > 0): ?>
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?= $arParams["MAX_FILE_SIZE"] ?>"/>
                <? endif ?>
                <div class="form-block form-block--mb30">
                    <div class="input-group">
                        <div class="select-block select-block--md" id="location-filter-block">
                            <span class="input-label input-label--mb input-label--gray">Локация</span>
                            <div class="custom-select" id="object-location-filter">
                                <div class="custom-select_head">
                                    <span class="custom-select_title">Выберите локацию</span>
                                    <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                                    </svg>
                                </div>
                                <div class="custom-select_body">
                                    <? foreach ($arResult['LOCATIONS'] as $location): ?>
                                        <div class="custom-select_item"
                                             data-id="<?= $location['ID'] ?>"><?= $location['NAME'] ?></div>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="select-block select-block--md" id="category-filter-block">
                            <span class="input-label input-label--mb input-label--gray">Категория</span>
                            <div class="custom-select" id="object-category-filter">
                                <div class="custom-select_head">
                                    <span class="custom-select_title">Выберите категорию</span>
                                    <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                                    </svg>
                                </div>
                                <div class="custom-select_body">
                                    <? foreach ($arResult['SECTIONS'] as $section): ?>
                                        <div class="custom-select_item"
                                             data-id="<?= $section['ID'] ?>"><?= $section['NAME'] ?></div>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="m-input-dates m-input-dates--md js-input-date-group" id="date-filter-block">
                            <div class="m-input-date-block">
                                <label for="arrival-date-filter" class="input-label">Дата заезда</label>
                                <input type="text" class="input-date" id="arrival-date-filter">
                            </div>
                            <div class="m-input-date-block">
                                <label for="departure-date-filter" class="input-label">Дата выезда</label>
                                <input type="text" class="input-date second-range-input" id="departure-date-filter">
                            </div>
                        </div>
                        <div class="m-input-dates m-input-dates--md" id="time-filter-block">
                            <div class="m-input-date-block">
                                <label for="filter-arr-time" class="input-label">Время заезда</label>
                                <div class="custom-select" id="filter-arr-time">
                                    <div class="custom-select_head">
                                        <span class="custom-select_title">Время заезда</span>
                                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                             fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                                        </svg>
                                    </div>
                                    <div class="custom-select_body">
                                        <div class="custom-select_item" data-id="8:00">8:00</div>
                                        <div class="custom-select_item" data-id="9:00">9:00</div>
                                        <div class="custom-select_item" data-id="10:00">10:00</div>
                                        <div class="custom-select_item" data-id="11:00">11:00</div>
                                        <div class="custom-select_item" data-id="12:00">12:00</div>
                                        <div class="custom-select_item" data-id="13:00">13:00</div>
                                        <div class="custom-select_item" data-id="14:00">14:00</div>
                                        <div class="custom-select_item" data-id="15:00">15:00</div>
                                        <div class="custom-select_item" data-id="16:00">16:00</div>
                                        <div class="custom-select_item" data-id="17:00">17:00</div>
                                        <div class="custom-select_item" data-id="18:00">18:00</div>
                                        <div class="custom-select_item" data-id="19:00">19:00</div>
                                        <div class="custom-select_item" data-id="20:00">20:00</div>
                                        <div class="custom-select_item" data-id="21:00">21:00</div>
                                        <div class="custom-select_item" data-id="22:00">22:00</div>
                                        <div class="custom-select_item" data-id="23:00">23:00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="m-input-date-block">
                                <label for="filter-dep-time" class="input-label">Время выезда</label>
                                <div class="custom-select" id="filter-dep-time">
                                    <div class="custom-select_head">
                                        <span class="custom-select_title">Время выезда</span>
                                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                             fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                                        </svg>
                                    </div>
                                    <div class="custom-select_body">
                                        <div class="custom-select_item" data-id="8:00">8:00</div>
                                        <div class="custom-select_item" data-id="9:00">9:00</div>
                                        <div class="custom-select_item" data-id="10:00">10:00</div>
                                        <div class="custom-select_item" data-id="11:00">11:00</div>
                                        <div class="custom-select_item" data-id="12:00">12:00</div>
                                        <div class="custom-select_item" data-id="13:00">13:00</div>
                                        <div class="custom-select_item" data-id="14:00">14:00</div>
                                        <div class="custom-select_item" data-id="15:00">15:00</div>
                                        <div class="custom-select_item" data-id="16:00">16:00</div>
                                        <div class="custom-select_item" data-id="17:00">17:00</div>
                                        <div class="custom-select_item" data-id="18:00">18:00</div>
                                        <div class="custom-select_item" data-id="19:00">19:00</div>
                                        <div class="custom-select_item" data-id="20:00">20:00</div>
                                        <div class="custom-select_item" data-id="21:00">21:00</div>
                                        <div class="custom-select_item" data-id="22:00">22:00</div>
                                        <div class="custom-select_item" data-id="23:00">23:00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="select-block">
                            <span class="input-label input-label--mb input-label--gray">Период бронирования</span>
                            <div class="radio-group" id="filter-period-block">
                                <div class="radio">
                                    <input type="radio" id="filter-period-couple" data-period="couple"
                                           name="filter-period" checked>
                                    <label for="filter-period-couple">
                                        <div class="radio_text">Сутки</div>
                                    </label>
                                </div>
                                <div class="radio">
                                    <input type="radio" id="filter-period-day" data-period="day" name="filter-period">
                                    <label for="filter-period-day">
                                        <div class="radio_text">День</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button class="gray-btn gray-btn--lg" id="set-filter">Применить фильтр</button>
                    </div>
                </div>
                <div id="object-select-block">
                    <? if (count($arResult['SECTION_OBJECTS']) > 10): ?>
                        <div class="form-block">
                            <h3 class="form-block_title">Выберите доступный объект</h3>
                            <div class="select-block select-block--lg">
                                <div class="custom-select" id="object-select">
                                    <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-selected-id="">Выберите доступный объект</span>
                                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                             fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                                        </svg>
                                    </div>
                                    <div class="custom-select_body">
                                        <? foreach ($arResult['SECTION_OBJECTS'] as $object): ?>
                                            <div class="custom-select_item"
                                                 data-id="<?= $object['ID'] ?>"
                                                 data-time-interval="<?= $object['TIME_INTERVAL']['TYPE'] ?>"
                                                 data-time-value="<?= $object['TIME_INTERVAL']['VALUE'] ?>"
                                                 data-time-limit-value="<?= $object['TIME_LIMIT'] ?>"
                                                 data-car-possibility-value="<?= $object['CAR_POSSIBILITY'] ?>"
                                                 data-car-capacity-value="<?= $object['CAR_CAPACITY'] ?>"
                                            ><?= $object['NAME'] ?></div>
                                        <? endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? else: ?>
                        <div class="form-block">
                            <h3 class="form-block_title">Выберите доступный объект</h3>
                            <div class="radio-list" id="objects-list">
                                <? foreach ($arResult['SECTION_OBJECTS'] as $object): ?>
                                    <div class="radio">
                                        <input type="radio" id="radio_<?= $object['ID'] ?>"
                                               name="PROPERTY[21][0][VALUE]"
                                               value="<?= $object['ID'] ?>"
                                               data-time-interval="<?= $object['TIME_INTERVAL']['TYPE'] ?>"
                                               data-time-value="<?= $object['TIME_INTERVAL']['VALUE'] ?>"
                                               data-time-limit-value="<?= $object['TIME_LIMIT'] ?>"
                                               data-car-possibility-value="<?= $object['CAR_POSSIBILITY'] ?>"
                                               data-car-capacity-value="<?= $object['CAR_CAPACITY'] ?>"
                                        >
                                        <label for="radio_<?= $object['ID'] ?>">
                                            <div class="radio_text"><?= $object['NAME'] ?></div>
                                        </label>

                                    </div>
                                <? endforeach; ?>
                            </div>
                        </div>
                    <? endif; ?>
                </div>
                <div id="booking-params" style="display: none">
                    <div id="booking-date-time-block">
                        <div class="form-block form-block--mb30">
                            <h3 class="form-block_title">Временной интервал</h3>
                            <div id="date-time-select-block">
                                <div class="input-group">
                                    <div class="radio-group" id="time-select-radio">
                                        <div class="radio" id="time-select-couple">
                                            <input type="radio" id="radio_07" data-period="couple" name="radio" checked>
                                            <label for="radio_07">
                                                <div class="radio_text">На несколько суток</div>
                                            </label>
                                        </div>
                                        <div class="radio" id="time-select-day">
                                            <input type="radio" id="radio_08" data-period="day" name="radio">
                                            <label for="radio_08">
                                                <div class="radio_text">Дневное пребывание</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="input input--sm">
                                        <label for="" class="input-label">Дата оформления <span
                                                    class="color-red">*</span></label>
                                        <input type="text" value="<?= $arResult["DATE_INSERT"] ?>" class="input-date"
                                               required readonly>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <div class="m-input-dates m-input-dates--md js-input-date-group">
                                        <div class="m-input-date-block">
                                            <label for="" class="input-label">Дата заезда <span
                                                        class="color-red">*</span></label>
                                            <input type="text" class="input-date" name="PROPERTY[11][0][VALUE]"
                                                   size="25"
                                                   required
                                                   autocomplete="off">
                                        </div>
                                        <div class="m-input-date-block">
                                            <label for="" class="input-label">Дата выезда <span
                                                        class="color-red">*</span></label>
                                            <input type="text" class="input-date second-range-input"
                                                   name="PROPERTY[12][0][VALUE]"
                                                   size="25" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="m-input-dates m-input-dates--md">
                                        <div class="m-input-date-block">
                                            <label for="">Время заезда <span class="color-red">*</span></label>
                                            <div class="custom-select custom-select--sm" id="arrival-time-select">
                                                <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время заезда">Время заезда</span>
                                                    <svg class="custom-select_icon" width="14" height="8"
                                                         viewBox="0 0 14 8"
                                                         fill="none"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                                                    </svg>
                                                </div>
                                                <div class="custom-select_body">
                                                    <div class="custom-select_item" data-id="8:00">8:00</div>
                                                    <div class="custom-select_item" data-id="9:00">9:00</div>
                                                    <div class="custom-select_item" data-id="10:00">10:00</div>
                                                    <div class="custom-select_item" data-id="11:00">11:00</div>
                                                    <div class="custom-select_item" data-id="12:00">12:00</div>
                                                    <div class="custom-select_item" data-id="13:00">13:00</div>
                                                    <div class="custom-select_item" data-id="14:00">14:00</div>
                                                    <div class="custom-select_item" data-id="15:00">15:00</div>
                                                    <div class="custom-select_item" data-id="16:00">16:00</div>
                                                    <div class="custom-select_item" data-id="17:00">17:00</div>
                                                    <div class="custom-select_item" data-id="18:00">18:00</div>
                                                    <div class="custom-select_item" data-id="19:00">19:00</div>
                                                    <div class="custom-select_item" data-id="20:00">20:00</div>
                                                    <div class="custom-select_item" data-id="21:00">21:00</div>
                                                    <div class="custom-select_item" data-id="22:00">22:00</div>
                                                    <div class="custom-select_item" data-id="23:00">23:00</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="m-input-date-block">
                                            <label for="">Время выезда <span class="color-red">*</span></label>
                                            <div class="custom-select custom-select--sm" id="departure-time-select">
                                                <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время выезда">Время выезда</span>
                                                    <svg class="custom-select_icon" width="14" height="8"
                                                         viewBox="0 0 14 8"
                                                         fill="none"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                                                    </svg>
                                                </div>
                                                <div class="custom-select_body">
                                                    <div class="custom-select_item" data-id="8:00">8:00</div>
                                                    <div class="custom-select_item" data-id="9:00">9:00</div>
                                                    <div class="custom-select_item" data-id="10:00">10:00</div>
                                                    <div class="custom-select_item" data-id="11:00">11:00</div>
                                                    <div class="custom-select_item" data-id="12:00">12:00</div>
                                                    <div class="custom-select_item" data-id="13:00">13:00</div>
                                                    <div class="custom-select_item" data-id="14:00">14:00</div>
                                                    <div class="custom-select_item" data-id="15:00">15:00</div>
                                                    <div class="custom-select_item" data-id="16:00">16:00</div>
                                                    <div class="custom-select_item" data-id="17:00">17:00</div>
                                                    <div class="custom-select_item" data-id="18:00">18:00</div>
                                                    <div class="custom-select_item" data-id="19:00">19:00</div>
                                                    <div class="custom-select_item" data-id="20:00">20:00</div>
                                                    <div class="custom-select_item" data-id="21:00">21:00</div>
                                                    <div class="custom-select_item" data-id="22:00">22:00</div>
                                                    <div class="custom-select_item" data-id="23:00">23:00</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-block">
                        <h3 class="form-block_title">Параметры заказа</h3>
                        <div class="input-group">
                            <div class="input input--md">
                                <label for="" class="input-label">Имя <span class="color-red">*</span></label>
                                <input type="text" name="PROPERTY[9][0]" size="30" required>
                            </div>
                            <div class="input input--md">
                                <label for="" class="input-label">Фамилия <span class="color-red">*</span></label>
                                <input type="text" name="PROPERTY[10][0]" size="30" required>
                            </div>
                            <div class="radio-group">
                                <div class="radio">
                                    <input type="radio"
                                           id="property_1"
                                           name="PROPERTY[15]"
                                           value="1"
                                    >
                                    <label for="property_1">
                                        <div class="radio_text">Есть разрешение</div>
                                    </label>
                                </div>
                                <div class="radio">
                                    <input type="radio"
                                           id="property_2"
                                           name="PROPERTY[15]"
                                           value="2"
                                           checked
                                    >
                                    <label for="property_2">
                                        <div class="radio_text">Разрешение не получено</div>
                                    </label>
                                </div>
                            </div>
                            <div class="m-booking-form-block">
                                <span class="m-booking-form-block_title">Укажите количество гостей </span>
                                <div class="input-counter-group">
                                    <div class="input-counter-item">
                                        <label for=""
                                               class="input-label input-label--mb input-label--gray">Всего
                                            <span class="color-red">*</span>
                                        </label>
                                        <div class="input-counter">
                                            <button class="input-counter_btn" type="button"
                                                    data-input-id="adult-quantity"
                                                    data-action="minus">
                                                <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                                </svg>
                                            </button>
                                            <input type="text" class="input-counter_input" id="adult-quantity"
                                                   name="PROPERTY[16][0]"
                                                   value="0" required>

                                            <button class="input-counter_btn" type="button"
                                                    data-input-id="adult-quantity"
                                                    data-action="plus">
                                                <svg width="13" height="13" viewBox="0 0 13 13" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0 6H13V7H0V6Z" fill="#313131"/>
                                                    <path d="M6 13L6 4.37112e-08L7 0L7 13H6Z" fill="#313131"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="input-counter-item">
                                        <label for=""
                                               class="input-label input-label--mb input-label--gray">Из них льготников
                                            <span class="color-red">*</span>
                                        </label>
                                        <div class="input-counter">
                                            <button class="input-counter_btn" type="button"
                                                    data-input-id="beneficiaries-quantity"
                                                    data-action="minus">
                                                <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                                </svg>
                                            </button>
                                            <input type="text" class="input-counter_input" id="beneficiaries-quantity"
                                                   name="PROPERTY[17][0]"
                                                   value="0" required>
                                            <button class="input-counter_btn" type="button"
                                                    data-input-id="beneficiaries-quantity"
                                                    data-action="plus">
                                                <svg width="13" height="13" viewBox="0 0 13 13" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0 6H13V7H0V6Z" fill="#313131"/>
                                                    <path d="M6 13L6 4.37112e-08L7 0L7 13H6Z" fill="#313131"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <button class="btn-dop-info js-open-r-modal" data-name="modal-benefit"
                                            type="button">
                                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                    d="M9.90938 14.875H11.2219V9.625H9.90938V14.875ZM10.4996 8.00625C10.704 8.00625 10.8755 7.93917 11.0141 7.805C11.1526 7.67083 11.2219 7.50458 11.2219 7.30625C11.2219 7.09552 11.1527 6.91888 11.0145 6.77633C10.8762 6.63378 10.7048 6.5625 10.5004 6.5625C10.296 6.5625 10.1245 6.63378 9.98594 6.77633C9.8474 6.91888 9.77812 7.09552 9.77812 7.30625C9.77812 7.50458 9.84726 7.67083 9.98554 7.805C10.1238 7.93917 10.2952 8.00625 10.4996 8.00625ZM10.5058 19.25C9.29928 19.25 8.16543 19.0203 7.10426 18.5609C6.04309 18.1016 5.1151 17.4745 4.32031 16.6797C3.52552 15.8849 2.89844 14.9564 2.43906 13.8941C1.97969 12.8319 1.75 11.6968 1.75 10.4891C1.75 9.28129 1.97969 8.14627 2.43906 7.08402C2.89844 6.02176 3.52552 5.09687 4.32031 4.30938C5.1151 3.52188 6.04363 2.89844 7.1059 2.43906C8.16815 1.97969 9.30316 1.75 10.5109 1.75C11.7187 1.75 12.8537 1.97969 13.916 2.43906C14.9782 2.89844 15.9031 3.52188 16.6906 4.30938C17.4781 5.09687 18.1016 6.02292 18.5609 7.0875C19.0203 8.15208 19.25 9.28764 19.25 10.4942C19.25 11.7007 19.0203 12.8346 18.5609 13.8957C18.1016 14.9569 17.4781 15.8836 16.6906 16.6757C15.9031 17.4678 14.9771 18.0949 13.9125 18.5569C12.8479 19.019 11.7124 19.25 10.5058 19.25Z"
                                                    fill="#EA9A00"/>
                                        </svg>
                                        <span>Категории льготников**</span>
                                    </button>
                                </div>
                            </div>
                            <div class="input input--md">
                                <label for="" class="input-label">E-mail</label>
                                <input type="email" name="PROPERTY[19][0]" size="30">
                            </div>
                            <div class="input input--md">
                                <label for="" class="input-label">Телефон <span class="color-red">*</span></label>
                                <input type="tel" name="PROPERTY[20][0]" size="30" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-block" id="car-possibility-block" style="display: none">
                        <h3 class="form-block_title">Наличие автомобиля</h3>
                        <div class="input-group">
                            <div class="radio-group" id="car-radio-group">
                                <div class="radio">
                                    <input type="radio"
                                           id="car-yes"
                                           name="car-radio"
                                           value="1"
                                    >
                                    <label for="car-yes">
                                        <div class="radio_text">Есть автомобиль</div>
                                    </label>
                                </div>
                                <div class="radio">
                                    <input type="radio"
                                           id="car-no"
                                           name="car-radio"
                                           value="0"
                                           checked
                                    >
                                    <label for="car-no">
                                        <div class="radio_text">Нет автомобиля</div>
                                    </label>
                                </div>
                            </div>
                            <div id="car-detail-hidden" style="display: none">
                                <div class="m-booking-form-block">
                                    <span class="m-booking-form-block_title">Укажите количество автомобилей</span>
                                    <div class="input-counter-group">
                                        <div class="input-counter-item">
                                            <label for="" class="input-label input-label--mb input-label--gray">
                                                Всего
                                            </label>
                                            <div class="input-counter">
                                                <button class="input-counter_btn" type="button"
                                                        data-input-id="car-quantity"
                                                        data-action="minus">
                                                    <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                                    </svg>
                                                </button>
                                                <input type="text" class="input-counter_input" id="car-quantity"
                                                       name=""
                                                       value="1">
                                                <button class="input-counter_btn" type="button"
                                                        data-input-id="car-quantity"
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
                                </div>
                                <div id="cars-list">
                                    <div class="input">
                                        <label for="PROPERTY[<?= GUEST_CARS ?>][0]" class="input-label">Номер автомобиля
                                            1</label>
                                        <input type="text" name="PROPERTY[<?= GUEST_CARS ?>][0]" size="30">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-block">
                        <div class="price">
                            <span class="price_title">Стоимость аренды</span>
                            <div class="price_value"><span id="booking-sum-value">0</span> ₽</div>
                        </div>
                    </div>

                    <div class="group-btn">
                        <button class="primary-btn group-btn_item" type="button" data-tab-action="order">
                            Забронировать
                        </button>
                    </div>
                </div>

                <input type="hidden" name="iblock_submit" value="Сохранить">
                <input type="hidden" name="PROPERTY[22][0]" value="<?= $arResult['MANAGER_FIO'] ?>">
                <input type="hidden" name="PROPERTY[43][0]" value="">
                <input type="hidden" name="PROPERTY[44][0]" value="">
                <input type="hidden" name="guest-car-prop-number" value="<?= GUEST_CARS ?>">
                <input type="hidden" name="CAR_CAPACITY" value="">
                <div class="form-warn-message"></div>
            </form>
        </div>
        <div class="tabs-content_item" data-id="3">
            <div class="preloader preloader--fixed">
                <div class="preloader_text">
                    <img src="<?= ASSETS ?>images/preloader.svg" alt="" class="preloader_icon">
                    <span class="preloader_title">Подождите, идет загрузка</span>
                </div>
            </div>
            <div class="blank blank--center" id="blank"></div>
            <div class="blank-map-section">
                <div class="blank-map" id="map"></div>
            </div>
            <div class="group-btn">
                <button class="primary-btn group-btn_item" type="button" id="edit-order" data-order-id="">
                    Изменить заказ
                </button>
                <button class="primary-btn group-btn_item" type="button" id="print-blank">Печать бланка</button>
            </div>
        </div>
    </div>
</div>