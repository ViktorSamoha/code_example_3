<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
$arResult['TOTAL_PRICE'] = 0;
if ($arResult['BASKET_DATA']) {
    foreach ($arResult['BASKET_DATA'] as &$basketItem) {
        if ($basketItem['PRICE']) {
            $arResult['TOTAL_PRICE'] += $basketItem['PRICE'];
        }
    }
}
