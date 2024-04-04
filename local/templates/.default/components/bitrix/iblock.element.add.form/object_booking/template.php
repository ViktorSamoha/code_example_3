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

if (!empty($arResult["ERRORS"])):?>
    <? ShowError(implode("<br />", $arResult["ERRORS"])) ?>
<?endif;
if ($arResult["MESSAGE"] <> ''):?>
    <? ShowNote($arResult["MESSAGE"]) ?>
<? endif ?>
<div class="modal_block">
    <button class="modal-close-btn">
        <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect y="2.82843" width="4" height="42.0722" transform="rotate(-45 0 2.82843)" fill="white"/>
            <rect x="3" y="32.8284" width="4" height="42.0722" transform="rotate(-135 3 32.8284)" fill="white"/>
        </svg>
    </button>
    <div class="booking-block">
        <div class="booking-block_info">
            <h2 class="booking-block_title"></h2>
            <div class="modal-text">
                <p>(согласно гл. VI п. 22 Положения о в том числе в лесах и на акватории Нугушского
                    водохранилища
                    (Постановление СМ РСФСР от 11 сентября 1986 г. № 398), запрещается:</p>
                <ul>
                    <li>охота, нахождение с любыми видами оружия, незаконными орудиями для рыбной ловли;</li>
                    <li>использование авто-, мото- и водного транспорта без разрешения администрации парка;</li>
                    <li>применение ядохимикатов и мойка транспорта;</li>
                    <li>загрязнение и замусоривание территории;</li>
                    <li>организация туристических стоянок и разведение костров за пределами специально
                        предусмотренных для этого
                        мест;
                    </li>
                    <li>уничтожение и повреждение деревьев, кустарников, напочвенного растительного покрова.
                    </li>
                    <li>повреждение почвенного покрова и геологических обнажений (раскопки, копка, распашка
                        земли, пробуксовка и
                        т.п.);
                    </li>
                    <li>уничтожение и повреждение природных объектов, сооружений, плакатов, аншлагов,
                        указателей, межевых
                        знаков,
                        квартальных и природоохранных столбов, дорог и троп;
                    </li>
                    <li>использование ракетниц и петард.</li>
                    <li>отклонение от маршрута, указанного в разрешительных документах;</li>
                    <li>заезжать за ограждение на маршруте «Куперля»;</li>
                    <li>находиться в заповедной зоне.</li>
                </ul>

                <p>Кроме этого, граждане при пребывании в лесах обязаны соблюдать правила пожарной безопасности
                    в лесах,
                    правила
                    санитарной безопасности в лесах, правила лесовосстановления и правила ухода за лесами (ст.
                    11 Лесного
                    кодекса
                    РФ).</p>
                <p>Согласно Постановлению Правительства РФ от 30.06.2007 №417 «Об утверждении Правил пожарной
                    безопасности в
                    лесах» существуют следующие правила:</p>
                <p>(согласно гл. VI п. 22 Положения о(Постановление СМ РСФСР от 11 сентября 1986 г. № 398), запрещается:</p>
                <ul>
                    <li>охота, нахождение с любыми видами оружия, незаконными орудиями для рыбной ловли;</li>
                    <li>использование авто-, мото- и водного транспорта без разрешения администрации парка;</li>
                    <li>применение ядохимикатов и мойка транспорта;</li>
                    <li>загрязнение и замусоривание территории;</li>
                    <li>организация туристических стоянок и разведение костров за пределами специально
                        предусмотренных для этого
                        мест;
                    </li>
                    <li>уничтожение и повреждение деревьев, кустарников, напочвенного растительного покрова.
                    </li>
                    <li>повреждение почвенного покрова и геологических обнажений (раскопки, копка, распашка
                        земли, пробуксовка и
                        т.п.);
                    </li>
                    <li>уничтожение и повреждение природных объектов, сооружений, плакатов, аншлагов,
                        указателей, межевых
                        знаков,
                        квартальных и природоохранных столбов, дорог и троп;
                    </li>
                    <li>использование ракетниц и петард.</li>
                    <li>отклонение от маршрута, указанного в разрешительных документах;</li>
                    <li>заезжать за ограждение на маршруте «Куперля»;</li>
                    <li>находиться в заповедной зоне.</li>
                </ul>

                <p>Кроме этого, граждане при пребывании в лесах обязаны соблюдать правила пожарной безопасности
                    в лесах,
                    правила
                    санитарной безопасности в лесах, правила лесовосстановления и правила ухода за лесами (ст.
                    11 Лесного
                    кодекса
                    РФ).</p>
                <p>Согласно Постановлению Правительства РФ от 30.06.2007 №417 «Об утверждении Правил пожарной
                    безопасности в
                    лесах» существуют следующие правила:</p>
            </div>
            <div class="booking-block_btns">
                <div class="checkbox">
                    <input type="checkbox" id="checkbox_21">
                    <label for="checkbox_21">
                        <div class="checkbox_text">Согласен с условиями пребывания на территории парка</div>
                    </label>
                </div>
                <button class="primary-btn primary-btn--lg primary-btn--disabled" type="button">Далее</button>
            </div>
        </div>
        <div class="booking-block_form">
            <h2 class="modal_title">Бронирование</h2>
            <span class="modal_subtitle"></span>
            <form name="iblock_add" action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data"
                  class="m-booking-form">
                <?= bitrix_sessid_post() ?>
                <? if (is_array($arResult["FIELDS"]) && !empty($arResult["FIELDS"])): ?>
                    <div class="input-group">
                        <div class="input input--md">
                            <label for="input-user-name">Имя <span class="color-red">*</span></label>
                            <input type="text" id="input-user-name"
                                   name="PROPERTY[9][0]" value="" required/>
                        </div>
                        <div class="input input--md">
                            <label for="input-user-surname">Фамилия <span class="color-red">*</span></label>
                            <input type="text" id="input-user-surname"
                                   name="PROPERTY[10][0]" value="" required/>
                        </div>
                        <div id="date-time-select-block">
                            <div class="radio-group" id="service-cost" style="display: none"></div>
                            <div class="m-input-dates m-input-dates--md js-input-date-group">
                                <div class="m-input-date-block">
                                    <label for="">Дата заезда <span class="color-red">*</span></label>
                                    <input type="text" name="PROPERTY[11][0][VALUE]"
                                           class="input-date" required autocomplete="off">
                                </div>
                                <div class="m-input-date-block">
                                    <label for="">Дата выезда <span class="color-red">*</span></label>
                                    <input type="text" name="PROPERTY[12][0][VALUE]"
                                           class="input-date second-range-input" required autocomplete="off">
                                </div>
                            </div>

                            <div class="m-input-dates m-input-dates--md">
                                <div class="m-input-date-block">
                                    <label for="">Время заезда <span class="color-red">*</span></label>
                                    <div class="custom-select custom-select--sm" id="arrival-time-select">
                                        <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время заезда">Время заезда</span>
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
                                    <label for="">Время выезда <span class="color-red">*</span></label>
                                    <div class="custom-select custom-select--sm" id="departure-time-select">
                                        <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время выезда">Время выезда</span>
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
                        </div>
                        <div class="radio-group">
                            <div class="radio">
                                <input type="radio"
                                       id="property_<?= $arResult["FIELDS"]['CHECK']['PERMISSION']['VALUES'][1]['ID'] ?>"
                                       name="<?= $arResult["FIELDS"]['CHECK']['PERMISSION']['PROP_NAME'] ?>"
                                       value="<?= $arResult["FIELDS"]['CHECK']['PERMISSION']['VALUES'][1]['ID'] ?>"
                                >
                                <label for="property_<?= $arResult["FIELDS"]['CHECK']['PERMISSION']['VALUES'][1]['ID'] ?>">
                                    <div class="radio_text">Есть разрешение</div>
                                </label>
                            </div>
                            <div class="radio">
                                <input type="radio"
                                       id="property_<?= $arResult["FIELDS"]['CHECK']['PERMISSION']['VALUES'][2]['ID'] ?>"
                                       name="<?= $arResult["FIELDS"]['CHECK']['PERMISSION']['PROP_NAME'] ?>"
                                       value="<?= $arResult["FIELDS"]['CHECK']['PERMISSION']['VALUES'][2]['ID'] ?>"
                                       checked
                                >
                                <label for="property_<?= $arResult["FIELDS"]['CHECK']['PERMISSION']['VALUES'][2]['ID'] ?>">
                                    <div class="radio_text">Разрешение не получено</div>
                                </label>
                            </div>
                        </div>
                        <div class="m-booking-form-block">
                            <span class="m-booking-form-block_title">Укажите количество гостей</span>
                            <div class="input-counter-group">
                                <div class="input-counter-item">
                                    <label for="" class="input-counter-item_title">Всего <span
                                                class="color-red">*</span></label>
                                    <div class="input-counter">
                                        <button class="input-counter_btn"
                                                type="button"
                                                data-input-id="adult-quantity"
                                                data-action="minus">
                                            <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                            </svg>
                                        </button>
                                        <input type="text" class="input-counter_input"
                                               name="<?= $arResult["FIELDS"]['NUMBER']['ADULTS']['PROP_NAME'] ?>"
                                               value="0"
                                               id="adult-quantity"
                                               required
                                        >
                                        <button class="input-counter_btn" type="button" data-input-id="adult-quantity"
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
                                    <label for="" class="input-counter-item_title">Из них льготников</label>
                                    <div class="input-counter">
                                        <button class="input-counter_btn" type="button"
                                                data-input-id="beneficiaries-quantity"
                                                data-action="minus">
                                            <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                            </svg>
                                        </button>
                                        <input type="text" class="input-counter_input"
                                               name="<?= $arResult["FIELDS"]['NUMBER']['BENIFICIARIES']['PROP_NAME'] ?>"
                                               value="0"
                                               id="beneficiaries-quantity"
                                        >
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
                                <button class="btn-dop-info js-open-r-modal" data-name="modal-benefit" type="button">
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

                        <div id="car-possibility-block" style="display: none">
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
                                                Всего <span class="color-red">*</span>
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
                                            1 <span class="color-red">*</span></label>
                                        <input type="text" name="PROPERTY[<?= GUEST_CARS ?>][0]" size="30">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="input input--md">
                            <label for="">E-mail <span class="color-red">*</span></label>
                            <input type="email" name="PROPERTY[19][0]" value="" required>
                        </div>
                        <div class="input input--md">
                            <label for="">Телефон <span class="color-red">*</span></label>
                            <input type="tel" name="PROPERTY[20][0]" value="" required>
                        </div>
                    </div>
                <? endif ?>
                <input type="hidden" id="input-object-id" name="PROPERTY[21][0][VALUE]" value="">
                <input type="hidden" id="iblock-record-name" name="PROPERTY[NAME][0]" value="">
                <input type="hidden" name="PROPERTY[22][0]" value="Онлайн">
                <input type="hidden" name="PROPERTY[32][0]" value="">
                <input type="hidden" name="PROPERTY[13][0]" value="">
                <input type="hidden" name="PROPERTY[14][0]" value="">
                <input type="hidden" name="VISIT_PERMISSION_COST" value="<?= VISIT_PERMISSION_COST ?>">
                <input type="hidden" name="PROPERTY[43][0]" value="">
                <input type="hidden" name="PROPERTY[44][0]" value="">
                <input type="hidden" name="guest-car-prop-number" value="<?= GUEST_CARS ?>">
                <input type="hidden" name="iblock_submit" value="<?= GetMessage("IBLOCK_FORM_SUBMIT") ?>">
                <? if ($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0): ?>
                    <tr>
                        <td><?= GetMessage("IBLOCK_FORM_CAPTCHA_TITLE") ?></td>
                        <td>
                            <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
                            <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>"
                                 width="180"
                                 height="40" alt="CAPTCHA"/>
                        </td>
                    </tr>
                    <tr>
                        <td><?= GetMessage("IBLOCK_FORM_CAPTCHA_PROMPT") ?><span class="starrequired">*</span>:</td>
                        <td><input type="text" name="captcha_word" maxlength="50" value=""></td>
                    </tr>
                <? endif ?>
                <div class="checkbox-list">
                    <div class="checkbox">
                        <input type="checkbox" id="stay-confirm">
                        <label for="stay-confirm">
                            <div class="checkbox_text">Согласен с <a href="#" id="js-open-visiting-modal"
                                                                     data-name="modal-visiting-rules">условиями
                                    пребывания на
                                    территории парка</a></div>
                        </label>
                    </div>
                    <div class="checkbox">
                        <input type="checkbox" id="personal-data-confirm">
                        <label for="personal-data-confirm">
                            <div class="checkbox_text">Согласен на <a href="#" id="js-open-personal-modal"
                                                                      data-name="modal-personal-data">обработку
                                    персональных
                                    данных</a></div>
                        </label>
                    </div>
                    <div class="checkbox">
                        <input type="checkbox" id="offer-confirm">
                        <label for="offer-confirm">
                            <div class="checkbox_text">Ознакомлен с <a href="#" id="js-open-offer-confirm"
                                                                       data-name="modal-offer-confirm">договором
                                    оферты</a></div>
                        </label>
                    </div>
                </div>
                <div class="m-booking-form_bottom">
                    <div class="price">
                        <span class="price_title">стоимость</span>
                        <div class="price_value"><span id="booking-sum-value">0</span> ₽</div>
                    </div>
                    <input type="submit" class="primary-btn primary-btn--lg" id="bookig-action-button" value="Оплата"/>
                </div>
                <div class="form-description">Все отмеченные звездочкой обязательны для заполнения</div>
                <div class="form-warn-message"></div>
            </form>
            <div id="ban-booking-msg"></div>
        </div>
    </div>
</div>