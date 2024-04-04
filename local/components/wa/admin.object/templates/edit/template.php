<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$APPLICATION->SetTitle("Редактирование объекта - " . $arResult["OBJECT_DATA"]["NAME"]);
?>
<?
//TODO:ДОДЕЛАТЬ ПОЛЯ С НОВЫМИ СВОЙСТВАМИ ОБЪЕКТА
?>
<form class="form-create-object" name="iblock_add">
    <input type="hidden" id="gallery-counter" value="<?= $arResult['COUNTERS']['GALLERY'] ?>">
    <input type="hidden" id="video-preview-counter" value="<?= $arResult['COUNTERS']['VIDEO_PREVIEW'] ?>">
    <input type="hidden" id="video-counter" value="<?= $arResult['COUNTERS']['VIDEO'] ?>">
    <input type="hidden" name="OBJECT_ID" value="<?= $arResult['OBJECT_DATA']['ID'] ?>">
    <div class="form-block">
        <h3 class="form-block_title">Данные об объекте</h3>
        <div class="input">
            <label for="" class="input-label input-label--mb input-label--gray">Название объекта</label>

            <input type="text"
                   name="NAME"
                   value="<?= $arResult["OBJECT_DATA"]["NAME"] ?>">

        </div>
        <div class="input">
            <label for="" class="input-label input-label--mb input-label--gray">Сортировка (положение объекта в
                списке)</label>
            <input type="text" name="SORT" value="<?= $arResult["OBJECT_DATA"]["SORT"] ?>">
        </div>
        <div class="lk-select-wrap">
            <div class="select-block" id="section-selector">
                <span class="input-label input-label--mb input-label--gray">Выберите раздел</span>
                <div class="custom-select">
                    <div class="custom-select_head">
                        <? if ($arResult["OBJECT_DATA"]["SECTION"]): ?>
                            <span class="custom-select_title"
                                  data-selected-id="<?= $arResult["OBJECT_DATA"]["SECTION"]["ID"] ?>"><?= $arResult["OBJECT_DATA"]["SECTION"]["NAME"] ?></span>
                        <? else: ?>
                            <span class="custom-select_title">Раздел</span>
                        <? endif; ?>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? foreach ($arResult['LOCATIONS']['CATEGORY'] as $section): ?>
                            <div class="custom-select_item"
                                 data-id="<?= $section['ID'] ?>"><?= $section['NAME'] ?></div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="lk-select-wrap">
            <div class="select-block" id="category-selector">
                <span class="input-label input-label--mb input-label--gray">Выберите или создайте категорию </span>
                <div class="custom-select">
                    <div class="custom-select_head">
                        <? if ($arResult["OBJECT_DATA"]["CATEGORY"]): ?>
                            <span class="custom-select_title"
                                  data-selected-id="<?= $arResult["OBJECT_DATA"]["CATEGORY"]["ID"] ?>"><?= $arResult["OBJECT_DATA"]["CATEGORY"]["NAME"] ?></span>
                        <? else: ?>
                            <span class="custom-select_title">Категория</span>
                        <? endif; ?>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body" id="category-select-body">
                        <? foreach ($arResult['LOCATIONS']['TYPE'] as $section): ?>
                            <div class="custom-select_item"
                                 data-id="<?= $section['ID'] ?>">
                                <?= $section['NAME'] ?>
                                <button class="select-btn-delete" data-select-type="category" type="button"
                                        data-item-id="<?= $section['ID'] ?>">
                                    <svg width="11" height="12" viewBox="0 0 11 12" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0.351562 1.2627L10.5054 11.4165M10.6445 1.2627L0.49061 11.4165"
                                              stroke="#F71E1E"></path>
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
            <div class="select-block" id="location-selector">
                <span class="input-label input-label--mb input-label--gray">Локация</span>
                <div class="custom-select">
                    <div class="custom-select_head">
                        <? if ($arResult["OBJECT_DATA"]["LOCATION"]): ?>
                            <span class="custom-select_title"
                                  data-selected-id="<?= $arResult["OBJECT_DATA"]["LOCATION"]["ID"] ?>"><?= $arResult["OBJECT_DATA"]["LOCATION"]["NAME"] ?></span>
                        <? else: ?>
                            <span class="custom-select_title">Локация</span>
                        <? endif; ?>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body" id="location-select-body">
                        <? foreach ($arResult['LOCATIONS']['LOCATION'] as $location): ?>
                            <div class="custom-select_item"
                                 data-id="<?= $location['ID'] ?>">
                                <?= $location['NAME'] ?>
                                <button class="select-btn-delete" data-select-type="location" type="button"
                                        data-item-id="<?= $location['ID'] ?>">
                                    <svg width="11" height="12" viewBox="0 0 11 12" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0.351562 1.2627L10.5054 11.4165M10.6445 1.2627L0.49061 11.4165"
                                              stroke="#F71E1E"></path>
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
        <? if ($arResult["PARTNERS_LIST"]): ?>
            <div class="lk-select-wrap">
                <div class="select-block">
                    <span class="input-label input-label--mb input-label--gray">Выберите или создайте партнера</span>
                    <div class="custom-select">
                        <div class="custom-select_head">
                            <span class="custom-select_title">Партнеры</span>
                            <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L7 7L13 1" stroke="#000"/>
                            </svg>
                        </div>
                        <div class="custom-select_body" id="partner-select-body">
                            <? foreach ($arResult['PARTNERS_LIST'] as $partner): ?>
                                <div class="custom-select_item">
                                    <div class="checkbox checkbox-w-btn">
                                        <input type="checkbox"
                                               id="PARTNERS_<?= $partner['ID'] ?>"
                                               value="<?= $partner['NAME'] ?>"
                                               name="PARTNERS[]"
                                            <?= $partner['CHECKED'] ? "checked" : "" ?>
                                        >
                                        <label for="PARTNERS_<?= $partner['ID'] ?>">
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
        <? endif; ?>
        <button class="put-on-map-btn js-open-r-modal" data-name="modal-put-on-map"
                type="button" <?= $arResult["OBJECT_DATA"]["IS_ROUTE"] ? 'style="display: none"' : '' ?>>
            <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                        d="M11.5026 11.2607C11.9658 11.2607 12.3611 11.097 12.6885 10.7696C13.016 10.4422 13.1797 10.0469 13.1797 9.58366C13.1797 9.12046 13.016 8.72515 12.6885 8.39772C12.3611 8.07029 11.9658 7.90658 11.5026 7.90658C11.0394 7.90658 10.6441 8.07029 10.3167 8.39772C9.98924 8.72515 9.82552 9.12046 9.82552 9.58366C9.82552 10.0469 9.98924 10.4422 10.3167 10.7696C10.6441 11.097 11.0394 11.2607 11.5026 11.2607ZM11.5026 21.0837C8.93108 18.8955 7.01042 16.863 5.74063 14.9863C4.47083 13.1095 3.83594 11.3725 3.83594 9.77533C3.83594 7.37949 4.6066 5.47081 6.14792 4.04928C7.68924 2.62776 9.47413 1.91699 11.5026 1.91699C13.5311 1.91699 15.316 2.62776 16.8573 4.04928C18.3986 5.47081 19.1693 7.37949 19.1693 9.77533C19.1693 11.3725 18.5344 13.1095 17.2646 14.9863C15.9948 16.863 14.0741 18.8955 11.5026 21.0837Z"
                        fill="black"/>
            </svg>
            <span>Отметить на карте</span>
        </button>
        <button class="put-on-map-btn js-open-r-modal" data-name="modal-route-on-map" type="button"
            <?= $arResult["OBJECT_DATA"]["IS_ROUTE"] ? '' : 'style="display: none"' ?>>
            <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                        d="M11.5026 11.2607C11.9658 11.2607 12.3611 11.097 12.6885 10.7696C13.016 10.4422 13.1797 10.0469 13.1797 9.58366C13.1797 9.12046 13.016 8.72515 12.6885 8.39772C12.3611 8.07029 11.9658 7.90658 11.5026 7.90658C11.0394 7.90658 10.6441 8.07029 10.3167 8.39772C9.98924 8.72515 9.82552 9.12046 9.82552 9.58366C9.82552 10.0469 9.98924 10.4422 10.3167 10.7696C10.6441 11.097 11.0394 11.2607 11.5026 11.2607ZM11.5026 21.0837C8.93108 18.8955 7.01042 16.863 5.74063 14.9863C4.47083 13.1095 3.83594 11.3725 3.83594 9.77533C3.83594 7.37949 4.6066 5.47081 6.14792 4.04928C7.68924 2.62776 9.47413 1.91699 11.5026 1.91699C13.5311 1.91699 15.316 2.62776 16.8573 4.04928C18.3986 5.47081 19.1693 7.37949 19.1693 9.77533C19.1693 11.3725 18.5344 13.1095 17.2646 14.9863C15.9948 16.863 14.0741 18.8955 11.5026 21.0837Z"
                        fill="black"/>
            </svg>
            <span>Маршрут на карте</span>
        </button>
        <div class="input-file-wrap">
                <span class="input-label input-label--mb15 input-label--gray">
                    Загрузите фото
                </span>
            <div class="input-file input-file--photo">
                <input id="file-input" type="file" data-id="DETAIL_GALLERY" multiple accept="image/png, image/jpeg">
                <label for="file-input">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M11.4974 19.4337H24.5307L20.2974 13.7337L16.8641 18.2337L14.5974 15.3337L11.4974 19.4337ZM8.66406 25.3337C8.13073 25.3337 7.66406 25.1337 7.26406 24.7337C6.86406 24.3337 6.66406 23.867 6.66406 23.3337V4.66699C6.66406 4.13366 6.86406 3.66699 7.26406 3.26699C7.66406 2.86699 8.13073 2.66699 8.66406 2.66699H27.3307C27.8641 2.66699 28.3307 2.86699 28.7307 3.26699C29.1307 3.66699 29.3307 4.13366 29.3307 4.66699V23.3337C29.3307 23.867 29.1307 24.3337 28.7307 24.7337C28.3307 25.1337 27.8641 25.3337 27.3307 25.3337H8.66406ZM4.66406 29.3337C4.13073 29.3337 3.66406 29.1337 3.26406 28.7337C2.86406 28.3337 2.66406 27.867 2.66406 27.3337V6.66699H4.66406V27.3337H25.3307V29.3337H4.66406Z"
                                fill="white"/>
                    </svg>
                    <span>Загрузить файл</span>
                </label>
                <? foreach ($arResult['OBJECT_DATA']['DETAIL_GALLERY'] as $k => $img): ?>
                    <? if ($img['SRC']): ?>
                        <input type="file" name="DETAIL_GALLERY_K_<?= $k ?>" value="<?= $img ?>">
                        <div class="input-file_preview active">
                            <img src="<?= $img['SRC'] ?>" alt="">
                            <button type="button" class="input-file-remove-btn" data-file-id="<?= $k ?>"
                                    data-delete-id="<?= $img['ID'] ?>"
                                    onclick="deletePic(this)">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                            d="M2.28795 2.28746C2.4286 2.14686 2.61933 2.06787 2.8182 2.06787C3.01707 2.06787 3.2078 2.14686 3.34845 2.28746L6.00045 4.93946L8.65245 2.28746C8.79391 2.15084 8.98336 2.07525 9.18001 2.07696C9.37665 2.07866 9.56476 2.15754 9.70382 2.2966C9.84288 2.43565 9.92175 2.62376 9.92346 2.82041C9.92517 3.01706 9.84957 3.20651 9.71296 3.34796L7.06095 5.99996L9.71296 8.65197C9.84957 8.79342 9.92517 8.98287 9.92346 9.17952C9.92175 9.37616 9.84288 9.56427 9.70382 9.70333C9.56476 9.84239 9.37665 9.92126 9.18001 9.92297C8.98336 9.92468 8.79391 9.84909 8.65245 9.71247L6.00045 7.06046L3.34845 9.71247C3.207 9.84909 3.01755 9.92468 2.8209 9.92297C2.62425 9.92126 2.43614 9.84239 2.29709 9.70333C2.15803 9.56427 2.07915 9.37616 2.07744 9.17952C2.07573 8.98287 2.15133 8.79342 2.28795 8.65197L4.93995 5.99996L2.28795 3.34796C2.14735 3.20732 2.06836 3.01658 2.06836 2.81771C2.06836 2.61884 2.14735 2.42811 2.28795 2.28746V2.28746Z"
                                            fill="#F2F2F2"/>
                                </svg>
                            </button>
                        </div>
                    <? endif; ?>
                <? endforeach; ?>
            </div>
        </div>

        <div class="input-file-wrap">
                <span class="input-label input-label--mb15 input-label--gray">
                    Загрузите превью для видео
                </span>
            <div class="input-file input-file--photo">
                <input id="preview-file-input" type="file" data-id="GALLERY_VIDEO_PREVIEW" multiple
                       accept="image/png, image/jpeg">
                <label for="preview-file-input">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M11.4974 19.4337H24.5307L20.2974 13.7337L16.8641 18.2337L14.5974 15.3337L11.4974 19.4337ZM8.66406 25.3337C8.13073 25.3337 7.66406 25.1337 7.26406 24.7337C6.86406 24.3337 6.66406 23.867 6.66406 23.3337V4.66699C6.66406 4.13366 6.86406 3.66699 7.26406 3.26699C7.66406 2.86699 8.13073 2.66699 8.66406 2.66699H27.3307C27.8641 2.66699 28.3307 2.86699 28.7307 3.26699C29.1307 3.66699 29.3307 4.13366 29.3307 4.66699V23.3337C29.3307 23.867 29.1307 24.3337 28.7307 24.7337C28.3307 25.1337 27.8641 25.3337 27.3307 25.3337H8.66406ZM4.66406 29.3337C4.13073 29.3337 3.66406 29.1337 3.26406 28.7337C2.86406 28.3337 2.66406 27.867 2.66406 27.3337V6.66699H4.66406V27.3337H25.3307V29.3337H4.66406Z"
                                fill="white"/>
                    </svg>
                    <span>Загрузить файл</span>
                </label>
                <? foreach ($arResult['OBJECT_DATA']['GALLERY_VIDEO_PREVIEW'] as $k => $img): ?>
                    <? if ($img['SRC']): ?>
                        <input type="file" name="GALLERY_VIDEO_PREVIEW_K_<?= $k ?>" value="<?= $img ?>">
                        <div class="input-file_preview active">
                            <img src="<?= $img['SRC'] ?>" alt="">
                            <button type="button" class="input-file-remove-btn" data-file-id="<?= $k ?>"
                                    data-delete-id="<?= $img['ID'] ?>"
                                    onclick="deletePic(this)">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                            d="M2.28795 2.28746C2.4286 2.14686 2.61933 2.06787 2.8182 2.06787C3.01707 2.06787 3.2078 2.14686 3.34845 2.28746L6.00045 4.93946L8.65245 2.28746C8.79391 2.15084 8.98336 2.07525 9.18001 2.07696C9.37665 2.07866 9.56476 2.15754 9.70382 2.2966C9.84288 2.43565 9.92175 2.62376 9.92346 2.82041C9.92517 3.01706 9.84957 3.20651 9.71296 3.34796L7.06095 5.99996L9.71296 8.65197C9.84957 8.79342 9.92517 8.98287 9.92346 9.17952C9.92175 9.37616 9.84288 9.56427 9.70382 9.70333C9.56476 9.84239 9.37665 9.92126 9.18001 9.92297C8.98336 9.92468 8.79391 9.84909 8.65245 9.71247L6.00045 7.06046L3.34845 9.71247C3.207 9.84909 3.01755 9.92468 2.8209 9.92297C2.62425 9.92126 2.43614 9.84239 2.29709 9.70333C2.15803 9.56427 2.07915 9.37616 2.07744 9.17952C2.07573 8.98287 2.15133 8.79342 2.28795 8.65197L4.93995 5.99996L2.28795 3.34796C2.14735 3.20732 2.06836 3.01658 2.06836 2.81771C2.06836 2.61884 2.14735 2.42811 2.28795 2.28746V2.28746Z"
                                            fill="#F2F2F2"/>
                                </svg>
                            </button>
                        </div>
                    <? endif; ?>
                <? endforeach; ?>
            </div>
        </div>

        <div class="input-file-wrap">
                <span class="input-label input-label--mb15 input-label--gray">
                    Загрузите видео
                </span>
            <div class="input-file input-file--photo">
                <input id="video-file-input" type="file" data-id="GALLERY_VIDEO" multiple
                       accept="video/mp4, video/webm">
                <label for="video-file-input">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M11.4974 19.4337H24.5307L20.2974 13.7337L16.8641 18.2337L14.5974 15.3337L11.4974 19.4337ZM8.66406 25.3337C8.13073 25.3337 7.66406 25.1337 7.26406 24.7337C6.86406 24.3337 6.66406 23.867 6.66406 23.3337V4.66699C6.66406 4.13366 6.86406 3.66699 7.26406 3.26699C7.66406 2.86699 8.13073 2.66699 8.66406 2.66699H27.3307C27.8641 2.66699 28.3307 2.86699 28.7307 3.26699C29.1307 3.66699 29.3307 4.13366 29.3307 4.66699V23.3337C29.3307 23.867 29.1307 24.3337 28.7307 24.7337C28.3307 25.1337 27.8641 25.3337 27.3307 25.3337H8.66406ZM4.66406 29.3337C4.13073 29.3337 3.66406 29.1337 3.26406 28.7337C2.86406 28.3337 2.66406 27.867 2.66406 27.3337V6.66699H4.66406V27.3337H25.3307V29.3337H4.66406Z"
                                fill="white"/>
                    </svg>
                    <span>Загрузить файл</span>
                </label>
                <? foreach ($arResult['OBJECT_DATA']['GALLERY_VIDEO'] as $k => $file): ?>
                    <? if ($file['SRC']): ?>
                        <input type="file" name="GALLERY_VIDEO_K_<?= $k ?>" value="<?= $file ?>">
                        <div class="input-file_preview active">
                            <img src="<?= $file['SRC'] ?>" alt="">
                            <button type="button" class="input-file-remove-btn" data-file-id="<?= $k ?>"
                                    data-delete-id="<?= $file['ID'] ?>"
                                    onclick="deletePic(this)">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                            d="M2.28795 2.28746C2.4286 2.14686 2.61933 2.06787 2.8182 2.06787C3.01707 2.06787 3.2078 2.14686 3.34845 2.28746L6.00045 4.93946L8.65245 2.28746C8.79391 2.15084 8.98336 2.07525 9.18001 2.07696C9.37665 2.07866 9.56476 2.15754 9.70382 2.2966C9.84288 2.43565 9.92175 2.62376 9.92346 2.82041C9.92517 3.01706 9.84957 3.20651 9.71296 3.34796L7.06095 5.99996L9.71296 8.65197C9.84957 8.79342 9.92517 8.98287 9.92346 9.17952C9.92175 9.37616 9.84288 9.56427 9.70382 9.70333C9.56476 9.84239 9.37665 9.92126 9.18001 9.92297C8.98336 9.92468 8.79391 9.84909 8.65245 9.71247L6.00045 7.06046L3.34845 9.71247C3.207 9.84909 3.01755 9.92468 2.8209 9.92297C2.62425 9.92126 2.43614 9.84239 2.29709 9.70333C2.15803 9.56427 2.07915 9.37616 2.07744 9.17952C2.07573 8.98287 2.15133 8.79342 2.28795 8.65197L4.93995 5.99996L2.28795 3.34796C2.14735 3.20732 2.06836 3.01658 2.06836 2.81771C2.06836 2.61884 2.14735 2.42811 2.28795 2.28746V2.28746Z"
                                            fill="#F2F2F2"/>
                                </svg>
                            </button>
                        </div>
                    <? endif; ?>
                <? endforeach; ?>
            </div>
        </div>

        <div class="textarea">
            <label for="" class="input-label input-label--mb input-label--gray">Описание объекта</label>
            <textarea name="DETAIL_TEXT"><?= $arResult['OBJECT_DATA']['DETAIL_TEXT'] ?></textarea>
        </div>
    </div>
    <div class="form-block">
        <h3 class="form-block_title">Особенности</h3>
        <div class="checkbox-list" id="characteristic-select">
            <?
            $counter = 0;
            foreach ($arResult['OBJECT_FEATURES'] as $feature):?>
                <div class="checkbox">
                    <input type="checkbox" id="checkbox_<?= $feature['VALUE'] ?>" value="<?= $feature['VALUE'] ?>"
                           name="LOCATION_FEATURES[<?= $counter ?>]" <?= $feature['CHECKED'] ? "checked" : "" ?>>
                    <label for="checkbox_<?= $feature['VALUE'] ?>">
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
                <?
                $counter++;
            endforeach; ?>

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
    <div class="form-block"
         id="object-prop-block" <?= (isset($arResult['OBJECT_DATA']['IS_ROUTE']) && $arResult['OBJECT_DATA']['IS_ROUTE']) ? 'style="display: none"' : '' ?>>
        <h3 class="form-block_title">Краткие характеристики</h3>
        <? if ($arResult['OBJECT_PROPS']): ?>
            <div class="form-item">
                <h4 class="form-item_title">Временной интервал</h4>
                <div class="checkbox-group">
                    <? foreach ($arResult['OBJECT_PROPS']['TIME_INTERVAL'] as $checkbox): ?>
                        <div class="checkbox">
                            <input type="checkbox" id="<?= $checkbox['PROPERTY_CODE'] . '_' . $checkbox['ID'] ?>"
                                   name="<?= $checkbox['PROPERTY_CODE'] ?>[]" value="<?= $checkbox['ID'] ?>"
                                <?= $checkbox['CHECKED'] ? "checked" : "" ?>>
                            <label for="<?= $checkbox['PROPERTY_CODE'] . '_' . $checkbox['ID'] ?>">
                                <div class="checkbox_text"><?= $checkbox['VALUE'] ?></div>
                            </label>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
            <div class="form-item">
                <h4 class="form-item_title">Стоимость услуги</h4>
                <div class="checkbox-group">
                    <? foreach ($arResult['OBJECT_PROPS']['SERVICE_COST'] as $checkbox): ?>
                        <div class="checkbox">
                            <input type="checkbox" id="<?= $checkbox['PROPERTY_CODE'] . '_' . $checkbox['ID'] ?>"
                                   name="<?= $checkbox['PROPERTY_CODE'] ?>[]" value="<?= $checkbox['ID'] ?>"
                                <?= $checkbox['CHECKED'] ? "checked" : "" ?>>
                            <label for="<?= $checkbox['PROPERTY_CODE'] . '_' . $checkbox['ID'] ?>">
                                <div class="checkbox_text"><?= $checkbox['VALUE'] ?></div>
                            </label>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
            <div class="form-item">
                <h4 class="form-item_title">Возможность бронирования</h4>
                <div class="radio-list">
                    <? foreach ($arResult['OBJECT_PROPS']['CAN_BOOK'] as $i => $radio_btn): ?>
                        <div class="radio">
                            <input type="radio"
                                   id="<?= $radio_btn['PROPERTY_CODE'] . '_' . $radio_btn['ID'] ?>"
                                   name="<?= $radio_btn['PROPERTY_CODE'] ?>"
                                   value="<?= $radio_btn['ID'] ?>"
                                <?= $radio_btn['CHECKED'] ? "checked" : "" ?>>
                            <label for="<?= $radio_btn['PROPERTY_CODE'] . '_' . $radio_btn['ID'] ?>">
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
                    'name' => "BOOKING_ALERT_MSG",
                    'id' => preg_replace("/[^a-z0-9]/i", '', "BOOKING_ALERT_MSG"),
                    'inputName' => "BOOKING_ALERT_MSG",
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
                    <? foreach ($arResult['OBJECT_PROPS']['CAR_POSSIBILITY'] as $i => $radio_btn): ?>
                        <div class="radio">
                            <input type="radio"
                                   id="<?= $radio_btn['PROPERTY_CODE'] . '_' . $radio_btn['ID'] ?>"
                                   name="<?= $radio_btn['PROPERTY_CODE'] ?>"
                                   value="<?= $radio_btn['ID'] ?>"
                                <?= $radio_btn['CHECKED'] ? "checked" : "" ?>>
                            <label for="<?= $radio_btn['PROPERTY_CODE'] . '_' . $radio_btn['ID'] ?>">
                                <div class="radio_text"><?= $radio_btn['VALUE'] ?></div>
                            </label>
                        </div>
                    <? endforeach; ?>
                </div>
                <div class="input-counter-item"
                     id="car-capacity-block"<?= $arResult['OBJECT_DATA']['CAR_POSSIBILITY'] == 'Да' ? '' : 'style="display: none"' ?> >
                    <label for="" class="input-counter-item_title">Количество машин</label>
                    <div class="input-counter">
                        <button class="input-counter_btn" type="button" data-input-id="car-capacity"
                                data-action="minus">
                            <svg width="13" height="1" viewBox="0 0 13 1" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 0H13V1H0V0Z" fill="#313131"/>
                            </svg>
                        </button>
                        <input type="text" class="input-counter_input"
                               name="CAR_CAPACITY"
                               id="car-capacity"
                               value="<?= $arResult['OBJECT_DATA']['CAR_CAPACITY'] ? $arResult['OBJECT_DATA']['CAR_CAPACITY'] : 1 ?>"
                        >
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
                    <? foreach ($arResult['OBJECT_PROPS']['TIME_UNLIMIT_OBJECT'] as $i => $radio_btn): ?>
                        <div class="radio">
                            <input type="radio"
                                   id="<?= $radio_btn['PROPERTY_CODE'] . '_' . $radio_btn['ID'] ?>"
                                   name="<?= $radio_btn['PROPERTY_CODE'] ?>"
                                   value="<?= $radio_btn['ID'] ?>"
                                <?= $radio_btn['CHECKED'] ? "checked" : "" ?>>
                            <label for="<?= $radio_btn['PROPERTY_CODE'] . '_' . $radio_btn['ID'] ?>">
                                <div class="radio_text"><?= $radio_btn['VALUE'] ?></div>
                            </label>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
        <? endif; ?>
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

                        <input type="text" class="input-counter_input"
                               value="<?= $arResult['OBJECT_DATA']['CAPACITY_ESTIMATED'] ?>"
                               name="CAPACITY_ESTIMATED"
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

                        <input type="text" class="input-counter_input"
                               value="<?= $arResult['OBJECT_DATA']['CAPACITY_MAXIMUM'] ?>"
                               name="CAPACITY_MAXIMUM"
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
            <div class="input-group"
                 id="price-block" <?= $arResult['OBJECT_DATA']['TIME_UNLIMIT_OBJECT'] != 'Нет' ? 'style="display: none"' : '' ?>>
                <div class="input input-lk-price"
                     data-type="non-fix-price">
                    <label for="">Стоимость суточного пребывания</label>
                    <input type="text"
                           value="<?= $arResult['OBJECT_DATA']['OBJECT_COST'] ?>"
                           name="OBJECT_COST">
                </div>
                <div class="input input-lk-price"
                     data-type="non-fix-price">
                    <label for="">стоимость чел. сверх рассчитанной до максимальной (руб.)</label>
                    <input type="text" value="<?= $arResult['OBJECT_DATA']['COST_PER_PERSON'] ?>"
                           name="COST_PER_PERSON">
                </div>
                <div class="input input-lk-price"
                     data-type="non-fix-price">
                    <label for="">Стоимость дневного пребывания</label>
                    <input type="text"
                           value="<?= $arResult['OBJECT_DATA']['OBJECT_DAILY_COST'] ?>"
                           name="OBJECT_DAILY_COST">
                </div>
                <div class="input input-lk-price"
                     data-type="non-fix-price">
                    <label for="">стоимость чел. сверх рассчитанной до максимальной (руб.)</label>
                    <input type="text" value="<?= $arResult['OBJECT_DATA']['COST_PER_PERSON_ONE_DAY'] ?>"
                           name="COST_PER_PERSON_ONE_DAY">
                </div>
                <div class="input input-lk-price"
                     data-type="fix-price">
                    <label for="">Фиксированная стоимость (руб.)</label>
                    <input type="text" value="<?= $arResult['OBJECT_DATA']['FIXED_COST'] ?>"
                           name="FIXED_COST">
                </div>
            </div>
        </div>

        <div class="form-item"
             id="work-time-node" <?= $arResult['OBJECT_DATA']['TIME_UNLIMIT_OBJECT'] != 'Да' ? 'style="display: none"' : '' ?>>
            <h4 class="form-item_title">Время работы</h4>
            <div class="input-group">
                <div class="m-input-dates m-input-dates--md">
                    <div class="m-input-date-block">
                        <label for="">Время начала работы<span class="color-red">*</span></label>
                        <div class="custom-select custom-select--sm" id="start-time-select">
                            <div class="custom-select_head">
                                <? if ($arResult['OBJECT_DATA']['START_TIME']): ?>
                                    <span class="custom-select_title"
                                          data-selected-id="<?= $arResult['OBJECT_DATA']['START_TIME'] ?>"><?= $arResult['OBJECT_DATA']['START_TIME'] ?></span>
                                <? else: ?>
                                    <span class="custom-select_title">Время начала работы</span>
                                <? endif; ?>
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
                            </div>
                        </div>
                    </div>
                    <div class="m-input-date-block">
                        <label for="">Время окончания работы<span class="color-red">*</span></label>
                        <div class="custom-select custom-select--sm" id="end-time-select">
                            <div class="custom-select_head">
                                <? if ($arResult['OBJECT_DATA']['END_TIME']): ?>
                                    <span class="custom-select_title"
                                          data-selected-id="<?= $arResult['OBJECT_DATA']['END_TIME'] ?>"><?= $arResult['OBJECT_DATA']['END_TIME'] ?></span>
                                <? else: ?>
                                    <span class="custom-select_title">Время окончания работы</span>
                                <? endif; ?>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-block"
         id="route-prop-block" <?= (isset($arResult['OBJECT_DATA']['IS_ROUTE']) && $arResult['OBJECT_DATA']['IS_ROUTE']) ? '' : 'style="display: none"' ?>>
        <h3 class="form-block_title">Краткие характеристики</h3>
        <div class="form-item">
            <!--<h4 class="form-item_title"></h4>-->
            <div class="input-group">
                <div class="input input-lk-price">
                    <label for="">Стоимость суточного пребывания</label>
                    <input type="text" value="<?= $arResult['OBJECT_DATA']['OBJECT_COST'] ?>" name="OBJECT_COST">
                </div>
                <div class="input input-lk-price">
                    <label for="">Суточная посещаемость маршрута</label>
                    <input type="text" value="<?= $arResult['OBJECT_DATA']['DAILY_TRAFFIC'] ?>" name="DAILY_TRAFFIC">
                </div>
            </div>
        </div>
    </div>
    <input type="button" class="primary-btn primary-btn--xl" value="Сохранить" onclick="saveObject()">
    <input type="hidden" name="NORTHERN_LATITUDE" value="<?= $arResult['OBJECT_DATA']['NORTHERN_LATITUDE'] ?>"
           id="object-n-l-coord"/>
    <input type="hidden" name="EASTERN_LONGITUDE" value="<?= $arResult['OBJECT_DATA']['EASTERN_LONGITUDE'] ?>"
           id="object-e-l-coord"/>
    <input type="hidden" name="ROUTE_COORDS" value="<?= $arResult['OBJECT_DATA']['ROUTE_COORDS'] ?>"
           id="route-coords"/>
    <div class="form-warn-message"></div>
</form>