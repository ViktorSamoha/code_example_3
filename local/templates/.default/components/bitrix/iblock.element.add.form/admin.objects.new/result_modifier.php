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

if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])) {
    foreach ($arResult["PROPERTY_LIST"] as $propertyID) {
        if (intval($propertyID) > 0) {
            if (
                $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "T"
                &&
                $arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] == "1"
            )
                $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "S";
            elseif (
                (
                    $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "S"
                    ||
                    $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "N"
                )
                &&
                $arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] > "1"
            )
                $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "T";

        } elseif ($propertyID == "NAME") {
            $arResult["FIELDS"]["STRING"][$propertyID] = [
                'NAME' => $propertyID,
                'PROP_NAME' => "PROPERTY[" . $propertyID . "][0]",
            ];
        } elseif ($propertyID == "DETAIL_TEXT") {
            $arResult["FIELDS"]["STRING"][$propertyID] = [
                'NAME' => $propertyID,
                'PROP_NAME' => "PROPERTY[" . $propertyID . "][0]",
            ];
        }


        if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y") {
            $inputNum = ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) ? count($arResult["ELEMENT_PROPERTIES"][$propertyID]) : 0;
            $inputNum += $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE_CNT"];
        } else {
            $inputNum = 1;
        }

        if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"])
            $INPUT_TYPE = "USER_TYPE";
        else
            $INPUT_TYPE = $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"];


        //d($INPUT_TYPE, $inputNum, $propertyID);

        switch ($INPUT_TYPE) {
            case "S":
                for ($i = 0; $i < $inputNum; $i++) {
                    if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
                        $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                    } elseif ($i == 0) {
                        $value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];

                    } else {
                        $value = "";
                    }

                    if (!empty($arResult["PROPERTY_LIST_FULL"][$propertyID]['ID'])) {
                        $arResult["FIELDS"]["STRING"][$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']] = [
                            'ID' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['ID'],
                            'NAME' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['NAME'],
                            'PROP_NAME' => "PROPERTY[" . $propertyID . "][" . $i . "]",
                        ];
                    }


                }


                break;
            case "N":

                for ($i = 0; $i < $inputNum; $i++) {
                    if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
                        $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                    } elseif ($i == 0) {
                        $value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];

                    } else {
                        $value = "";
                    }

                    $arResult["FIELDS"]["NUMBER"][$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']] = [
                        'ID' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['ID'],
                        'NAME' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['NAME'],
                        'PROP_NAME' => "PROPERTY[" . $propertyID . "][" . $i . "]",
                    ];

                }

                break;
            case "USER_TYPE":
                for ($i = 0; $i < $inputNum; $i++) {
                    if ($arResult["PROPERTY_LIST_FULL"][$propertyID]['USER_TYPE'] == 'directory') {

                        $arResult["FIELDS"]["FEATURES"][$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']][$i] = [
                            'PROP_NAME' => "PROPERTY[" . $propertyID . "][" . $i . "][VALUE][]",
                        ];

                    }
                }
                break;
            case "L":
                if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["LIST_TYPE"] == "C")
                    $type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "checkbox" : "radio";
                else
                    $type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "multiselect" : "dropdown";

                switch ($type):
                    case "checkbox":
                    case "radio":
                        foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum) {
                            $checked = false;
                            if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
                                if (is_array($arResult["ELEMENT_PROPERTIES"][$propertyID])) {
                                    foreach ($arResult["ELEMENT_PROPERTIES"][$propertyID] as $arElEnum) {
                                        if ($arElEnum["VALUE"] == $key) {
                                            $checked = true;
                                            break;
                                        }
                                    }
                                }
                            } else {
                                if ($arEnum["DEF"] == "Y") $checked = true;
                            }
                            $arResult["FIELDS"]["CHECK"][$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']] = [
                                'ID' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['ID'],
                                'NAME' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['NAME'],
                                'VALUES' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['ENUM'],
                                'PROP_NAME' => "PROPERTY[" . $propertyID . "]",
                            ];
                        }
                        break;

                    case "dropdown":
                    case "multiselect":

                        if (intval($propertyID) > 0) $sKey = "ELEMENT_PROPERTIES";
                        else $sKey = "ELEMENT";

                        foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum) {
                            $checked = false;
                            if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
                                foreach ($arResult[$sKey][$propertyID] as $elKey => $arElEnum) {
                                    if ($key == $arElEnum["VALUE"]) {
                                        $checked = true;
                                        break;
                                    }
                                }
                            } else {
                                if ($arEnum["DEF"] == "Y") $checked = true;
                            }

                            $arResult["FIELDS"]["SELECT"][$propertyID] = [
                                'NAME' => $propertyID,
                                'VALUES' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['ENUM'],
                                'PROP_NAME' => "PROPERTY[" . $propertyID . "]",
                            ];


                        }
                        break;
                endswitch;
                break;

            case "F":

                $arResult["FIELDS"]["FILES"][$propertyID]['NAME'] = $arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"];

                for ($i = 0; $i < $inputNum; $i++) {
                    $arResult["FIELDS"]["FILES"][$propertyID]['VALUES'][$i] = [
                        "file" => "PROPERTY_FILE_" . $propertyID . "_" . $i . "\"",
                        "hidden" => "PROPERTY[" . $propertyID . "][" . $i . "]",
                    ];
                }
                break;
        }

        if ($arResult["PROPERTY_LIST_FULL"][$propertyID]['PROPERTY_TYPE'] == 'E') {
            $arResult["FIELDS"]["OBJECT"][$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']] = [
                'ID' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['ID'],
                'NAME' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['NAME'],
                'PROP_NAME' => "PROPERTY[" . $propertyID . "]"
            ];
        }

    }
}

$arFilter = array('IBLOCK_ID' => IB_OBJECT);
$sections_list = CIBlockSection::GetList([], $arFilter, true, ["ID", "NAME"]);
while ($ar_sections = $sections_list->GetNext()) {
    $arResult["FIELDS"]['SECTIONS'][$ar_sections['ID']] = $ar_sections;
}

$arFilter = array('IBLOCK_ID' => IB_LOCATIONS);
$sections_list = CIBlockElement::GetList([], $arFilter, false, [], ["ID", "NAME"]);
while ($ar_sections = $sections_list->GetNext()) {
    $arResult["FIELDS"]['LOCATIONS'][$ar_sections['ID']] = $ar_sections;
}

$hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_FEATURES)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();

$data = $entity_data_class::getList(array(
    "select" => ["*"],
    "order" => ["ID" => "ASC"],
    "filter" => []
));

while ($arData = $data->Fetch()) {
    $arResult["FIELDS"]["FEATURES"]["DETAIL_FEATURES"]["VALUES"][] = [
        "NAME" => $arData["UF_OF_NAME"],
        "VALUE" => $arData["UF_XML_ID"],
    ];
}

$arResult['USER_DATA'] = $arParams['USER_DATA'];

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
    $arResult['FIELDS']['PARTNERS'][] = [
        'ID' => $arData['ID'],
        'NAME' => $arData['UF_NAME'],
    ];
}
