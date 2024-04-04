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

if (!$arResult["NavShowAlways"]) {
    if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
        return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"] . "&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?" . $arResult["NavQueryString"] : "");
?>
<nav class="page-nav">
    <? if ($arResult["NavPageNomer"] > 1): ?>
        <? if ($arResult["bSavePage"]): ?>
            <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=1"
               class="page-nav_link"><?= GetMessage("nav_begin") ?></a>
            <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] - 1) ?>"
               class="page-nav_prev">
                <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M4.70512 9.35355L11.3516 2.70711L10.6445 2L3.2909 9.35355L10.6445 16.7071L11.3516 16L4.70512 9.35355Z"
                          fill="#ED8C00"/>
                </svg>
            </a>
        <? else: ?>
            <a href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>"
               class="page-nav_link"><?= GetMessage("nav_begin") ?></a>
            <? if ($arResult["NavPageNomer"] > 2): ?>
                <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] - 1) ?>"
                   class="page-nav_prev">
                    <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M4.70512 9.35355L11.3516 2.70711L10.6445 2L3.2909 9.35355L10.6445 16.7071L11.3516 16L4.70512 9.35355Z"
                              fill="#ED8C00"/>
                    </svg>
                </a>
            <? else: ?>
                <a href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>" class="page-nav_prev">
                    <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M4.70512 9.35355L11.3516 2.70711L10.6445 2L3.2909 9.35355L10.6445 16.7071L11.3516 16L4.70512 9.35355Z"
                              fill="#ED8C00"/>
                    </svg>
                </a>
            <? endif ?>
        <? endif ?>
    <? else: ?>
        <a href="#" class="page-nav_link">Начало</a>
        <a href="#" class="page-nav_prev">
            <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M4.70512 9.35355L11.3516 2.70711L10.6445 2L3.2909 9.35355L10.6445 16.7071L11.3516 16L4.70512 9.35355Z"
                      fill="#ED8C00"/>
            </svg>
        </a>
    <? endif ?>
    <? while ($arResult["nStartPage"] <= $arResult["nEndPage"]): ?>
        <? if ($arResult["nStartPage"] == $arResult["NavPageNomer"]): ?>
            <a href="#" class="page-nav_item active"><?= $arResult["nStartPage"] ?></a>
        <? elseif ($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false): ?>
            <a href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>"
               class="page-nav_item "><?= $arResult["nStartPage"] ?></a>
        <? else: ?>
            <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["nStartPage"] ?>"
               class="page-nav_item">
                <?= $arResult["nStartPage"] ?>
            </a>
        <? endif ?>
        <? $arResult["nStartPage"]++ ?>
    <? endwhile ?>
    <? if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]): ?>
        <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] + 1) ?>"
           class="page-nav_next">
            <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M13.2949 9.35355L6.64844 2.70711L7.35554 2L14.7091 9.35355L7.35554 16.7071L6.64844 16L13.2949 9.35355Z"
                      fill="#ED8C00"/>
            </svg>
        </a>
        <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["NavPageCount"] ?>"
           class="page-nav_link">
            <?= GetMessage("nav_end") ?>
        </a>
    <? else: ?>
        <a href="#" class="page-nav_next">
            <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M13.2949 9.35355L6.64844 2.70711L7.35554 2L14.7091 9.35355L7.35554 16.7071L6.64844 16L13.2949 9.35355Z"
                      fill="#ED8C00"/>
            </svg>
        </a>
        <a href="#" class="page-nav_link"><?= GetMessage("nav_end") ?></a>
    <? endif ?>
<!--    <?/* if ($arResult["bShowAll"]): */?>
        <noindex>
            <?/* if ($arResult["NavShowAll"]): */?>
               <a
                        href="<?/*= $arResult["sUrlPath"] */?>?<?/*= $strNavQueryString */?>SHOWALL_<?/*= $arResult["NavNum"] */?>=0"
                        rel="nofollow"><?/*= GetMessage("nav_paged") */?></a>
            <?/* else: */?>
               <a
                        href="<?/*= $arResult["sUrlPath"] */?>?<?/*= $strNavQueryString */?>SHOWALL_<?/*= $arResult["NavNum"] */?>=1"
                        rel="nofollow"><?/*= GetMessage("nav_all") */?></a>
            <?/* endif */?>
        </noindex>
    --><?/* endif */?>
</nav>