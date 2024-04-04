<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
/** @var array $arParams */
$statusValue = 'Статус';
if ($arParams['FILTER_STATUS_VALUE']) {
    switch ($arParams['FILTER_STATUS_VALUE']) {
        case 'under-consideration':
            $statusValue = 'На рассмотрении';
            break;
        case 'deny':
            $statusValue = 'Отказано';
            break;
        case 'approve':
            $statusValue = 'Одобрено';
            break;
        case 'blocked':
            $statusValue = 'Заблокирован';
            break;
    }
}
$arResult['STATUS_VALUE'] = $statusValue;