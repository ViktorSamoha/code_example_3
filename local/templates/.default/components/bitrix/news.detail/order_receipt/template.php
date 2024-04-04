<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
$this->setFrameMode(true);
?>
<style>
    @media print {
        #panel, #print-blank {
            display: none;
        }
    }
</style>
<div class="blank blank--center" id="blank">
    <table class="blank-table">
        <tr>
            <th>Наименование объекта</th>
            <td><?= $arResult['BLANK_FIELDS']['OBJECT_NAME'] ?></td>
        </tr>
        <tr>
            <th>Категория</th>
            <td><?= $arResult['BLANK_FIELDS']['OBJECT_CATEGORY'] ?></td>
        </tr>
        <tr>
            <th>Локация</th>
            <td><?= $arResult['BLANK_FIELDS']['OBJECT_LOCATION'] ?></td>
        </tr>
        <tr>
            <th>Дата и время заезда</th>
            <td><?= $arResult['BLANK_FIELDS']['RENTAL_DATE'] . ' ' . $arResult['BLANK_FIELDS']['CHECK_IN_TIME'] ?></td>
        </tr>
        <tr>
            <th>Дата и время выезда</th>
            <td><?= $arResult['BLANK_FIELDS']['DEPARTURE_DATE'] . ' ' . $arResult['BLANK_FIELDS']['DEPARTURE_TIME'] ?></td>
        </tr>
        <tr>
            <th>ФИО арендатора</th>
            <td><?= $arResult['BLANK_FIELDS']['TENANT'] ?></td>
        </tr>
        <tr>
            <th>Состав</th>
            <td>
                <?= $arResult['BLANK_FIELDS']['ADULTS'] > 0 ? "Общее число: " . $arResult['BLANK_FIELDS']['ADULTS'] . "," : "" ?>
                <?= $arResult['BLANK_FIELDS']['BENIFICIARIES'] > 0 ? "из них льготников: " . $arResult['BLANK_FIELDS']['BENIFICIARIES'] : "" ?>
            </td>
        </tr>
        <tr>
            <th>Уникальный номер</th>
            <td><?= $arResult['BLANK_FIELDS']['ORDER_ID'] ?></td>
        </tr>
        <tr>
            <th>Тип бронирования</th>
            <td><?= $arResult['BLANK_FIELDS']['MANAGER'] ?></td>
        </tr>
        <? if (!empty($arResult['BLANK_FIELDS']['GUEST_CARS'])): ?>
            <? foreach ($arResult['BLANK_FIELDS']['GUEST_CARS'] as $guest_car): ?>
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
                        <td><?= $arResult['BLANK_FIELDS']['OBJECT_COST'] ?> ₽</td>
                    </tr>
                    <? if ($arResult['DISPLAY_PROPERTIES']['PERMISSION']['VALUE_ENUM_ID'] == 2 && $arResult['BLANK_FIELDS']['PERMISSION_COST']): ?>
                        <tr>
                            <th>Разрешение на посещение</th>
                            <td><?= $arResult['BLANK_FIELDS']['PERMISSION_COUNT'] ?> чел
                                - <?= $arResult['BLANK_FIELDS']['PERMISSION_COST'] ?> ₽
                            </td>
                        </tr>
                    <? else: ?>
                        <tr>
                            <th>Разрешение на посещение</th>
                            <td>Имеется</td>
                        </tr>
                    <? endif; ?>
                    <? if ($arResult['BLANK_FIELDS']['PERSON_OVER'] && $arResult['BLANK_FIELDS']['PERSON_OVER_COST']): ?>
                        <tr>
                            <th>Дополнительные места</th>
                            <td><?= $arResult['BLANK_FIELDS']['PERSON_OVER'] ?> чел
                                - <?= $arResult['BLANK_FIELDS']['PERSON_OVER_COST'] ?> ₽
                            </td>
                        </tr>
                    <? endif; ?>
                    <tr>
                        <th>Итого</th>
                        <td><?= $arResult['BLANK_FIELDS']['COST'] ?> ₽</td>
                    </tr>
                    <? if (isset($arResult['BLANK_FIELDS']['PAYMENT_STATUS']['SHOW_PAY_BTN']) && $arResult['BLANK_FIELDS']['PAYMENT_STATUS']['SHOW_PAY_BTN'] == 'Y'): ?>
                        <tr>
                            <th>Статус</th>
                            <td>Не оплачен</td>
                        </tr>
                    <? elseif (isset($arResult['BLANK_FIELDS']['PAYMENT_STATUS'])): ?>
                        <tr>
                            <th>Статус</th>
                            <td>Заказ оплачен: <?= $arResult['BLANK_FIELDS']['PAYMENT_STATUS'] ?></td>
                        </tr>
                    <? endif; ?>
                </table>
            </td>
        </tr>
    </table>
    <div class="blank_right">
        <img src="<?= ASSETS ?>images/f-logo.svg" alt="" class="blank_logo">
        <? if ($arResult['BLANK_FIELDS']['QRCODE']): ?>
            <div class="qr">
                <img src="<?= $arResult['BLANK_FIELDS']['QRCODE'] ?>" alt="">
            </div>
        <? endif; ?>
    </div>
</div>
<div class="blank-map-section">
    <div class="blank-map" id="map">
    </div>
</div>
<? if (isset($arResult['BLANK_FIELDS']['PAYMENT_STATUS']['SHOW_PAY_BTN']) && $arResult['BLANK_FIELDS']['PAYMENT_STATUS']['SHOW_PAY_BTN'] == 'Y'): ?>
    <div class="group-btn" style="width: 50%;margin-left:auto;margin-right: auto;">
        <button class="primary-btn primary-btn--center primary-btn--lg" type="button"
                onclick="window.location.href='<?= $arResult['BLANK_FIELDS']['PAYMENT_STATUS']['PAY_BTN_LINK'] ?>'">
            Оплатить
        </button>
        <button class="primary-btn primary-btn--center primary-btn--lg" type="button" id="print-blank">Печать бланка
        </button>
    </div>
<? else: ?>
    <div class="group-btn" style="width: 50%;margin-left:auto;margin-right: auto;">
        <button class="primary-btn primary-btn--center primary-btn--lg" type="button"
                onclick="window.location.href='/'">
            Вернуться
        </button>
        <button class="primary-btn primary-btn--center primary-btn--lg" type="button" id="print-blank">Печать бланка
        </button>
    </div>
<? endif; ?>
<script>
    let page_map = new PageMap(<?= CUtil::PhpToJSObject($arResult['MAP_JSON'])?>);
    page_map.init();
</script>