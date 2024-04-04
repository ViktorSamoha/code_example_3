<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$partners = getPartnersList();
?>
<? foreach ($partners as $partner): ?>
    <div class="custom-select_item">
        <div class="checkbox checkbox-w-btn">
            <input type="checkbox"
                   id="PARTNERS_<?= $partner['ID'] ?>"
                   value="<?= $partner['NAME'] ?>"
                   name="PARTNERS"
            >
            <label for="PARTNERS_<?= $partner['ID'] ?>">
                <div class="checkbox_text"><?= $partner['NAME'] ?></div>
            </label>
        </div>
    </div>
<? endforeach; ?>