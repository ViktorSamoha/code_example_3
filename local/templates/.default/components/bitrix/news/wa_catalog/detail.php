<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<section class="page page--gray">
    <div class="page_content">
        <div class="page-top">
            <a href="/" class="page-top_logo-mobile">
                <img src="<?= ASSETS ?>images/f-logo.svg" alt="">
            </a>
            <div class="page-top_wrap content-wrap">
                <div class="page-top_left">
                    <a href="/" class="page-top_logo">
                        <img src="<?= ASSETS ?>images/logo-small.svg" alt="">
                    </a>
                    <? if ($USER->IsAuthorized()): ?>
                        <a href="/user/visiting_permit/" class="aside-top-btn aside-top-btn--main rounded-btn">Оформить
                            разрешение на посещение</a>
                    <? else: ?>
                        <a href="/permission/" class="aside-top-btn aside-top-btn--main rounded-btn">Оформить
                            разрешение на посещение</a>
                    <? endif; ?>
                </div>
                <button class="basket-btn" type="button" onclick="openBasketModal();">Корзина</button>
            </div>
        </div>
        <div class="content-wrap">
            <div class="d-location">
                <? $APPLICATION->IncludeComponent(
                    "bitrix:breadcrumb",
                    "wa_breadcrumbs",
                    array(
                        "PATH" => "",
                        "SITE_ID" => "s1",
                        "START_FROM" => "0"
                    )
                ); ?>
                <div class="d-location-page">
                    <? $ElementID = $APPLICATION->IncludeComponent(
                        "bitrix:news.detail",
                        "",
                        array(
                            "DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
                            "DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
                            "DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
                            "DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
                            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
                            "PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
                            "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["detail"],
                            "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                            "META_KEYWORDS" => $arParams["META_KEYWORDS"],
                            "META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
                            "BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
                            "SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
                            "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
                            "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
                            "SET_TITLE" => $arParams["SET_TITLE"],
                            "MESSAGE_404" => $arParams["MESSAGE_404"],
                            "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                            "SHOW_404" => $arParams["SHOW_404"],
                            "FILE_404" => $arParams["FILE_404"],
                            "INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
                            "ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
                            "ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
                            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                            "CACHE_TIME" => $arParams["CACHE_TIME"],
                            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                            "USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
                            "GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
                            "DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
                            "DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
                            "PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
                            "PAGER_SHOW_ALWAYS" => "N",
                            "PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
                            "PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
                            "CHECK_DATES" => $arParams["CHECK_DATES"],
                            "ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
                            "ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
                            "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                            "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                            "IBLOCK_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["news"],
                            "USE_SHARE" => $arParams["USE_SHARE"],
                            "SHARE_HIDE" => $arParams["SHARE_HIDE"],
                            "SHARE_TEMPLATE" => $arParams["SHARE_TEMPLATE"],
                            "SHARE_HANDLERS" => $arParams["SHARE_HANDLERS"],
                            "SHARE_SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
                            "SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
                            "ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
                            'STRICT_SECTION_CHECK' => (isset($arParams['STRICT_SECTION_CHECK']) ? $arParams['STRICT_SECTION_CHECK'] : ''),
                        ),
                        $component
                    ); ?>
                    <div class="d-location-page_aside">
                        <?
                        $APPLICATION->IncludeComponent(
                            "wa:calendar",
                            ".default",
                            [
                                'ELEMENT_ID' => $ElementID,
                            ],
                            false
                        );
                        ?>
                        <? $APPLICATION->ShowViewContent('aside_price'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>