<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CJSCore::Init();
?>
<section class="authorization">
    <div class="bx-system-auth-form authorization_text">
        <a href="/" class="authorization_logo">
            <img src="<?= ASSETS ?>images/lk-logo.svg" alt="logo_image">
        </a>
        <h1 class="authorization_title">Войти в личный кабинет</h1>
        <?
        if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
            ShowMessage($arResult['ERROR_MESSAGE']);
        ?>
        <? if ($arResult["FORM_TYPE"] == "login"): ?>
            <form name="system_auth_form<?= $arResult["RND"] ?>" method="post" target="_top"
                  action="<?= $arResult["AUTH_URL"] ?>" class="form-authorization">
                <? if ($arResult["BACKURL"] <> ''): ?>
                    <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
                <? endif ?>
                <? foreach ($arResult["POST"] as $key => $value): ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/>
                <? endforeach ?>
                <input type="hidden" name="AUTH_FORM" value="Y"/>
                <input type="hidden" name="TYPE" value="AUTH"/>
                <div class="authorization-item">
                    <label for="" class="authorization-item_label"><?= GetMessage("AUTH_LOGIN") ?></label>
                    <input type="text" class="authorization-item_input" name="USER_LOGIN" maxlength="50" value=""
                           size="17"/>
                    <script>
                        BX.ready(function () {
                            var loginCookie = BX.getCookie("<?=CUtil::JSEscape($arResult["~LOGIN_COOKIE_NAME"])?>");
                            if (loginCookie) {
                                var form = document.forms["system_auth_form<?=$arResult["RND"]?>"];
                                var loginInput = form.elements["USER_LOGIN"];
                                loginInput.value = loginCookie;
                            }
                        });
                    </script>
                </div>
                <div class="authorization-item">
                    <label for="" class="authorization-item_label"><?= GetMessage("AUTH_PASSWORD") ?></label>
                    <input type="password" class="authorization-item_input" name="USER_PASSWORD" maxlength="255"
                           size="17" autocomplete="off"/>
                    <? if ($arResult["SECURE_AUTH"]): ?>
                        <span class="bx-auth-secure" id="bx_auth_secure<?= $arResult["RND"] ?>"
                              title="<? echo GetMessage("AUTH_SECURE_NOTE") ?>" style="display:none">
					        <div class="bx-auth-secure-icon"></div>
				        </span>
                        <noscript>
				            <span class="bx-auth-secure" title="<? echo GetMessage("AUTH_NONSECURE_NOTE") ?>">
					            <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
				            </span>
                        </noscript>
                        <script type="text/javascript">
                            document.getElementById('bx_auth_secure<?=$arResult["RND"]?>').style.display = 'inline-block';
                        </script>
                    <? endif ?>
                    <? if ($arResult["CAPTCHA_CODE"]): ?>
                        <? echo GetMessage("AUTH_CAPTCHA_PROMT") ?>:<br/>
                        <input type="hidden" name="captcha_sid" value="<? echo $arResult["CAPTCHA_CODE"] ?>"/>
                        <img src="/bitrix/tools/captcha.php?captcha_sid=<? echo $arResult["CAPTCHA_CODE"] ?>"
                             width="180" height="40" alt="CAPTCHA"/><br/><br/>
                        <input type="text" name="captcha_word" maxlength="50" value=""/>
                    <? endif ?>
                    <a href="/auth/forget.php" class="authorization-item_link">Забыли пароль?</a>
                </div>
                <input class="primary-btn primary-btn--lg" type="submit" name="Login"
                       value="<?= GetMessage("AUTH_LOGIN_BUTTON") ?>"/>
                <? if ($arResult["NEW_USER_REGISTRATION"] == "Y"): ?>
                    <div class="form-authorization_description">
                        Ещё не зарегистрированы?
                        <noindex>
                            <a href="<?= $arResult["AUTH_REGISTER_URL"] ?>"><?= GetMessage("AUTH_REGISTER") ?></a>
                        </noindex>
                    </div>
                <? endif ?>
            </form>
        <?
        elseif ($arResult["FORM_TYPE"] == "otp"):
            ?>
            <form name="system_auth_form<?= $arResult["RND"] ?>" method="post" target="_top"
                  action="<?= $arResult["AUTH_URL"] ?>">
                <? if ($arResult["BACKURL"] <> ''): ?>
                    <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
                <? endif ?>
                <input type="hidden" name="AUTH_FORM" value="Y"/>
                <input type="hidden" name="TYPE" value="OTP"/>
                <table width="95%">
                    <tr>
                        <td colspan="2">
                            <? echo GetMessage("auth_form_comp_otp") ?><br/>
                            <input type="text" name="USER_OTP" maxlength="50" value="" size="17" autocomplete="off"/>
                        </td>
                    </tr>
                    <? if ($arResult["CAPTCHA_CODE"]): ?>
                        <tr>
                            <td colspan="2">
                                <? echo GetMessage("AUTH_CAPTCHA_PROMT") ?>:<br/>
                                <input type="hidden" name="captcha_sid" value="<? echo $arResult["CAPTCHA_CODE"] ?>"/>
                                <img src="/bitrix/tools/captcha.php?captcha_sid=<? echo $arResult["CAPTCHA_CODE"] ?>"
                                     width="180" height="40" alt="CAPTCHA"/><br/><br/>
                                <input type="text" name="captcha_word" maxlength="50" value=""/></td>
                        </tr>
                    <? endif ?>
                    <? if ($arResult["REMEMBER_OTP"] == "Y"): ?>
                        <tr>
                            <td valign="top"><input type="checkbox" id="OTP_REMEMBER_frm" name="OTP_REMEMBER"
                                                    value="Y"/>
                            </td>
                            <td width="100%"><label for="OTP_REMEMBER_frm"
                                                    title="<? echo GetMessage("auth_form_comp_otp_remember_title") ?>"><? echo GetMessage("auth_form_comp_otp_remember") ?></label>
                            </td>
                        </tr>
                    <? endif ?>
                    <tr>
                        <td colspan="2"><input type="submit" name="Login"
                                               value="<?= GetMessage("AUTH_LOGIN_BUTTON") ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <noindex><a href="<?= $arResult["AUTH_LOGIN_URL"] ?>"
                                        rel="nofollow"><? echo GetMessage("auth_form_comp_auth") ?></a></noindex>
                            <br/></td>
                    </tr>
                </table>
            </form>
        <?
        else:
            ?>
            <? LocalRedirect("/user/"); ?>
        <? endif ?>
    </div>
</section>