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

$APPLICATION->SetTitle("Создание нового объекта");
?>
<? if (!empty($arResult["ERRORS"])): ?>
    <? ShowError(implode("<br />", $arResult["ERRORS"])) ?>
<? elseif ($arResult["MESSAGE"] <> ''): ?>
    <? ShowNote($arResult["MESSAGE"]) ?>
<? else: ?>
    <form class="form-create-object" name="iblock_add" action="<?= POST_FORM_ACTION_URI ?>" method="post"
          enctype="multipart/form-data">
        <?= bitrix_sessid_post() ?>
        <? if ($arParams["MAX_FILE_SIZE"] > 0): ?><input type="hidden" name="MAX_FILE_SIZE"
                                                         value="<?= $arParams["MAX_FILE_SIZE"] ?>" /><? endif ?>
        <div class="form-block">
            <h3 class="form-block_title">Данные об объекте</h3>
            <div class="input">
                <label for="" class="input-label input-label--mb input-label--gray">Название объекта</label>
                <input type="text" name="<?= $arResult['FIELDS']['STRING']['NAME']['PROP_NAME'] ?>" value="">
            </div>
            <div class="input">
                <label for="" class="input-label input-label--mb input-label--gray">Сортировка (положение объекта в
                    списке)</label>
                <input type="text" name="PROPERTY[SORT][0]" value="">
            </div>

            <div class="lk-select-wrap">
                <div class="select-block">
                    <span class="input-label input-label--mb input-label--gray">Выберите или создайте категорию </span>

                    <div class="custom-select">

                        <div class="custom-select_head">
                            <span class="custom-select_title">Категория</span>
                            <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L7 7L13 1" stroke="#000"/>
                            </svg>
                        </div>

                        <div class="custom-select_body"
                             data-select-name="<?= $arResult['FIELDS']['SELECT']['IBLOCK_SECTION']['PROP_NAME'] ?>"
                             id="category-select-body">
                            <? foreach ($arResult['FIELDS']['SECTIONS'] as $section): ?>
                                <div class="custom-select_item"
                                     data-id="<?= $section['ID'] ?>"><?= $section['NAME'] ?>
                                    <button class="select-btn-delete"
                                            type="button"
                                            data-select-type="category"
                                            data-item-id="<?= $section['ID'] ?>"
                                    >
                                        <svg width="11" height="12" viewBox="0 0 11 12" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.351562 1.2627L10.5054 11.4165M10.6445 1.2627L0.49061 11.4165"
                                                  stroke="#F71E1E"/>
                                        </svg>
                                    </button>
                                </div>
                            <? endforeach; ?>
                        </div>

                    </div>

                </div>
                <button class="btn-create js-open-r-modal" data-name="modal-add-category" type="button">
                    <svg width="14" height="16" viewBox="0 0 14 16" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M13.668 6.86914V8.41406H0.0234375V6.86914H13.668ZM7.61133 0.511719V15.0039H5.9707V0.511719H7.61133Z"
                                fill="#313131"/>
                    </svg>
                    <span>Создать категорию</span>
                </button>
            </div>
            <div class="lk-select-wrap">
                <div class="select-block">
                    <span class="input-label input-label--mb input-label--gray">Локализация</span>
                    <div class="custom-select">
                        <div class="custom-select_head">
                            <span class="custom-select_title">Локация</span>
                            <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L7 7L13 1" stroke="#000"/>
                            </svg>
                        </div>
                        <div class="custom-select_body"
                             data-select-name="<?= $arResult['FIELDS']['OBJECT']['LOCATION']['PROP_NAME'] . "[0]" ?>"
                             id="location-select-body">
                            <? foreach ($arResult['FIELDS']['LOCATIONS'] as $location): ?>
                                <div class="custom-select_item"
                                     data-id="<?= $location['ID'] ?>">
                                    <?= $location['NAME'] ?>
                                    <button class="select-btn-delete"
                                            data-select-type="location"
                                            type="button"
                                            data-item-id="<?= $location['ID'] ?>"
                                    >
                                        <svg width="11" height="12" viewBox="0 0 11 12" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.351562 1.2627L10.5054 11.4165M10.6445 1.2627L0.49061 11.4165"
                                                  stroke="#F71E1E"/>
                                        </svg>
                                    </button>
                                </div>
                            <? endforeach; ?>
                        </div>
                    </div>
                </div>
                <button class="btn-create js-open-r-modal" data-name="modal-add-location" type="button">
                    <svg width="14" height="16" viewBox="0 0 14 16" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M13.668 6.86914V8.41406H0.0234375V6.86914H13.668ZM7.61133 0.511719V15.0039H5.9707V0.511719H7.61133Z"
                                fill="#313131"/>
                    </svg>
                    <span>Создать локацию</span>
                </button>
            </div>
            <div class="lk-select-wrap">
                <div class="select-block">
                    <span class="input-label input-label--mb input-label--gray">Выберите или создайте партнера</span>
                    <div class="custom-select">
                        <div class="custom-select_head">
                            <span class="custom-select_title">Партнер</span>
                            <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L7 7L13 1" stroke="#000"/>
                            </svg>
                        </div>
                        <div class="custom-select_body"
                             data-select-name="PROPERTY[70]"
                             id="partner-select-body">
                            <? foreach ($arResult['FIELDS']['PARTNERS'] as $partner): ?>
                                <div class="custom-select_item">
                                    <div class="checkbox checkbox-w-btn">
                                        <input type="checkbox"
                                               id="partner_cb_<?= $partner['ID'] ?>"
                                               value="<?= $partner['ID'] ?>"
                                               name="PROPERTY[70][]"
                                        >
                                        <label for="partner_cb_<?= $partner['ID'] ?>">
                                            <div class="checkbox_text"><?= $partner['NAME'] ?></div>
                                        </label>
                                    </div>
                                </div>
                            <? endforeach; ?>
                        </div>
                    </div>
                </div>
                <button class="btn-create js-open-r-modal" data-name="modal-add-partner" type="button">
                    <svg width="14" height="16" viewBox="0 0 14 16" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M13.668 6.86914V8.41406H0.0234375V6.86914H13.668ZM7.61133 0.511719V15.0039H5.9707V0.511719H7.61133Z"
                                fill="#313131"/>
                    </svg>
                    <span>Добавить партнера</span>
                </button>
            </div>
            <button class="put-on-map-btn js-open-r-modal" data-name="modal-put-on-map" type="button">
                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M11.5026 11.2607C11.9658 11.2607 12.3611 11.097 12.6885 10.7696C13.016 10.4422 13.1797 10.0469 13.1797 9.58366C13.1797 9.12046 13.016 8.72515 12.6885 8.39772C12.3611 8.07029 11.9658 7.90658 11.5026 7.90658C11.0394 7.90658 10.6441 8.07029 10.3167 8.39772C9.98924 8.72515 9.82552 9.12046 9.82552 9.58366C9.82552 10.0469 9.98924 10.4422 10.3167 10.7696C10.6441 11.097 11.0394 11.2607 11.5026 11.2607ZM11.5026 21.0837C8.93108 18.8955 7.01042 16.863 5.74063 14.9863C4.47083 13.1095 3.83594 11.3725 3.83594 9.77533C3.83594 7.37949 4.6066 5.47081 6.14792 4.04928C7.68924 2.62776 9.47413 1.91699 11.5026 1.91699C13.5311 1.91699 15.316 2.62776 16.8573 4.04928C18.3986 5.47081 19.1693 7.37949 19.1693 9.77533C19.1693 11.3725 18.5344 13.1095 17.2646 14.9863C15.9948 16.863 14.0741 18.8955 11.5026 21.0837Z"
                            fill="black"/>
                </svg>
                <span>Отметить на карте</span>
            </button>
            <div class="input-file-wrap">
            <span class="input-label input-label--mb15 input-label--gray">
                Загрузите фото
            </span>
                <div class="input-file input-file--photo">
                    <input id="file-input" type="file" data-id="5" multiple accept="image/png, image/jpeg">
                    <label for="file-input">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M11.4974 19.4337H24.5307L20.2974 13.7337L16.8641 18.2337L14.5974 15.3337L11.4974 19.4337ZM8.66406 25.3337C8.13073 25.3337 7.66406 25.1337 7.26406 24.7337C6.86406 24.3337 6.66406 23.867 6.66406 23.3337V4.66699C6.66406 4.13366 6.86406 3.66699 7.26406 3.26699C7.66406 2.86699 8.13073 2.66699 8.66406 2.66699H27.3307C27.8641 2.66699 28.3307 2.86699 28.7307 3.26699C29.1307 3.66699 29.3307 4.13366 29.3307 4.66699V23.3337C29.3307 23.867 29.1307 24.3337 28.7307 24.7337C28.3307 25.1337 27.8641 25.3337 27.3307 25.3337H8.66406ZM4.66406 29.3337C4.13073 29.3337 3.66406 29.1337 3.26406 28.7337C2.86406 28.3337 2.66406 27.867 2.66406 27.3337V6.66699H4.66406V27.3337H25.3307V29.3337H4.66406Z"
                                    fill="white"/>
                        </svg>
                        <span>Загрузить файл</span>
                    </label>

                </div>
            </div>
            <div class="input-file-wrap">
            <span class="input-label input-label--mb15 input-label--gray">
                Загрузите превью для видео
            </span>
                <div class="input-file input-file--photo">
                    <input id="preview-file-input" data-id="72" type="file" multiple accept="image/png, image/jpeg">
                    <label for="preview-file-input">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M11.4974 19.4337H24.5307L20.2974 13.7337L16.8641 18.2337L14.5974 15.3337L11.4974 19.4337ZM8.66406 25.3337C8.13073 25.3337 7.66406 25.1337 7.26406 24.7337C6.86406 24.3337 6.66406 23.867 6.66406 23.3337V4.66699C6.66406 4.13366 6.86406 3.66699 7.26406 3.26699C7.66406 2.86699 8.13073 2.66699 8.66406 2.66699H27.3307C27.8641 2.66699 28.3307 2.86699 28.7307 3.26699C29.1307 3.66699 29.3307 4.13366 29.3307 4.66699V23.3337C29.3307 23.867 29.1307 24.3337 28.7307 24.7337C28.3307 25.1337 27.8641 25.3337 27.3307 25.3337H8.66406ZM4.66406 29.3337C4.13073 29.3337 3.66406 29.1337 3.26406 28.7337C2.86406 28.3337 2.66406 27.867 2.66406 27.3337V6.66699H4.66406V27.3337H25.3307V29.3337H4.66406Z"
                                    fill="white"/>
                        </svg>
                        <span>Загрузить файл</span>
                    </label>
                </div>
            </div>
            <div class="input-file-wrap">
            <span class="input-label input-label--mb15 input-label--gray">
                Загрузите видео
            </span>
                <div class="input-file input-file--photo">
                    <input id="video-file-input" data-id="73" type="file" multiple accept="video/mp4, video/webm">
                    <label for="video-file-input">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M11.4974 19.4337H24.5307L20.2974 13.7337L16.8641 18.2337L14.5974 15.3337L11.4974 19.4337ZM8.66406 25.3337C8.13073 25.3337 7.66406 25.1337 7.26406 24.7337C6.86406 24.3337 6.66406 23.867 6.66406 23.3337V4.66699C6.66406 4.13366 6.86406 3.66699 7.26406 3.26699C7.66406 2.86699 8.13073 2.66699 8.66406 2.66699H27.3307C27.8641 2.66699 28.3307 2.86699 28.7307 3.26699C29.1307 3.66699 29.3307 4.13366 29.3307 4.66699V23.3337C29.3307 23.867 29.1307 24.3337 28.7307 24.7337C28.3307 25.1337 27.8641 25.3337 27.3307 25.3337H8.66406ZM4.66406 29.3337C4.13073 29.3337 3.66406 29.1337 3.26406 28.7337C2.86406 28.3337 2.66406 27.867 2.66406 27.3337V6.66699H4.66406V27.3337H25.3307V29.3337H4.66406Z"
                                    fill="white"/>
                        </svg>
                        <span>Загрузить файл</span>
                    </label>

                </div>
            </div>

            <div class="textarea">
                <label for="" class="input-label input-label--mb input-label--gray">Описание объекта</label>
                <textarea name="<?= $arResult['FIELDS']['STRING']['DETAIL_TEXT']['PROP_NAME'] ?>"></textarea>
            </div>
        </div>
        <div class="form-block">
            <h3 class="form-block_title">Краткие характеристики</h3>
            <div class="checkbox-list" id="characteristic-select">
                <? foreach ($arResult['FIELDS']['FEATURES']['DETAIL_FEATURES']['VALUES'] as $i => $feature): ?>
                    <div class="checkbox checkbox-w-btn">
                        <input type="checkbox"
                               id="checkbox_<?= $i ?>"
                               value="<?= $feature['VALUE'] ?>"
                               name="<?= "PROPERTY[8][" . $i . "][VALUE]" ?>"
                               data-number="<?= $i ?>">
                        <label for="checkbox_<?= $i ?>">
                            <div class="checkbox_text"><?= $feature['NAME'] ?></div>
                            <button class="btn-delete"
                                    type="button"
                                    data-item-id="<?= $feature['VALUE'] ?>"
                            >
                                <svg width="11" height="12" viewBox="0 0 11 12" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.351562 1.2627L10.5054 11.4165M10.6445 1.2627L0.49061 11.4165"
                                          stroke="#F71E1E"/>
                                </svg>
                            </button>
                        </label>
                    </div>
                <? endforeach; ?>
            </div>
            <button class="btn-create js-open-r-modal" data-name="modal-add-characteristic" type="button">
                <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M13.668 6.86914V8.41406H0.0234375V6.86914H13.668ZM7.61133 0.511719V15.0039H5.9707V0.511719H7.61133Z"
                            fill="#313131"/>
                </svg>
                <span>Создать характеристику</span>
            </button>
        </div>

        <div class="form-block">
            <h3 class="form-block_title">Стоимость услуги</h3>
            <div class="form-item">
                <h4 class="form-item_title">Временной интервал</h4>
                <div class="checkbox-group">
                    <? foreach ($arResult['FIELDS']['CHECK']['TIME_INTERVAL']['VALUES'] as $i => $time_interval_value): ?>
                        <div class="checkbox">
                            <input type="checkbox" id="time_checkbox_<?= $i ?>"
                                   name="<?= $arResult['FIELDS']['CHECK']['TIME_INTERVAL']['PROP_NAME'] . '[' . $i . ']' ?>"
                                   value="<?= $time_interval_value['ID'] ?>">
                            <label for="time_checkbox_<?= $i ?>">
                                <div class="checkbox_text"><?= $time_interval_value['VALUE'] ?></div>
                            </label>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
            <div class="form-item">
                <h4 class="form-item_title">Стоимость услуги</h4>
                <div class="checkbox-group">
                    <? foreach ($arResult['FIELDS']['CHECK']['SERVICE_COST']['VALUES'] as $i => $service_cost_value): ?>
                        <div class="checkbox">
                            <input type="checkbox" id="service_checkbox_<?= $i ?>"
                                   name="<?= $arResult['FIELDS']['CHECK']['SERVICE_COST']['PROP_NAME'] . '[' . $i . ']' ?>"
                                   value="<?= $service_cost_value['ID'] ?>">
                            <label for="service_checkbox_<?= $i ?>">
                                <div class="checkbox_text"><?= $service_cost_value['VALUE'] ?></div>
                            </label>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
            <div class="form-item">
                <h4 class="form-item_title">Возможность бронирования</h4>
                <div class="radio-list">
                    <? foreach ($arResult['FIELDS']['SELECT']['36']['VALUES'] as $i => $radio_btn): ?>
                        <div class="radio">
                            <input type="radio"
                                   id="radio_btn_<?= $i ?>"
                                   name="PROPERTY[36][0][VALUE]"
                                   value="<?= $radio_btn['ID'] ?>"
                            >
                            <label for="radio_btn_<?= $i ?>">
                                <div class="radio_text"><?= $radio_btn['VALUE'] ?></div>
                            </label>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
            <div class="form-item" id="booking-alert-msg-block" style="display: none">
                <label for="" class="input-label input-label--mb input-label--gray">Сообщение при невозможности
                    бронирования</label>
                <?

                $LHE = new CHTMLEditor;
                $LHE->Show(array(
                    'name' => "PROPERTY[38][0]",
                    'id' => preg_replace("/[^a-z0-9]/i", '', "PROPERTY[38][0]"),
                    'inputName' => "PROPERTY[38][0]",
                    'content' => '',
                    'width' => '100%',
                    'minBodyWidth' => 350,
                    'normalBodyWidth' => 555,
                    'height' => '200',
                    'bAllowPhp' => false,
                    'limitPhpAccess' => false,
                    'autoResize' => true,
                    'autoResizeOffset' => 40,
                    'useFileDialogs' => false,
                    'saveOnBlur' => true,
                    'showTaskbars' => false,
                    'showNodeNavi' => false,
                    'askBeforeUnloadPage' => false,
                    'bbCode' => false,
                    'siteId' => SITE_ID,
                    'controlsMap' => array(
                        array('id' => 'Bold', 'compact' => true, 'sort' => 80),
                        array('id' => 'Italic', 'compact' => true, 'sort' => 90),
                        array('id' => 'Underline', 'compact' => true, 'sort' => 100),
                        array('id' => 'Strikeout', 'compact' => true, 'sort' => 110),
                        array('id' => 'RemoveFormat', 'compact' => true, 'sort' => 120),
                        array('id' => 'Color', 'compact' => true, 'sort' => 130),
                        array('id' => 'FontSelector', 'compact' => false, 'sort' => 135),
                        array('id' => 'FontSize', 'compact' => false, 'sort' => 140),
                        array('separator' => true, 'compact' => false, 'sort' => 145),
                        array('id' => 'OrderedList', 'compact' => true, 'sort' => 150),
                        array('id' => 'UnorderedList', 'compact' => true, 'sort' => 160),
                        array('id' => 'AlignList', 'compact' => false, 'sort' => 190),
                        array('separator' => true, 'compact' => false, 'sort' => 200),
                        array('id' => 'InsertLink', 'compact' => true, 'sort' => 210),
                        array('id' => 'InsertImage', 'compact' => false, 'sort' => 220),
                        array('id' => 'InsertVideo', 'compact' => true, 'sort' => 230),
                        array('id' => 'InsertTable', 'compact' => false, 'sort' => 250),
                        array('separator' => true, 'compact' => false, 'sort' => 290),
                        array('id' => 'Fullscreen', 'compact' => false, 'sort' => 310),
                        array('id' => 'More', 'compact' => true, 'sort' => 400)
                    ),
                ));

                ?>
            </div>
            <div class="form-item">
                <h4 class="form-item_title">Возможность добраться на машине</h4>
                <div class="radio-list" id="car-radio-list">
                    <div class="radio">
                        <input type="radio"
                               id="car-yes"
                               name="PROPERTY[<?= CAR_POSSIBILITY ?>][0][VALUE]"
                               value="<?= CAR_POSSIBILITY_YES_VAL_LIST_ID ?>"
                        >
                        <label for="car-yes">
                            <div class="radio_text">Да</div>
                        </label>
                    </div>
                    <div class="radio">
                        <input type="radio"
                               id="car-no"
                               name="PROPERTY[<?= CAR_POSSIBILITY ?>][0][VALUE]"
                               value="<?= CAR_POSSIBILITY_NO_VAL_LIST_ID ?>"
                        >
                        <label for="car-no">
                            <div class="radio_text">Нет</div>
                        </label>
                    </div>
                </div>
                <div class="input-counter-item" id="car-capacity-block" style="display: none">
                    <label for="" class="input-counter-item_title">Количество машин</label>
                    <div class="input-counter">
                        <button class="input-counter_btn" type="button" data-input-id="car-capacity"
                                data-action="minus">
                            <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                            </svg>
                        </button>
                        <input type="text" class="input-counter_input" value="1"
                               name="PROPERTY[<?= CAR_CAPACITY ?>][0][VALUE]"
                               id="car-capacity">
                        <button class="input-counter_btn" type="button" data-input-id="car-capacity"
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
            <div class="form-item">
                <h4 class="form-item_title">Объект без ограничения по времени</h4>
                <div class="radio-list">
                    <? foreach ($arResult['FIELDS']['SELECT']['66']['VALUES'] as $i => $radio_btn): ?>
                        <div class="radio">
                            <input type="radio"
                                   id="time_limit_radio_btn_<?= $i ?>"
                                   name="PROPERTY[66][0][VALUE]"
                                   value="<?= $radio_btn['ID'] ?>"
                            >
                            <label for="time_limit_radio_btn_<?= $i ?>">
                                <div class="radio_text"><?= $radio_btn['VALUE'] ?></div>
                            </label>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
            <div class="form-item">
                <h4 class="form-item_title">Вместительность объекта (человек)</h4>
                <div class="input-counter-group">
                    <div class="input-counter-item">
                        <label for="" class="input-counter-item_title">Рассчетная</label>
                        <div class="input-counter">

                            <button class="input-counter_btn" type="button" data-input-id="capacity-est"
                                    data-action="minus">
                                <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                </svg>
                            </button>

                            <input type="text" class="input-counter_input" value="1"
                                   name="<?= $arResult['FIELDS']['STRING']['CAPACITY_ESTIMATED']['PROP_NAME'] ?>"
                                   id="capacity-est">

                            <button class="input-counter_btn" type="button" data-input-id="capacity-est"
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
                        <label for="" class="input-counter-item_title">Максимальная</label>
                        <div class="input-counter">
                            <button class="input-counter_btn" type="button" data-input-id="capacity-max"
                                    data-action="minus">
                                <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                                </svg>
                            </button>

                            <input type="text" class="input-counter_input" value="1"
                                   name="<?= $arResult['FIELDS']['STRING']['CAPACITY_MAXIMUM']['PROP_NAME'] ?>"
                                   id="capacity-max">

                            <button class="input-counter_btn" type="button" data-input-id="capacity-max"
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
                <div class="input-group" id="price-block" style="display: none">
                    <div class="input input-lk-price" data-type="non-fix-price">
                        <label for="">Стоимость суточного пребывания</label>
                        <input type="text"
                               value=""
                               name="PROPERTY[30][0]">
                    </div>
                    <div class="input input-lk-price" data-type="non-fix-price">
                        <label for="">Стоимость чел. сверх рассчитанной до максимальной (руб.)</label>
                        <input type="text" value=""
                               name="<?= $arResult['FIELDS']['STRING']['COST_PER_PERSON']['PROP_NAME'] ?>">
                    </div>
                    <div class="input input-lk-price" data-type="non-fix-price">
                        <label for="">Стоимость дневного пребывания</label>
                        <input type="text"
                               value=""
                               name="PROPERTY[35][0]">
                    </div>
                    <div class="input input-lk-price" data-type="non-fix-price">
                        <label for="">Стоимость чел. сверх рассчитанной до максимальной (руб.)</label>
                        <input type="text" value=""
                               name="PROPERTY[37][0]">
                    </div>
                    <div class="input input-lk-price" data-type="fix-price">
                        <label for="">Фиксированная стоимость (руб.)</label>
                        <input type="text" value=""
                               name="PROPERTY[67][0]">
                    </div>
                </div>
            </div>

            <div class="form-item" id="work-time-node" style="display: none">
                <h4 class="form-item_title">Время работы</h4>
                <div class="input-group">
                    <div class="m-input-dates m-input-dates--md">
                        <div class="m-input-date-block">
                            <label for="">Время начала работы<span class="color-red">*</span></label>
                            <div class="custom-select custom-select--sm" id="start-time-select">
                                <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время начала работы">Время начала работы</span>
                                    <svg class="custom-select_icon" width="14" height="8"
                                         viewBox="0 0 14 8"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                                    </svg>
                                </div>
                                <div class="custom-select_body" data-select-name="PROPERTY[68][0]">
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
                                </div>
                            </div>
                        </div>
                        <div class="m-input-date-block">
                            <label for="">Время окончания работы<span class="color-red">*</span></label>
                            <div class="custom-select custom-select--sm" id="end-time-select">
                                <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время окончания работы">Время окончания работы</span>
                                    <svg class="custom-select_icon" width="14" height="8"
                                         viewBox="0 0 14 8"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                                    </svg>
                                </div>
                                <div class="custom-select_body" data-select-name="PROPERTY[69][0]">
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="submit" class="primary-btn primary-btn--xl" value="Сохранить">

        <? /* hidden inputs */ ?>
        <div style="display: none" id="hidden-inputs">
            <input type="hidden" name="iblock_submit" value="<?= GetMessage("IBLOCK_FORM_SUBMIT") ?>"/>
            <input type="hidden" name="PROPERTY[2][0]" value="" id="object-n-l-coord"/>
            <input type="hidden" name="PROPERTY[3][0]" value="" id="object-e-l-coord"/>
            <input type="hidden" name="PROPERTY[4][13]" value=""/>
            <input type="file" name="PROPERTY_FILE_4_13" value=""/>
            <input type="hidden" name="PROPERTY[6][0]" value=""/>
            <input type="hidden" name="PROPERTY[7][0]" value=""/>
        </div>
        <? /* hidden inputs */ ?>

    </form>
<? endif; ?>
<!--</div>-->
