<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */

/** @var array $arResult */

use \Bitrix\Main\Loader;
use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;

Loader::includeModule("iblock");
Loader::includeModule("highloadblock");

//достаем из hl блока "Партнеры" список партнеров
$hlblock = HL\HighloadBlockTable::getById(HL_PARTNERS)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();

$data = $entity_data_class::getList(array(
    "select" => array("*"),
    "order" => array("ID" => "ASC"),
    "filter" => array()
));

while ($arData = $data->Fetch()) {
    $arResult['PARTNERS'][] = [
        'ID' => $arData['ID'],
        'NAME' => $arData['UF_NAME'],
    ];
}

$arSelect = array("ID", "NAME", "ACTIVE");
$arFilter = array("IBLOCK_ID" => IB_LOCATIONS);
$res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);

if ($res) {
    unset($arResult['ITEMS']);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arResult['ITEMS'][$arFields['ID']] = [
            'ID' => $arFields['ID'],
            'NAME' => $arFields['NAME'],
            'ACTIVE' => $arFields['ACTIVE'],
            'PARTNERS' => $arResult['PARTNERS']
        ];
    }
}


foreach ($arResult['ITEMS'] as &$item) {
    $arObjectPartners = [];
    $res = CIBlockElement::GetList(array(), ["IBLOCK_ID" => IB_LOCATIONS, 'ID' => $item['ID']], false, [], ["PROPERTY_PARTNERS"]);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        if (isset($arFields['PROPERTY_PARTNERS_VALUE']) && !empty($arFields['PROPERTY_PARTNERS_VALUE'])) {
            $arObjectPartners[] = $arFields['PROPERTY_PARTNERS_VALUE'];
        }
    }
    if (!empty($arObjectPartners)) {
        $arPartnerData = [];
        $hlblock = HL\HighloadBlockTable::getById(HL_PARTNERS)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        foreach ($arObjectPartners as $partnerId) {
            $data = $entity_data_class::getList(array(
                "select" => array("*"),
                "order" => array("ID" => "ASC"),
                "filter" => array("ID" => $partnerId)
            ));
            while ($arData = $data->Fetch()) {
                if (!empty($arData)) {
                    $arPartnerData[$arData['ID']] = [
                        'ID' => $arData['ID'],
                        'NAME' => $arData['UF_NAME'],
                    ];
                }

            }
        }
    }
    if (!empty($arPartnerData)) {
        foreach ($item['PARTNERS'] as &$partner) {
            foreach ($arPartnerData as $itemPartner) {
                if ($itemPartner['ID'] == $partner['ID']) {
                    $partner['SELECTED'] = true;
                }
            }
        }
    }
    unset($partner);
    if (!empty($item['PARTNERS'])) {
        foreach ($item['PARTNERS'] as $partner) {
            if ($partner['SELECTED']) {
                $item['FIRST_SELECTED_PARTNER'] = $partner['NAME'];
                break;
            }
        }

    }
    unset($arPartnerData, $partner, $itemPartner);
}
