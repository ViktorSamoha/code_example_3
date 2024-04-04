<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if ($arResult['BLANK']): ?>
    <? $APPLICATION->SetTitle($arResult['BLANK']['NAME']); ?>
    <style>
        @media print {
            #panel, #print-blank {
                display: none;
            }
        }
    </style>
    <section class="lk">
        <div class="lk_content">
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
                        <? if ($arResult['BLANK']['USER_DRIVING_LICENSE']): ?>
                            <tr>
                                <th>Права на управление техникой:</th>
                                <td><?= $arResult['BLANK']['USER_DRIVING_LICENSE'] ?></td>
                                <td></td>
                            </tr>
                        <? endif; ?>
                        <? if ($arResult['BLANK']['VEHICLE_MARK_MODEL']): ?>
                            <tr>
                                <th>Марка и модель ТС:</th>
                                <td><?= $arResult['BLANK']['VEHICLE_MARK_MODEL'] ?></td>
                                <td></td>
                            </tr>
                        <? endif; ?>
                        <? if ($arResult['BLANK']['INSPECTION_DATE']): ?>
                            <tr>
                                <th>Тех. осмотр</th>
                                <td><?= $arResult['BLANK']['INSPECTION_DATE'] ?></td>
                                <td></td>
                            </tr>
                        <? endif; ?>
                        <tr>
                            <th>Маршрут</th>
                            <td><?= $arResult['BLANK']['ROUTE'] ?></td>
                            <td></td>
                        </tr>
                    </table>
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
                <button class="primary-btn group-btn_item" type="button" id="print-blank">Печать бланка</button>
            </div>
        </div>
    </section>
<? endif; ?>