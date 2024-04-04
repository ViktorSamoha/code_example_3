<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if ($arResult['ELEMENTS']): ?>
    <div class="form-block border-top">
        <h3 class="form-block_title">История заказов</h3>
        <div class="table-wrap">
            <table class="table table--reverse">
                <tr>
                    <th>Номер</th>
                    <th>Наименование</th>
                    <th>Кто забронировал</th>
                    <th>Даты бронирования</th>
                    <th>Статус</th>
                    <th></th>
                </tr>
                <? foreach ($arResult['ELEMENTS'] as $arRow): ?>
                    <tr>
                        <td><?= $arRow['ID'] ?></td>
                        <td><?= $arRow['NAME'] ?></td>
                        <td><? if ($arRow['BOOKING_TYPE'] == 'Онлайн'): ?>
                                <div class="online">онлайн</div>
                            <? else: ?>
                                <?= $arRow['BOOKING_TYPE'] ?>
                            <? endif; ?>
                        </td>
                        <td>с <?= $arRow['ARRIVAL_DATE'] ?> по <?= $arRow['DEPARTURE_DATE'] ?></td>
                        <td class="<?= $arRow['STATUS_CLASS'] ?>"><?= $arRow['STATUS'] ?></td>
                        <td>
                            <a href="<?= $arRow['BLANK_LINK'] ?>" class="btn-edit">
                                <svg width="14" height="18" viewBox="0 0 14 18" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                            d="M1.79688 17.7C1.36354 17.7 1.00521 17.5584 0.721875 17.275C0.438542 16.9917 0.296875 16.6334 0.296875 16.2V1.80005C0.296875 1.36672 0.438542 1.00838 0.721875 0.725049C1.00521 0.441716 1.36354 0.300049 1.79688 0.300049H9.64688L13.6969 4.35005V16.2C13.6969 16.6334 13.5552 16.9917 13.2719 17.275C12.9885 17.5584 12.6302 17.7 12.1969 17.7H1.79688ZM9.29688 4.70005H12.9969L9.29688 1.00005V4.70005Z"
                                            fill="#ED8C00"/>
                                </svg>
                                <span>Квитанция</span>
                            </a>
                        </td>
                    </tr>
                <? endforeach; ?>
            </table>
        </div>
    </div>
    <? if ($arResult["NAV_STRING"] <> ''): ?>
        <?= $arResult["NAV_STRING"] ?>
    <? endif ?>
<? endif; ?>