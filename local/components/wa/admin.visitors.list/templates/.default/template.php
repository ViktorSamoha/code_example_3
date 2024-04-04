<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if ($arResult['ELEMENTS']): ?>
    <div class="table-wrap">
        <table class="table">
            <tr>
                <th>ФИО</th>
                <th>Телефон</th>
                <th>E-mail</th>
                <th></th>
            </tr>
            <? foreach ($arResult['ELEMENTS'] as $arUser): ?>
                <tr>
                    <td><?= $arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME'] ?></td>
                    <td><?= $arUser['WORK_PHONE'] ?></td>
                    <td><?= $arUser['EMAIL'] ?></td>
                    <td>
                        <a href="/admin/user_profile/?ID=<?= $arUser['ID'] ?>"
                           class="link link--not-line">посмотреть</a>
                    </td>
                </tr>
            <? endforeach; ?>
        </table>
    </div>
    <? if ($arResult["NAV_STRING"] <> ''): ?>
        <?= $arResult["NAV_STRING"] ?>
    <? endif ?>
<? endif ?>