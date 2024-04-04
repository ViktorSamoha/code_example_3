<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */

$arItems = [];

foreach ($arResult['ITEMS'] as &$item) {

    $arItems[$item['ID']] = [
        "ID" => $item['ID'],
        "USER_FIO" => $item['PROPERTIES']['NAME']['VALUE'] . ' ' . $item['PROPERTIES']['NAME']['VALUE'],
        "BOOKING_TYPE" => $item['PROPERTIES']['BOOKING_TYPE']['VALUE'],
        "ARRIVAL_DATE" => $item['PROPERTIES']['ARRIVAL_DATE']['VALUE'],
        "DEPARTURE_DATE" => $item['PROPERTIES']['DEPARTURE_DATE']['VALUE'],
        "CHECK_IN_TIME" => $item['PROPERTIES']['CHECK_IN_TIME']['VALUE'],
        "DEPARTURE_TIME" => $item['PROPERTIES']['DEPARTURE_TIME']['VALUE'],
        "GROUP"=>[
            "ADULTS_COUNT" => $item['PROPERTIES']['ADULTS']['VALUE'],
            "BENIFICIARIES_COUNT" => $item['PROPERTIES']['BENIFICIARIES']['VALUE'],
            "KIDS_COUNT" => $item['PROPERTIES']['KIDS']['VALUE'],
        ],
    ];

    $res = CIBlockElement::GetByID($item["PROPERTIES"]['BOOKING_OBJECT']['VALUE']);

    if ($ar_res = $res->GetNext()) {

        $arItems[$item['ID']]["OBJECT"] = [
            "ID" => $ar_res["ID"],
            "NAME" => $ar_res["NAME"],
        ];
    }

}
$arResult['ITEMS'] = $arItems;
