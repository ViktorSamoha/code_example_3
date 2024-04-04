<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if ($_SERVER['HTTP_REFERER']): ?>
    <input type="hidden" value="<?= $_SERVER['HTTP_REFERER'] ?>" id="back-url">
<? endif; ?>
<form action="" class="form-visiting-permit" id="add-transport-form">
    <? if ($arParams['EDIT_VEHICLE'] && $arParams['EDIT_VEHICLE'] == 'Y'): ?>
        <input type="hidden" value="<?= $arResult['USER_VEHICLE']['ID'] ?>" name="USER_VEHICLE_ID">
    <? endif; ?>
    <div class="form-block">
        <h3 class="form-block_title">Данные о ТС</h3>
        <div class="select-block">
            <div class="input-label input-label--mb input-label--gray">Укажите тип ТС</div>
            <div class="custom-select">
                <div class="custom-select_head">
                    <span class="custom-select_title" id="vehicle-type-select"
                          data-selected-id="<?= $arResult['USER_VEHICLE']['VEHICLE_TYPE']['XML_ID'] ?>"><?= $arResult['USER_VEHICLE']['VEHICLE_TYPE']['TYPE'] ?></span>
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
        <div class="checkbox-list">
            <div class="checkbox">
                <input type="checkbox" id="checkbox-vehicle-register"
                       name="VEHICLE_REGISTER"
                    <?= $arResult['USER_VEHICLE']['IS_REGISTER']['VALUE'] == 'Нет' ? 'checked' : '' ?>
                >
                <label for="checkbox-vehicle-register">
                    <div class="checkbox_text">Данный вид транспорта не подлежит регистрации</div>
                </label>
            </div>
        </div>
        <div class="form-block_text">
            В поле ниже необходимо загрузить:
            <ol>
                <li>Документ об образовании,</li>
                <li>Паспорт (включая информацию о прописке),</li>
                <li>СНИЛС (при наличии)</li>
            </ol>
        </div>
    </div>
    <div class="form-block"
         id="register-vehicle-props-block" <?= $arResult['USER_VEHICLE']['IS_REGISTER']['VALUE'] == 'Нет' ? 'style="display:none;"' : '' ?>>
        <h3 class="form-block_title">Права на управление техникой</h3>
        <div class="input-group">
            <div class="input input--md">
                <label for="" class="input-label">Серия</label>
                <input type="text" required name="DRIVING_LICENSE_SERIES"
                       value="<?= $arResult['USER_VEHICLE']['DRIVING_LICENSE_SERIES'] ?>">
            </div>
            <div class="input input--md">
                <label for="" class="input-label">Номер</label>
                <input type="text" required name="DRIVING_LICENSE_NUMBER"
                       value="<?= $arResult['USER_VEHICLE']['DRIVING_LICENSE_NUMBER'] ?>">
            </div>
        </div>
        <div class="input-file-wrap">
            <span class="input-label input-label--mb15 input-label--gray">Загрузите фото или pdf</span>
            <div class="input-file input-file--photo">
                <input id="driving-license-files" type="file" accept=".png, .jpg, .jpeg, .pdf" multiple
                       data-id="DRIVING_LICENSE_FILES">
                <label for="driving-license-files">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M11.4974 19.4337H24.5307L20.2974 13.7337L16.8641 18.2337L14.5974 15.3337L11.4974 19.4337ZM8.66406 25.3337C8.13073 25.3337 7.66406 25.1337 7.26406 24.7337C6.86406 24.3337 6.66406 23.867 6.66406 23.3337V4.66699C6.66406 4.13366 6.86406 3.66699 7.26406 3.26699C7.66406 2.86699 8.13073 2.66699 8.66406 2.66699H27.3307C27.8641 2.66699 28.3307 2.86699 28.7307 3.26699C29.1307 3.66699 29.3307 4.13366 29.3307 4.66699V23.3337C29.3307 23.867 29.1307 24.3337 28.7307 24.7337C28.3307 25.1337 27.8641 25.3337 27.3307 25.3337H8.66406ZM4.66406 29.3337C4.13073 29.3337 3.66406 29.1337 3.26406 28.7337C2.86406 28.3337 2.66406 27.867 2.66406 27.3337V6.66699H4.66406V27.3337H25.3307V29.3337H4.66406Z"
                                fill="white"/>
                    </svg>
                    <span>Загрузить файл</span>
                </label>
                <? if ($arResult['USER_VEHICLE']['DRIVING_LICENSE_FILES']): ?>
                    <input type="hidden" id="dlf-count"
                           value="<?= count($arResult['USER_VEHICLE']['DRIVING_LICENSE_FILES']) ?>">
                    <? foreach ($arResult['USER_VEHICLE']['DRIVING_LICENSE_FILES'] as $f_pos => $file): ?>
                        <input type="file" name=DRIVING_LICENSE_FVAL_<?= $f_pos ?>"
                               value="<?= $file['FILE_DATA'] ?>">
                        <div class="input-file_preview active">
                            <? if ($file['FILE_TYPE'] == "application/pdf"): ?>
                                <img src="/local/templates/.default/assets/img/default_doc_icon.svg" alt="">
                            <? else: ?>
                                <img src="<?= $file['FILE_PATH'] ?>" alt="">
                            <? endif; ?>
                            <button type="button" class="input-file-remove-btn" data-property-id="DRIVING_LICENSE_FILES"
                                    data-file-id="<?= $file['ID'] ?>" onclick="deleteFile(this)"
                                    data-delete-id="<?= $file['ID'] ?>">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                            d="M2.28795 2.28746C2.4286 2.14686 2.61933 2.06787 2.8182 2.06787C3.01707 2.06787 3.2078 2.14686 3.34845 2.28746L6.00045 4.93946L8.65245 2.28746C8.79391 2.15084 8.98336 2.07525 9.18001 2.07696C9.37665 2.07866 9.56476 2.15754 9.70382 2.2966C9.84288 2.43565 9.92175 2.62376 9.92346 2.82041C9.92517 3.01706 9.84957 3.20651 9.71296 3.34796L7.06095 5.99996L9.71296 8.65197C9.84957 8.79342 9.92517 8.98287 9.92346 9.17952C9.92175 9.37616 9.84288 9.56427 9.70382 9.70333C9.56476 9.84239 9.37665 9.92126 9.18001 9.92297C8.98336 9.92468 8.79391 9.84909 8.65245 9.71247L6.00045 7.06046L3.34845 9.71247C3.207 9.84909 3.01755 9.92468 2.8209 9.92297C2.62425 9.92126 2.43614 9.84239 2.29709 9.70333C2.15803 9.56427 2.07915 9.37616 2.07744 9.17952C2.07573 8.98287 2.15133 8.79342 2.28795 8.65197L4.93995 5.99996L2.28795 3.34796C2.14735 3.20732 2.06836 3.01658 2.06836 2.81771C2.06836 2.61884 2.14735 2.42811 2.28795 2.28746V2.28746Z"
                                            fill="#F2F2F2"/>
                                </svg>
                            </button>
                        </div>
                    <? endforeach; ?>
                <? else: ?>
                    <div class="input-file_preview">
                        <button class="input-file-remove-btn">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M2.28795 2.28746C2.4286 2.14686 2.61933 2.06787 2.8182 2.06787C3.01707 2.06787 3.2078 2.14686 3.34845 2.28746L6.00045 4.93946L8.65245 2.28746C8.79391 2.15084 8.98336 2.07525 9.18001 2.07696C9.37665 2.07866 9.56476 2.15754 9.70382 2.2966C9.84288 2.43565 9.92175 2.62376 9.92346 2.82041C9.92517 3.01706 9.84957 3.20651 9.71296 3.34796L7.06095 5.99996L9.71296 8.65197C9.84957 8.79342 9.92517 8.98287 9.92346 9.17952C9.92175 9.37616 9.84288 9.56427 9.70382 9.70333C9.56476 9.84239 9.37665 9.92126 9.18001 9.92297C8.98336 9.92468 8.79391 9.84909 8.65245 9.71247L6.00045 7.06046L3.34845 9.71247C3.207 9.84909 3.01755 9.92468 2.8209 9.92297C2.62425 9.92126 2.43614 9.84239 2.29709 9.70333C2.15803 9.56427 2.07915 9.37616 2.07744 9.17952C2.07573 8.98287 2.15133 8.79342 2.28795 8.65197L4.93995 5.99996L2.28795 3.34796C2.14735 3.20732 2.06836 3.01658 2.06836 2.81771C2.06836 2.61884 2.14735 2.42811 2.28795 2.28746V2.28746Z"
                                        fill="#F2F2F2"/>
                            </svg>
                        </button>
                    </div>
                <? endif; ?>
            </div>
        </div>
    </div>

    <div class="form-block">
        <h3 class="form-block_title">Сведения о технике</h3>
        <div class="input-group">
            <div class="input input--lg">
                <label for="" class="input-label">марка и модель ТС</label>
                <input type="text" required name="MODEL" value="<?= $arResult['USER_VEHICLE']['MODEL'] ?>">
            </div>
        </div>
        <div class="input-file-wrap">
            <span class="input-label input-label--mb15 input-label--gray">Загрузите копию документа</span>
            <div class="input-file input-file--photo">
                <input id="technical-passport-files" type="file" multiple data-id="TECHNICAL_PASSPORT">
                <label for="technical-passport-files">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M11.4974 19.4337H24.5307L20.2974 13.7337L16.8641 18.2337L14.5974 15.3337L11.4974 19.4337ZM8.66406 25.3337C8.13073 25.3337 7.66406 25.1337 7.26406 24.7337C6.86406 24.3337 6.66406 23.867 6.66406 23.3337V4.66699C6.66406 4.13366 6.86406 3.66699 7.26406 3.26699C7.66406 2.86699 8.13073 2.66699 8.66406 2.66699H27.3307C27.8641 2.66699 28.3307 2.86699 28.7307 3.26699C29.1307 3.66699 29.3307 4.13366 29.3307 4.66699V23.3337C29.3307 23.867 29.1307 24.3337 28.7307 24.7337C28.3307 25.1337 27.8641 25.3337 27.3307 25.3337H8.66406ZM4.66406 29.3337C4.13073 29.3337 3.66406 29.1337 3.26406 28.7337C2.86406 28.3337 2.66406 27.867 2.66406 27.3337V6.66699H4.66406V27.3337H25.3307V29.3337H4.66406Z"
                                fill="white"/>
                    </svg>
                    <span>Загрузить файл</span>
                </label>
                <? if ($arResult['USER_VEHICLE']['TECHNICAL_PASSPORT']): ?>
                    <input type="hidden" id="tp-count"
                           value="<?= count($arResult['USER_VEHICLE']['TECHNICAL_PASSPORT']) ?>">
                    <? foreach ($arResult['USER_VEHICLE']['TECHNICAL_PASSPORT'] as $f_pos => $file): ?>
                        <input type="file" name=TECHNICAL_PASSPORT_FVAL_<?= $f_pos ?>"
                               value="<?= $file['FILE_DATA'] ?>">
                        <div class="input-file_preview active">
                            <? if ($file['FILE_TYPE'] == "application/pdf"): ?>
                                <img src="/local/templates/.default/assets/img/default_doc_icon.svg" alt="">
                            <? else: ?>
                                <img src="<?= $file['FILE_PATH'] ?>" alt="">
                            <? endif; ?>
                            <button type="button" class="input-file-remove-btn"
                                    data-property-id="TECHNICAL_PASSPORT_FILES"
                                    data-file-id="<?= $file['ID'] ?>" onclick="deleteFile(this)"
                                    data-delete-id="<?= $file['ID'] ?>">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                            d="M2.28795 2.28746C2.4286 2.14686 2.61933 2.06787 2.8182 2.06787C3.01707 2.06787 3.2078 2.14686 3.34845 2.28746L6.00045 4.93946L8.65245 2.28746C8.79391 2.15084 8.98336 2.07525 9.18001 2.07696C9.37665 2.07866 9.56476 2.15754 9.70382 2.2966C9.84288 2.43565 9.92175 2.62376 9.92346 2.82041C9.92517 3.01706 9.84957 3.20651 9.71296 3.34796L7.06095 5.99996L9.71296 8.65197C9.84957 8.79342 9.92517 8.98287 9.92346 9.17952C9.92175 9.37616 9.84288 9.56427 9.70382 9.70333C9.56476 9.84239 9.37665 9.92126 9.18001 9.92297C8.98336 9.92468 8.79391 9.84909 8.65245 9.71247L6.00045 7.06046L3.34845 9.71247C3.207 9.84909 3.01755 9.92468 2.8209 9.92297C2.62425 9.92126 2.43614 9.84239 2.29709 9.70333C2.15803 9.56427 2.07915 9.37616 2.07744 9.17952C2.07573 8.98287 2.15133 8.79342 2.28795 8.65197L4.93995 5.99996L2.28795 3.34796C2.14735 3.20732 2.06836 3.01658 2.06836 2.81771C2.06836 2.61884 2.14735 2.42811 2.28795 2.28746V2.28746Z"
                                            fill="#F2F2F2"/>
                                </svg>
                            </button>
                        </div>
                    <? endforeach; ?>
                <? else: ?>
                    <div class="input-file_preview">
                        <button class="input-file-remove-btn">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M2.28795 2.28746C2.4286 2.14686 2.61933 2.06787 2.8182 2.06787C3.01707 2.06787 3.2078 2.14686 3.34845 2.28746L6.00045 4.93946L8.65245 2.28746C8.79391 2.15084 8.98336 2.07525 9.18001 2.07696C9.37665 2.07866 9.56476 2.15754 9.70382 2.2966C9.84288 2.43565 9.92175 2.62376 9.92346 2.82041C9.92517 3.01706 9.84957 3.20651 9.71296 3.34796L7.06095 5.99996L9.71296 8.65197C9.84957 8.79342 9.92517 8.98287 9.92346 9.17952C9.92175 9.37616 9.84288 9.56427 9.70382 9.70333C9.56476 9.84239 9.37665 9.92126 9.18001 9.92297C8.98336 9.92468 8.79391 9.84909 8.65245 9.71247L6.00045 7.06046L3.34845 9.71247C3.207 9.84909 3.01755 9.92468 2.8209 9.92297C2.62425 9.92126 2.43614 9.84239 2.29709 9.70333C2.15803 9.56427 2.07915 9.37616 2.07744 9.17952C2.07573 8.98287 2.15133 8.79342 2.28795 8.65197L4.93995 5.99996L2.28795 3.34796C2.14735 3.20732 2.06836 3.01658 2.06836 2.81771C2.06836 2.61884 2.14735 2.42811 2.28795 2.28746V2.28746Z"
                                        fill="#F2F2F2"/>
                            </svg>
                        </button>
                    </div>
                <? endif; ?>
            </div>
        </div>
        <div class="input-group">
            <div class="input input--sm">
                <label for="" class="input-label">Техосмотр</label>
                <input type="text" required class="input-date" name="INSPECTION_DATE"
                       value="<?= $arResult['USER_VEHICLE']['INSPECTION_DATE'] ?>">
            </div>
        </div>
        <div class="input-file-wrap">
            <span class="input-label input-label--mb15 input-label--gray">Загрузите копию документа</span>
            <div class="input-file input-file--photo">
                <input id="inspection-files" type="file" multiple data-id="INSPECTION_FILES">
                <label for="inspection-files">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M11.4974 19.4337H24.5307L20.2974 13.7337L16.8641 18.2337L14.5974 15.3337L11.4974 19.4337ZM8.66406 25.3337C8.13073 25.3337 7.66406 25.1337 7.26406 24.7337C6.86406 24.3337 6.66406 23.867 6.66406 23.3337V4.66699C6.66406 4.13366 6.86406 3.66699 7.26406 3.26699C7.66406 2.86699 8.13073 2.66699 8.66406 2.66699H27.3307C27.8641 2.66699 28.3307 2.86699 28.7307 3.26699C29.1307 3.66699 29.3307 4.13366 29.3307 4.66699V23.3337C29.3307 23.867 29.1307 24.3337 28.7307 24.7337C28.3307 25.1337 27.8641 25.3337 27.3307 25.3337H8.66406ZM4.66406 29.3337C4.13073 29.3337 3.66406 29.1337 3.26406 28.7337C2.86406 28.3337 2.66406 27.867 2.66406 27.3337V6.66699H4.66406V27.3337H25.3307V29.3337H4.66406Z"
                                fill="white"/>
                    </svg>
                    <span>Загрузить файл</span>
                </label>
                <? if ($arResult['USER_VEHICLE']['INSPECTION_FILES']): ?>
                    <input type="hidden" id="if-count"
                           value="<?= count($arResult['USER_VEHICLE']['INSPECTION_FILES']) ?>">
                    <? foreach ($arResult['USER_VEHICLE']['INSPECTION_FILES'] as $f_pos => $file): ?>
                        <input type="file" name=INSPECTION_FILES_FVAL_<?= $f_pos['ID'] ?>"
                               value="<?= $file['FILE_DATA'] ?>">
                        <div class="input-file_preview active">
                            <? if ($file['FILE_TYPE'] == "application/pdf"): ?>
                                <img src="/local/templates/.default/assets/img/default_doc_icon.svg" alt="">
                            <? else: ?>
                                <img src="<?= $file['FILE_PATH'] ?>" alt="">
                            <? endif; ?>
                            <button type="button" class="input-file-remove-btn" data-property-id="INSPECTION_FILES"
                                    data-file-id="<?= $file['ID'] ?>" onclick="deleteFile(this)"
                                    data-delete-id="<?= $file['ID'] ?>">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                            d="M2.28795 2.28746C2.4286 2.14686 2.61933 2.06787 2.8182 2.06787C3.01707 2.06787 3.2078 2.14686 3.34845 2.28746L6.00045 4.93946L8.65245 2.28746C8.79391 2.15084 8.98336 2.07525 9.18001 2.07696C9.37665 2.07866 9.56476 2.15754 9.70382 2.2966C9.84288 2.43565 9.92175 2.62376 9.92346 2.82041C9.92517 3.01706 9.84957 3.20651 9.71296 3.34796L7.06095 5.99996L9.71296 8.65197C9.84957 8.79342 9.92517 8.98287 9.92346 9.17952C9.92175 9.37616 9.84288 9.56427 9.70382 9.70333C9.56476 9.84239 9.37665 9.92126 9.18001 9.92297C8.98336 9.92468 8.79391 9.84909 8.65245 9.71247L6.00045 7.06046L3.34845 9.71247C3.207 9.84909 3.01755 9.92468 2.8209 9.92297C2.62425 9.92126 2.43614 9.84239 2.29709 9.70333C2.15803 9.56427 2.07915 9.37616 2.07744 9.17952C2.07573 8.98287 2.15133 8.79342 2.28795 8.65197L4.93995 5.99996L2.28795 3.34796C2.14735 3.20732 2.06836 3.01658 2.06836 2.81771C2.06836 2.61884 2.14735 2.42811 2.28795 2.28746V2.28746Z"
                                            fill="#F2F2F2"/>
                                </svg>
                            </button>
                        </div>
                    <? endforeach; ?>
                <? else: ?>
                    <div class="input-file_preview">
                        <button class="input-file-remove-btn">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M2.28795 2.28746C2.4286 2.14686 2.61933 2.06787 2.8182 2.06787C3.01707 2.06787 3.2078 2.14686 3.34845 2.28746L6.00045 4.93946L8.65245 2.28746C8.79391 2.15084 8.98336 2.07525 9.18001 2.07696C9.37665 2.07866 9.56476 2.15754 9.70382 2.2966C9.84288 2.43565 9.92175 2.62376 9.92346 2.82041C9.92517 3.01706 9.84957 3.20651 9.71296 3.34796L7.06095 5.99996L9.71296 8.65197C9.84957 8.79342 9.92517 8.98287 9.92346 9.17952C9.92175 9.37616 9.84288 9.56427 9.70382 9.70333C9.56476 9.84239 9.37665 9.92126 9.18001 9.92297C8.98336 9.92468 8.79391 9.84909 8.65245 9.71247L6.00045 7.06046L3.34845 9.71247C3.207 9.84909 3.01755 9.92468 2.8209 9.92297C2.62425 9.92126 2.43614 9.84239 2.29709 9.70333C2.15803 9.56427 2.07915 9.37616 2.07744 9.17952C2.07573 8.98287 2.15133 8.79342 2.28795 8.65197L4.93995 5.99996L2.28795 3.34796C2.14735 3.20732 2.06836 3.01658 2.06836 2.81771C2.06836 2.61884 2.14735 2.42811 2.28795 2.28746V2.28746Z"
                                        fill="#F2F2F2"/>
                            </svg>
                        </button>
                    </div>
                <? endif; ?>
            </div>
        </div>
    </div>
    <? if ($arParams['EDIT_VEHICLE'] && $arParams['EDIT_VEHICLE'] == 'Y'): ?>
        <button class="primary-btn primary-btn--lg" id="edit-vehicle-data">Сохранить ТС</button>
    <? else: ?>
        <button class="primary-btn primary-btn--lg" id="save-vehicle-data">Сохранить ТС</button>
    <? endif; ?>
    <br>
    <div id="form-errors"></div>
</form>