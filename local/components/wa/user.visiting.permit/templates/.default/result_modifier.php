<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
$price = VISIT_PERMISSION_COST;
if ($arResult['USER_GROUP']) {
    $price = VISIT_PERMISSION_COST;
    foreach ($arResult['USER_GROUP'] as &$arVisitor) {
        if ($arVisitor['PREFERENTIAL_CATEGORY']) {
            foreach ($arResult['PREF_CATEGORIES'] as $prefCategory) {
                if ($arVisitor['PREFERENTIAL_CATEGORY'] == $prefCategory['VALUE']) {
                    $arVisitor['PREFERENTIAL_CATEGORY'] = $prefCategory;
                }
                if ($prefCategory['VALUE'] == "Дети до 18 лет") {
                    $arResult['PREFERENTIAL_CATEGORY_SELECT_ID'] = $prefCategory['ID'];
                }
            }
        }
        if ($arVisitor['LOCATION']) {
            foreach ($arResult['USER_LOCATIONS'] as $location) {
                if ($arVisitor['LOCATION'] == $location['VALUE']) {
                    $arVisitor['LOCATION'] = $location;
                }
            }
        }
    }
    unset($arVisitor, $prefCategory, $location);
}
$arResult['PRICE'] = $price;