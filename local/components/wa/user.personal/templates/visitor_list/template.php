<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if (isset($arResult['USER_DATA']['USER_GROUP']) && !empty($arResult['USER_DATA']['USER_GROUP'])): ?>
    <? foreach ($arResult['USER_DATA']['USER_GROUP'] as $visitor): ?>
        <li class="group_item">
            <div class="group-item_title"><?= $visitor['LAST_NAME'] . ' ' . $visitor['NAME'] . ' ' . $visitor['SECOND_NAME'] ?></div>
            <button class="group-item_btn"
                    onclick="deleteVisitor(<?= $visitor['ID'] ?>)" type="button">
                <svg width="14" height="15" viewBox="0 0 14 15" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M2.79167 14.0833C2.47222 14.0833 2.19444 13.9652 1.95833 13.7291C1.72222 13.493 1.60417 13.2083 1.60417 12.8749V1.54159H0.75V0.604085H4.3125V0.020752H9.6875V0.604085H13.25V1.54159H12.3958V12.8749C12.3958 13.2083 12.2778 13.493 12.0417 13.7291C11.8056 13.9652 11.5278 14.0833 11.2083 14.0833H2.79167ZM4.9375 11.3749H5.89583V3.29159H4.9375V11.3749ZM8.10417 11.3749H9.0625V3.29159H8.10417V11.3749Z"
                            fill="#EA9A00"/>
                </svg>
            </button>
        </li>
    <? endforeach; ?>
<? endif; ?>