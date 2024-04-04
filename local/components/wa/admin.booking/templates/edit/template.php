<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$APPLICATION->SetTitle("Редактирование заказа №" . $arResult['ORDER_DATA']['ID']);
?>
<form name="iblock_add" class="lk-booking-form">
    <div class="preloader preloader--fixed">
        <div class="preloader_text">
            <img src="<?= ASSETS ?>images/preloader.svg" alt="" class="preloader_icon">
            <span class="preloader_title">Подождите, идет загрузка</span>
        </div>
    </div>
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['ID'] ?>" name="BOOKING_OBJECT_ID">
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['BOOKING_OBJECT_PRICE'] ?>"
           name="BOOKING_OBJECT_PRICE">
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['ID'] ?>" name="ORDER_ID">
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['BOOKING_OBJECT_PERIOD'] ?>"
           name="BOOKING_OBJECT_PERIOD">
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['TIME_UNLIMIT_OBJECT'] ?>"
           name="TIME_UNLIMIT_OBJECT">
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['CAPACITY_MAXIMUM'] ?>"
           name="CAPACITY_MAXIMUM">
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['CAPACITY_ESTIMATED'] ?>"
           name="CAPACITY_ESTIMATED">
    <input type="hidden" value="<?= VISIT_PERMISSION_COST ?>" name="VISIT_PERMISSION_COST">
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['FIXED_COST'] ?>" name="FIXED_COST">
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['OBJECT_DAILY_COST'] ?>"
           name="OBJECT_DAILY_COST">
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['COST_PER_PERSON'] ?>"
           name="COST_PER_PERSON">
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['COST_PER_PERSON_ONE_DAY'] ?>"
           name="COST_PER_PERSON_ONE_DAY">
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['IS_ROUTE'] ?>" name="IS_ROUTE">
    <input type="hidden" value="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['DAILY_TRAFFIC'] ?>" name="DAILY_TRAFFIC">
    <input type="hidden"
           value="<?= $arResult['ADMIN_DATA']['LAST_NAME'] . ' ' . $arResult['ADMIN_DATA']['NAME'] . ' ' . $arResult['ADMIN_DATA']['SECOND_NAME'] ?>"
           name="ADMIN">
    <div class="form-block form-block--mb30">
        <div class="input-group">
            <div class="select-block select-block--lg">
                <span class="input-label input-label--mb input-label--gray">Локация</span>
                <div class="custom-select">
                    <div class="custom-select_head">
                                    <span class="custom-select_title" id="form-location-filter"
                                          data-selected-id="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['SECTIONS'][2]['ID'] ?>"><?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['SECTIONS'][2]['NAME'] ?></span>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                             fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? foreach ($arResult['LOCATIONS_STRUCTURE']['LOCATION'] as $location): ?>
                            <div class="custom-select_item"
                                 data-id="<?= $location['ID'] ?>"><?= $location['NAME'] ?></div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="input-date-block input-date-block--sm">
                <span class="input-label input-label--mb input-label--gray">Даты (заезд-выезд)</span>
                <div class="input-dates js-input-date-group">
                    <input type="text" class="input-date" name="form-arrival-date-filter" autocomplete="off">
                    <input type="text" class="input-date second-range-input" name="form-departure-date-filter"
                           autocomplete="off">
                </div>
            </div>
            <div class="select-block select-block--lg">
                <span class="input-label input-label--mb input-label--gray">Категория</span>
                <div class="custom-select">
                    <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          id="form-category-filter"
                                          data-selected-id="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['SECTIONS'][1]['ID'] ?>"><?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['SECTIONS'][1]['NAME'] ?></span>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                             fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? foreach ($arResult['LOCATIONS_STRUCTURE']['TYPE'] as $section): ?>
                            <div class="custom-select_item"
                                 data-id="<?= $section['ID'] ?>"><?= $section['NAME'] ?></div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <button class="gray-btn gray-btn--lg" type="button" onclick="setFilterAction()">Применить фильтр</button>
    </div>
    <div id="object-select-block">
        <? if (count($arResult['LOCATION_OBJECTS']) > 10): ?>
            <div class="form-block form-block--mb30">
                <h3 class="form-block_title">Выберите доступный объект</h3>
                <div class="select-block select-block--lg">
                    <div class="custom-select" id="object-select">
                        <div class="custom-select_head">
                            <? if (!empty($arResult['ORDER_DATA']['BOOKING_OBJECT'])): ?>
                                <span class="custom-select_title"
                                      data-selected-id="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['ID'] ?>"><?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['NAME'] ?></span>
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
                            <? foreach ($arResult['LOCATION_OBJECTS'] as $object): ?>
                                <div class="custom-select_item"
                                     data-id="<?= $object['ID'] ?>"><?= $object['NAME'] ?></div>
                            <? endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <? else: ?>
            <div class="form-block">
                <h3 class="form-block_title">Выберите доступный объект</h3>
                <div class="radio-list" id="objects-list">
                    <? foreach ($arResult['LOCATION_OBJECTS'] as $object): ?>
                        <div class="radio">
                            <input type="radio" id="radio_<?= $object['ID'] ?>"
                                   name="OBJECT"
                                <?= $object['ID'] == $arResult['ORDER_DATA']['BOOKING_OBJECT']['ID'] ? 'checked' : '' ?>
                                   value="<?= $object['ID'] ?>"
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
                <? if ($arResult['ORDER_DATA']['IS_ROUTE'] == 'true'): ?>
                    <div class="input-group">
                        <div class="input input--md">
                            <label for="" class="input-label">Дата оформления <span class="color-red">*</span></label>
                            <input type="text" value="<?= $arResult['ORDER_DATA']["DATE_INSERT"] ?>" name="ACTIVE_FROM"
                                   class="input-date" required="" readonly="">
                        </div>
                        <div class="m-input-dates m-input-dates--md js-input-date-group">
                            <div class="m-input-date-block">
                                <label for="" class="input-label">Дата заезда <span
                                            class="color-red">*</span></label>
                                <input type="text" class="input-date" name="ARRIVAL_DATE"
                                       size="25"
                                       value="<?= $arResult['ORDER_DATA']['ARRIVAL_DATE'] ?>"
                                       autocomplete="off">
                            </div>
                            <div class="m-input-date-block">
                                <label for="" class="input-label">Дата выезда <span
                                            class="color-red">*</span></label>
                                <input type="text" class="input-date second-range-input"
                                       name="DEPARTURE_DATE"
                                       size="25"
                                       value="<?= $arResult['ORDER_DATA']['DEPARTURE_DATE'] ?>"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>
                <? else: ?>
                    <? if ($arResult['ORDER_DATA']['BOOKING_OBJECT']['TIME_UNLIMIT_OBJECT'] == 'Да'): ?>
                        <div class="form-block form-block--mb30">
                            <h3 class="form-block_title">Дата и время заезда</h3>
                            <div id="date-time-select-block">
                                <div class="input-group">
                                    <div class="input">
                                        <label for="" class="input-label">Дата оформления <span
                                                    class="color-red">*</span></label>
                                        <input type="text"
                                               value="<?= $arResult['ORDER_DATA']["DATE_INSERT"] ?>"
                                               class="input-date"
                                               required>
                                    </div>
                                    <div class="m-input-dates m-input-dates--md">
                                        <div class="m-input-date-block">
                                            <label for="" class="input-label">Дата заезда</label>
                                            <input type="text" class="input-date" name="ARRIVAL_DATE"
                                                   size="25"
                                                   required
                                                   value="<?= $arResult['ORDER_DATA']['ARRIVAL_DATE'] ?>"
                                                   autocomplete="off">
                                        </div>
                                        <div class="m-input-date-block">
                                            <label for="" class="input-label">Время заезда</label>
                                            <div class="custom-select custom-select--sm" id="arrival-time-select">
                                                <div class="custom-select_head">
                                                    <span class="custom-select_title"
                                                          data-default-value="Время заезда"
                                                          data-selected-id="<?= $arResult['ORDER_DATA']['CHECK_IN_TIME'] ?>"><?= $arResult['ORDER_DATA']['CHECK_IN_TIME'] ?></span>
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
                        <div class="input-group" id="booking-period">
                            <div class="radio-group" id="time-select-radio">
                                <div class="radio">
                                    <input type="radio" id="radio_07" data-period="couple"
                                           name="radio" <?= isset($arResult['ORDER_DATA']['RENT_PERIOD']) ? ($arResult['ORDER_DATA']['RENT_PERIOD'] == 'couple' ? 'checked' : '') : 'checked' ?>>
                                    <label for="radio_07">
                                        <div class="radio_text">На несколько суток</div>
                                    </label>
                                </div>
                                <div class="radio">
                                    <input type="radio" id="radio_08" data-period="day"
                                           name="radio" <?= isset($arResult['ORDER_DATA']['RENT_PERIOD']) ? ($arResult['ORDER_DATA']['RENT_PERIOD'] == 'day' ? 'checked' : '') : '' ?>>
                                    <label for="radio_08">
                                        <div class="radio_text">Дневное пребывание</div>
                                    </label>
                                </div>
                            </div>
                            <div class="input input--sm">
                                <label for="" class="input-label">Дата оформления <span
                                            class="color-red">*</span></label>
                                <input type="text" value="<?= $arResult['ORDER_DATA']["DATE_INSERT"] ?>"
                                       class="input-date">
                            </div>
                        </div>

                        <div class="input-group">
                            <div class="m-input-dates m-input-dates--md js-input-date-group">
                                <div class="m-input-date-block">
                                    <label for="" class="input-label">Дата заезда <span
                                                class="color-red">*</span></label>
                                    <input type="text" class="input-date" name="ARRIVAL_DATE"
                                           size="25"
                                           value="<?= $arResult['ORDER_DATA']['ARRIVAL_DATE'] ?>"
                                           autocomplete="off">
                                </div>
                                <div class="m-input-date-block">
                                    <label for="" class="input-label">Дата выезда <span
                                                class="color-red">*</span></label>
                                    <input type="text" class="input-date second-range-input"
                                           name="DEPARTURE_DATE"
                                           size="25"
                                           value="<?= $arResult['ORDER_DATA']['DEPARTURE_DATE'] ?>"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="m-input-dates m-input-dates--md">
                                <div class="m-input-date-block">
                                    <label for="">Время заезда</label>
                                    <div class="custom-select custom-select--sm" id="arrival-time-select">
                                        <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время заезда"
                                          data-selected-id="<?= $arResult['ORDER_DATA']['CHECK_IN_TIME'] ?>"><?= $arResult['ORDER_DATA']['CHECK_IN_TIME'] ?></span>
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
                                          data-selected-id="<?= $arResult['ORDER_DATA']['DEPARTURE_TIME'] ?>"><?= $arResult['ORDER_DATA']['DEPARTURE_TIME'] ?></span>
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
                <? endif; ?>
            </div>
        </div>
        <div class="form-block">
            <h3 class="form-block_title">Параметры заказа</h3>
            <div class="input-group">
                <div class="input input--md">
                    <label for="" class="input-label">Имя <span class="color-red">*</span></label>
                    <input type="text" name="NAME" size="30" required
                           value="<?= $arResult['ORDER_DATA']['USER_NAME'] ?>">
                </div>
                <div class="input input--md">
                    <label for="" class="input-label">Фамилия <span class="color-red">*</span></label>
                    <input type="text" name="LAST_NAME" size="30" required
                           value="<?= $arResult['ORDER_DATA']['USER_SURNAME'] ?>">
                </div>
                <div class="input input--md">
                    <label for="" class="input-label">E-mail</label>
                    <input type="email" name="EMAIL" size="30" value="<?= $arResult['ORDER_DATA']['USER_EMAIL'] ?>">
                </div>
                <div class="input input--md">
                    <label for="" class="input-label">Телефон <span class="color-red">*</span></label>
                    <input type="tel" name="PHONE" size="30" required
                           value="<?= $arResult['ORDER_DATA']['USER_PHONE'] ?>">
                </div>
                <div class="radio-group">
                    <div class="radio">
                        <input type="radio"
                               id="property_1"
                               name="PERMISSION"
                               value="1"
                            <?= $arResult['ORDER_DATA']['PERMISSION'] == "Да" ? 'checked' : '' ?>
                        >
                        <label for="property_1">
                            <div class="radio_text">Есть разрешение</div>
                        </label>
                    </div>
                    <div class="radio">
                        <input type="radio"
                               id="property_2"
                               name="PERMISSION"
                               value="2"
                            <?= $arResult['ORDER_DATA']['PERMISSION'] == "Нет" ? 'checked' : '' ?>
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
                                       name="GUESTS_COUNT"
                                       value="<?= $arResult['ORDER_DATA']['GUESTS_COUNT'] ?>"
                                       max="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['CAPACITY_MAXIMUM'] ?>"
                                >

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
                                       name="BENIFICIARIES_COUNT"
                                       value="<?= $arResult['ORDER_DATA']['BENIFICIARIES_COUNT'] ?>"
                                       max="<?= $arResult['ORDER_DATA']['BOOKING_OBJECT']['CAPACITY_MAXIMUM'] ?>"
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
            </div>
        </div>
        <div class="form-block"
             id="car-possibility-block" <?= $arResult['ORDER_DATA']['BOOKING_OBJECT']["CAR_POSSIBILITY"] == 'Да' ? '' : 'style="display: none"' ?>>
            <h3 class="form-block_title">Наличие автомобиля</h3>
            <div class="input-group">
                <div class="radio-group" id="car-radio-group">
                    <div class="radio">
                        <input type="radio"
                               id="car-yes"
                               name="car-radio"
                               value="1"
                            <?= is_array($arResult["ORDER_DATA"]["GUEST_CARS"]) ? 'checked' : '' ?>
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
                            <?= !is_array($arResult["ORDER_DATA"]["GUEST_CARS"]) ? 'checked' : '' ?>
                        >
                        <label for="car-no">
                            <div class="radio_text">Нет автомобиля</div>
                        </label>
                    </div>
                </div>
                <div id="car-detail-hidden" <?= is_array($arResult["ORDER_DATA"]["GUEST_CARS"]) ? '' : 'style="display: none"' ?>>
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
                                           name="GUEST_CARS"
                                           max="<?= $arResult["ORDER_DATA"]["BOOKING_OBJECT"]["CAR_CAPACITY"] ?>"
                                           value="<?= is_array($arResult["ORDER_DATA"]["GUEST_CARS"]) ? count($arResult["ORDER_DATA"]["GUEST_CARS"]) : '1' ?>"
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
                        <? if (is_array($arResult["ORDER_DATA"]["GUEST_CARS"])): ?>
                            <? foreach ($arResult["ORDER_DATA"]["GUEST_CARS"] as $i => $carIdValue): ?>
                                <div class="input">
                                    <label for="GUEST_CARS_K_<?= $i ?>" class="input-label">Номер
                                        автомобиля
                                        <?= $i + 1 ?></label>
                                    <input type="text" name="GUEST_CARS_K_<?= $i ?>"
                                           size="30"
                                           value="<?= $carIdValue ?>">
                                </div>
                            <? endforeach; ?>
                        <? else: ?>
                            <div class="input">
                                <label for="GUEST_CARS_K_0" class="input-label">Номер
                                    автомобиля 1</label>
                                <input type="text" name="GUEST_CARS_K_0" size="30"
                                       value="<?= $arResult["ORDER_DATA"]["GUEST_CARS"][0] ?>">
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
                        <? if (!empty($arResult['ORDER_DATA']['IS_PAYED'])): ?>
                            <span class="custom-select_title"
                                  data-selected-id="<?= $arResult['ORDER_DATA']['IS_PAYED'] ?>">Оплачен</span>
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
                                <?= $arResult['ORDER_DATA']['BOOKING_COST'] ?>
                            </span>
                    ₽
                </div>
            </div>
        </div>
        <div class="group-btn">
            <button class="primary-btn group-btn_item" type="button" onclick="saveOrderAction()">
                Сохранить изменения
            </button>
        </div>
    </div>
    <div class="form-warn-message"></div>
</form>