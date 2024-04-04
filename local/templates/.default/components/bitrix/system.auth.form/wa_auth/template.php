<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CJSCore::Init();
?>
<section class="authorization">
    <div class="authorization_text">
        <a href="/" class="authorization_logo">
            <img src="<?=ASSETS ?>images/lk-logo.svg" alt="lk-logo">
        </a>
        <h1 class="authorization_title">Войти в личный кабинет</h1>
        <?
        if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
            ShowMessage($arResult['ERROR_MESSAGE']);
        ?>
        <? if ($arResult["FORM_TYPE"] == "login"): ?>
            <form class="form-authorization" name="system_auth_form<?= $arResult["RND"] ?>" method="post" target="_top"
                  action="<?= $arResult["AUTH_URL"] ?>">
                <? if ($arResult["BACKURL"] <> ''): ?>
                    <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
                <? endif ?>
                <? foreach ($arResult["POST"] as $key => $value): ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/>
                <? endforeach ?>
                <input type="hidden" name="AUTH_FORM" value="Y"/>
                <input type="hidden" name="TYPE" value="AUTH"/>

                <input type="text" name="USER_LOGIN" placeholder="Логин">
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
                <input type="password" name="USER_PASSWORD" placeholder="Пароль">
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
                    <img src="/bitrix/tools/captcha.php?captcha_sid=<? echo $arResult["CAPTCHA_CODE"] ?>" width="180"
                         height="40" alt="CAPTCHA"/><br/><br/>
                    <input type="text" name="captcha_word" maxlength="50" value=""/>
                <? endif ?>
                <input class="primary-btn primary-btn--lg" type="submit" name="Login" value="Войти"/>
            </form>
        <? endif ?>
    </div>
</section>
