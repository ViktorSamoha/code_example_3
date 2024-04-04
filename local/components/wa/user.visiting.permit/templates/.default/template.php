<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<form action="" class="form-visiting-permit">
    <input type="hidden" value="<?= $arResult['USER_RECORD_ID'] ?>" name="USER_RECORD_ID">
    <input type="hidden" value="1" name="VISITORS_COUNT">
    <div class="form-block">
        <h3 class="form-block_title">Контакты пользователя</h3>
        <div class="input-group">
            <div class="input input--sm">
                <label for="" class="input-label">Телефон<span class="color-red">*</span></label>
                <input type="text" required value="<?= $arResult['USER_PHONE'] ?>" name="USER_PHONE" maxlength="12">
            </div>
        </div>
    </div>
    <div class="form-block">
        <h3 class="form-block_title">Срок действия</h3>
        <div class="m-input-dates m-input-dates--md js-input-date-group">
            <div class="m-input-date-block">
                <label for="" class="input-label">Дата заезда<span class="color-red">*</span></label>
                <input type="text" class="c-input-date" required name="USER_ARRIVAL_DATE">
            </div>
            <div class="m-input-date-block">
                <label for="" class="input-label">Дата выезда<span class="color-red">*</span></label>
                <input type="text" class="c-input-date second-range-input" name="USER_DEPARTURE_DATE">
            </div>
        </div>
    </div>
    <div class="form-block">
        <h3 class="form-block_title">Данные посетителей</h3>
        <div class="form-visitor-group">
            <? if ($arResult['USER_GROUP']): ?>
                <div class="lk-select-wrap">
                    <div class="select-block">
                        <div class="input-label input-label--mb input-label--gray">Выберите посетителей</div>
                        <div class="custom-select">
                            <div class="custom-select_head">
                                <span class="custom-select_title"></span>
                                <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L7 7L13 1" stroke="#000"/>
                                </svg>
                            </div>
                            <div class="custom-select_body" id="visitors-list">
                                <? foreach ($arResult['USER_GROUP'] as $k => $arVisitor): ?>
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
                <div id="ajax"></div>
            <? endif; ?>
            <div class="form-visitor">
                <div class="input-group">
                    <div class="input input--sm">
                        <span><?= $arResult['USER_LAST_NAME'] . ' ' . $arResult['USER_NAME'] . ' ' . $arResult['USER_SECOND_NAME'] ?></span>
                    </div>
                </div>
            </div>
            <!-- дополнительный посетитель -->
            <div id="ajax"></div>
        </div>
        <br>
        <button class="btn-create" type="button" id="add-visitor">
            <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                        d="M13.668 6.86914V8.41406H0.0234375V6.86914H13.668ZM7.61133 0.511719V15.0039H5.9707V0.511719H7.61133Z"
                        fill="#313131"/>
            </svg>
            <span>Добавить посетителя</span>
        </button>
    </div>
    <div class="form-block border-top">
        <div class="checkbox-list">
            <div class="checkbox">
                <input type="checkbox" id="personal-data-confirm">
                <label for="personal-data-confirm">
                    <div class="checkbox_text">Даю согласие на обработку персональных данных</div>
                </label>
            </div>
            <div class="checkbox">
                <input type="checkbox" id="visiting-rules-confirm">
                <label for="visiting-rules-confirm">
                    <div class="checkbox_text">Я согласен с правилами посещения территории нац. парка</div>
                </label>
            </div>
        </div>
        <div class="price">
            <span class="price_title">К оплате</span>
            <div class="price_value"><span id="permission-price"><?= $arResult['PRICE'] ?></span> ₽</div>
        </div>
    </div>
    <button class="primary-btn primary-btn--lg" id="get-permission">Оформить</button>
    <br>
    <div id="form-errors"></div>
</form>