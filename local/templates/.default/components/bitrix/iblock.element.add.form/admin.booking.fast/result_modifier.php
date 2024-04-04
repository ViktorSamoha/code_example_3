<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */

/** @var array $arResult */

use \Bitrix\Main\Loader;

Loader::includeModule("iblock");
$dateCreate = CIBlockFormatProperties::DateFormat(
    'j.m.Y / h:m',
    MakeTimeStamp(
        new DateTime(),
        CSite::GetDateFormat()
    )
);
$arDateCreate = explode('/', $dateCreate);
$arResult['DATE_INSERT'] = $arDateCreate[0];

//достаем данные менеджера
global $USER;
$user = getUserData();
$arResult['MANAGER_FIO'] = $user['LOGIN'];
$arResult['MANAGER_OBJECTS'] = $user['USER_OBJECTS'];
$arResult['MANAGER_LOCATIONS'] = $user['USER_LOCATIONS'];
$arObjects = [];
if(!empty($arResult['MANAGER_OBJECTS'])){
    $arManagerObjectsId =[];
    foreach ($arResult['MANAGER_OBJECTS'] as $managerObject){
        $arManagerObjectsId[] = $managerObject['ID'];
    }
}
if ($arResult['MANAGER_LOCATIONS']) {
    $arManagerLocations = [];
    foreach ($arResult['MANAGER_LOCATIONS'] as $location) {
        $arManagerLocations[] = $location['ID'];
    }
    $arSelect = array("ID", "NAME", "PROPERTY_TIME_UNLIMIT_OBJECT", "PROPERTY_CAR_POSSIBILITY", "PROPERTY_CAR_CAPACITY");
    $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "PROPERTY_LOCATION" => $arManagerLocations, "ACTIVE" => "Y", '=PROPERTY_CAN_BOOK' => 36);
    if(isset($arManagerObjectsId) && !empty($arManagerObjectsId)){
        $arFilter['ID']=$arManagerObjectsId;
    }
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $prop = CIBlockElement::GetProperty(IB_LOCATIONS, $arFields['ID'], array("id" => "asc"), array("CODE" => "TIME_INTERVAL"));
        $arPropVal = [];
        while ($prop_ob = $prop->GetNext()) {
            if (isset($prop_ob['VALUE']) && isset($prop_ob['VALUE_ENUM'])) {
                $arPropVal[] = [
                    'ID' => $prop_ob['VALUE'],
                    'NAME' => $prop_ob['VALUE_ENUM'],
                ];
            }
        }
        $arObjects[] = $arFields['ID'];
        $arResult['OBJECTS'][$arFields['ID']] = $arFields;
        if (isset($arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'])) {
            if ($arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'] == 'Да') {
                $arResult['OBJECTS'][$arFields['ID']]['TIME_LIMIT'] = 'Y';
            } else {
                $arResult['OBJECTS'][$arFields['ID']]['TIME_LIMIT'] = 'N';
            }
        } else {
            $arResult['OBJECTS'][$arFields['ID']]['TIME_LIMIT'] = 'N';
        }
        if (isset($arFields['PROPERTY_CAR_POSSIBILITY_VALUE'])) {
            if ($arFields['PROPERTY_CAR_POSSIBILITY_VALUE'] == 'Да') {
                $arResult['OBJECTS'][$arFields['ID']]['CAR_POSSIBILITY'] = 'Y';
            } else {
                $arResult['OBJECTS'][$arFields['ID']]['CAR_POSSIBILITY'] = 'N';
            }
        } else {
            $arResult['OBJECTS'][$arFields['ID']]['CAR_POSSIBILITY'] = 'N';
        }
        if (isset($arFields['PROPERTY_CAR_CAPACITY_VALUE'])) {
            $arResult['OBJECTS'][$arFields['ID']]['CAR_CAPACITY'] = $arFields['PROPERTY_CAR_CAPACITY_VALUE'];
        } else {
            $arResult['OBJECTS'][$arFields['ID']]['CAR_CAPACITY'] = '';
        }
        if (!empty($arPropVal)) {
            if (count($arPropVal) == 2) {
                $arResult['OBJECTS'][$arFields['ID']]["TIME_INTERVAL"]['TYPE'] = 'multi';
                $arResult['OBJECTS'][$arFields['ID']]["TIME_INTERVAL"]['VALUE'] = 'null';
            } else {
                $arResult['OBJECTS'][$arFields['ID']]["TIME_INTERVAL"]['TYPE'] = 'single';
                $arResult['OBJECTS'][$arFields['ID']]["TIME_INTERVAL"]['VALUE'] = $arPropVal[0]['ID'];
            }
        }
    }
}
//достаем категории объектов
$structure = getLocationStructure();
$arResult['SECTIONS'] = $structure['TYPE'];
//сортируем объекты по порядку
asort($arResult['OBJECTS']);
