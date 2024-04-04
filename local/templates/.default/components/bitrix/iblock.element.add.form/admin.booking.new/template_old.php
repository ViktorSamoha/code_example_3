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
    <h2 class="modal_title">Бронирование</h2>
    <span class="modal_subtitle"></span>
    <form name="iblock_add" action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data"
          class="m-booking-form">
        <?= bitrix_sessid_post() ?>
        <? if (is_array($arResult["FIELDS"]) && !empty($arResult["FIELDS"])): ?>
            <div class="input-group">

                <div class="input input--md">
                    <label for="<?= $arResult["FIELDS"]['STRING']['NAME']['PROP_NAME'] ?>">Имя</label>
                    <input type="text" id="input-user-name"
                           name="<?= $arResult["FIELDS"]['STRING']['NAME']['PROP_NAME'] ?>" value=""/>
                </div>

                <div class="input input--md">
                    <label for="<?= $arResult["FIELDS"]['STRING']['SURNAME']['PROP_NAME'] ?>">Фамилия</label>
                    <input type="text" id="input-user-surname"
                           name="<?= $arResult["FIELDS"]['STRING']['SURNAME']['PROP_NAME'] ?>" value=""/>
                </div>

                <div class="m-input-dates m-input-dates--md js-input-date-group">
                    <div class="m-input-date-block">
                        <label for="<?= $arResult["FIELDS"]['DATE']['ARRIVAL_DATE']['PROP_NAME'] ?>">Дата заезда</label>
                        <input type="text" name="<?= $arResult["FIELDS"]['DATE']['ARRIVAL_DATE']['PROP_NAME'] ?>"
                               class="input-date">
                    </div>
                    <div class="m-input-date-block">
                        <label for="<?= $arResult["FIELDS"]['DATE']['DEPARTURE_DATE']['PROP_NAME'] ?>">Дата
                            выезда</label>
                        <input type="text" name="<?= $arResult["FIELDS"]['DATE']['DEPARTURE_DATE']['PROP_NAME'] ?>"
                               class="input-date second-range-input">
                    </div>
                </div>

                <div class="m-input-dates m-input-dates--md">
                    <div class="m-input-date-block">
                        <label for="<?= $arResult["FIELDS"]['STRING']['CHECK_IN_TIME']['PROP_NAME'] ?>">Время
                            заезда</label>
                        <input type="text" name="<?= $arResult["FIELDS"]['STRING']['CHECK_IN_TIME']['PROP_NAME'] ?>"
                               class="input-time">
                    </div>
                    <div class="m-input-date-block">
                        <label for="<?= $arResult["FIELDS"]['STRING']['DEPARTURE_TIME']['PROP_NAME'] ?>">Время
                            выезда</label>
                        <input type="text" name="<?= $arResult["FIELDS"]['STRING']['DEPARTURE_TIME']['PROP_NAME'] ?>"
                               class="input-time">
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
                            <label for="" class="input-counter-item_title">Взрослых</label>
                            <div class="input-counter">

                                <button class="input-counter_btn" type="button">
                                    <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                    </svg>
                                </button>

                                <input type="text" class="input-counter_input"
                                       name="<?= $arResult["FIELDS"]['NUMBER']['ADULTS']['PROP_NAME'] ?>" value="">

                                <button class="input-counter_btn" type="button">
                                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 6H13V7H0V6Z" fill="#313131"/>
                                        <path d="M6 13L6 4.37112e-08L7 0L7 13H6Z" fill="#313131"/>
                                    </svg>
                                </button>

                            </div>
                        </div>

                        <div class="input-counter-item">
                            <label for="" class="input-counter-item_title">Льготников</label>
                            <div class="input-counter">
                                <button class="input-counter_btn" type="button">
                                    <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                    </svg>
                                </button>
                                <input type="text" class="input-counter_input"
                                       name="<?= $arResult["FIELDS"]['NUMBER']['BENIFICIARIES']['PROP_NAME'] ?>"
                                       value="">
                                <button class="input-counter_btn" type="button">
                                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 6H13V7H0V6Z" fill="#313131"/>
                                        <path d="M6 13L6 4.37112e-08L7 0L7 13H6Z" fill="#313131"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="input-counter-item">
                            <label for="" class="input-counter-item_title">Детей</label>
                            <div class="input-counter">
                                <button class="input-counter_btn" type="button">
                                    <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                    </svg>
                                </button>
                                <input type="text" class="input-counter_input"
                                       name="<?= $arResult["FIELDS"]['NUMBER']['KIDS']['PROP_NAME'] ?>" value="">
                                <button class="input-counter_btn" type="button">
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

                <div class="input input--md">
                    <label for="<?= $arResult["FIELDS"]['STRING']['EMAIL']['PROP_NAME'] ?>">E-mail</label>
                    <input type="email" name="<?= $arResult["FIELDS"]['STRING']['EMAIL']['PROP_NAME'] ?>" value="">
                </div>

                <div class="input input--md">
                    <label for="<?= $arResult["FIELDS"]['STRING']['PHONE']['PROP_NAME'] ?>">Телефон</label>
                    <input type="tel" name="<?= $arResult["FIELDS"]['STRING']['PHONE']['PROP_NAME'] ?>" value="">
                </div>

            </div>
        <? endif ?>

        <input type="hidden" id="input-object-id"
               name="<?= $arResult["FIELDS"]['OBJECT']['BOOKING_OBJECT']['PROP_NAME'] ?>" value="">

        <input type="hidden" id="iblock-record-name" name="PROPERTY[NAME][0]" value="">

        <input type="hidden" name="iblock_submit" value="<?= GetMessage("IBLOCK_FORM_SUBMIT") ?>">

        <? if ($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0): ?>
            <tr>
                <td><?= GetMessage("IBLOCK_FORM_CAPTCHA_TITLE") ?></td>
                <td>
                    <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
                    <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180"
                         height="40" alt="CAPTCHA"/>
                </td>
            </tr>
            <tr>
                <td><?= GetMessage("IBLOCK_FORM_CAPTCHA_PROMPT") ?><span class="starrequired">*</span>:</td>
                <td><input type="text" name="captcha_word" maxlength="50" value=""></td>
            </tr>
        <? endif ?>

        <div class="m-booking-form_bottom">
            <div class="price">
                <span class="price_title">стоимость</span>
                <div class="price_value">4 200 ₽</div>
            </div>
            <input type="submit" class="primary-btn primary-btn--lg" id="bookig-action-button" value="Оплата"/>
        </div>
    </form>
</div>