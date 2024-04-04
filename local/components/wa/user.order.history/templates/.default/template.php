<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if ($arResult['ELEMENTS']): ?>
    <div class="table-wrap">
        <table class="table">
            <tr>
                <th>Название</th>
                <th>Даты бронирования</th>
                <th>Состав группы</th>
                <th>Статус</th>
                <th></th>
            </tr>
            <? foreach ($arResult['ELEMENTS'] as $arElement): ?>
                <tr>
                    <td><?= $arElement['NAME'] ?></td>
                    <td>с <?= $arElement['ARRIVAL_DATE'] ?> <br>
                        по <?= $arElement['DEPARTURE_DATE'] ?>
                    </td>
                    <? if ($arElement['USER_GROUP']): ?>
                        <td>
                            <? foreach ($arElement['USER_GROUP'] as $arVisitor): ?>
                                <?= $arVisitor['LAST_NAME'] . ' ' . $arVisitor['NAME'] . ' ' . $arVisitor['SECOND_NAME'] . '<br>' ?>
                            <? endforeach; ?>
                        </td>
                    <? else: ?>
                        <td></td>
                    <? endif; ?>
                    <td>
                        <? if ($arElement['PAYMENT_STATUS']): ?>
                            <?= $arElement['PAYMENT_STATUS'] ?>
                        <? elseif ($arElement['PERMISSION_STATUS']): ?>
                            <?= $arElement['PERMISSION_STATUS']['VALUE'] ?>
                        <? else: ?>
                            Не оплачено
                        <? endif; ?>
                    </td>
                    <td>
                        <? if ($arElement['PAYMENT_STATUS'] == 'Оплачено'): ?>
                            <a href="<?= $arElement['BLANK_LINK'] ?>" target="_blank" class="btn-edit">
                                <span>Бланк</span>
                            </a>
                        <? else: ?>
                            <? if ($arElement['PAYMENT_LINK']): ?>
                                <a href="<?= $arElement['PAYMENT_LINK'] ?>" target="_blank" class="btn-edit">
                                    <span>Оплатить</span>
                                </a>
                            <? else: ?>
                                <a href="<?= $arElement['BLANK_LINK'] ?>" target="_blank" class="btn-edit">
                                    <span>Бланк</span>
                                </a>
                            <? endif; ?>
                        <? endif; ?>
                    </td>
                </tr>
            <? endforeach; ?>
        </table>
    </div>
    <? if ($arResult["NAV_STRING"] <> ''): ?>
        <?= $arResult["NAV_STRING"] ?>
    <? endif ?>
<? else: ?>
    <div class="table-wrap">
        <p>Нет записей</p>
    </div>
<? endif; ?>
