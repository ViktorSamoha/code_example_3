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

use Bitrix\Main\Loader;

$this->setFrameMode(true);
?>
<?
if ($arResult['VARIABLES']['SECTION_CODE']) {
    Loader::includeModule("iblock");
    $sectionId = null;
    $arFilter = array('IBLOCK_ID' => IB_LOCATIONS, 'GLOBAL_ACTIVE' => 'Y', 'CODE' => $arResult['VARIABLES']['SECTION_CODE']);
    $db_list = CIBlockSection::GetList(array($by => $order), $arFilter, true, ['ID']);
    while ($ar_result = $db_list->GetNext()) {
        $sectionId = $ar_result['ID'];
    }
    if ($sectionId) {
        ?>
        <section class="main-screen">
            <video loop autoplay muted -webkit-playsinline playsinline class="main-screen_video">
                <source src="<?= DEFAULT_TEMPLATE ?>video/background.mp4" type="video/mp4"/>
            </video>
            <div class="main-screen_wrap content-wrap">
                <a href="/" class="logo">
                    <img src="<?= ASSETS ?>images/logo.svg" alt="">
                </a>
                <h1 class="main-screen_title">Добро пожаловать!</h1>
                <span class="main-screen_subtitle">Сервис бронирования туристических услуг на территории </span>
                <a href="#content" class="ms-down-btn">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.9974 30.4583L11.1641 21.5833L12.0807 20.6667L19.9974 28.6667L27.9141
                    20.6667L28.8307 21.5833L19.9974 30.4583ZM19.9974 20.25L11.1641 11.4167L12.0807 10.5L19.9974
                    18.4583L27.9141 10.5L28.8307 11.4167L19.9974 20.25Z" fill="#F1B533"/>
                    </svg>
                </a>
            </div>
        </section>
        <section class="page-w-aside" id="content">

            <?
            $APPLICATION->IncludeComponent(
                "wa:filter",
                "",
                [
                    'PARENT_SECTION_ID' => $sectionId,
                ],
                false
            );
            ?>

            <?


            $APPLICATION->IncludeComponent(
                "wa:bron",
                "elements_list",
                [
                    "IBLOCK_TYPE" => "facility",
                    "IBLOCK_ID" => IB_OBJECT,
                    "FIELD_CODE" => [
                        0 => "NAME",
                        1 => "PREVIEW_PICTURE",
                        2 => "DETAIL_PAGE_URL",
                    ],
                    "PROPERTY_CODE" => [
                        0 => "PROPERTY_LOCATION",
                        1 => "PROPERTY_CAPACITY_MAXIMUM",
                    ],
                    "SECTION_PROPERTY_CODE" => [
                        0 => "UF_CB_SVG_ICON",
                        1 => "UF_CB_ICON_CLASS_COLOR",
                    ],
                    "LOCATIONS_IBLOCK_TYPE" => "location",
                    "LOCATIONS_IBLOCK_ID" => IB_LOCATIONS,
                    'PARENT_SECTION_ID' => $sectionId,
                    'GET_LOCATION_LIST' => 'Y',
                    'FILTER_ELEMENTS_ID' => explode(',', $_REQUEST['ELEMENTS_ID']),
                    'FILTER_SECTIONS_ID' => explode(',', $_REQUEST['SECTIONS_ID']),
                ],
                false
            );


            ?>
        </section>
    <? } else {
        LocalRedirect("/index.php");
    }
} else {
    LocalRedirect("/index.php");
}
?>

