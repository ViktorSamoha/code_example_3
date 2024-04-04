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
<div id="ajaxResult">
    <div class="table-wrap">
        <? if (!empty($arResult['ITEMS'])): ?>
            <table class="table">
                <tr>
                    <th>Название локации</th>
                    <th>Активность</th>
                    <th>Партнеры</th>
                    <th></th>
                </tr>
                <?
                $lastItemKey = array_key_last($arResult['ITEMS']);
                ?>
                <? foreach ($arResult['ITEMS'] as $k => $location): ?>
                    <?
                    $isLastElement = false;
                    if ($k == $lastItemKey) {
                        $isLastElement = true;
                    }
                    ?>
                    <tr>
                        <td><?= $location['NAME'] ?></td>
                        <? if ($location['ACTIVE'] == 'Y'): ?>
                            <td>Активная</td>
                        <? else: ?>
                            <td>Неактивная</td>
                        <? endif; ?>
                        <td>
                            <div class="custom-select custom-select--sm <?= $isLastElement ? 'custom-select--open-up' : '' ?>">
                                <div class="custom-select_head">
                                    <span class="custom-select_title"><?= $location['FIRST_SELECTED_PARTNER'] ? $location['FIRST_SELECTED_PARTNER'] : 'Партнер' ?></span>
                                    <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                                    </svg>
                                </div>
                                <div class="custom-select_body"
                                     data-select-name="LOC_<?= $location['ID'] ?>_PARTNER"
                                     id="partner-select-<?= $location['ID'] ?>">
                                    <? foreach ($location['PARTNERS'] as $partner): ?>
                                        <div class="custom-select_item">
                                            <div class="checkbox checkbox-w-btn">
                                                <input type="checkbox"
                                                       id="partner_cb_<?= $location['ID'] ?>_<?= $partner['ID'] ?>"
                                                       value="<?= $partner['ID'] ?>"
                                                       name="<?= $partner['NAME'] ?>"
                                                    <?= (isset($partner['SELECTED']) && $partner['SELECTED']) ? 'checked="checked"' : '' ?>
                                                       data-location-id="<?= $location['ID'] ?>"
                                                       onclick="setLocationPartner(this);"
                                                >
                                                <label for="partner_cb_<?= $location['ID'] ?>_<?= $partner['ID'] ?>">
                                                    <div class="checkbox_text"><?= $partner['NAME'] ?></div>
                                                </label>
                                            </div>
                                        </div>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <? if ($location['ACTIVE'] == 'Y'): ?>
                                <a href="#" class="lk-loc_btn" data-action="deactivate"
                                   data-id="<?= $location['ID'] ?>">
                                    <span>Деактивировать</span>
                                </a>
                            <? else: ?>
                                <a href="#" class="lk-loc_btn" data-action="activate" data-id="<?= $location['ID'] ?>">
                                    <span>Активировать</span>
                                </a>
                            <? endif; ?>
                        </td>
                    </tr>
                <? endforeach; ?>
            </table>
        <? else: ?>
            <p>Список пуст</p>
        <? endif; ?>
    </div>
</div>
