<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

CJSCore::Init(array("fx"));//js не работает без этой строчки при анонимном пользователе

global $USER;
$user = getUserData();
?>
<section class="m-page page page--green" id="content">
    <div class="page_content content-wrap">
        <div class="page-w-aside_top">
            <? if ($USER->IsAuthorized()): ?>
                <a href="/user/visiting_permit/" class="aside-top-btn aside-top-btn--main rounded-btn">Оформить
                    разрешение на посещение</a>
            <? else: ?>
                <a href="/permission/" class="aside-top-btn aside-top-btn--main rounded-btn">Оформить
                    разрешение на посещение</a>
            <? endif; ?>
            <div class="page-w-aside_top page-w-aside_top--block">
                <div class="user">
                    <div class="user_text">
                        <? if ($USER->IsAuthorized()): ?>
                            <a href="/user/"
                               class="user_name"><?= $user['LAST_NAME'] . ' ' . $user['NAME'] . ' ' . $user['SECOND_NAME'] ?></a>
                            <a href="/?logout=yes&<?= bitrix_sessid_get() ?>" class="user_logout">Выйти</a>
                        <? else: ?>
                            <a href="/auth/" class="user_name">Войти</a>
                        <? endif ?>
                    </div>
                </div>
                <button class="basket-btn" type="button" onclick="openBasketModal();">Корзина</button>
            </div>
        </div>
        <div class="catalog-list">
            <div class="catalog-block" id="ajax-data">
                <? if ($arResult['LOCATIONS']): ?>
                    <div class="catalog catalog--two">
                        <? foreach ($arResult['LOCATIONS'] as $arLocation): ?>
                            <div class="card">
                                <div class="card_top">
                                    <img src="<?= $arLocation['PICTURE']['src'] ?>" alt="" class="card_img">
                                    <div class="card_text">
                                        <h3 class="card_title"><?= $arLocation['NAME'] ?></h3>
                                        <span class="card_subtitle"><?= $arLocation['SUBTITLE'] ?></span>
                                        <a href="/catalog/<?= $arLocation['CODE'] ?>/"
                                           class="secondary-btn">Выбрать</a>
                                    </div>
                                </div>
                                <div class="card_bottom"><?= $arLocation['DESCRIPTION'] ?></div>
                            </div>
                        <? endforeach; ?>
                    </div>
                <? endif; ?>
            </div>
        </div>
    </div>
</section>
<!--<script>
    window.addEventListener("load", (event) => {
        let connector = new Bron(<? /*echo CUtil::PhpToJSObject($arResult['LOCATIONS'])*/ ?>);
        connector.init();
    });
</script>-->