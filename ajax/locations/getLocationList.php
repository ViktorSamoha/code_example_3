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

if ($parentSectionId) {
    if (Loader::includeModule("iblock")) {
        $parentSectionName = '';
        $res = CIBlockSection::GetByID($parentSectionId);
        if ($ar_res = $res->GetNext()) {
            $parentSectionName = $ar_res['NAME'];
        }
        $arParentSections = [];
        $arFilter = array('IBLOCK_ID' => IB_LOCATIONS, 'GLOBAL_ACTIVE' => 'Y', 'SECTION_ID' => $parentSectionId);
        $arSelect = ['ID', 'NAME', 'DEPTH_LEVEL'];
        $db_list = CIBlockSection::GetList(array($by => $order), $arFilter, true, $arSelect);
        while ($ar_result = $db_list->GetNext()) {
            $arParentSections[] = $ar_result;
        }
        unset($arFilter, $arSelect, $db_list, $ar_result);
        if (!empty($arParentSections)) {
            foreach ($arParentSections as &$parentSection) {
                $arFilter = array('IBLOCK_ID' => IB_LOCATIONS, 'GLOBAL_ACTIVE' => 'Y', 'SECTION_ID' => $parentSection['ID']);
                $arSelect = ['ID', 'NAME', 'DESCRIPTION', 'PICTURE'];
                $db_list = CIBlockSection::GetList(array($by => $order), $arFilter, true, $arSelect);
                while ($ar_result = $db_list->GetNext()) {
                    $picture = false;
                    if ($ar_result['PICTURE']) {
                        $picture = CFile::ResizeImageGet($ar_result['PICTURE'], array('width' => 450, 'height' => 310), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                    }
                    $parentSection['CHILDS'][] = [
                        'ID' => $ar_result['ID'],
                        'NAME' => $ar_result['NAME'],
                        'DESCRIPTION' => $ar_result['DESCRIPTION'],
                        'PICTURE' => $picture,
                    ];
                }
            }

            ?>
            <div class="title-w-btn">
                <h2 class="title"><?= $parentSectionName ?></h2>
                <button class="open-filter-btn">Фильтр</button>
            </div>
            <div class="tabs-block active-tabs">
                <div class="tabs">
                    <?
                    $firstElement = array_key_first($arParentSections);
                    foreach ($arParentSections as $key => $section) { ?>
                        <button class="tab <?= ($key == $firstElement) ? 'active' : '' ?>" type="button"
                                data-id="<?= $section['ID'] ?>"><?= $section['NAME'] ?></button>
                        <?
                    }
                    unset($key, $section);
                    ?>
                </div>
                <div class="tabs-content">
                    <? foreach ($arParentSections as $key => $section) { ?>
                        <div class="tabs-content_item <?= ($key == $firstElement) ? 'active' : '' ?>"
                             data-id="<?= $section['ID'] ?>">
                            <? if ($section['CHILDS']): ?>
                                <div class="catalog catalog--three">
                                    <? foreach ($section['CHILDS'] as $child) { ?>
                                        <div class="card">
                                            <div class="card_top">
                                                <? if ($child['PICTURE']): ?>
                                                    <img src="<?= $child['PICTURE']['src'] ?>" alt="" class="card_img">
                                                <? else: ?>
                                                    <img src="<?= ASSETS ?>images/card_01.jpeg" alt="" class="card_img">
                                                <? endif; ?>
                                                <div class="card_text">
                                                    <h3 class="card_title"><?= $child['NAME'] ?></h3>
                                                    <a href="javascript:void(0);" onclick="getInnerSectionElements(<?= $child['ID'] ?>)" class="secondary-btn">Выбрать</a>
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
            <?
        }
    }
}
