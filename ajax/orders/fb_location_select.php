<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader,
    Bitrix\Main\Entity;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$values = $request->getPostList();

Loader::includeModule("iblock");
if (isset($values['location_id'])) {
    $arSelect = ["ID", "NAME", "PROPERTY_TIME_UNLIMIT_OBJECT"];
    $arFilter = ["IBLOCK_ID" => IB_OBJECT, "PROPERTY_LOCATION" => $values['location_id'], "ACTIVE" => "Y",];
    $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $prop = CIBlockElement::GetProperty(IB_OBJECT, $arFields['ID'], array("sort" => "asc"), array("CODE" => "TIME_INTERVAL"));
        $arPropVal = [];
        while ($prop_ob = $prop->GetNext()) {
            if (isset($prop_ob['VALUE']) && isset($prop_ob['VALUE_ENUM'])) {
                $arPropVal[] = [
                    'ID' => $prop_ob['VALUE'],
                    'NAME' => $prop_ob['VALUE_ENUM'],
                ];
            }
        }
        $arResult['SECTIONS'][$arFields['ID']] = $arFields;
        if (isset($arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'])) {
            if ($arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'] == 'Да') {
                $arResult['SECTIONS'][$arFields['ID']]['TIME_LIMIT'] = 'Y';
            } else {
                $arResult['SECTIONS'][$arFields['ID']]['TIME_LIMIT'] = 'N';
            }
        } else {
            $arResult['SECTIONS'][$arFields['ID']]['TIME_LIMIT'] = 'N';
        }
        if (!empty($arPropVal)) {
            if (count($arPropVal) == 2) {
                $arResult['SECTIONS'][$arFields['ID']]["TIME_INTERVAL"]['TYPE'] = 'multi';
                $arResult['SECTIONS'][$arFields['ID']]["TIME_INTERVAL"]['VALUE'] = 'null';
            } else {
                $arResult['SECTIONS'][$arFields['ID']]["TIME_INTERVAL"]['TYPE'] = 'single';
                $arResult['SECTIONS'][$arFields['ID']]["TIME_INTERVAL"]['VALUE'] = $arPropVal[0]['ID'];
            }
        }else{
            $arResult['SECTIONS'][$arFields['ID']]["TIME_INTERVAL"]['TYPE'] = 'multi';
            $arResult['SECTIONS'][$arFields['ID']]["TIME_INTERVAL"]['VALUE'] = 'null';
        }
    }
}

echo json_encode($arResult['SECTIONS']);

?>