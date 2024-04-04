<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<div class="title-wrap">
    <h3 class="form-block_title">Личные данные</h3>
    <a href="" class="btn-edit">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <path
                    d="M1.5 17.9997V15.731H16.5V17.9997H1.5ZM3.0375 13.7435V11.2497L10.0312 4.25599L12.525 6.74974L5.53125 13.7435H3.0375ZM13.35 5.92474L10.8563 3.43099L12.4313 1.85599C12.5688 1.69349 12.725 1.60911 12.9 1.60286C13.075 1.59661 13.25 1.68099 13.425 1.85599L14.8875 3.31849C15.05 3.48099 15.1312 3.65286 15.1312 3.83411C15.1312 4.01536 15.0625 4.18724 14.925 4.34974L13.35 5.92474Z"
                    fill="#ED8C00"/>
        </svg>
        <span id="edit-user-data">Редактировать</span>
    </a>
</div>
<table class="blank-table blank-table--auto">
    <tr>
        <th>ФИО</th>
        <td><?= $arResult['USER_DATA']['LAST_NAME'] . ' ' . $arResult['USER_DATA']['NAME'] . ' ' . $arResult['USER_DATA']['SECOND_NAME'] ?></td>
    </tr>
    <tr>
        <th>Телефон</th>
        <td><?= $arResult['USER_DATA']['PHONE'] ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= $arResult['USER_DATA']['EMAIL'] ?></td>
    </tr>
    <? if ($arResult['USER_DATA']['PREF_CATEGORY']): ?>
        <tr>
            <th>Льготная категория</th>
            <td><?= $arResult['USER_DATA']['PREF_CATEGORY']['VALUE'] ?></td>
        </tr>
    <? else: ?>
        <tr>
            <th>Льготная категория</th>
            <td>Отсутствует</td>
        </tr>
    <? endif; ?>
</table>
