<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */

$arElements = [];

if($arResult['VISITORS_LIST']){
    foreach ($arResult['VISITORS_LIST'] as $arUser){
        $arElements[] = $arUser;
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
