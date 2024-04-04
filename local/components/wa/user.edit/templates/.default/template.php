<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$APPLICATION->SetTitle("Редактирование пользователя");

?>
<? if (!$arResult['ERRORS']): ?>
    <form class="form-create-lk" name="user_edit_form">
        <?= bitrix_sessid_post() ?>
        <input type="hidden" name="USER_ID" value="<?= $arResult['USER']['ID'] ?>"/>
        <div class="form-block">
            <h3 class="form-block_title">Данные авторизации </h3>
            <div class="input">
                <label for="" class="input-label input-label--mb input-label--gray">Логин <span
                            class="color-red">*</span></label>
                <input type="text" name="USER_LOGIN" value="<?= $arResult['USER']['LOGIN'] ?>">
            </div>
            <div class="input">
                <label for="" class="input-label input-label--mb input-label--gray">E-mail <span
                            class="color-red">*</span></label>
                <input type="email" name="USER_EMAIL" value="<?= $arResult['USER']['EMAIL'] ?>">
            </div>
            <div class="input">
                <label for="" class="input-label input-label--mb input-label--gray">Новый пароль</label>
                <div class="password">
                    <input type="password" class="password_input" name="USER_NEW_PASSWORD">
                    <button class="password-control-btn" type="button"></button>
                </div>
            </div>
            <div class="input">
                <label for="" class="input-label input-label--mb input-label--gray">Повторите новый пароль <span
                            class="color-red">*</span></label>
                <div class="password">
                    <input type="password" class="password_input" name="USER_CONFIRM_NEW_PASSWORD">
                    <button class="password-control-btn" type="button"></button>
                </div>
            </div>
        </div>
        <div class="form-block">
            <h3 class="form-block_title">Выберите роль <span class="asterisk">*</span></h3>
            <div class="radio-list">
                <div class="radio">
                    <input type="radio" id="radio-user-role-8" name="USER_ROLE"
                           value="8" <?= in_array('8', $arResult['USER']['GROUPS']) ? 'checked' : '' ?>>
                    <label for="radio-user-role-8">
                        <div class="radio_text">Оператор</div>
                    </label>
                </div>
                <div class="radio">
                    <input type="radio" id="radio-user-role-7" name="USER_ROLE"
                           value="7" <?= in_array('7', $arResult['USER']['GROUPS']) ? 'checked' : '' ?>>
                    <label for="radio-user-role-7">
                        <div class="radio_text">Администратор</div>
                    </label>
                </div>
                <div class="radio">
                    <input type="radio" id="radio-user-role-9" name="USER_ROLE"
                           value="9" <?= in_array('9', $arResult['USER']['GROUPS']) ? 'checked' : '' ?>>
                    <label for="radio-user-role-9">
                        <div class="radio_text">Бронь</div>
                    </label>
                </div>
            </div>
            <div class="select-block">
                <h3 class="form-block_title">Локация</h3>
                <div class="custom-select" id="user-location-select">
                    <div class="custom-select_head">
                        <span class="custom-select_title">Выберите локацию</span>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? foreach ($arResult['LOCATIONS'] as $location): ?>
                            <div class="custom-select_item">
                                <div class="checkbox checkbox-w-btn">
                                    <input type="checkbox"
                                           id="checkbox_<?= $location['ID'] ?>"
                                           value="<?= $location['ID'] ?>"
                                           name="UF_USER_LOCATIONS[]"
                                        <?= $location['CONDITION'] ?>
                                    >
                                    <label for="checkbox_<?= $location['ID'] ?>">
                                        <div class="checkbox_text"><?= $location['NAME'] ?></div>
                                    </label>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="select-block">
                <h3 class="form-block_title">Объект</h3>
                <div class="custom-select" id="user-object-select">
                    <div class="custom-select_head">
                        <span class="custom-select_title">Выберите объект</span>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? foreach ($arResult['OBJECTS'] as $object): ?>
                            <div class="custom-select_item">
                                <div class="checkbox checkbox-w-btn">
                                    <input type="checkbox"
                                           id="checkbox_<?= $object['ID'] ?>"
                                           value="<?= $object['ID'] ?>"
                                           name="UF_USER_OBJECTS[]"
                                        <?= $object['CONDITION'] ?>
                                    >
                                    <label for="checkbox_<?= $object['ID'] ?>">
                                        <div class="checkbox_text"><?= $object['NAME'] ?></div>
                                    </label>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <button class="primary-btn primary-btn--xl" id="user-edit-form-submit-btn">Сохранить изменения</button>
        <div class="form-description">Все данные обязательны для заполнения</div>
    </form>
<? else: ?>
    Ошибка!
<? endif; ?>
