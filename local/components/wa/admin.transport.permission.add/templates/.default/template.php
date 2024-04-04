<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<form action="" class="form-visiting-permit">
    <div class="form-block">
        <h3 class="form-block_title">Быстрый поиск по зарегистрированным посетителям</h3>
        <div class="search">
            <label for="" class="input-label input-label--mb input-label--gray">Введите номер
                телефона</label>
            <div class="search_wrap">
                <input type="text" class="search_input" id="search-user-phone" value="<?= $arResult['USER_NUMBER'] ?>">
                <button class="search_btn" type="button" id="search-user-action">найти</button>
            </div>
        </div>
        <? if ($arResult['USER_NUMBER']): ?>
            <div class="select-block" id="ajax-select-block">
                <div class="input-label input-label--mb input-label--gray">Посетители, привязанные к номеру
                </div>
                <div class="custom-select" id="ajax-select">
                    <div class="custom-select_head">
                        <span class="custom-select_title"
                              data-selected-id="<?= $arResult['USER_DATA']['USER_RECORD_DATA']['ID'] ?>"><?= $arResult['USER_DATA']['LAST_NAME'] . ' ' . $arResult['USER_DATA']['NAME'] . ' ' . $arResult['USER_DATA']['SECOND_NAME'] ?></span>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? foreach ($arResult['FOUND_USERS'] as $foundUser): ?>
                            <div class="custom-select_item"
                                 data-id="<?= $foundUser['USER_RECORD_ID'] ?>"><?= $foundUser['LAST_NAME'] . ' ' . $foundUser['NAME'] . ' ' . $foundUser['SECOND_NAME'] ?></div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
        <? else: ?>
            <div class="select-block" id="ajax-select-block"></div>
        <? endif; ?>
    </div>
    <? if ($arResult['USER_VEHICLES']): ?>
        <div class="form-block">
            <h3 class="form-block_title">Данные о ТС</h3>
            <div class="lk-select-wrap">
                <div class="select-block">
                    <div class="input-label input-label--mb input-label--gray">Выбрать доступные транспортные
                        средства
                    </div>
                    <div class="custom-select">
                        <div class="custom-select_head">
                            <span class="custom-select_title" id="user-vehicle">Выбрать транспортное средство</span>
                            <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L7 7L13 1" stroke="#000"/>
                            </svg>
                        </div>
                        <div class="custom-select_body">
                            <? foreach ($arResult['USER_VEHICLES'] as $userVehicle): ?>
                                <div class="custom-select_item"
                                     data-id="<?= $userVehicle['ID'] ?>"><?= $userVehicle['VEHICLE_TYPE'] . ' ' . $userVehicle['MODEL'] ?>
                                </div>
                            <? endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <? else: ?>
        <div class="form-block">
            <h3 class="form-block_title">Данные о ТС</h3>
            <div class="select-block">
                <div class="input-label input-label--mb input-label--gray">Укажите тип ТС</div>
                <div class="custom-select">
                    <div class="custom-select_head">
                    <span class="custom-select_title" id="vehicle-type-select"
                          data-selected-id=""></span>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? foreach ($arResult['VEHICLE_TYPES'] as $type): ?>
                            <div class="custom-select_item"
                                 data-id="<?= $type['UF_XML_ID'] ?>"><?= $type['UF_TYPE'] ?></div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
            <h3 class="form-block_title">Сведения о технике</h3>
            <div class="input-group">
                <div class="input input--lg">
                    <label for="" class="input-label">марка и модель ТС</label>
                    <input type="text" required name="MODEL" value="">
                </div>
            </div>
        </div>
    <? endif; ?>
    <div class="form-block">
        <h3 class="form-block_title">Данные о маршруте</h3>
        <div class="select-block">
            <div class="input-label input-label--mb input-label--gray">Выбрать маршрут</div>
            <div class="custom-select">
                <div class="custom-select_head">
                    <span class="custom-select_title" id="route">Выбрать маршрут</span>
                    <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                    </svg>
                </div>
                <div class="custom-select_body">
                    <? foreach ($arResult['ROUTS'] as $route): ?>
                        <div class="custom-select_item" data-id="<?= $route['ID'] ?>"><?= $route['NAME'] ?></div>
                    <? endforeach; ?>
                </div>
            </div>
        </div>
        <div id="route-map"></div>
    </div>

    <div class="form-block">
        <h3 class="form-block_title">Срок действия</h3>
        <div class="m-input-dates m-input-dates--md js-input-date-group">
            <div class="m-input-date-block">
                <label for="" class="input-label">Дата заезда<span class="color-red">*</span></label>
                <input type="text" class="input-date" name="ARRIVAL_DATE">
            </div>
            <div class="m-input-date-block">
                <label for="" class="input-label">Дата выезда<span class="color-red">*</span></label>
                <input type="text" class="input-date second-range-input" name="DEPARTURE_DATE">
            </div>
        </div>
    </div>

    <? if ($arResult['USER_DATA']): ?>
        <div class="form-block">
            <h3 class="form-block_title">Данные посетителя</h3>
            <div class="form-visitor-group">
                <div class="form-visitor js-parent-hidden-block">
                    <input type="hidden" name="USER_RECORD_ID"
                           value="<?= $arResult['USER_DATA']['USER_RECORD_DATA']['ID'] ?>"/>
                    <div class="input-group">
                        <div class="input input--sm">
                            <label for="" class="input-label">Фамилия <span class="color-red">*</span></label>
                            <input type="text" name="LAST_NAME" value="<?= $arResult['USER_DATA']['LAST_NAME'] ?>"/>
                        </div>
                        <div class="input input--sm">
                            <label for="" class="input-label">Имя</label>
                            <input type="text" name="NAME" value="<?= $arResult['USER_DATA']['NAME'] ?>"/>
                        </div>
                        <div class="input input--sm">
                            <label for="" class="input-label">Отчество</label>
                            <input type="text"
                                   name="SECOND_NAME" value="<?= $arResult['USER_DATA']['SECOND_NAME'] ?>"/>
                        </div>
                        <div class="input input--md">
                            <label for="">E-mail</label>
                            <input type="email" name="EMAIL" value="<?= $arResult['USER_DATA']['EMAIL'] ?>"/>
                        </div>
                        <div class="input input--md">
                            <label for="">Телефон</label>
                            <input type="tel" name="PHONE" value="<?= $arResult['USER_DATA']['PHONE'] ?>"/>
                        </div>
                    </div>
                    <div class="form-block js-parent-hidden-block">
                        <div class="checkbox-list">
                            <div class="checkbox">
                                <input type="checkbox" id="pref-category-checkbox"
                                       class="js-switch-hidden-block" <?= $arResult['USER_DATA']['USER_RECORD_DATA']['PREF_CATEGORY'] ? 'checked' : '' ?>>
                                <label for="pref-category-checkbox">
                                    <div class="checkbox_text">Льготная категория</div>
                                </label>
                            </div>
                        </div>
                        <div class="form-block-w-dop-inputs js-show-dop-inputs">
                            <div class="select-block <?= $arResult['USER_DATA']['USER_RECORD_DATA']['PREF_CATEGORY'] ? '' : 'hidden' ?> js-hidden-block">
                                <div class="input-label input-label--mb input-label--gray">Выберите льготу <span
                                            class="color-red">*</span>
                                </div>

                                <div class="custom-select" id="user-edit-pref-select">
                                    <div class="custom-select_head">
                                        <? if ($arResult['USER_DATA']['USER_RECORD_DATA']['PREF_CATEGORY']): ?>
                                            <span class="custom-select_title"
                                                  data-selected-id=""><?= $arResult['USER_DATA']['USER_RECORD_DATA']['PREF_CATEGORY'] ?></span>
                                        <? else: ?>
                                            <span class="custom-select_title"
                                                  data-selected-id="">Льготная категория</span>
                                        <? endif; ?>
                                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                             fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                                        </svg>
                                    </div>
                                    <div class="custom-select_body">
                                        <? foreach ($arResult['PREF_CATEGORIES'] as $pref_category): ?>
                                            <div class="custom-select_item"
                                                 data-id='<?= $pref_category['ID'] ?>'><?= $pref_category['VALUE'] ?></div>
                                        <? endforeach; ?>
                                    </div>
                                </div>
                                <div class="select-block dop-inputs_item <?= $arResult['USER_DATA']['USER_RECORD_DATA']['LOCATION'] ? '' : 'hidden' ?>"
                                     data-id="<?= NATIVE_ID ?>">
                                    <div class="input-label input-label--mb input-label--gray">Выберите населенный
                                        пункт<span
                                                class="color-red">*</span>
                                    </div>
                                    <div class="custom-select" id="user-edit-location-select">
                                        <div class="custom-select_head">
                                            <? if ($arResult['USER_DATA']['USER_RECORD_DATA']['LOCATION']): ?>
                                                <span class="custom-select_title"><?= $arResult['USER_DATA']['USER_RECORD_DATA']['LOCATION'] ?></span>
                                            <? else: ?>
                                                <span class="custom-select_title">Населенный пункт</span>
                                            <? endif; ?>

                                            <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                                 fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 1L7 7L13 1" stroke="#000"/>
                                            </svg>
                                        </div>
                                        <div class="custom-select_body">
                                            <? foreach ($arResult['USER_LOCATIONS'] as $user_location): ?>
                                                <div class="custom-select_item"
                                                     data-id='<?= $user_location['ID'] ?>'><?= $user_location['VALUE'] ?></div>
                                            <? endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="input-group">
                                    <div class="input input--sm">
                                        <label for="" class="input-label">Номер док-та подтверждающего льготу<span
                                                    class="color-red">*</span></label>
                                        <input type="text" name="PREF_DOC_NUMBER"
                                               value="<?= $arResult['USER_DATA']['USER_RECORD_DATA']['PREF_DOC_NUMBER'] ?>">
                                    </div>
                                    <div class="input input--sm">
                                        <label for="" class="input-label">Дата выдачи<span
                                                    class="color-red">*</span></label>
                                        <input type="text" class="input-date c-doc-date" name="PREF_DOC_DATE"
                                               value="<?= $arResult['USER_DATA']['USER_RECORD_DATA']['PREF_DOC_DATE'] ?>">
                                    </div>
                                    <div class="input input--sm input--message">
                                        <span class="color-red">Данный документ должен быть при вас</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    <? else: ?>
        <div class="form-block">
            <h3 class="form-block_title">Данные посетителя</h3>

            <div class="form-visitor-group">
                <div class="form-visitor js-parent-hidden-block">
                    <div class="input-group">
                        <div class="input input--sm">
                            <label for="" class="input-label">Фамилия <span class="color-red">*</span></label>
                            <input type="text" name="LAST_NAME" required/>
                        </div>
                        <div class="input input--sm">
                            <label for="" class="input-label">Имя <span class="color-red">*</span></label>
                            <input type="text" name="NAME" required/>
                        </div>
                        <div class="input input--sm">
                            <label for="" class="input-label">Отчество</label>
                            <input type="text"
                                   name="SECOND_NAME"/>
                        </div>
                        <div class="input input--md">
                            <label for="">E-mail<span class="color-red">*</span></label>
                            <input type="email" name="EMAIL" required/>
                        </div>
                        <div class="input input--md">
                            <label for="">Телефон<span class="color-red">*</span></label>
                            <input type="tel" name="PHONE" required/>
                        </div>
                    </div>
                    <div class="form-block js-parent-hidden-block">
                        <div class="checkbox-list">
                            <div class="checkbox">
                                <input type="checkbox" id="pref-category-checkbox"
                                       class="js-switch-hidden-block">
                                <label for="pref-category-checkbox">
                                    <div class="checkbox_text">Льготная категория</div>
                                </label>
                            </div>
                        </div>
                        <div class="form-block-w-dop-inputs js-show-dop-inputs">
                            <div class="select-block hidden js-hidden-block">
                                <div class="input-label input-label--mb input-label--gray">Выберите льготу <span
                                            class="color-red">*</span>
                                </div>

                                <div class="custom-select" id="user-edit-pref-select">
                                    <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-selected-id="">Льготная категория</span>
                                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                             fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                                        </svg>
                                    </div>
                                    <div class="custom-select_body">
                                        <? foreach ($arResult['PREF_CATEGORIES'] as $pref_category): ?>
                                            <div class="custom-select_item"
                                                 data-id='<?= $pref_category['ID'] ?>'><?= $pref_category['VALUE'] ?></div>
                                        <? endforeach; ?>
                                    </div>
                                </div>
                                <div class="select-block dop-inputs_item hidden" data-id="<?= NATIVE_ID ?>">
                                    <div class="input-label input-label--mb input-label--gray">Выберите населенный
                                        пункт<span
                                                class="color-red">*</span>
                                    </div>
                                    <div class="custom-select" id="user-edit-location-select">
                                        <div class="custom-select_head">
                                            <span class="custom-select_title">Населенный пункт</span>
                                            <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                                 fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 1L7 7L13 1" stroke="#000"/>
                                            </svg>
                                        </div>
                                        <div class="custom-select_body">
                                            <? foreach ($arResult['USER_LOCATIONS'] as $user_location): ?>
                                                <div class="custom-select_item"
                                                     data-id='<?= $user_location['ID'] ?>'><?= $user_location['VALUE'] ?></div>
                                            <? endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="input-group">
                                    <div class="input input--sm">
                                        <label for="" class="input-label">Номер док-та подтверждающего льготу<span
                                                    class="color-red">*</span></label>
                                        <input type="text" name="PREF_DOC_NUMBER">
                                    </div>
                                    <div class="input input--sm">
                                        <label for="" class="input-label">Дата выдачи<span
                                                    class="color-red">*</span></label>
                                        <input type="text" class="input-date c-doc-date" name="PREF_DOC_DATE">
                                    </div>
                                    <div class="input input--sm input--message">
                                        <span class="color-red">Данный документ должен быть при вас</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <? endif; ?>
    <div class="form-block">
        <h3 class="form-block_title">Разрешение на посещение</h3>
        <div class="radio-group">
            <div class="radio">
                <input type="radio" id="visit-permission-yes" name="visit-permission" value="yes" checked>
                <label for="visit-permission-yes">
                    <div class="radio_text">Есть</div>
                </label>
            </div>
            <div class="radio">
                <input type="radio" id="visit-permission-no" name="visit-permission" value="no">
                <label for="visit-permission-no">
                    <div class="radio_text">Нет</div>
                </label>
            </div>
        </div>
        <div class="input input--lg" id="visit-permission-input">
            <label for="">Номер разрешения на посещение</label>
            <input type="text" value="" name="PERMISSION_CODE">
        </div>
    </div>
    <button class="primary-btn primary-btn--lg" id="add-vehicle-permission">Оформить</button>
    <br>
    <div id="form-errors"></div>
</form>
