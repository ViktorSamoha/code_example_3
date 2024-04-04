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

$arFields = [];

if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):
    foreach ($arResult["PROPERTY_LIST"] as $propertyID):

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


        switch ($INPUT_TYPE):
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
                    $arFields[$propertyID] = call_user_func_array($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"],
                        array(
                            $arResult["PROPERTY_LIST_FULL"][$propertyID],
                            array(
                                "VALUE" => $value,
                                "DESCRIPTION" => $description,
                            ),
                            array(
                                "VALUE" => "PROPERTY[" . $propertyID . "][" . $i . "][VALUE]",
                                "DESCRIPTION" => "PROPERTY[" . $propertyID . "][" . $i . "][DESCRIPTION]",
                                "FORM_NAME" => "iblock_add",
                            ),
                        ));
                }
                break;
            case "TAGS":
                /*$APPLICATION->IncludeComponent(
                    "bitrix:search.tags.input",
                    "",
                    array(
                        "VALUE" => $arResult["ELEMENT"][$propertyID],
                        "NAME" => "PROPERTY[" . $propertyID . "][0]",
                        "TEXT" => 'size="' . $arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"] . '"',
                    ), null, array("HIDE_ICONS" => "Y")
                );*/
                break;
            case "HTML":
                /*$LHE = new CHTMLEditor;
                $LHE->Show(array(
                    'name' => "PROPERTY[" . $propertyID . "][0]",
                    'id' => preg_replace("/[^a-z0-9]/i", '', "PROPERTY[" . $propertyID . "][0]"),
                    'inputName' => "PROPERTY[" . $propertyID . "][0]",
                    'content' => $arResult["ELEMENT"][$propertyID],
                    'width' => '100%',
                    'minBodyWidth' => 350,
                    'normalBodyWidth' => 555,
                    'height' => '200',
                    'bAllowPhp' => false,
                    'limitPhpAccess' => false,
                    'autoResize' => true,
                    'autoResizeOffset' => 40,
                    'useFileDialogs' => false,
                    'saveOnBlur' => true,
                    'showTaskbars' => false,
                    'showNodeNavi' => false,
                    'askBeforeUnloadPage' => true,
                    'bbCode' => false,
                    'siteId' => SITE_ID,
                    'controlsMap' => array(
                        array('id' => 'Bold', 'compact' => true, 'sort' => 80),
                        array('id' => 'Italic', 'compact' => true, 'sort' => 90),
                        array('id' => 'Underline', 'compact' => true, 'sort' => 100),
                        array('id' => 'Strikeout', 'compact' => true, 'sort' => 110),
                        array('id' => 'RemoveFormat', 'compact' => true, 'sort' => 120),
                        array('id' => 'Color', 'compact' => true, 'sort' => 130),
                        array('id' => 'FontSelector', 'compact' => false, 'sort' => 135),
                        array('id' => 'FontSize', 'compact' => false, 'sort' => 140),
                        array('separator' => true, 'compact' => false, 'sort' => 145),
                        array('id' => 'OrderedList', 'compact' => true, 'sort' => 150),
                        array('id' => 'UnorderedList', 'compact' => true, 'sort' => 160),
                        array('id' => 'AlignList', 'compact' => false, 'sort' => 190),
                        array('separator' => true, 'compact' => false, 'sort' => 200),
                        array('id' => 'InsertLink', 'compact' => true, 'sort' => 210),
                        array('id' => 'InsertImage', 'compact' => false, 'sort' => 220),
                        array('id' => 'InsertVideo', 'compact' => true, 'sort' => 230),
                        array('id' => 'InsertTable', 'compact' => false, 'sort' => 250),
                        array('separator' => true, 'compact' => false, 'sort' => 290),
                        array('id' => 'Fullscreen', 'compact' => false, 'sort' => 310),
                        array('id' => 'More', 'compact' => true, 'sort' => 400)
                    ),
                ));*/
                break;
            case "T":
                for ($i = 0; $i < $inputNum; $i++) {

                    if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
                        $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                    } elseif ($i == 0) {
                        $value = intval($propertyID) > 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
                    } else {
                        $value = "";
                    }

                    $arFields[$propertyID][$i] = [
                        "name" => "PROPERTY[" . $propertyID . "][" . $i . "]",
                        'value' => $value,
                    ];

                }
                break;
            case "G":
            case "E":
            case "S":
            case "N":
                for ($i = 0; $i < $inputNum; $i++) {
                    if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
                        $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                    } elseif ($i == 0) {
                        $value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];

                    } else {
                        $value = "";
                    }

                    $arFields[$propertyID][$i] = [
                        "name" => "PROPERTY[" . $propertyID . "][" . $i . "]",
                        'value' => $value,
                    ];

                    if ($propertyID == 1) {

                        $arFields[$propertyID][$i] = [
                            "name" => "PROPERTY[" . $propertyID . "][" . $i . "]",
                        ];

                        $arSelect = array("ID", "NAME");
                        $res = CIBlockElement::GetList([], array("IBLOCK_ID" => "2"), false, array("nPageSize" => 1000), $arSelect);
                        while ($arSction = $res->Fetch()) {
                            if ($value == $arSction["ID"]) {
                                $selected = "selected";
                            } else {
                                $selected = "";
                            }

                            $arFields[$propertyID][$i]['values'][] = [
                                "value" => $arSction["ID"],
                                "selected" => $selected,
                                "name" => $arSction["NAME"],
                            ];

                        }

                    }
                }
                break;

            case "F":
                for ($i = 0; $i < $inputNum; $i++) {
                    $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];

                    //d($arResult["ELEMENT_PROPERTIES"][$propertyID][$i]);

                    $arFields[$propertyID][$i] = [
                        "hidden" => [
                            "name" => "PROPERTY[" . $propertyID . "][" . ($arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i) . "]",
                            "value" => $value,
                        ],
                        "file" => [
                            "name" => "PROPERTY_FILE_" . $propertyID . "_" . ($arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i),
                        ],
                    ];

                    if (!empty($value) && is_array($arResult["ELEMENT_FILES"][$value])) {

                        $arFields[$propertyID][$i] = [
                            'type' => 'checkbox',
                            'name' => "DELETE_FILE[" . $propertyID . "][" . ($arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i) . "]",
                            'id' => "file_delete_" . $propertyID . "_" . $i,
                            "value" => "Y",
                        ];

                        if ($arResult["ELEMENT_FILES"][$value]["IS_IMAGE"]) {

                            $arFields[$propertyID][$i] = [
                                "src" => $arResult["ELEMENT_FILES"][$value]["SRC"],
                                "value" => $arResult["ELEMENT_FILES"][$value],
                                "prop_id" => $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"],
                            ];

                        } else {

                            $arFields[$propertyID][$i] = [
                                "name" => $arResult["ELEMENT_FILES"][$value]["ORIGINAL_NAME"],
                                "src" => $arResult["ELEMENT_FILES"][$value]["SRC"],
                                "prop_id" => $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"],
                                "id" => $arResult["ELEMENT_FILES"][$value]["ID"]
                            ];

                        }
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

                            $arFields[$propertyID][] = [
                                'type' => $type,
                                'name' => "PROPERTY[" . $propertyID . "]" . $type == "checkbox" ? "[" . $key . "]" : "",
                                'value' => $key,
                                'id' => "property_" . $key,
                                'checked' => $checked ? "checked" : "",
                                'content' => $arEnum["VALUE"]
                            ];

                        }
                        break;

                    case "dropdown":
                    case "multiselect":

                        $arFields[$propertyID][] = [
                            'type' => $type,
                            'name' => "PROPERTY[" . $propertyID . "]" . $type == "multiselect" ? "[]" : "",
                        ];

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

                            $arFields[$propertyID]['values'][] = [
                                'value' => $key,
                                'checked' => $checked ? "selected" : "",
                                'content' => $arEnum["VALUE"],
                            ];

                        }

                        break;

                endswitch;
                break;
        endswitch;
    endforeach;
endif;

$hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_FEATURES)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();

$data = $entity_data_class::getList(array(
    "select" => ["*"],
    "order" => ["ID" => "DESC"],
    "filter" => []
));

while ($arData = $data->Fetch()) {
    $arFields["FEATURES"]["DETAIL_FEATURES"]["VALUES"][] = [
        "NAME" => $arData["UF_OF_NAME"],
        "VALUE" => $arData["UF_XML_ID"],
    ];
}

$res = CIBlockElement::GetProperty(IB_OBJECT, $arResult['ELEMENT']['ID'], "sort", "asc", array("CODE" => "DETAIL_FEATURES"));
while ($ob = $res->GetNext()) {
    foreach ($arFields['FEATURES']['DETAIL_FEATURES']['VALUES'] as &$feature) {
        if ($feature['VALUE'] == $ob['VALUE']) {
            $feature['checked'] = true;
        }
    }
}

if (!empty($arFields)) {
    $arResult['FIELDS'] = $arFields;
}

$arSelect = array(
    "PROPERTY_BOOKING_ALERT_MSG",
);
$arFilter = array("IBLOCK_ID" => IB_OBJECT, "ID" => $arResult['ELEMENT']['ID']);
$res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
while ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();
    if (isset($arFields['PROPERTY_BOOKING_ALERT_MSG_VALUE']['TEXT'])) {
        $arResult['FIELDS']['BOOKING_ALERT_MSG']['TEXT'] = htmlspecialchars_decode($arFields['PROPERTY_BOOKING_ALERT_MSG_VALUE']['TEXT']);
    }
}

if (isset($arResult['FIELDS'][66]['values']) && !empty($arResult['FIELDS'][66]['values'])) {
    foreach ($arResult['FIELDS'][66]['values'] as $value) {
        if ($value['checked'] == "selected") {
            $arResult['OBJECT_TIME_LIMIT'] = $value['content'];
        }
    }
}


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

//проходим по списку партнеров и сравниваем с указанными партнерами на объекте
foreach ($arResult['FIELDS']['PARTNERS'] as &$partner) {
    foreach ($arResult['FIELDS'][OBJECT_PROPERTY_PARTNERS] as $object_partner_property_value) {
        if ($object_partner_property_value['value'] != '') {
            if ($object_partner_property_value['value'] == $partner['ID']) {
                $partner['SELECTED'] = true;
            }
        }
    }
}
unset($partner);
foreach ($arResult['FIELDS']['PARTNERS'] as $partner) {
    if ($partner['SELECTED'] == true) {
        $arResult['FIRST_SELECTED_PARTNER'] = $partner['NAME'];
        break;
    }
}

foreach ($arResult['FIELDS'][CAR_POSSIBILITY]['values'] as $carPossibilityValue) {
    if ($carPossibilityValue['checked'] == 'selected') {
        if ($carPossibilityValue['content'] == 'Да') {
            $arResult['SHOW_CAR_CAPACITY_BLOCK'] = true;
        }
    }
}
