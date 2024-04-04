<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><?

ShowMessage($arParams["~AUTH_RESULT"]);

?>
<section class="authorization">
    <div class="authorization_text">
        <a href="/" class="authorization_logo">
            <img src="<?= ASSETS ?>images/lk-logo.svg" alt="">
        </a>
        <h1 class="authorization_title"><? echo GetMessage("sys_forgot_pass_label") ?></h1>
        <form name="bform" method="post" target="_top" action="<?= $arResult["AUTH_URL"] ?>"
              class="form-authorization form-authorization--recovery">
            <?
            if ($arResult["BACKURL"] <> '') {
                ?>
                <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
                <?
            }
            ?>
            <input type="hidden" name="AUTH_FORM" value="Y">
            <input type="hidden" name="TYPE" value="SEND_PWD">
            <div class="authorization-item">
                <label for="" class="authorization-item_label"><?= GetMessage("sys_forgot_pass_login1") ?></label>
                <input type="text" name="USER_LOGIN" value="<?= $arResult["USER_LOGIN"] ?>"
                       class="authorization-item_input"/>
                <input type="hidden" name="USER_EMAIL"/>
            </div>
            <? if ($arResult["PHONE_REGISTRATION"]): ?>

                <div style="margin-top: 16px">
                    <div><b><?= GetMessage("sys_forgot_pass_phone") ?></b></div>
                    <div><input type="text" name="USER_PHONE_NUMBER" value="<?= $arResult["USER_PHONE_NUMBER"] ?>"/>
                    </div>
                    <div><? echo GetMessage("sys_forgot_pass_note_phone") ?></div>
                </div>
            <? endif; ?>
            <? if ($arResult["USE_CAPTCHA"]): ?>
                <div style="margin-top: 16px">
                    <div>
                        <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
                        <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180"
                             height="40" alt="CAPTCHA"/>
                    </div>
                    <div><? echo GetMessage("system_auth_captcha") ?></div>
                    <div><input type="text" name="captcha_word" maxlength="50" value=""/></div>
                </div>
            <? endif ?>
            <div class="authorization_description">
                <? echo GetMessage("sys_forgot_pass_note_email") ?>
            </div>
            <input type="submit" name="send_account_info" class="primary-btn primary-btn--lg"
                   value="<?= GetMessage("AUTH_SEND") ?>"/>
            <div class="form-authorization_description">
                <a href="<?= $arResult["AUTH_AUTH_URL"] ?>"><?= GetMessage("AUTH_AUTH") ?></a>
            </div>
        </form>
    </div>
</section>
<script type="text/javascript">
    document.bform.onsubmit = function () {
        document.bform.USER_EMAIL.value = document.bform.USER_LOGIN.value;
    };
    document.bform.USER_LOGIN.focus();
</script>
