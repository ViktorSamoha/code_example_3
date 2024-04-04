<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 * @global CUser $USER
 * @global CMain $APPLICATION
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if ($arResult["SHOW_SMS_FIELD"] == true) {
    CJSCore::Init('phone_auth');
}
?>
<section class="authorization">
    <div class="bx-auth-reg authorization_text">
        <a href="/" class="authorization_logo">
            <img src="<?= ASSETS ?>images/lk-logo.svg" alt="site_logo">
        </a>

        <h1 class="authorization_title">Регистрация</h1>

        <? if ($USER->IsAuthorized()): ?>
            <? LocalRedirect("/user/"); ?>
        <? else: ?>

            <? if ($arResult["USE_EMAIL_CONFIRMATION"] === "Y"): ?>
            <p><? echo GetMessage("REGISTER_EMAIL_WILL_BE_SENT") ?></p>
        <? endif ?>

        <? if ($arResult["SHOW_SMS_FIELD"] == true): ?>

            <form method="post" action="<?= POST_FORM_ACTION_URI ?>" name="regform">
                <?
                if ($arResult["BACKURL"] <> ''):
                    ?>
                    <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
                <?
                endif;
                ?>
                <input type="hidden" name="SIGNED_DATA" value="<?= htmlspecialcharsbx($arResult["SIGNED_DATA"]) ?>"/>
                <table>
                    <tbody>
                    <tr>
                        <td><? echo GetMessage("main_register_sms") ?><span class="starrequired">*</span></td>
                        <td><input size="30" type="text" name="SMS_CODE"
                                   value="<?= htmlspecialcharsbx($arResult["SMS_CODE"]) ?>" autocomplete="off"/></td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td><input type="submit" name="code_submit_button"
                                   value="<? echo GetMessage("main_register_sms_send") ?>"/></td>
                    </tr>
                    </tfoot>
                </table>
            </form>

            <script>
                new BX.PhoneAuth({
                    containerId: 'bx_register_resend',
                    errorContainerId: 'bx_register_error',
                    interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
                    data:
                        <?=CUtil::PhpToJSObject([
                            'signedData' => $arResult["SIGNED_DATA"],
                        ])?>,
                    onError:
                        function (response) {
                            var errorDiv = BX('bx_register_error');
                            var errorNode = BX.findChildByClassName(errorDiv, 'errortext');
                            errorNode.innerHTML = '';
                            for (var i = 0; i < response.errors.length; i++) {
                                errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
                            }
                            errorDiv.style.display = '';
                        }
                });
            </script>

            <div id="bx_register_error" style="display:none"><? ShowError("error") ?></div>

            <div id="bx_register_resend"></div>

        <? else: ?>
            <form method="post" action="<?= POST_FORM_ACTION_URI ?>" name="regform" enctype="multipart/form-data"
                  class="form-authorization form-authorization--reg">
                <?
                if ($arResult["BACKURL"] <> ''):
                    ?>
                    <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
                <?
                endif;
                ?>
                <? foreach ($arResult["SHOW_FIELDS"] as $FIELD): ?>
                <? if ($arResult["ERRORS"][$FIELD]): ?>
                <div class="authorization-item authorization-item--error">
                    <? elseif ($FIELD == 'CONFIRM_PASSWORD' && $arResult["ERRORS"][0] == "Неверное подтверждение пароля.<br>"): ?>
                    <div class="authorization-item authorization-item--error">
                        <? else: ?>
                        <div class="authorization-item">
                            <? endif; ?>
                            <? if ($FIELD !== 'LOGIN') { ?>
                                <label for=""
                                       class="authorization-item_label"><?= GetMessage("REGISTER_FIELD_" . $FIELD) ?>
                                    <? if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"): ?>
                                        <span class="starrequired">*</span>
                                    <? endif ?>
                                </label>
                            <? } ?>
                            <?
                            switch ($FIELD) {
                            case "PASSWORD":
                                ?>
                                <div class="password">
                                    <input size="30" type="password" name="REGISTER[<?= $FIELD ?>]"
                                           value="<?= $arResult["VALUES"][$FIELD] ?>" autocomplete="off"
                                           class="bx-auth-input authorization-item_input password_input"/>
                                    <button class="password-control-btn" type="button"></button>
                                </div>
                            <? if ($arResult["SECURE_AUTH"]): ?>
                                <span class="bx-auth-secure" id="bx_auth_secure"
                                      title="<? echo GetMessage("AUTH_SECURE_NOTE") ?>"
                                      style="display:none">
					                    <div class="bx-auth-secure-icon"></div>
				                    </span>
                                <noscript>
				                        <span class="bx-auth-secure"
                                              title="<? echo GetMessage("AUTH_NONSECURE_NOTE") ?>">
					                        <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
				                        </span>
                                </noscript>
                                <script type="text/javascript">
                                    document.getElementById('bx_auth_secure').style.display = 'inline-block';
                                </script>
                            <? endif ?>
                            <?
                            break;
                            case "CONFIRM_PASSWORD":
                            ?>
                                <div class="password">
                                    <input size="30" type="password" name="REGISTER[<?= $FIELD ?>]"
                                           value="<?= $arResult["VALUES"][$FIELD] ?>" autocomplete="off"
                                           class="authorization-item_input password_input"/>
                                    <button class="password-control-btn" type="button"></button>
                                </div>

                            <?
                            break;
                            case "WORK_PHONE":
                            ?>
                            <input class="authorization-item_input" size="30" type="tel" name="REGISTER[<?= $FIELD ?>]"
                                   value="<?= $arResult["VALUES"][$FIELD] ?>"/>
                            <?
                            break;
                            default:
                            ?>
                            <? if ($FIELD == 'EMAIL') { ?>
                            <input class="authorization-item_input" size="30" type="email"
                                   name="REGISTER[<?= $FIELD ?>]"
                                   onkeyup="document.getElementById('login-field').value = this.value"
                                   value="<?= $arResult["VALUES"][$FIELD] ?>"/>
                            <? } elseif ($FIELD == 'LOGIN') { // Скрываем поле LOGIN ?>
                            <input id="login-field" size="30" type="hidden" name="REGISTER[<?= $FIELD ?>]"
                                   value="<?= $arResult["VALUES"][$FIELD] ?>"/>
                            <? } else { ?>
                            <input class="authorization-item_input" size="30" type="text" name="REGISTER[<?= $FIELD ?>]"
                                   value="<?= $arResult["VALUES"][$FIELD] ?>"/>
                            <? } ?>
                            <?
                            } ?>
                            <? if ($arResult["ERRORS"][$FIELD] && $FIELD != 'LOGIN'): ?>
                                <div class="authorization-item_error">введен
                                    некорректный <?= GetMessage("REGISTER_FIELD_" . $FIELD) ?></div>
                            <? elseif ($FIELD == 'CONFIRM_PASSWORD' && $arResult["ERRORS"][0] == "Неверное подтверждение пароля.<br>"): ?>
                                <div class="authorization-item_error">Неверное подтверждение пароля</div>
                            <? endif; ?>
                            <? if ($FIELD == 'PASSWORD' && !$arResult["ERRORS"][$FIELD]): ?>
                                <? echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"]; ?>
                            <? endif; ?>
                        </div>
                        <? endforeach ?>
                        <?
                        /* CAPTCHA */
                        if ($arResult["USE_CAPTCHA"] == "Y") {
                        ?>
                        <? if ($arResult["ERRORS"][0] && $arResult["ERRORS"][0] == "Неверно введено слово с картинки"): ?>
                        <div class="authorization-item authorization-item--error">
                            <? else: ?>
                            <div class="authorization-item">
                                <? endif; ?>
                                <label for=""
                                       class="authorization-item_label"><?= GetMessage("REGISTER_CAPTCHA_TITLE") ?></label>
                                <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
                                <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>"
                                     width="180" height="40" alt="CAPTCHA"/>
                                <?= GetMessage("REGISTER_CAPTCHA_PROMT") ?>:<span class="starrequired">*</span>
                                <input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off"
                                       class="authorization-item_input"/>
                                <? if ($arResult["ERRORS"][0] && $arResult["ERRORS"][0] == "Неверно введено слово с картинки"): ?>
                                    <div class="authorization-item_error"><? ShowError($arResult["ERRORS"][0]); ?></div>
                                    <br>
                                <? endif; ?>
                            </div>
                            <?
                            }
                            /* !CAPTCHA */
                            ?>
                            <input type="submit" name="register_submit_button" class="primary-btn primary-btn--lg"
                                   value="<?= GetMessage("AUTH_REGISTER") ?>"/>
                            <div class="form-authorization_description">
                                Уже есть аккаунт?
                                <a href="/auth/">Войти</a>
                            </div>
            </form>
        <? endif //$arResult["SHOW_SMS_FIELD"] == true ?>
        <? endif ?>
    </div>
</section>