<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if ($arResult['ELEMENTS']): ?>
    <div class="table-wrap">
        <table class="table table--reverse">
            <tr>
                <th>Номер</th>
                <th>ФИО</th>
                <th>Наименование</th>
                <th>Вид ТС</th>
                <th>Даты заезда</th>
                <th>Дата выезда</th>
                <th>Статус</th>
                <th></th>
                <th></th>
            </tr>
            <? foreach ($arResult['ELEMENTS'] as $arElement): ?>
                <tr>
                    <td><?= $arElement['ID'] ?></td>
                    <td>
                        <?= $arElement['USER_FIO'] . '<br>' . $arElement['USER_PHONE'] ?>
                    </td>
                    <td>Разрешение на транспортное средство</td>
                    <td><?= $arElement['VEHICLE_NAME'] ?></td>
                    <td><?= $arElement['ARRIVAL_DATE'] ?></td>
                    <td><?= $arElement['DEPARTURE_DATE'] ?></td>
                    <td class="<?= $arElement['STATUS_COLOR'] ?>"><?= $arElement['PERMISSION_STATUS'] ?></td>
                    <td>
                        <? if ($arElement['PERMISSION_STATUS'] == 'Заблокирован'): ?>
                            <a href="javascript:void(0);" class="link link--not-line color-red"
                               onclick="unblockUserVehicle(<?= $arElement['USER_VEHICLE'] ?>);">Разблокировать</a>
                        <? else: ?>
                            <a href="javascript:void(0);" class="link link--not-line"
                               onclick="blockUserVehicle(<?= $arElement['USER_VEHICLE'] ?>);">Заблокировать
                            </a>
                        <? endif; ?>
                    </td>
                    <td>
                        <a href="<?= $arElement['LINK'] ?>" target="_blank" class="link link--not-line">посмотреть</a>
                    </td>
                </tr>
            <? endforeach; ?>
        </table>
    </div>
    <? if ($arResult["NAV_STRING"] <> ''): ?>
        <?= $arResult["NAV_STRING"] ?>
    <? endif ?>
<? endif ?>