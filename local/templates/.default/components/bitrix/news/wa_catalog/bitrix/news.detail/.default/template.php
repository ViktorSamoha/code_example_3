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
    <div class="d-location-page_content">
        <h1 class="lk-head_title"><?= $arResult['NAME']; ?></h1>
        <div class="location-labels">
            <div class="location-label">
                <div class="location-label_icon">
                    <? if ($arResult['DISPLAY_PROPERTIES']['ICON']): ?>
                        <img src="<?= $arResult['DISPLAY_PROPERTIES']['ICON']["FILE_VALUE"]["SRC"] ?>" alt="">
                    <? else: ?>
                        <img src="/local/templates/.default/assets/img/default_map_icon.svg" alt="">
                    <? endif; ?>
                </div>
                <span class="location-label_title"><?= $arResult['OBJECT_DATA']['OBJECT_TYPE'] ?></span>
            </div>
            <div class="location-label">
                <div class="location-label_icon">
                    <img src="<?= ASSETS ?>images/category_05.svg" alt="">
                </div>
                <span class="location-label_title"><?= $arResult['OBJECT_DATA']['COORDS'] ?></span>
            </div>
        </div>
        <? if ($arResult['OBJECT_DATA']['GALLERY']): ?>
            <div class="photo-grid">
                <?
                $lastKey = array_key_last($arResult['OBJECT_DATA']['GALLERY']);
                $galleryPictureCount = count($arResult['OBJECT_DATA']['GALLERY']);
                foreach ($arResult['OBJECT_DATA']['GALLERY'] as $key => $picture):?>
                    <? if ($galleryPictureCount > 1): ?>
                        <? if ($key != $lastKey): ?>
                            <a href="<?= $picture['src'] ?>" class="photo-grid_item" data-fancybox="gallery">
                                <img src="<?= $picture['src'] ?>" alt="">
                            </a>
                        <? else: ?>
                            <? if ($key < 5): ?>
                                <a href="<?= $picture['src'] ?>" class="photo-grid_item" data-fancybox="gallery">
                                    <img src="<?= $picture['src'] ?>" alt="">
                                    <div class="photo-grid_text">
                                        <div class="photo-grid_text_wrap">
                                            <span><?= $galleryPictureCount ?></span> фото
                                        </div>
                                    </div>
                                </a>
                            <? else: ?>
                                <a href="<?= $picture['src'] ?>" class="photo-grid_item" data-fancybox="gallery"
                                   style="display: none">
                                    <img src="<?= $picture['src'] ?>" alt="">
                                </a>
                            <? endif; ?>
                        <? endif; ?>
                    <? else: ?>
                        <a href="<?= $picture['src'] ?>" class="photo-grid_item" data-fancybox="gallery">
                            <img src="<?= $picture['src'] ?>" alt="">
                        </a>
                    <? endif; ?>
                <? endforeach; ?>
            </div>
        <? endif; ?>
        <div class="d-location_block">
            <h2 class="d-location_title">Описание</h2>
            <div class="d-location_description">
                <?= $arResult['DETAIL_TEXT']; ?>
            </div>
        </div>
        <? if ($arResult['OBJECT_DATA']['LOCATION_FEATURES']): ?>
            <div class="d-location_block">
                <h2 class="d-location_title">Удобства и услуги</h2>
                <div class="icons-group">
                    <? foreach ($arResult['OBJECT_DATA']['LOCATION_FEATURES'] as $feature): ?>
                        <div class="icon-item">
                            <div class="icon-item_img">
                                <img src="<?= $feature['ICON'] ?>" alt="">
                            </div>
                            <span class="icon-item_title"><?= $feature['NAME'] ?></span>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
        <? endif; ?>
        <div class="d-location_block">
            <h2 class="d-location_title">Объект на карте</h2>
            <div class="d-location_map" id="map"></div>
        </div>
    </div>
<? if ($arResult['MAP_JSON']): ?>
    <script>
        let object_page = new objectPage(<?= CUtil::PhpToJSObject($arResult['MAP_JSON'])?>);
        object_page.init();
    </script>
<? endif; ?>