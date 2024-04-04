<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server;

$context = Context::getCurrent();
$request = $context->getRequest();
$_get = $request->getQueryList();
$user = getUserData();

$arResult['ARCHIVE'] = false;
if ($_get['ARCHIVE'] && $_get['ARCHIVE'] == 'Y') {
    $arResult['ARCHIVE'] = true;
}

if($_get['LOCATION_ID']){
    if($user['USER_LOCATIONS']){
        foreach ($user['USER_LOCATIONS'] as &$userLocation){
            if($userLocation['ID'] == $_get['LOCATION_ID']){
                $arResult['SELECTED_LOCATION'] = $userLocation;
            }
        }
    }
}

$arResult['LOCATIONS'] = $user['USER_LOCATIONS'];
$arResult['USER'] = $user;

