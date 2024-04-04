<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$APPLICATION->SetTitle("Список пользователей");

?>
<div class="table-wrap">
    <table class="table" id="user-table">
        <tr>
            <th>ID</th>
            <th>Логин</th>
            <th>Роль</th>
            <th>Дата регистрации</th>
            <th>Локация</th>
            <th>Объект</th>
            <th></th>
            <th></th>
        </tr>
        <? foreach ($arResult['USERS'] as $arUser): ?>
            <tr>
                <td><?= $arUser['ID'] ?></td>
                <td><?= $arUser['LOGIN'] ?></td>
                <td><?= $arUser['GROUP']['NAME'] ?></td>
                <td><?= $arUser['DATE_REGISTER'] ?></td>
                <td>
                    <? if (isset($arUser['LOCATIONS']) && !empty($arUser['LOCATIONS'])): ?>
                        <? foreach ($arUser['LOCATIONS'] as $user_location): ?>
                            <?= $user_location['NAME'] ?>
                            <hr>
                        <? endforeach; ?>
                    <? endif; ?>
                </td>
                <td>
                    <? if (isset($arUser['OBJECTS']) && !empty($arUser['OBJECTS'])): ?>
                        <? foreach ($arUser['OBJECTS'] as $user_object): ?>
                            <?= $user_object['NAME'] ?><hr>
                        <? endforeach; ?>
                    <? endif; ?>
                </td>
                <td>
                    <a href="/admin/users/edit/?id=<?= $arUser['ID'] ?>" class="btn-edit">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M1.5 17.9997V15.731H16.5V17.9997H1.5ZM3.0375 13.7435V11.2497L10.0312 4.25599L12.525 6.74974L5.53125 13.7435H3.0375ZM13.35 5.92474L10.8563 3.43099L12.4313 1.85599C12.5688 1.69349 12.725 1.60911 12.9 1.60286C13.075 1.59661 13.25 1.68099 13.425 1.85599L14.8875 3.31849C15.05 3.48099 15.1312 3.65286 15.1312 3.83411C15.1312 4.01536 15.0625 4.18724 14.925 4.34974L13.35 5.92474Z"
                                    fill="#ED8C00"/>
                        </svg>
                        <span>Редактировать</span>
                    </a>
                </td>
                <td>
                    <button class="btn-remove js-open-r-modal" data-name="modal-delete-user"
                            data-id="<?= $arUser['ID'] ?>">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5.79167 17.083C5.47222 17.083 5.19444 16.965 4.95833 16.7288C4.72222 16.4927 4.60417 16.208 4.60417 15.8747V4.54134H3.75V3.60384H7.3125V3.02051H12.6875V3.60384H16.25V4.54134H15.3958V15.8747C15.3958 16.208 15.2778 16.4927 15.0417 16.7288C14.8056 16.965 14.5278 17.083 14.2083 17.083H5.79167ZM7.9375 14.3747H8.89583V6.29134H7.9375V14.3747ZM11.1042 14.3747H12.0625V6.29134H11.1042V14.3747Z"
                                    fill="#ED8C00"/>
                        </svg>
                    </button>
                </td>
            </tr>
        <? endforeach; ?>
    </table>
</div>
