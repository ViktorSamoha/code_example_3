<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
global $USER;
?>
<section class="modal" id="modal-booking">
    <div class="modal_block">
        <button class="modal-close-btn" type="button"
                onclick="document.querySelector('#modal-booking').classList.remove('active')">
            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="2.82843" width="4" height="42.0722" transform="rotate(-45 0 2.82843)" fill="white"/>
                <rect x="3" y="32.8284" width="4" height="42.0722" transform="rotate(-135 3 32.8284)" fill="white"/>
            </svg>
        </button>

        <div class="booking-block">
            <div class="booking-block_info">
                <h2 class="booking-block_title">Правила посещения территории </h2>
                <div class="modal-text">
                    <p>(согласно гл. VI п. 22 )
                        На территории , в том числе в лесах и на акватории
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
                        <li>уничтожение и повреждение деревьев, кустарников, напочвенного растительного покрова.</li>
                        <li>повреждение почвенного покрова и геологических обнажений (раскопки, копка, распашка земли,
                            пробуксовка и
                            т.п.);
                        </li>
                        <li>уничтожение и повреждение природных объектов, сооружений, плакатов, аншлагов, указателей,
                            межевых
                            знаков,
                            квартальных и природоохранных столбов, дорог и троп;
                        </li>
                        <li>использование ракетниц и петард.</li>
                        <li>отклонение от маршрута, указанного в разрешительных документах;</li>
                        <li>заезжать за ограждение на маршруте «Куперля»;</li>
                        <li>находиться в заповедной зоне.</li>
                    </ul>

                    <p>Кроме этого, граждане при пребывании в лесах обязаны соблюдать правила пожарной безопасности в
                        лесах,
                        правила
                        санитарной безопасности в лесах, правила лесовосстановления и правила ухода за лесами (ст. 11
                        Лесного
                        кодекса
                        РФ).</p>
                    <p>Согласно Постановлению Правительства РФ от 30.06.2007 №417 «Об утверждении Правил пожарной
                        безопасности в
                        лесах» существуют следующие правила:</p>
                    <p>(согласно гл. VI п. 22 Положени, в том числе в лесах и на акватории Нугушского
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
                        <li>уничтожение и повреждение деревьев, кустарников, напочвенного растительного покрова.</li>
                        <li>повреждение почвенного покрова и геологических обнажений (раскопки, копка, распашка земли,
                            пробуксовка и
                            т.п.);
                        </li>
                        <li>уничтожение и повреждение природных объектов, сооружений, плакатов, аншлагов, указателей,
                            межевых
                            знаков,
                            квартальных и природоохранных столбов, дорог и троп;
                        </li>
                        <li>использование ракетниц и петард.</li>
                        <li>отклонение от маршрута, указанного в разрешительных документах;</li>
                        <li>заезжать за ограждение на маршруте «Куперля»;</li>
                        <li>находиться в заповедной зоне.</li>
                    </ul>

                    <p>Кроме этого, граждане при пребывании в лесах обязаны соблюдать правила пожарной безопасности в
                        лесах,
                        правила
                        санитарной безопасности в лесах, правила лесовосстановления и правила ухода за лесами (ст. 11
                        Лесного
                        кодекса
                        РФ).</p>
                    <p>Согласно Постановлению Правительства РФ от 30.06.2007 №417 «Об утверждении Правил пожарной
                        безопасности в
                        лесах» существуют следующие правила:</p>
                </div>
                <div class="booking-block_btns">
                    <div class="checkbox">
                        <input type="checkbox" id="modal-rules-confirm" onclick="modalBookingRulesConfirm(this)">
                        <label for="modal-rules-confirm">
                            <div class="checkbox_text">Согласен с условиями пребывания на территории парка</div>
                        </label>
                    </div>
                    <button class="primary-btn primary-btn--lg primary-btn--disabled" type="button"
                            onclick="modalBookingShowForm()">Далее
                    </button>
                </div>

            </div>

            <div class="booking-block_form">
                <h2 class="modal_title">Бронирование</h2>
                <? if ($arResult['LOCATION_DATA']): ?>
                    <span class="modal_subtitle"><?= $arResult['LOCATION_DATA']['NAME'] ?></span>
                    <form action="" class="m-booking-form">
                        <input type="hidden" name="LOCATION_ID" value="<?= $arResult['LOCATION_DATA']['ID'] ?>">
                        <input type="hidden" name="USER_ID" value="<?= $arResult['USER_DATA']['ID'] ?>">
                        <input type="hidden" name="BOOKING_PERIOD"
                               value="<?= $arResult['LOCATION_DATA']['BOOKING_PERIOD'] ?>">
                        <input type="hidden" name="TIME_UNLIMIT_OBJECT"
                               value="<?= $arResult['LOCATION_DATA']['TIME_UNLIMIT_OBJECT'] ?>">
                        <input type="hidden" name="CAPACITY_ESTIMATED"
                               value="<?= $arResult['LOCATION_DATA']['CAPACITY_ESTIMATED'] ?>">
                        <input type="hidden" name="CAPACITY_MAXIMUM"
                               value="<?= $arResult['LOCATION_DATA']['CAPACITY_MAXIMUM'] ?>">
                        <input type="hidden" name="FIXED_COST"
                               value="<?= $arResult['LOCATION_DATA']['FIXED_COST'] ?>">
                        <input type="hidden" name="PERMIT_COST"
                               value="<?= VISIT_PERMISSION_COST ?>">
                        <input type="hidden" name="OBJECT_COST"
                               value="<?= $arResult['LOCATION_DATA']['OBJECT_COST'] ?>">
                        <input type="hidden" name="OBJECT_DAILY_COST"
                               value="<?= $arResult['LOCATION_DATA']['OBJECT_DAILY_COST'] ?>">
                        <input type="hidden" name="COST_PER_PERSON_ONE_DAY"
                               value="<?= $arResult['LOCATION_DATA']['COST_PER_PERSON_ONE_DAY'] ?>">
                        <input type="hidden" name="COST_PER_PERSON"
                               value="<?= $arResult['LOCATION_DATA']['COST_PER_PERSON'] ?>">
                        <div class="input-group">
                            <div class="input input--md">
                                <label for="">Имя <span class="color-red">*</span></label>
                                <input type="text" name="NAME" value="<?= $arResult['USER_DATA']['NAME'] ?>" required>
                            </div>
                            <div class="input input--md">
                                <label for="">Фамилия</label>
                                <input type="text" name="LAST_NAME" value="<?= $arResult['USER_DATA']['LAST_NAME'] ?>"
                                       required>
                            </div>
                            <div class="m-input-dates m-input-dates--md">
                                <div class="m-input-date-block">
                                    <label for="">Дата заезда</label>
                                    <input type="text" class="input-date" name="ARRIVAL_DATE" required>
                                </div>
                                <div class="m-input-date-block">
                                    <label for="">Дата выезда</label>
                                    <input type="text" class="input-date second-range-input" name="DEPARTURE_DATE"
                                    >
                                </div>
                            </div>
                            <div class="m-input-dates m-input-dates--md">
                                <div class="m-input-date-block">
                                    <label for="">Время заезда</label>
                                    <div class="custom-select custom-select--sm"
                                         id="modal-booking-arrival-time-select">
                                        <!-- class loading -->
                                        <div class="custom-select_head">
                                            <span class="custom-select_title">Время заезда</span>
                                            <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                                 fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 1L7 7L13 1" stroke="#000"/>
                                            </svg>
                                            <span class="select-loader"></span>
                                        </div>
                                        <div class="custom-select_body">
                                            <div class="custom-select_item">8:00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-input-date-block">
                                    <label for="">Время выезда</label>
                                    <!-- <input type="text" class="input-time"> -->
                                    <div class="custom-select custom-select--sm"
                                         id="modal-booking-departure-time-select">
                                        <div class="custom-select_head">
                                            <span class="custom-select_title">Время выезда</span>
                                            <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                                 fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 1L7 7L13 1" stroke="#000"/>
                                            </svg>
                                        </div>
                                        <div class="custom-select_body">
                                            <div class="custom-select_item">20:00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="permit-block">
                                <div class="permit-block_top">
                                    <div class="radio-group">
                                        <div class="radio">
                                            <input type="radio" id="radio_01" name="permit" value="yes"
                                                <?= isset($arResult['USER_DATA']['PERMISSIONS']) ? 'checked' : '' ?>
                                            >
                                            <label for="radio_01">
                                                <div class="radio_text">Есть разрешение</div>
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <input type="radio" id="radio_02" name="permit"
                                                   value="no" <?= isset($arResult['USER_DATA']['PERMISSIONS']) ? '' : 'checked' ?>>
                                            <label for="radio_02">
                                                <div class="radio_text">Разрешение не получено</div>
                                            </label>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" onclick="bookingModalAddPermission(this)"
                                       class="primary-btn <?= isset($arResult['USER_DATA']['PERMISSIONS']) ? 'hidden' : '' ?>">Добавить</a>
                                </div>
                                <div class="input input--md <?= isset($arResult['USER_DATA']['PERMISSIONS']) ? '' : 'hidden' ?>">
                                    <label for="">№ разрешения</label>
                                    <input type="text" name="VISITING_PERMISSION_ID"
                                           value="<?= $arResult['USER_DATA']['PERMISSIONS'][0]['ID'] ?>">
                                </div>
                            </div>

                            <div class="m-booking-form-block">
                                <div class="m-booking-form-block_title">Укажите количество гостей <span
                                            class="asterisk">*</span></div>

                                <div class="input-counter-group">
                                    <div class="input-counter-item">
                                        <label for="" class="input-counter-item_title">Всего <span
                                                    class="color-red">*</span></label>
                                        <div class="input-counter">
                                            <button class="input-counter_btn"
                                                    type="button"
                                                    data-input-id="modal-booking-guest-quantity"
                                                    data-action="minus">
                                                <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                                </svg>
                                            </button>
                                            <input type="text" class="input-counter_input"
                                                   name="GUEST_QUANTITY"
                                                   value="1"
                                                   id="modal-booking-guest-quantity"
                                                   max="<?= $arResult['LOCATION_DATA']['CAPACITY_MAXIMUM'] ?>"
                                                   required
                                            >
                                            <button class="input-counter_btn" type="button"
                                                    data-input-id="modal-booking-guest-quantity"
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
                                                    data-input-id="modal-booking-beneficiaries-quantity"
                                                    data-action="minus">
                                                <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                                </svg>
                                            </button>
                                            <input type="text" class="input-counter_input"
                                                   name="BENEFICIARIES_QUANTITY"
                                                   value="0"
                                                   max="<?= $arResult['LOCATION_DATA']['CAPACITY_MAXIMUM'] ?>"
                                                   id="modal-booking-beneficiaries-quantity"
                                            >
                                            <button class="input-counter_btn" type="button"
                                                    data-input-id="modal-booking-beneficiaries-quantity"
                                                    data-action="plus">
                                                <svg width="13" height="13" viewBox="0 0 13 13" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0 6H13V7H0V6Z" fill="#313131"/>
                                                    <path d="M6 13L6 4.37112e-08L7 0L7 13H6Z" fill="#313131"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <button class="btn-dop-info btn-dop-info--sm js-open-r-modal"
                                            data-name="modal-benefit"
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
                            <? if ($arResult['LOCATION_DATA']['CAR_POSSIBILITY'] && $arResult['LOCATION_DATA']['CAR_POSSIBILITY'] == "Да"): ?>
                                <div id="modal-booking-car-possibility-block">
                                    <div class="radio-group" id="modal-booking-car-radio-group">
                                        <div class="radio">
                                            <input type="radio"
                                                   id="modal-booking-car-yes"
                                                   name="car-radio"
                                                   value="yes"
                                            >
                                            <label for="modal-booking-car-yes">
                                                <div class="radio_text">Есть автомобиль</div>
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <input type="radio"
                                                   id="modal-booking-car-no"
                                                   name="car-radio"
                                                   value="no"
                                                   checked
                                            >
                                            <label for="modal-booking-car-no">
                                                <div class="radio_text">Нет автомобиля</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div id="modal-booking-car-detail-hidden" style="display: none">
                                        <div class="m-booking-form-block">
                                            <span class="m-booking-form-block_title">Укажите количество автомобилей</span>
                                            <div class="input-counter-group">
                                                <div class="input-counter-item">
                                                    <label for="" class="input-label input-label--mb input-label--gray">
                                                        Всего <span class="color-red">*</span>
                                                    </label>
                                                    <div class="input-counter">
                                                        <button class="input-counter_btn" type="button"
                                                                data-input-id="modal-booking-car-quantity"
                                                                data-action="minus">
                                                            <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                                                 xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                                            </svg>
                                                        </button>
                                                        <input type="text" class="input-counter_input"
                                                               id="modal-booking-car-quantity"
                                                               name=""
                                                               value="1"
                                                               max="<?= $arResult['LOCATION_DATA']['CAR_CAPACITY'] ?>"
                                                               required
                                                        >
                                                        <button class="input-counter_btn" type="button"
                                                                data-input-id="modal-booking-car-quantity"
                                                                data-action="plus">
                                                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none"
                                                                 xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M0 6H13V7H0V6Z" fill="#313131"/>
                                                                <path d="M6 13L6 4.37112e-08L7 0L7 13H6Z"
                                                                      fill="#313131"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="modal-booking-cars-list">
                                            <div class="input">
                                                <label for="" class="input-label">Номер автомобиля
                                                    1 <span class="color-red">*</span></label>
                                                <input type="text" name="CAR_ID_1" size="30">
                                            </div>
                                        </div>
                                        <div class="permit-block" id="transport-permit-block">
                                            <div class="permit-block_top">
                                                <div class="radio-group">
                                                    <div class="radio">
                                                        <input type="radio" id="transport_permit_radio_1"
                                                               name="transport-permit" value="yes">
                                                        <label for="transport_permit_radio_1">
                                                            <div class="radio_text">Есть разрешение</div>
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <input type="radio" id="transport_permit_radio_2"
                                                               name="transport-permit"
                                                               value="no">
                                                        <label for="transport_permit_radio_2">
                                                            <div class="radio_text">Разрешение не получено</div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <a href="javascript:void(0);"
                                                   onclick="bookingModalAddTransportPermission(this)"
                                                   class="primary-btn hidden">Добавить</a>
                                            </div>
                                            <div class="input input--md hidden">
                                                <label for="">№ разрешения</label>
                                                <input type="text" name="TRANSPORT_PERMISSION_ID" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <? endif; ?>
                            <div class="input input--md">
                                <label for="">E-mail</label>
                                <input type="email" name="EMAIL" value="<?= $arResult['USER_DATA']['EMAIL'] ?>">
                            </div>
                            <div class="input input--md">
                                <label for="">Телефон</label>
                                <input type="tel" name="PHONE" value="<?= $arResult['USER_DATA']['PHONE'] ?>">
                            </div>
                        </div>
                        <div class="checkbox-list">
                            <div class="checkbox">
                                <input type="checkbox" id="modal-booking-stay-confirm">
                                <label for="modal-booking-stay-confirm">
                                    <div class="checkbox_text">Согласен с <a href="javascript:void(0);"
                                                                             id="js-open-visiting-modal"
                                                                             data-name="modal-visiting-rules">условиями
                                            пребывания на
                                            территории парка</a></div>
                                </label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" id="modal-booking-personal-data-confirm">
                                <label for="modal-booking-personal-data-confirm">
                                    <div class="checkbox_text">Согласен на <a href="javascript:void(0);"
                                                                              id="js-open-personal-modal"
                                                                              data-name="modal-personal-data">обработку
                                            персональных
                                            данных</a></div>
                                </label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" id="modal-booking-offer-confirm">
                                <label for="modal-booking-offer-confirm">
                                    <div class="checkbox_text">Ознакомлен с <a href="javascript:void(0);"
                                                                               id="js-open-offer-confirm"
                                                                               data-name="modal-offer-confirm">договором
                                            оферты</a></div>
                                </label>
                            </div>
                        </div>

                        <div class="m-booking-form_bottom">
                            <div class="price">
                                <span class="price_title">стоимость</span>
                                <div class="price_value"><span
                                            id="modal-booking-price"><?= $arResult['LOCATION_DATA']['PRICE'] ?></span> ₽
                                </div>
                            </div>
                            <button class="primary-btn primary-btn--lg" type="button"
                                    onclick="bookingModalAdd2Basket()">Добавить в корзину
                            </button>
                        </div>
                        <div class="form-description">Все отмеченные звездочкой обязательны для заполнения</div>
                        <div class="form-warn-message"></div>
                    </form>
                <? endif; ?>
            </div>

        </div>
    </div>
</section>
