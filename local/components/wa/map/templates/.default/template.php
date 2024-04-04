<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

    <div class="location-group">
        <? if ($arResult['LOCATIONS']): ?>
            <? foreach ($arResult['LOCATIONS'] as $location): ?>
                <div class="location">
                    <div class="location_img">
                        <? if ($location['PICTURE']): ?>
                            <img src="<?= $location['PICTURE']['src'] ?>" alt="">
                        <? else: ?>
                            <img src="<?= ASSETS ?>images/card_01.jpeg" alt="">
                        <? endif; ?>
                    </div>
                    <div class="location_text">
                        <h3 class="location_title"><?= $location['NAME'] ?></h3>
                        <div class="location-labels">
                            <div class="location-label">
                                <div class="location-label_icon">
                                    <? if ($location['ICON']): ?>
                                        <img src="<?= $location['ICON'] ?>" alt="">
                                    <? else: ?>
                                        <img src="/local/templates/.default/assets/img/default_map_icon.svg" alt="">
                                    <? endif; ?>
                                </div>
                                <span class="location-label_title"><?= $location['OBJECT_TYPE'] ?></span>
                            </div>
                            <div class="location-label">
                                <div class="location-label_icon">
                                    <img src="<?= ASSETS ?>images/category_05.svg" alt="">
                                </div>
                                <span class="location-label_title"><?= $location['NORTHERN_LATITUDE'] . ', ' . $location['EASTERN_LONGITUDE'] ?></span>
                            </div>
                        </div>
                        <div class="location_info">
                            <? if ($location['FEATURES']): ?>
                                <?
                                $lastKey = array_key_last($location['FEATURES']);
                                foreach ($location['FEATURES'] as $key => $feature):?>
                                    <? if ($key != $lastKey): ?>
                                        <?= $feature . ' • ' ?>
                                    <? else: ?>
                                        <?= $feature ?>
                                    <? endif; ?>
                                <? endforeach; ?>
                            <? endif; ?>
                        </div>
                    </div>
                    <div class="location_price">
                        <div class="l-price">
                            <span><?= $location['PRICE'] ?> ₽ /</span> <?= $location['PRICE_TYPE'] ?>
                        </div>
                        <button class="primary-btn" onclick="callBookingModal(<?= $location['ID'] ?>)">Выбрать</button>
                        <a href="<?= $location['LINK'] ?>" class="location_link" target="_blank">Подробнее</a>
                    </div>
                </div>
            <? endforeach; ?>
        <? else: ?>
            <p>Нет объектов соответствующих критериям поиска!</p>
        <? endif; ?>
    </div>

    </div><? /*i-location_aside - end*/ ?>

    <div class="i-location_content">
        <div class="content-item active" data-name="map">
            <div class="map" id="map"></div>
        </div>
    </div>


<? if ($arParams['RESTART_MAP']): ?>
    <? if ($arResult['MAP_JSON']): ?>
        <script>
            map.reinit(<?echo CUtil::PhpToJSObject($arResult['MAP_JSON'])?>);
        </script>
    <? else: ?>
        <script>
            map.empty();
        </script>
    <? endif; ?>
<? else: ?>
    <? if ($arResult['MAP_JSON']): ?>
        <script>
            //window.addEventListener("load", (event) => {
            let map = new yandexMap(<?echo CUtil::PhpToJSObject($arResult['MAP_JSON'])?>);
            map.init();
            //});
        </script>
    <? endif; ?>
<? endif; ?>