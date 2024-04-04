<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<form action="" class="form-visiting-permit">
    <input type="hidden" value="0" name="VISITORS_COUNT">
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
                              data-name="USER_RECORD_ID"
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
    <div class="form-block">
        <h3 class="form-block_title">Срок действия</h3>
        <div class="m-input-dates m-input-dates--md js-input-date-group">
            <div class="m-input-date-block">
                <label for="" class="input-label">Дата заезда<span class="color-red">*</span></label>
                <input type="text" class="c-input-date" name="ARRIVAL_DATE">
            </div>
            <div class="m-input-date-block">
                <label for="" class="input-label">Дата выезда<span class="color-red">*</span></label>
                <input type="text" class="c-input-date second-range-input" name="DEPARTURE_DATE">
            </div>
        </div>
    </div>
    <? if ($arResult['USER_DATA']): ?>
        <div class="form-block">
            <h3 class="form-block_title">Данные пользователя</h3>

            <div class="form-visitor-group">
                <div class="form-visitor js-parent-hidden-block">
                    <div class="input-group">
                        <div class="input input--sm">
                            <label for="" class="input-label">Фамилия</label>
                            <input type="text" value="<?= $arResult['USER_DATA']['LAST_NAME'] ?>" readonly>
                        </div>
                        <div class="input input--sm">
                            <label for="" class="input-label">Имя</label>
                            <input type="text" value="<?= $arResult['USER_DATA']['NAME'] ?>" readonly>
                        </div>
                        <div class="input input--sm">
                            <label for="" class="input-label">Отчество</label>
                            <input type="text" value="<?= $arResult['USER_DATA']['SECOND_NAME'] ?>" readonly>
                        </div>
                    </div>

                    <div class="checkbox-list">
                        <div class="checkbox">
                            <input type="checkbox" id="checkbox_01"
                                   class="js-switch-hidden-block" <?= $arResult['USER_DATA']['USER_RECORD_DATA']['PREF_CATEGORY'] ? 'checked' : '' ?>>
                            <label for="checkbox_01">
                                <div class="checkbox_text">Льготная категория</div>
                            </label>
                        </div>
                    </div>
                    <? if ($arResult['USER_DATA']['USER_RECORD_DATA']): ?>
                        <div class="form-block-w-dop-inputs js-show-dop-inputs js-hidden-block">
                            <div class="input input--sm">
                                <label for="" class="input-label">Льготная категория</label>
                                <input type="text"
                                       value="<?= $arResult['USER_DATA']['USER_RECORD_DATA']['PREF_CATEGORY'] ?>"
                                       readonly>
                            </div>
                            <div class="dop-inputs_item ">
                                <? if ($arResult['USER_DATA']['USER_RECORD_DATA']['PREF_CATEGORY'] == "Проживающие в близлежащих населенных пунктах"): ?>
                                    <div class="input input--sm">
                                        <label for="" class="input-label">Населенный пункт</label>
                                        <input type="text"
                                               value="<?= $arResult['USER_DATA']['USER_RECORD_DATA']['LOCATION'] ?>"
                                               readonly>
                                    </div>
                                <? endif; ?>
                                <div class="input-group">
                                    <div class="input input--sm">
                                        <label for="" class="input-label">Номер док-та подтверждающего
                                            льготу</label>
                                        <input type="text"
                                               value="<?= $arResult['USER_DATA']['USER_RECORD_DATA']['PREF_DOC_NUMBER'] ?>"
                                               readonly>
                                    </div>
                                    <div class="input input--sm">
                                        <label for="" class="input-label">Дата выдачи</label>
                                        <input type="text" class="input-date"
                                               value="<?= $arResult['USER_DATA']['USER_RECORD_DATA']['PREF_DOC_DATE'] ?>"
                                               readonly>
                                    </div>
                                    <div class="input input--sm input--message">
                                        <span class="color-red">Данный документ должен быть при вас</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? endif; ?>
                </div>
            </div>
        </div>

        <div class="form-block">
            <h3 class="form-block_title">Данные посетителей</h3>
            <div class="form-visitor-group">
                <? if ($arResult['USER_DATA']['USER_GROUP']): ?>
                    <div class="lk-select-wrap">
                        <div class="select-block">
                            <div class="input-label input-label--mb input-label--gray">Выберите посетителей</div>
                            <div class="custom-select">
                                <div class="custom-select_head">
                                    <span class="custom-select_title">Добавить посетителя</span>
                                    <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                                    </svg>
                                </div>
                                <div class="custom-select_body" id="visitors-list">
                                    <? foreach ($arResult['USER_DATA']['USER_GROUP'] as $k => $arVisitor): ?>
                                        <div class="custom-select_item">

                                            <div class="checkbox checkbox-w-btn">
                                                <input type="checkbox" id="checkbox_<?= $k ?>"
                                                       value="<?= $arVisitor['ID'] ?>"
                                                       name="VISITORS[]">
                                                <label for="checkbox_<?= $k ?>">
                                                    <div class="checkbox_text"><?= $arVisitor['LAST_NAME'] . ' ' . $arVisitor['NAME'] . ' ' . $arVisitor['SECOND_NAME'] ?></div>
                                                </label>
                                            </div>

                                        </div>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <!--<button type="button" class="search_btn" id="add-visitors">
                            <span>Выбрать</span>
                        </button>-->
                    </div>
                    <div id="ajax-visitors"></div>
                <? endif; ?>
                <div class="form-visitor">
                    <div class="input-group">
                        <div class="input input--sm">
                            <span><?= $arResult['USER_DATA']['LAST_NAME'] . ' ' . $arResult['USER_DATA']['NAME'] . ' ' . $arResult['USER_DATA']['SECOND_NAME'] ?></span>
                        </div>
                    </div>
                </div>
                <!-- дополнительный посетитель -->
                <div id="ajax-visitors"></div>
            </div>
            <!-- <br>
             <button class="btn-create" type="button" id="add-visitor">
                 <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path
                             d="M13.668 6.86914V8.41406H0.0234375V6.86914H13.668ZM7.61133 0.511719V15.0039H5.9707V0.511719H7.61133Z"
                             fill="#313131"/>
                 </svg>
                 <span>Добавить посетителя</span>
             </button>-->
        </div>
    <? endif; ?>
    <div class="form-block border-top">
        <div class="price">
            <span class="price_title">К оплате</span>
            <div class="price_value"><span id="permission-price"><?= $arResult['PRICE'] ?></span> ₽</div>
        </div>
    </div>
    <button class="primary-btn primary-btn--lg" id="get-permission">Оформить</button>
    <br>
    <div id="form-errors"></div>
</form>