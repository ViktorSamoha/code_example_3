<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$locationStructure = getLocationStructure();
?>
<? foreach ($locationStructure['LOCATION'] as $location): ?>
    <div class="custom-select_item"
         data-id="<?= $location['ID'] ?>">
        <?= $location['NAME'] ?>
        <button class="select-btn-delete"
                type="button"
                data-select-type="location"
                data-item-id="<?= $location['ID'] ?>"
        >
            <svg width="11" height="12" viewBox="0 0 11 12" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M0.351562 1.2627L10.5054 11.4165M10.6445 1.2627L0.49061 11.4165"
                      stroke="#F71E1E"/>
            </svg>
        </button>
    </div>
<? endforeach; ?>