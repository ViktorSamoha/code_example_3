<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */

$arElements = [];

if($arResult['USER_PERMISSIONS']){
    foreach ($arResult['USER_PERMISSIONS'] as $userPermission){
        $arElements[] = $userPermission;
    }
}
if($arResult['USER_TRANSPORT_PERMISSIONS']){
    foreach ($arResult['USER_TRANSPORT_PERMISSIONS'] as $userTransportPermission){
        $arElements[] = $userTransportPermission;
    }
}
if(!empty($arElements)){
    usort($arElements, function($a, $b) {
        return strtotime($a['DATE_CREATE']) - strtotime($b['DATE_CREATE']);
    });
    $arElements = array_reverse($arElements);
}
$rs_ObjectList = new CDBResult;
$rs_ObjectList->InitFromArray($arElements);
$rs_ObjectList->NavStart(10, false);
$arResult["NAV_STRING"] = $rs_ObjectList->GetPageNavString("", '');
$arResult["PAGE_START"] = $rs_ObjectList->SelectedRowsCount() - ($rs_ObjectList->NavPageNomer - 1) * $rs_ObjectList->NavPageSize;
while ($ar_Field = $rs_ObjectList->Fetch()) {

    $arResult['ELEMENTS'][] = $ar_Field;
}
