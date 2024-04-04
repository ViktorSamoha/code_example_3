<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */

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
        } elseif (($propertyID == "TAGS") && CModule::IncludeModule('search'))
            $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "TAGS";

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

                    $arResult["FIELDS"]["STRING"][$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']] = [
                        'ID' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['ID'],
                        'NAME' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['NAME'],
                        'PROP_NAME' => "PROPERTY[" . $propertyID . "][" . $i . "]",
                    ];

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
                    if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
                        $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["~VALUE"] : $arResult["ELEMENT"][$propertyID];
                        $description = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["DESCRIPTION"] : "";
                    } elseif ($i == 0) {
                        $value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
                        $description = "";
                    } else {
                        $value = "";
                        $description = "";
                    }
                    if ($arResult["PROPERTY_LIST_FULL"][$propertyID]['USER_TYPE'] == 'Date') {
                        $arResult["FIELDS"]["DATE"][$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']] = [
                            'ID' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['ID'],
                            'NAME' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['NAME'],
                            'PROP_NAME' => "PROPERTY[" . $propertyID . "][" . $i . "][VALUE]",
                        ];
                    }
                   /* else {
                        $arResult["FIELDS"]["TIME"][$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']] = [
                            'ID' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['ID'],
                            'NAME' => $arResult["PROPERTY_LIST_FULL"][$propertyID]['NAME'],
                            'PROP_NAME' => "PROPERTY[" . $propertyID . "][" . $i . "][VALUE]",
                        ];
                    }*/
                }
                break;
            case "L":
                if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["LIST_TYPE"] == "C")
                    $type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "checkbox" : "radio";
                else
                    $type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "multiselect" : "dropdown";

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






