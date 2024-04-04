<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$APPLICATION->SetTitle($arResult['PARENT_SECTION_NAME']);

CJSCore::Init(array("fx"));//js не работает без этой строчки при анонимном пользователе

global $USER;
$user = getUserData();

if ($arParams['FILTER_ELEMENTS_ID']) {
    $filterElements = $arParams['FILTER_ELEMENTS_ID'];
    if (!empty($filterElements)) {
        ?>
        <script>
            var filterArr = <?php echo json_encode($filterElements); ?>;
        </script>
        <?
    }
}

?>
<div class="page-w-aside_content">
    <div class="page-w-aside_top page-w-aside_top--space-between">
        <div class="breadcrumbs">
            <a href="/" class="breadcrumbs_item">Главная</a>
            <a href="javascript:void(0);" class="breadcrumbs_item"><?= $arResult['PARENT_SECTION_NAME'] ?></a>
        </div>
        <div class="page-w-aside_top page-w-aside_top--block">
            <div class="user">
                <div class="user_text">
                    <? if ($USER->IsAuthorized()): ?>
                        <a href="/user/" class="user_name"><?= $user['LAST_NAME'] . ' ' . $user['NAME'] . ' ' . $user['SECOND_NAME'] ?></a>
                        <a href="/?logout=yes&<?= bitrix_sessid_get() ?>" class="user_logout">Выйти</a>
                    <?else:?>
                        <a href="/auth/" class="user_name">Войти</a>
                    <? endif ?>
                </div>
            </div>
            <button class="basket-btn" type="button" onclick="openBasketModal();">Корзина</button>
        </div>
    </div>
    <div class="catalog-list">
        <div class="catalog-block">
            <div class="title-w-btn">
                <h2 class="title"><?= $arResult['PARENT_SECTION_NAME'] ?></h2>
                <button class="open-filter-btn">Фильтр</button>
            </div>
            <div class="tabs-block active-tabs">
                <div class="tabs">
                    <?
                    $firstElement = array_key_first($arResult['PARENT_SECTIONS']);
                    foreach ($arResult['PARENT_SECTIONS'] as $key => $section) { ?>
                        <button class="tab <?= ($key == $firstElement) ? 'active' : '' ?>" type="button"
                                data-id="<?= $section['ID'] ?>"><?= $section['NAME'] ?></button>
                        <?
                    }
                    unset($key, $section);
                    ?>
                </div>
                <div class="tabs-content">
                    <? foreach ($arResult['PARENT_SECTIONS'] as $key => $section) { ?>
                        <div class="tabs-content_item <?= ($key == $firstElement) ? 'active' : '' ?>"
                             data-id="<?= $section['ID'] ?>">
                            <? if ($section['CHILDS']): ?>
                                <div class="catalog catalog--three">
                                    <? foreach ($section['CHILDS'] as $child) { ?>
                                        <div class="card">
                                            <div class="card_top">
                                                <? if ($child['PICTURE']): ?>
                                                    <img src="<?= $child['PICTURE']['src'] ?>" alt=""
                                                         class="card_img">
                                                <? else: ?>
                                                    <img src="<?= ASSETS ?>images/card_01.jpeg" alt=""
                                                         class="card_img">
                                                <? endif; ?>
                                                <div class="card_text">
                                                    <h3 class="card_title"><?= $child['NAME'] ?></h3>
                                                    <a href="javascript:void(0);"
                                                       onclick="getInnerSectionElements('<?= $arResult['PARENT_SECTION_CODE'] ?>', <?= $child['ID'] ?>,filterArr)"
                                                       class="secondary-btn">Выбрать</a>
                                                </div>
                                            </div>
                                            <div class="card_bottom"><?= $child['DESCRIPTION'] ?></div>
                                        </div>
                                        <?
                                    } ?>
                                </div>
                            <? endif; ?>
                        </div>
                        <?
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>