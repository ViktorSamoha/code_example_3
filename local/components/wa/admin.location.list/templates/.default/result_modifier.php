<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */

$arElements = [];
$arPartners = getPartnersList();
if ($arResult['SECTIONS']) {
    foreach ($arResult['SECTIONS'] as &$arSection) {
        if (isset($arSection['SELECTED_PARTNERS']) && !empty($arSection['SELECTED_PARTNERS'])) {
            if (!empty($arPartners)) {
                $arSection['PARTNERS'] = $arPartners;
                foreach ($arSection['PARTNERS'] as &$partner) {
                    foreach ($arSection['SELECTED_PARTNERS'] as $selectedPartner) {
                        if ($selectedPartner['ID'] == $partner['ID']) {
                            $partner['SELECTED'] = true;
                        }
                    }
                }
            }
        }else{
            $arSection['PARTNERS'] = $arPartners;
        }
        $arElements[] = $arSection;
    }
}

$rs_ObjectList = new CDBResult;
$rs_ObjectList->InitFromArray($arElements);
$rs_ObjectList->NavStart(10, false);
$arResult["NAV_STRING"] = $rs_ObjectList->GetPageNavString("", '');
$arResult["PAGE_START"] = $rs_ObjectList->SelectedRowsCount() - ($rs_ObjectList->NavPageNomer - 1) * $rs_ObjectList->NavPageSize;
while ($ar_Field = $rs_ObjectList->Fetch()) {

    $arResult['ELEMENTS'][] = $ar_Field;
}


