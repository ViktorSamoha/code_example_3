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

$APPLICATION->SetTitle("Редактирование заказа №" . $arResult['ELEMENT']['ID']);

$isFast = false;
if (isset($_REQUEST['fast'])) {
    if ($_REQUEST['fast'] == 'Y') {
        $isFast = true;
    }
}

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
            <form name="iblock_add" action="<?= POST_FORM_ACTION_URI ?>" class="lk-booking-form" method="post"
                  enctype="multipart/form-data">
                <?= bitrix_sessid_post() ?>
                <? if ($arParams["MAX_FILE_SIZE"] > 0): ?>
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?= $arParams["MAX_FILE_SIZE"] ?>"/>
                <? endif ?>

                <div class="form-block form-block--mb30">
                    <div class="input-group">
                        <div class="select-block select-block--lg" id="location-filter-block">
                            <span class="input-label input-label--mb input-label--gray">Локация</span>
                            <div class="custom-select" id="object-location-filter">
                                <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-selected-id="<?= $arResult['ELEMENT_LOCATION']['ID'] ?>"><?= $arResult['ELEMENT_LOCATION']['NAME'] ?></span>
                                    <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                                    </svg>
                                </div>
                                <div class="custom-select_body">
                                    <? foreach ($arResult['SECTIONS']['LOCATION'] as $location): ?>
                                        <div class="custom-select_item"
                                             data-id="<?= $location['ID'] ?>"><?= $location['NAME'] ?></div>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="input-date-block input-date-block--sm">
                            <span class="input-label input-label--mb input-label--gray">Даты (заезд-выезд)</span>
                            <div class="input-dates js-input-date-group">
                                <input type="text" class="input-date" name="arrival-date-input" autocomplete="off">
                                <input type="text" class="input-date second-range-input" name="departure-date-input"
                                       autocomplete="off">
                            </div>
                        </div>
                        <div class="select-block select-block--lg">
                            <span class="input-label input-label--mb input-label--gray">Категория</span>
                            <div class="custom-select" id="object-category-filter">
                                <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-selected-id="<?= $arResult['ELEMENT_SECTION']['ID'] ?>"><?= $arResult['ELEMENT_SECTION']['NAME'] ?></span>
                                    <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                                    </svg>
                                </div>
                                <div class="custom-select_body">
                                    <? foreach ($arResult['SECTIONS']['TYPE'] as $section): ?>
                                        <div class="custom-select_item"
                                             data-id="<?= $section['ID'] ?>"><?= $section['NAME'] ?></div>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="object-select-block">
                    <? if (count($arResult['SECTION_OBJECTS']) > 10): ?>
                        <div class="form-block form-block--mb30">
                            <h3 class="form-block_title">Выберите доступный объект</h3>
                            <div class="select-block select-block--lg">
                                <div class="custom-select" id="object-select">
                                    <div class="custom-select_head">
                                        <? if (!empty($arResult['SELECTED_OBJECT'])): ?>
                                            <span class="custom-select_title"
                                                  data-selected-id="<?= $arResult['SELECTED_OBJECT']['ID'] ?>"><?= $arResult['SELECTED_OBJECT']['NAME'] ?></span>
                                        <? else: ?>
                                            <span class="custom-select_title" data-selected-id="">Выберите доступный объект</span>
                                        <? endif; ?>
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
                                               name="PROPERTY[21][0][VALUE]" <?= $object['CHECKED'] ? "checked" : "" ?>
                                               value="<?= $object['ID'] ?>"
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
                <div id="booking-params">
                    <div class="form-block form-block--mb30">
                        <h3 class="form-block_title">Временной интервал</h3>
                        <div id="date-time-select-block">
                            <? if ($arResult['TIME_UNLIMIT_OBJECT'] == 'Y'): ?>
                                <div class="form-block form-block--mb30">
                                    <h3 class="form-block_title">Дата и время заезда</h3>
                                    <div id="date-time-select-block">
                                        <div class="input-group">
                                            <div class="input">
                                                <label for="" class="input-label">Дата оформления <span
                                                            class="color-red">*</span></label>
                                                <input type="text" value="<?= $arResult["DATE_INSERT"] ?>"
                                                       class="input-date"
                                                       required readonly>
                                            </div>
                                            <div class="m-input-dates m-input-dates--md">
                                                <div class="m-input-date-block">
                                                    <label for="" class="input-label">Дата заезда</label>
                                                    <input type="text" class="input-date" name="PROPERTY[11][0][VALUE]"
                                                           size="25"
                                                           required
                                                           value="<?= $arResult["ELEMENT_PROPERTIES"][11][0]['VALUE'] ?>"
                                                           autocomplete="off">
                                                </div>
                                                <div class="m-input-date-block">
                                                    <label for="" class="input-label">Время заезда</label>
                                                    <div class="custom-select custom-select--sm" id="time-select">
                                                        <div class="custom-select_head">
                                                    <span class="custom-select_title"
                                                          data-default-value="Время заезда"
                                                          data-selected-id="<?= $arResult["ELEMENT_PROPERTIES"][13][0]['VALUE'] ?>"><?= $arResult["ELEMENT_PROPERTIES"][13][0]['VALUE'] ?></span>
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
                            <? else: ?>
                                <div class="input-group">
                                    <div class="radio-group" id="time-select-radio">
                                        <div class="radio">
                                            <input type="radio" id="radio_07" data-period="couple"
                                                   name="radio" <?= isset($arResult['RENT_PERIOD']) ? ($arResult['RENT_PERIOD'] == 'couple' ? 'checked' : '') : 'checked' ?>>
                                            <label for="radio_07">
                                                <div class="radio_text">На несколько суток</div>
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <input type="radio" id="radio_08" data-period="day"
                                                   name="radio" <?= isset($arResult['RENT_PERIOD']) ? ($arResult['RENT_PERIOD'] == 'day' ? 'checked' : '') : '' ?>>
                                            <label for="radio_08">
                                                <div class="radio_text">Дневное пребывание</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="input input--sm">
                                        <label for="" class="input-label">Дата оформления <span
                                                    class="color-red">*</span></label>
                                        <input type="text" value="<?= $arResult["DATE_INSERT"] ?>" class="input-date"
                                               readonly>
                                    </div>
                                </div>

                                <div class="input-group">
                                    <div class="m-input-dates m-input-dates--md js-input-date-group">
                                        <div class="m-input-date-block">
                                            <label for="" class="input-label">Дата заезда <span
                                                        class="color-red">*</span></label>
                                            <input type="text" class="input-date" name="PROPERTY[11][0][VALUE]"
                                                   size="25"
                                                   value="<?= $arResult["ELEMENT_PROPERTIES"][11][0]['VALUE'] ?>"
                                                   autocomplete="off">
                                        </div>
                                        <div class="m-input-date-block">
                                            <label for="" class="input-label">Дата выезда <span
                                                        class="color-red">*</span></label>
                                            <input type="text" class="input-date second-range-input"
                                                   name="PROPERTY[12][0][VALUE]"
                                                   size="25"
                                                   value="<?= $arResult["ELEMENT_PROPERTIES"][12][0]['VALUE'] ?>"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="m-input-dates m-input-dates--md">

                                        <!--<div class="m-input-date-block">
                                    <label for="" class="input-label">Время заезда <span
                                                class="color-red">*</span></label>
                                    <input type="text" name="PROPERTY[13][0]" class="input-time" size="30"
                                           value="<? /*= $arResult["ELEMENT_PROPERTIES"][13][0]['VALUE'] */ ?>">
                                </div>
                                <div class="m-input-date-block">
                                    <label for="" class="input-label">Время выезда <span
                                                class="color-red">*</span></label>
                                    <input type="text" name="PROPERTY[14][0]" class="input-time" size="30"
                                           value="<? /*= $arResult["ELEMENT_PROPERTIES"][14][0]['VALUE'] */ ?>">
                                </div>-->

                                        <div class="m-input-date-block">
                                            <label for="">Время заезда</label>
                                            <div class="custom-select custom-select--sm" id="arrival-time-select">
                                                <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время заезда"
                                          data-selected-id="<?= $arResult["ELEMENT_PROPERTIES"][13][0]['VALUE'] ?>"><?= $arResult["ELEMENT_PROPERTIES"][13][0]['VALUE'] ?></span>
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
                                            <label for="">Время выезда</label>
                                            <!-- <input type="text" class="input-time"> -->
                                            <div class="custom-select custom-select--sm" id="departure-time-select">
                                                <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время выезда"
                                          data-selected-id="<?= $arResult["ELEMENT_PROPERTIES"][14][0]['VALUE'] ?>"><?= $arResult["ELEMENT_PROPERTIES"][14][0]['VALUE'] ?></span>
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
                            <? endif; ?>
                        </div>
                    </div>
                    <div class="form-block">
                        <h3 class="form-block_title">Параметры заказа</h3>
                        <div class="input-group">
                            <? if (!$isFast): ?>
                                <div class="input input--md">
                                    <label for="" class="input-label">Имя <span
                                                class="color-red">*</span></label>
                                    <input type="text" name="PROPERTY[9][0]" size="30"
                                           value="<?= $arResult["ELEMENT_PROPERTIES"][9][0]['VALUE'] ?>">
                                </div>
                                <div class="input input--md">
                                    <label for="" class="input-label">Фамилия <span
                                                class="color-red">*</span></label>
                                    <input type="text" name="PROPERTY[10][0]" size="30"
                                           value="<?= $arResult["ELEMENT_PROPERTIES"][10][0]['VALUE'] ?>">
                                </div>
                            <? endif; ?>
                            <div class="radio-group">
                                <div class="radio">
                                    <input type="radio"
                                           id="property_1"
                                           name="PROPERTY[15]"
                                           value="1"
                                        <?= $arResult["ELEMENT_PROPERTIES"][15][0]['VALUE'] == "1" ? 'checked' : '' ?>
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
                                        <?= $arResult["ELEMENT_PROPERTIES"][15][0]['VALUE'] == "2" ? 'checked' : '' ?>
                                    >
                                    <label for="property_2">
                                        <div class="radio_text">Разрешение не получено</div>
                                    </label>
                                </div>
                            </div>
                            <div class="m-booking-form-block">
                                <span class="m-booking-form-block_title">Укажите количество гостей</span>
                                <div class="input-counter-group">
                                    <div class="input-counter-item">
                                        <label for=""
                                               class="input-label input-label--mb input-label--gray">Всего <span
                                                    class="color-red">*</span></label>
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
                                                   value="<?= $arResult["ELEMENT_PROPERTIES"][16][0]['VALUE'] ?>">

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
                                               class="input-label input-label--mb input-label--gray">Из них
                                            льготников</label>
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
                                                   value="<?= $arResult["ELEMENT_PROPERTIES"][17][0]['VALUE'] ?>">
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
                            <? if (!$isFast): ?>
                                <div class="input input--md">
                                    <label for="" class="input-label">E-mail</label>
                                    <input type="email" name="PROPERTY[19][0]" size="30"
                                           value="<?= $arResult["ELEMENT_PROPERTIES"][19][0]['VALUE'] ?>">
                                </div>
                                <div class="input input--md">
                                    <label for="" class="input-label">Телефон <span
                                                class="color-red">*</span></label>
                                    <input type="tel" name="PROPERTY[20][0]" size="30"
                                           value="<?= $arResult["ELEMENT_PROPERTIES"][20][0]['VALUE'] ?>" required>
                                </div>
                            <? endif; ?>
                        </div>
                    </div>
                    <div class="form-block"
                         id="car-possibility-block" <?= $arResult["CAR_POSSIBILITY"] == 'Y' ? '' : 'style="display: none"' ?>>
                        <h3 class="form-block_title">Наличие автомобиля</h3>
                        <div class="input-group">
                            <div class="radio-group" id="car-radio-group">
                                <div class="radio">
                                    <input type="radio"
                                           id="car-yes"
                                           name="car-radio"
                                           value="1"
                                        <?= $arResult["SHOW_CAR_BLOCK"] ? 'checked' : '' ?>
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
                                        <?= !$arResult["SHOW_CAR_BLOCK"] ? 'checked' : '' ?>
                                    >
                                    <label for="car-no">
                                        <div class="radio_text">Нет автомобиля</div>
                                    </label>
                                </div>
                            </div>
                            <div id="car-detail-hidden" <?= $arResult["SHOW_CAR_BLOCK"] ? '' : 'style="display: none"' ?>>
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
                                                       value="<?= is_array($arResult["ELEMENT_PROPERTIES"][GUEST_CARS]) ? count($arResult["ELEMENT_PROPERTIES"][GUEST_CARS]) : '1' ?>"
                                                >
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
                                    <? if (is_array($arResult["ELEMENT_PROPERTIES"][GUEST_CARS])): ?>
                                        <? foreach ($arResult["ELEMENT_PROPERTIES"][GUEST_CARS] as $i => $carIdValue): ?>
                                            <div class="input">
                                                <label for="PROPERTY[<?= GUEST_CARS ?>][<?= $i ?>]" class="input-label">Номер
                                                    автомобиля
                                                    <?= $i + 1 ?></label>
                                                <input type="text" name="PROPERTY[<?= GUEST_CARS ?>][<?= $i ?>]"
                                                       size="30"
                                                       value="<?= $carIdValue['VALUE'] ?>">
                                            </div>
                                        <? endforeach; ?>
                                    <? else: ?>
                                        <div class="input">
                                            <label for="PROPERTY[<?= GUEST_CARS ?>][0]" class="input-label">Номер
                                                автомобиля 1</label>
                                            <input type="text" name="PROPERTY[<?= GUEST_CARS ?>][0]" size="30"
                                                   value="<?= $arResult["ELEMENT_PROPERTIES"][GUEST_CARS][0]['VALUE'] ?>">
                                        </div>
                                    <? endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-block form-block--mb30">
                        <h3 class="form-block_title">Cтатус заказа</h3>
                        <div class="select-block select-block--lg">
                            <div class="custom-select" id="order-status-select">
                                <div class="custom-select_head">
                                    <? if (!empty($arResult["ELEMENT_PROPERTIES"][40][0]['VALUE'])): ?>
                                        <span class="custom-select_title"
                                              data-selected-id="<?= $arResult["ELEMENT_PROPERTIES"][40][0]['VALUE'] ?>">Оплачен</span>
                                    <? else: ?>
                                        <span class="custom-select_title"
                                              data-selected-id="">Выберите статус заказа</span>
                                    <? endif; ?>
                                    <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                                    </svg>
                                </div>
                                <div class="custom-select_body">
                                    <div class="custom-select_item" data-id="11">Оплачен</div>
                                    <div class="custom-select_item" data-id="false">Не оплачен</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-block">
                        <div class="price">
                            <span class="price_title">Стоимость аренды</span>
                            <div class="price_value">
                            <span id="order-sum-value">
                                <?= $arResult["ELEMENT_PROPERTIES"][32][0]['VALUE'] ?>
                            </span>
                                ₽
                            </div>
                        </div>
                    </div>
                    <div class="group-btn">
                        <button class="primary-btn group-btn_item" type="button" data-tab-action="order">
                            Сохранить изменения
                        </button>
                    </div>
                </div>

                <input type="hidden" name="PROPERTY[33][0]"
                       value="<?= $arResult['ELEMENT_PROPERTIES'][33][0]['VALUE'] ?>">
                <input type="hidden" name="PROPERTY[39][0]"
                       value="<?= $arResult['ELEMENT_PROPERTIES'][39][0]['VALUE'] ?>">
                <input type="hidden" name="iblock_submit" value="<?= GetMessage("IBLOCK_FORM_SUBMIT") ?>">
                <input type="hidden" name="PROPERTY[22][0]" value="<?= $arResult['MANAGER_FIO'] ?>">
                <input type="hidden" name="order_id" id="order_id" value="<?= $arResult['ELEMENT']['ID'] ?>">
                <input type="hidden" id="object_id" value="<?= $arResult['ELEMENT_PROPERTIES'][21][0]['VALUE'] ?>">
                <input type="hidden" id="arr_date" value="<?= $arResult['ELEMENT_PROPERTIES'][11][0]['VALUE'] ?>">
                <input type="hidden" id="dep_date" value="<?= $arResult['ELEMENT_PROPERTIES'][12][0]['VALUE'] ?>">
                <input type="hidden" name="PROPERTY[CHECK_PAYMENT]" value="N">
                <input type="hidden" name="time_limit_value" value="<?= $arResult['TIME_UNLIMIT_OBJECT'] ?>">
                <input type="hidden" name="guest-car-prop-number" value="<?= GUEST_CARS ?>">
                <input type="hidden" name="CAR_CAPACITY" value="<?= $arResult['CAR_CAPACITY'] ?>">
                <div class="form-warn-message"></div>
            </form>
        </div>
        <div class="tabs-content_item" data-id="3">
            <div class="blank blank--center" id="blank"></div>
            <div class="blank-map-section">
                <div class="blank-map" id="map"></div>
            </div>
            <div class="group-btn">
                <button class="primary-btn group-btn_item" type="button" id="edit-order"
                        data-order-id="" <?= $isFast ? 'data-booking-fast="true"' : '' ?>>
                    Изменить заказ
                </button>
                <button class="primary-btn group-btn_item" type="button" id="print-blank">Печать бланка</button>
            </div>
        </div>
    </div>
</div>
