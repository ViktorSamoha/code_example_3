<? require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$values = $request->getPostList();

$parentSectionId = $values['id'];
$parentSectionCode = $values['parentSectionCode'];
$filterElementsId = $values['filterElementsId'] ? $values['filterElementsId'] : null;
if ($parentSectionId) {
    if (Loader::includeModule("iblock")) {
        $parentSectionName = '';
        $res = CIBlockSection::GetByID($parentSectionId);
        if ($ar_res = $res->GetNext()) {
            $parentSectionName = $ar_res['NAME'];
        }
        $arLocations = [];
        $arSelect = [
            "ID",
            "IBLOCK_ID",
            "NAME",
            'PREVIEW_PICTURE',
            'PROPERTY_PRICE',
            'PROPERTY_NORTHERN_LATITUDE',
            'PROPERTY_EASTERN_LONGITUDE',
            'PROPERTY_ICON',
            'CODE'
        ];
        $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "ACTIVE" => "Y", 'SECTION_ID' => $parentSectionId);
        if (isset($filterElementsId) && !empty($filterElementsId)) {
            $arFilter['ID'] = $filterElementsId;
        }
        $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $picture = false;
            if ($arFields['PREVIEW_PICTURE']) {
                $picture = CFile::ResizeImageGet($arFields['PREVIEW_PICTURE'], array('width' => 194, 'height' => 151), BX_RESIZE_IMAGE_PROPORTIONAL, true);
            }
            $locationData =
                [
                    'ID' => $arFields['ID'],
                    'NAME' => $arFields['NAME'],
                    'PICTURE' => $picture,
                    'PRICE' => $arFields['PROPERTY_PRICE_VALUE'],
                    'NORTHERN_LATITUDE' => $arFields['PROPERTY_NORTHERN_LATITUDE_VALUE'],
                    'EASTERN_LONGITUDE' => $arFields['PROPERTY_EASTERN_LONGITUDE_VALUE'],
                    'ICON' => CFile::GetPath($arFields['PROPERTY_ICON_VALUE']),
                ];
            if ($parentSectionCode) {
                $locationData['LINK'] = '/catalog/' . $parentSectionCode . '/' . $arFields['CODE'] . '/';
            }
            $arLocations[] = $locationData;
        }
        unset($res, $ob);
        if (!empty($arLocations)) {
            foreach ($arLocations as &$location) {
                $res = CIBlockElement::GetProperty(IB_LOCATIONS, $location['ID'], array("sort" => "asc"), array("CODE" => "LOCATION_FEATURES"));
                while ($ob = $res->GetNext()) {
                    $prop = $ob['VALUE'];
                    $location['FEATURES'][] = $prop;
                }
                unset($res, $ob, $prop);
                $res = CIBlockElement::GetProperty(IB_LOCATIONS, $location['ID'], array("sort" => "asc"), array("CODE" => "PRICE_TYPE"));
                while ($ob = $res->GetNext()) {
                    $prop = $ob['VALUE_ENUM'];
                    $location['PRICE_TYPE'] = $prop;
                }
                unset($res, $ob, $prop);
                $res = CIBlockElement::GetProperty(IB_LOCATIONS, $location['ID'], array("sort" => "asc"), array("CODE" => "OBJECT_TYPE"));
                while ($ob = $res->GetNext()) {
                    $prop = $ob['VALUE'];
                    $location['OBJECT_TYPE'] = $prop;
                }
            }
            unset($location);
            if (Loader::includeModule("highloadblock")) {
                $hlbl = HL_OBJECT_FEATURES;
                $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();
                foreach ($arLocations as &$location) {
                    if ($location['FEATURES']) {
                        foreach ($location['FEATURES'] as &$xmlId) {
                            $rsData = $entity_data_class::getList(array(
                                "select" => array("UF_OF_NAME"),
                                "order" => array("ID" => "ASC"),
                                "filter" => array("UF_XML_ID" => $xmlId)
                            ));
                            while ($arData = $rsData->Fetch()) {
                                $xmlId = $arData["UF_OF_NAME"];
                            }
                        }
                    }
                }
                unset($location, $hlbl, $hlblock, $entity, $entity_data_class, $rsData);
                $hlbl = HL_OBJECT_TYPE;
                $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();
                foreach ($arLocations as &$location) {
                    if ($location['OBJECT_TYPE']) {
                        $rsData = $entity_data_class::getList(array(
                            "select" => array("UF_NAME"),
                            "order" => array("ID" => "ASC"),
                            "filter" => array("UF_XML_ID" => $location['OBJECT_TYPE'])
                        ));
                        while ($arData = $rsData->Fetch()) {
                            $location['OBJECT_TYPE'] = $arData["UF_NAME"];
                        }
                    }
                }
                unset($location, $hlbl, $hlblock, $entity, $entity_data_class, $rsData);
            }
            ?>
            <h2 class="modal_title"><?= $parentSectionName ?></h2>
            <div class="modal-text">
                <p>Настоящий порядок определяет категории физических лиц, посещающих территорию национального парка (за
                    исключением заповедной зоны и участков, расположенных в границах населенных пунктов и дорог общего
                    пользования),
                    бесплатно:</p>
            </div>
            <div class="location-group">
                <? foreach ($arLocations as $location): ?>
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
                            <? if ($USER->IsAuthorized()): ?>
                                <button type="button" class="primary-btn" onclick="callBookingModal(<?= $location['ID'] ?>)">
                                    Выбрать
                                </button>
                            <? else: ?>
                                <button type="button" class="primary-btn js-open-modal"
                                        data-name="modal-auth">
                                    Выбрать
                                </button>
                            <? endif; ?>
                            <a href="<?= $location['LINK'] ?>" class="location_link">Подробнее</a>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
            <?

        }
    }
}