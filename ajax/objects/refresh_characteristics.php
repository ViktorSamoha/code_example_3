<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;

Loader::includeModule("highloadblock");
$hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_FEATURES)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();
$data = $entity_data_class::getList(array(
    "select" => array("*"),
    "order" => array("ID" => "ASC"),
));
$arResult = [];
while ($arData = $data->Fetch()) {
    $arResult[] = [
        'NAME' => $arData['UF_OF_NAME'],
        'VALUE' => $arData['UF_XML_ID'],
    ];
}
$counter = 0;
?>
<? foreach ($arResult as $i => $feature): ?>
    <div class="checkbox">
        <input type="checkbox" id="checkbox_<?= $feature['VALUE'] ?>" value="<?= $feature['VALUE'] ?>"
               name="LOCATION_FEATURES[<?= $counter ?>]" <?= $feature['CHECKED'] ? "checked" : "" ?>>
        <label for="checkbox_<?= $feature['VALUE'] ?>">
            <div class="checkbox_text"><?= $feature['NAME'] ?></div>
            <button class="btn-delete"
                    type="button"
                    data-item-id="<?= $feature['VALUE'] ?>"
            >
                <svg width="11" height="12" viewBox="0 0 11 12" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.351562 1.2627L10.5054 11.4165M10.6445 1.2627L0.49061 11.4165"
                          stroke="#F71E1E"/>
                </svg>
            </button>
        </label>
    </div>
    <?
    $counter++;
endforeach; ?>