<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */

$arElements = [];

if($arResult['ORDERS']){
    foreach ($arResult['ORDERS'] as $arOrder){
        $arElements[] = $arOrder;
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
