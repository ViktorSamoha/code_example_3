<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */

$arElements = [];
$filterStatus = false;

//фильтрация по статусу
if ($_GET['STATUS']) {
    switch ($_GET['STATUS']) {
        case 'under-consideration':
            $filterStatus = "На рассмотрении";
            break;
        case 'deny':
            $filterStatus = "Отказано";
            break;
        case 'approve':
            $filterStatus = "Одобрено";
            break;
        case 'blocked':
            $filterStatus = "Заблокирован";
            break;
        case 'all':
            $filterStatus = false;
            break;
    }
}
if ($arResult['TRANSPORT_PERMISSIONS']) {
    foreach ($arResult['TRANSPORT_PERMISSIONS'] as $arPermission) {
        //присваиваем цвет в зависимости от статуса
        if (isset($arPermission['PERMISSION_STATUS'])) {
            switch ($arPermission['PERMISSION_STATUS']) {
                case "На рассмотрении":
                    $arPermission['STATUS_COLOR'] = 'bg-blue';
                    break;
                case "Отказано":
                    $arPermission['STATUS_COLOR'] = 'bg-red';
                    break;
                case "Одобрено":
                    $arPermission['STATUS_COLOR'] = 'bg-green';
                    break;
            }
        }
        //достаем значение свойства "Заблокировано" у транспортного средства пользователя
        $res = CIBlockElement::GetProperty(IB_TRANSPORT, $arPermission['USER_VEHICLE'], array("sort" => "asc"), array("CODE" => "BLOCKED"));
        while ($ob = $res->GetNext()) {
            $arPermission['USER_VEHICLE_BLOCK_STATUS'] = $ob['VALUE_ENUM'];
        }
        if (isset($arPermission['USER_VEHICLE_BLOCK_STATUS']) && $arPermission['USER_VEHICLE_BLOCK_STATUS'] == 'Да') {
            $arPermission['STATUS_COLOR'] = 'bg-red';
            $arPermission['PERMISSION_STATUS'] = 'Заблокирован';
        }
        if ($filterStatus) {
            if ($arPermission['PERMISSION_STATUS'] == $filterStatus) {
                $arElements[] = $arPermission;
            }
        } else {
            $arElements[] = $arPermission;
        }
    }
}
//формируем постраничную навигацию
$rs_ObjectList = new CDBResult;
$rs_ObjectList->InitFromArray($arElements);
$rs_ObjectList->NavStart(10, false);
$arResult["NAV_STRING"] = $rs_ObjectList->GetPageNavString("", '');
$arResult["PAGE_START"] = $rs_ObjectList->SelectedRowsCount() - ($rs_ObjectList->NavPageNomer - 1) * $rs_ObjectList->NavPageSize;
while ($ar_Field = $rs_ObjectList->Fetch()) {

    $arResult['ELEMENTS'][] = $ar_Field;
}
