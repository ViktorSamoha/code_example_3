<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if ($arResult['BLANK']): ?>
    <style>
        @media print {
            #panel, #print-blank, .rounded-btn, #save-blank, #status-block, .lk-head {
                display: none;
            }
        }
    </style>
    <div class="i-blank">
        <div class="i-blank_left">
            <h3 class="i-title"><?= $arResult['BLANK']['NAME'] ?></h3>
            <span class="i-subtitle"><?= $arResult['BLANK']['STATUS_STRING'] ?></span>
            <table class="blank-table mb40 blank-table--auto">
                <tr>
                    <th>ФИО</th>
                    <td><?= $arResult['BLANK']['FIO'] ?></td>
                    <td></td>
                </tr>
                <tr>
                    <th>Сроки пребывания</th>
                    <td><?= $arResult['BLANK']['DATE_INTERVAL'] ?></td>
                    <td></td>
                </tr>
                <tr>
                    <th>Права на управление техникой:</th>
                    <td><?= $arResult['BLANK']['USER_DRIVING_LICENSE'] ?></td>
                    <td>
                        <? if ($arResult['BLANK']['DRIVING_LICENSE_FILES']): ?>
                            <? foreach ($arResult['BLANK']['DRIVING_LICENSE_FILES'] as $k => $drivingLicenseFile): ?>
                                <? if ($drivingLicenseFile['CONTENT_TYPE'] == "image/png"): ?>
                                    <? if ($k == 0): ?>
                                        <a href="<?= $drivingLicenseFile['SRC'] ?>" class="rounded-btn rounded-btn--sm"
                                           data-fancybox="doc-1">Посмотреть</a>
                                    <? else: ?>
                                        <div style="display:none">
                                            <a data-fancybox="doc-1" href="<?= $drivingLicenseFile['SRC'] ?>">
                                                <img src="<?= $drivingLicenseFile['SRC'] ?>"/>
                                            </a>
                                        </div>
                                    <? endif; ?>
                                <? else: ?>
                                    <a href="<?= $drivingLicenseFile['SRC'] ?>" target="_blank"
                                       class="rounded-btn rounded-btn--sm">Посмотреть документ</a>
                                <? endif; ?>
                            <? endforeach; ?>
                        <? endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Марка и модель ТС:</th>
                    <td><?= $arResult['BLANK']['VEHICLE_MARK_MODEL'] ?></td>
                    <td>
                        <? if ($arResult['BLANK']['TECHNICAL_PASSPORT']): ?>
                            <? foreach ($arResult['BLANK']['TECHNICAL_PASSPORT'] as $k => $techPassFile): ?>
                                <? if ($techPassFile['CONTENT_TYPE'] == "image/png"): ?>
                                    <? if ($k == 0): ?>
                                        <a href="<?= $techPassFile['SRC'] ?>" class="rounded-btn rounded-btn--sm"
                                           data-fancybox="doc-2">Посмотреть</a>
                                    <? else: ?>
                                        <div style="display:none">
                                            <a data-fancybox="doc-2" href="<?= $techPassFile['SRC'] ?>">
                                                <img src="<?= $techPassFile['SRC'] ?>"/>
                                            </a>
                                        </div>
                                    <? endif; ?>
                                <? else: ?>
                                    <a href="<?= $techPassFile['SRC'] ?>" target="_blank"
                                       class="rounded-btn rounded-btn--sm">Посмотреть документ</a>
                                <? endif; ?>
                            <? endforeach; ?>
                        <? endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Тех. осмотр</th>
                    <td><?= $arResult['BLANK']['INSPECTION_DATE'] ?></td>
                    <td>
                        <? if ($arResult['BLANK']['INSPECTION_FILES']): ?>
                            <? foreach ($arResult['BLANK']['INSPECTION_FILES'] as $k => $inspectionFile): ?>
                                <? if ($inspectionFile['CONTENT_TYPE'] == "image/png"): ?>
                                    <? if ($k == 0): ?>
                                        <a href="<?= $inspectionFile['SRC'] ?>" class="rounded-btn rounded-btn--sm"
                                           data-fancybox="doc-3">Посмотреть</a>
                                    <? else: ?>
                                        <div style="display:none">
                                            <a data-fancybox="doc-3" href="<?= $inspectionFile['SRC'] ?>">
                                                <img src="<?= $inspectionFile['SRC'] ?>"/>
                                            </a>
                                        </div>
                                    <? endif; ?>
                                <? else: ?>
                                    <a href="<?= $inspectionFile['SRC'] ?>" target="_blank"
                                       class="rounded-btn rounded-btn--sm">Посмотреть документ</a>
                                <? endif; ?>
                            <? endforeach; ?>
                        <? endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Маршрут</th>
                    <td><?= $arResult['BLANK']['ROUTE'] ?></td>
                    <td></td>
                </tr>
            </table>
            <? if (isset($arResult['BLANK']['STATUS']) && $arResult['BLANK']['STATUS'] != "На рассмотрении"): ?>
                <div class="form-block form-block--w-hidden-block" id="status-block">
                    <h3 class="form-block_title">Статус разрешения</h3>
                    <div class="permit-block_top">
                        <div class="radio-group">
                            <div class="radio">
                                <input type="radio" id="radio_01" name="permit-status"
                                       value="yes" <?= $arResult['BLANK']['STATUS'] == 'Одобрено' ? 'checked' : '' ?>>
                                <label for="radio_01">
                                    <div class="radio_text">Одобрено</div>
                                </label>
                            </div>
                            <div class="radio">
                                <input type="radio" id="radio_02" name="permit-status" value="no"
                                       data-name="show-hidden-block" <?= $arResult['BLANK']['STATUS'] == 'Отказано' ? 'checked' : '' ?>>
                                <label for="radio_02">
                                    <div class="radio_text">Отказано</div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="textarea textarea--lg <?= $arResult['BLANK']['STATUS'] == 'Одобрено' ? 'hidden' : '' ?>"
                         data-name="hidden-block">
                        <label for="">Причина отказа</label>
                        <textarea name="DENY_TEXT" class=""><?= $arResult['BLANK']['DENY_TEXT'] ?></textarea>
                    </div>
                </div>
            <? else: ?>
                <div class="form-block form-block--w-hidden-block" id="status-block">
                    <h3 class="form-block_title">Статус разрешения</h3>
                    <div class="permit-block_top">
                        <div class="radio-group">
                            <div class="radio">
                                <input type="radio" id="radio_01" name="permit-status"
                                       value="yes" checked>
                                <label for="radio_01">
                                    <div class="radio_text">Одобрено</div>
                                </label>
                            </div>
                            <div class="radio">
                                <input type="radio" id="radio_02" name="permit-status" value="no"
                                       data-name="show-hidden-block">
                                <label for="radio_02">
                                    <div class="radio_text">Отказано</div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="textarea textarea--lg hidden"
                         data-name="hidden-block">
                        <label for="">Причина отказа</label>
                        <textarea name="DENY_TEXT" class=""></textarea>
                    </div>
                </div>
            <? endif; ?>
            <!--<div class="blank-map">
                <img src="images/map.jpeg" alt="">
            </div>-->

        </div>

        <div class="i-blank_right">
            <img src="<?= ASSETS ?>images/i-blank-logo.svg" alt="" class="blank_logo">
            <div class="i-blank-description">
                В соответствии с положением о

                <span class="color-red">ЗАПРЕЩАЕТСЯ</span> нахождение в заповедной
                зоне!
                 при себе
                иметь данное разрешение, <span class="color-red">а также документ
              удостоверяющий личность и документ
              удостоверяющий льготу если имеется
              таковая.</span> Предъявить по требованию
                государственного инспектора.
            </div>
            <? if ($arResult['BLANK']['QR_CODE']): ?>
                <div class="qr">
                    <img src="<?= $arResult['BLANK']['QR_CODE'] ?>" alt="">
                </div>
            <? endif; ?>
        </div>
    </div>
    <div class="group-btn">
        <button class="gray-btn group-btn_item" type="button" id="save-blank">Изменить статус</button>
        <button class="primary-btn group-btn_item" type="button" id="print-blank">Печать бланка</button>
    </div>
    <div id="form-errors"></div>
    <input type="hidden" name="PERMISSION_ID" value="<?= $arResult['PERMISSION_DATA']['ID'] ?>">
<? endif; ?>