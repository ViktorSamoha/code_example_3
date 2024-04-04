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
                    <span class="i-subtitle"><?= $arResult['BLANK']['PAYMENT_STRING'] ?></span>
                    <table class="blank-table mb40">
                        <tr>
                            <th>Наименование объекта</th>
                            <td><?= $arResult['BLANK']['OBJECT_NAME'] ?></td>
                        </tr>
                        <tr>
                            <th>Категория</th>
                            <td><?= $arResult['BLANK']['OBJECT_CATEGORY'] ?></td>
                        </tr>
                        <tr>
                            <th>Локация</th>
                            <td><?= $arResult['BLANK']['OBJECT_LOCATION'] ?></td>
                        </tr>
                        <tr>
                            <th>Дата и время заезда</th>
                            <td><?= $arResult['BLANK']['ARRIVAL_DATE_TIME'] ?></td>
                        </tr>
                        <tr>
                            <th>Дата и время выезда</th>
                            <td><?= $arResult['BLANK']['DEPARTURE_DATE_TIME'] ?></td>
                        </tr>
                        <tr>
                            <th>ФИО арендатора</th>
                            <td><?= $arResult['BLANK']['USER_FIO'] ?></td>
                        </tr>
                        <tr>
                            <th>Состав группы</th>
                            <td>Общее число — <?= $arResult['BLANK']['GUEST_COUNT'] ?> человека <br>
                                <?= $arResult['BLANK']['BENIFICIARIES'] > 0 ? "из них льготников: " . $arResult['BLANK']['BENIFICIARIES'] : "" ?>
                            </td>

                        </tr>
                        <tr>
                            <th>Уникальный номер</th>
                            <td><?= $arResult['BLANK']['ID'] ?></td>
                        </tr>
                        <tr>
                            <th>Тип бронирования</th>
                            <td><?= $arResult['BLANK']['BOOKING_TYPE'] ?></td>
                        </tr>

                        <tr>
                            <th>Разрешение</th>
                            <? if ($arResult['BLANK']['PERMISSION'] == 'Да'): ?>
                                <td>Есть</td>
                            <? else: ?>
                                <td>Нет</td>
                            <? endif; ?>
                        </tr>

                        <? if (!empty($arResult['BLANK']['GUEST_CARS'])): ?>
                            <? foreach ($arResult['BLANK']['GUEST_CARS'] as $guest_car): ?>
                                <tr>
                                    <th>Номер автомобиля</th>
                                    <td><?= $guest_car ?></td>
                                </tr>
                            <? endforeach; ?>
                        <? endif; ?>
                        <tr>
                            <th>Стоимость</th>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="td-w-padding">
                                <table>
                                    <tr>
                                        <th>Стоимость аренды</th>
                                        <td><?= $arResult['BLANK']['OBJECT_RENT_COST'] ?> ₽</td>
                                    </tr>

                                    <tr>
                                        <th>Разрешение на посещение</th>
                                        <td><?= $arResult['BLANK']['PERMISSION_COUNT'] ?> чел
                                            - <?= $arResult['BLANK']['PERMISSION_COST'] ?> ₽
                                        </td>
                                    </tr>
                                    <? if ($arResult['BLANK']['PERMISSION'] == 'Да'): ?>
                                        <tr>
                                            <th>Разрешение на посещение</th>
                                            <td>Имеется</td>
                                        </tr>
                                    <? endif; ?>
                                    <? if ($arResult['BLANK']['PERSON_OVER'] && $arResult['BLANK']['PERSON_OVER_COST']): ?>
                                        <tr>
                                            <th>Дополнительные места</th>
                                            <td><?= $arResult['BLANK']['PERSON_OVER'] ?> чел
                                                - <?= $arResult['BLANK']['PERSON_OVER_COST'] ?> ₽
                                            </td>
                                        </tr>
                                    <? endif; ?>
                                    <tr>
                                        <th>Итого</th>
                                        <td><?= $arResult['BLANK']['BOOKING_COST'] ?> ₽</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <!--<div class="blank-map">
                        <img src="<? /*= ASSETS */ ?>images/map.jpeg" alt="">
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
                <button class="primary-btn group-btn_item" type="button" id="print-blank">Печать бланка</button>
            </div>
        </div>
    </section>
<? endif; ?>