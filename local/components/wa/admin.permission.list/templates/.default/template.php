<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if ($arResult['ELEMENTS']): ?>
    <div class="table-wrap">
        <table class="table table--reverse">
            <tr>
                <th>Номер</th>
                <th>ФИО</th>
                <th>Наименование</th>
                <th>Кто забронировал</th>
                <th>Даты заезда</th>
                <th>Дата выезда</th>
                <th></th>
            </tr>
            <? foreach ($arResult['ELEMENTS'] as $arElement): ?>
                <tr>
                    <td><?= $arElement['ID'] ?></td>
                    <td>
                        <?= $arElement['USER_FIO'] . '<br>' . $arElement['USER_PHONE'] ?>
                    </td>
                    <td>Разрешение на посещение</td>
                    <td>
                        <? if ($arElement['BOOKING_TYPE'] == 'Онлайн'): ?>
                            <div class="online">онлайн</div>
                        <? else: ?>
                            <?= $arElement['BOOKING_TYPE'] ?>
                        <? endif; ?>
                    </td>
                    <td><?= $arElement['ARRIVAL_DATE'] ?></td>
                    <td><?= $arElement['DEPARTURE_DATE'] ?></td>
                    <td>
                        <a href="<?= $arElement['LINK'] ?>" class="link link--not-line">посмотреть</a>
                    </td>
                </tr>
            <? endforeach; ?>
        </table>
    </div>
    <? if ($arResult["NAV_STRING"] <> ''): ?>
        <?= $arResult["NAV_STRING"] ?>
    <? endif ?>
<? endif ?>