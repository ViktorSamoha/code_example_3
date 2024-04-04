<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/*use Bitrix\Main\Context;
use Bitrix\Main\Request; */

use Bitrix\Main\Diag\Debug;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class WAStat extends CBitrixComponent implements Controllerable
{

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'getTable' => [
                'prefilters' => [],
            ],
            'getExcel' => [
                'prefilters' => [],
            ],
        ];
    }

    public function getLocationSumValue($calculate_by, $arValues, $locationId)
    {
        if (isset($calculate_by) && !empty($arValues)) {
            switch ($calculate_by) {
                case 'UF_OBJECT_ID':
                    return count($arValues[$locationId]);
                case 'UF_ORDER_SUM':
                    $sum = 0;
                    foreach ($arValues[$locationId] as $object) {
                        $sum += $object['UF_ORDER_SUM'];
                    }
                    return $sum;
                case 'UF_PERMIT_COUNT':
                    $permit_count = [];
                    foreach ($arValues[$locationId] as $object) {
                        if (!empty($permit_count)) {
                            $permit_count['BENEFIT_PERMIT_COUNT'] += $object['UF_BENEFIT_PERMIT_COUNT'];
                            $permit_count['PERMIT_COUNT'] += $object['UF_PERMIT_COUNT'];
                            $permit_count['SUM'] += $object['UF_PERMIT_COUNT'] + $object['UF_BENEFIT_PERMIT_COUNT'];
                        } else {
                            $permit_count = [
                                'BENEFIT_PERMIT_COUNT' => $object['UF_BENEFIT_PERMIT_COUNT'],
                                'PERMIT_COUNT' => $object['UF_PERMIT_COUNT'],
                                'SUM' => $object['UF_PERMIT_COUNT'] + $object['UF_BENEFIT_PERMIT_COUNT'],
                            ];
                        }
                    }
                    return $permit_count;
                case 'UF_STAY_DURATION':
                    $duration_count_values = [];
                    foreach ($arValues[$locationId] as $object) {
                        if (!empty($duration_count_values)) {
                            if ($object["UF_STAY_DURATION"] == "День") {
                                $duration_count_values['DAILY'] += 1;
                                $duration_count_values['SUM'] += 1;
                            } else {
                                $duration_count_values['COUPLE_DAY'] += 1;
                                $duration_count_values['SUM'] += 1;
                            }
                        } else {
                            if ($object["UF_STAY_DURATION"] == "День") {
                                $duration_count_values['SUM'] = $duration_count_values['DAILY'] = 1;
                            } else {
                                $duration_count_values['SUM'] = $duration_count_values['COUPLE_DAY'] = 1;
                                $duration_count_values['DAILY'] = 0;
                            }
                        }
                    }
                    return $duration_count_values;
            }
        } else {
            return false;
        }
    }

    public function getObjectsSumValue($calculate_by, $arValues)
    {
        if (isset($calculate_by) && !empty($arValues)) {
            $this->getObjects($this->arObjects, $this->arParams);
            switch ($calculate_by) {
                case 'UF_OBJECT_ID':
                    $arObjectsSum = [];
                    $arObjects = [];
                    foreach ($arValues as $object) {
                        $arObjectsSum[] = [
                            'NAME' => $this->arObjects["OBJECTS"][$object['UF_OBJECT_ID']]['NAME'],
                            'DATE_INSERT' => $object['UF_DATE_INSERT']->format('d.m.Y'),
                            'ARRIVAL_DATE' => $object['UF_ARRIVAL_DATE']->format('d.m.Y'),
                            'DEPARTURE_DATE' => $object['UF_DEPARTURE_DATE']->format('d.m.Y'),
                            'COUNT' => 1,
                        ];
                    }
                    return $arObjectsSum;
                case 'UF_ORDER_SUM':
                    $arObjectsSum = [];
                    foreach ($arValues as $object) {
                        $arObjectsSum[] = [
                            'NAME' => $this->arObjects["OBJECTS"][$object['UF_OBJECT_ID']]['NAME'],
                            'DATE_INSERT' => $object['UF_DATE_INSERT']->format('d.m.Y'),
                            'ARRIVAL_DATE' => $object['UF_ARRIVAL_DATE']->format('d.m.Y'),
                            'DEPARTURE_DATE' => $object['UF_DEPARTURE_DATE']->format('d.m.Y'),
                            'ORDER_SUM' => $object['UF_ORDER_SUM'],
                        ];
                    }
                    return $arObjectsSum;
                case 'UF_PERMIT_COUNT':
                    $permit_count = [];
                    foreach ($arValues as $object) {
                        $permit_count[] = [
                            'NAME' => $this->arObjects["OBJECTS"][$object['UF_OBJECT_ID']]['NAME'],
                            'DATE_INSERT' => $object['UF_DATE_INSERT']->format('d.m.Y'),
                            'ARRIVAL_DATE' => $object['UF_ARRIVAL_DATE']->format('d.m.Y'),
                            'DEPARTURE_DATE' => $object['UF_DEPARTURE_DATE']->format('d.m.Y'),
                            'BENEFIT_PERMIT_COUNT' => $object['UF_BENEFIT_PERMIT_COUNT'],
                            'PERMIT_COUNT' => $object['UF_PERMIT_COUNT'],
                            'SUM' => $object['UF_PERMIT_COUNT'] + $object['UF_BENEFIT_PERMIT_COUNT'],
                        ];
                    }
                    return $permit_count;
                case 'UF_STAY_DURATION':
                    $duration_count_values = [];
                    foreach ($arValues as $object) {
                        if ($object["UF_STAY_DURATION"] == "День") {
                            $duration_count_values[] = [
                                'NAME' => $this->arObjects["OBJECTS"][$object['UF_OBJECT_ID']]['NAME'],
                                'DATE_INSERT' => $object['UF_DATE_INSERT']->format('d.m.Y'),
                                'ARRIVAL_DATE' => $object['UF_ARRIVAL_DATE']->format('d.m.Y'),
                                'DEPARTURE_DATE' => $object['UF_DEPARTURE_DATE']->format('d.m.Y'),
                                'SUM' => 1,
                                'DAILY' => 1,
                            ];
                        } else {
                            $duration_count_values[] = [
                                'NAME' => $this->arObjects["OBJECTS"][$object['UF_OBJECT_ID']]['NAME'],
                                'DATE_INSERT' => $object['UF_DATE_INSERT']->format('d.m.Y'),
                                'ARRIVAL_DATE' => $object['UF_ARRIVAL_DATE']->format('d.m.Y'),
                                'DEPARTURE_DATE' => $object['UF_DEPARTURE_DATE']->format('d.m.Y'),
                                'SUM' => 1,
                                'DAILY' => 0,
                                'COUPLE_DAY' => 1,
                            ];
                        }
                    }
                    return $duration_count_values;
            }
        } else {
            return false;
        }
    }

    //функция, которая считает необходимые значения для отчета и формирует единую структуру
    public function calculatePropertyValue($calculate_by, $statData)
    {
        if (isset($calculate_by) && !empty($statData)) {
            $this->getLocations($this->arLocations, $this->arParams);
            //сортируем записи из таблицы со статистикой по оператору
            $arOperatorsData = [];
            foreach ($statData as $id => $dataRow) {
                if (isset($dataRow["UF_OPERATOR"])) {
                    $arOperatorsData[$dataRow["UF_OPERATOR"]][] = $id;
                }
            }
            //формируем массив оператор->локация->объекты
            $arOperatorObjects = [];
            foreach ($arOperatorsData as $operatorLogin => $arDataRowId) {
                foreach ($arDataRowId as $dataRowId) {
                    $arOperatorObjects[$operatorLogin][$statData[$dataRowId]["UF_LOCATION"]][] = $statData[$dataRowId];
                }
            }
            //формируем массив для таблицы в виде: оператор->локация->сумма локации + объекты->сумма по объектам
            $arOperatorStat = [];
            foreach ($arOperatorObjects as $operatorLogin => $locationObjects) {
                foreach ($locationObjects as $locationId => $locationObjectsValue) {
                    $arOperatorStat[$operatorLogin][$this->arLocations["LOCATIONS"][$locationId]['NAME']] = [
                        'LOCATION_SUM_VALUE' => $this->getLocationSumValue($calculate_by, $locationObjects, $locationId),
                        'OBJECTS_SUM_VALUE' => $this->getObjectsSumValue($calculate_by, $locationObjectsValue),
                    ];

                }
            }
            //формируем 1 столбец таблицы с общими данными
            $arStatSum = [];
            $location_sum_value = [];
            $objects_sum_value = [];
            $complex_location_sum_value = [];
            $complex_objects_sum_value = [];
            foreach ($arOperatorStat as $operatorLogin => $statRow) {
                foreach ($statRow as $locationName => $statValues) {
                    switch ($calculate_by) {
                        case 'UF_PERMIT_COUNT':
                            if (is_array($statValues)) {
                                if (!empty($complex_location_sum_value[$locationName])) {
                                    if (!isset($complex_location_sum_value[$locationName]["BENEFIT_PERMIT_COUNT"])) {
                                        $complex_location_sum_value[$locationName]["BENEFIT_PERMIT_COUNT"] = 0;
                                    }
                                    if (!isset($complex_location_sum_value[$locationName]["PERMIT_COUNT"])) {
                                        $complex_location_sum_value[$locationName]["PERMIT_COUNT"] = 0;
                                    }
                                    if (!isset($complex_location_sum_value[$locationName]["SUM"])) {
                                        $complex_location_sum_value[$locationName]["SUM"] = 0;
                                    }
                                    $complex_location_sum_value[$locationName]["BENEFIT_PERMIT_COUNT"] += $statValues["LOCATION_SUM_VALUE"]["BENEFIT_PERMIT_COUNT"];
                                    $complex_location_sum_value[$locationName]["PERMIT_COUNT"] += $statValues["LOCATION_SUM_VALUE"]["PERMIT_COUNT"];
                                    $complex_location_sum_value[$locationName]["SUM"] += $statValues["LOCATION_SUM_VALUE"]["PERMIT_COUNT"] + $statValues["LOCATION_SUM_VALUE"]["BENEFIT_PERMIT_COUNT"];
                                } else {
                                    $complex_location_sum_value[$locationName]["BENEFIT_PERMIT_COUNT"] = $statValues["LOCATION_SUM_VALUE"]["BENEFIT_PERMIT_COUNT"];
                                    $complex_location_sum_value[$locationName]["PERMIT_COUNT"] = $statValues["LOCATION_SUM_VALUE"]["PERMIT_COUNT"];
                                    $complex_location_sum_value[$locationName]["SUM"] = $statValues["LOCATION_SUM_VALUE"]["PERMIT_COUNT"] + $statValues["LOCATION_SUM_VALUE"]["BENEFIT_PERMIT_COUNT"];
                                }
                                foreach ($statValues["OBJECTS_SUM_VALUE"] as $objectName => $objectValue) {
                                    $objectValue['OPERATOR'] = $operatorLogin;
                                    $complex_objects_sum_value[$locationName][] = $objectValue;
                                }
                                $arStatSum['first_cell'][$locationName] = [
                                    'LOCATION_SUM_VALUE' => $complex_location_sum_value[$locationName],
                                    'OBJECTS_SUM_VALUE' => $complex_objects_sum_value[$locationName]
                                ];
                            }
                            break;
                        case 'UF_STAY_DURATION':
                            if (is_array($statValues)) {
                                if (!empty($complex_location_sum_value[$locationName])) {
                                    if (!isset($complex_location_sum_value[$locationName]["COUPLE_DAY"])) {
                                        $complex_location_sum_value[$locationName]["COUPLE_DAY"] = 0;
                                    }
                                    if (!isset($complex_location_sum_value[$locationName]["DAILY"])) {
                                        $complex_location_sum_value[$locationName]["DAILY"] = 0;
                                    }
                                    if (!isset($complex_location_sum_value[$locationName]["SUM"])) {
                                        $complex_location_sum_value[$locationName]["SUM"] = 0;
                                    }
                                    $complex_location_sum_value[$locationName]["COUPLE_DAY"] += $statValues["LOCATION_SUM_VALUE"]["COUPLE_DAY"];
                                    $complex_location_sum_value[$locationName]["DAILY"] += $statValues["LOCATION_SUM_VALUE"]["DAILY"];
                                    $complex_location_sum_value[$locationName]["SUM"] += $statValues["LOCATION_SUM_VALUE"]["DAILY"] + $statValues["LOCATION_SUM_VALUE"]["COUPLE_DAY"];
                                } else {
                                    $complex_location_sum_value[$locationName]["COUPLE_DAY"] = $statValues["LOCATION_SUM_VALUE"]["COUPLE_DAY"];
                                    $complex_location_sum_value[$locationName]["DAILY"] = $statValues["LOCATION_SUM_VALUE"]["DAILY"];
                                    $complex_location_sum_value[$locationName]["SUM"] = $statValues["LOCATION_SUM_VALUE"]["DAILY"] + $statValues["LOCATION_SUM_VALUE"]["COUPLE_DAY"];
                                }
                                foreach ($statValues["OBJECTS_SUM_VALUE"] as $objectName => $objectValue) {
                                    $objectValue['OPERATOR'] = $operatorLogin;
                                    $complex_objects_sum_value[$locationName][] = $objectValue;
                                }
                                $arStatSum['first_cell'][$locationName] = [
                                    'LOCATION_SUM_VALUE' => $complex_location_sum_value[$locationName],
                                    'OBJECTS_SUM_VALUE' => $complex_objects_sum_value[$locationName]
                                ];
                            }
                            break;
                        default:
                            if (empty($location_sum_value)) {
                                $location_sum_value[$locationName] = 0;
                            }
                            $location_sum_value[$locationName] += $statValues["LOCATION_SUM_VALUE"];
                            foreach ($statValues["OBJECTS_SUM_VALUE"] as $objectValue) {
                                $objectValue['OPERATOR'] = $operatorLogin;
                                $objects_sum_value[$locationName][] = $objectValue;
                            }
                            $arStatSum['first_cell'][$locationName] = [
                                'LOCATION_SUM_VALUE' => $location_sum_value[$locationName],
                                'OBJECTS_SUM_VALUE' => $objects_sum_value[$locationName]
                            ];
                            break;
                    }
                }
            }
            return array_merge($arStatSum, $arOperatorStat);
        } else {
            return false;
        }
    }

    //функция, которая превращает столбцы в строки таблицы
    public function convertTableCells($tableCells, $VALUES)
    {
        if (isset($tableCells) && !empty($tableCells)) {
            $tableRows = [];
            $tableRows['operators'] = ['Дата оформления', 'Дата заезда', 'Дата выезда',];
            foreach ($tableCells as $operator => $cellValue) {
                $tableRows['operators'][] = $operator;
                foreach ($cellValue as $locationName => $locationData) {
                    $tableRows['locations'][$locationName] = [];
                }
            }
            unset($cellValue, $operator, $locationData);
            foreach ($tableRows['locations'] as $tableLocationName => $emptyValue) {
                foreach ($tableRows['operators'] as &$operator) {
                    $locationData = $tableCells[$operator][$tableLocationName];
                    if (isset($locationData) && !empty($locationData)) {
                        $tableRows['locations'][$tableLocationName]['sum_values'][$operator] = $locationData["LOCATION_SUM_VALUE"];
                    } else {
                        $tableRows['locations'][$tableLocationName]['sum_values'][$operator] = '';
                    }
                }
            }
            unset($cellValue, $locationName, $operator, $locationData);
            foreach ($tableCells["first_cell"] as $locationName => $locationData) {
                foreach ($locationData["OBJECTS_SUM_VALUE"] as $rowObjects) {
                    $arObjectRow = [
                        "NAME" => $rowObjects["NAME"],
                    ];
                    foreach ($tableRows['operators'] as $operator) {
                        if ($rowObjects["OPERATOR"] == $operator || $operator == 'first_cell') {
                            if (is_array($VALUES)) {
                                foreach ($VALUES as $VALUE) {
                                    $arObjectRow[$operator][$VALUE] = $rowObjects[$VALUE];
                                }
                            } else {
                                $arObjectRow[$operator] = $rowObjects[$VALUES];
                            }
                        } else {
                            switch ($operator) {
                                case"Дата оформления":
                                    $arObjectRow[$operator] = $rowObjects["DATE_INSERT"];
                                    break;
                                case"Дата заезда":
                                    $arObjectRow[$operator] = $rowObjects["ARRIVAL_DATE"];
                                    break;
                                case"Дата выезда":
                                    $arObjectRow[$operator] = $rowObjects["DEPARTURE_DATE"];
                                    break;
                                default:
                                    $arObjectRow[$operator] = '';
                                    break;
                            }
                        }
                    }
                    $tableRows['locations'][$locationName]['objects'][] = $arObjectRow;
                }
            }
            unset($operator, $locationName, $locationData, $objectName, $objectValue, $tableLocationName);
            if (!empty($tableRows)) {
                return $tableRows;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function prepareTableData($tableRows, $propertyName1, $propertyName2)
    {
        if (!empty($tableRows)) {
            foreach ($tableRows as $rowType => &$tableRow) {
                if ($rowType == 'locations') {
                    foreach ($tableRow as $locationName => &$rowElements) {
                        foreach ($rowElements as $rowElementsType => &$rowElementsValues) {
                            switch ($rowElementsType) {
                                case 'sum_values':
                                    foreach ($rowElementsValues as $rowValueName => $rowValue) {
                                        if (is_array($rowValue)) {
                                            $rowElements['location_sub_row'][$locationName][$propertyName1][$rowValueName] = $rowValue[$propertyName1];
                                            $rowElements['location_sub_row'][$locationName][$propertyName2][$rowValueName] = $rowValue[$propertyName2];
                                            $rowElementsValues[$rowValueName] = $rowValue['SUM'];
                                        } else {
                                            $rowElements['location_sub_row'][$locationName][$propertyName1][$rowValueName] = '';
                                            $rowElements['location_sub_row'][$locationName][$propertyName2][$rowValueName] = '';
                                        }
                                    }
                                    break;
                                case 'objects':
                                    foreach ($rowElementsValues as $i => &$rowObject) {
                                        foreach ($rowObject as $rowObjectDataElementName => &$rowObjectDataElement) {
                                            if (is_array($rowObjectDataElement)) {
                                                if ($rowObjectDataElementName != "NAME") {
                                                    $rowElements['object_sub_row'][$locationName][$i][$propertyName1][$rowObjectDataElementName] = $rowObjectDataElement[$propertyName1];
                                                    $rowElements['object_sub_row'][$locationName][$i][$propertyName2][$rowObjectDataElementName] = $rowObjectDataElement[$propertyName2];
                                                }
                                                $rowObject[$rowObjectDataElementName] = $rowObjectDataElement['SUM'];
                                            } else {
                                                if ($rowObjectDataElementName != "NAME") {
                                                    $rowElements['object_sub_row'][$locationName][$i][$propertyName1][$rowObjectDataElementName] = '';
                                                    $rowElements['object_sub_row'][$locationName][$i][$propertyName2][$rowObjectDataElementName] = '';
                                                }
                                            }
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }
            }
            return $tableRows;
        } else {
            return false;
        }
    }

    //функция, которая генерит таблицу
    public function createObjectTable($tableName, $cellName, $tableRows, $subRowName1 = '', $subRowName2 = '', $subRowPropertyName1 = '', $subRowPropertyName2 = '')
    {
        if (isset($tableRows) && !empty($tableRows)) {
            $html = '<div class="form-block"><h3 class="form-block_title">' . $tableName . '</h3><div class="table-wrap"><table class="table table--reverse table--bs0 table--sm">';
            foreach ($tableRows as $rowType => $tableRow) {
                switch ($rowType) {
                    case 'operators':
                        $rowHtml = '<tr><th></th>';
                        foreach ($tableRow as $rowElement) {
                            if ($rowElement == 'first_cell') {
                                $rowHtml .= '<th class="text-center">' . $cellName . '</th>';
                            } else {
                                $rowHtml .= '<th class="text-center">' . $rowElement . '</th>';
                            }
                        }
                        $rowHtml .= '</tr>';
                        $html .= $rowHtml;
                        unset($rowHtml);
                        break;
                    case 'locations':
                        $rowHtml = '<tr class="tr-border-bottom">';
                        foreach ($tableRow as $locationName => $rowElements) {
                            $rowHtml .= '<td><b>' . $locationName . '</b></td>';
                            foreach ($rowElements as $rowElementsType => $rowElementsValues) {
                                switch ($rowElementsType) {
                                    case 'sum_values':
                                        foreach ($rowElementsValues as $rowLocationSumValue) {
                                            $rowHtml .= '<td><b>' . $rowLocationSumValue . '</b></td>';
                                        }
                                        $rowHtml .= '</tr>';
                                        if (isset($rowElements['location_sub_row'][$locationName])) {
                                            if ($subRowName1 != '') {
                                                $rowHtml .= '<td><b>' . $subRowName1 . '</b></td>';
                                            }
                                            foreach ($rowElements['location_sub_row'][$locationName][$subRowPropertyName1] as $subRowLocationValue) {
                                                $rowHtml .= '<td><b>' . $subRowLocationValue . '</b></td>';
                                            }
                                            $rowHtml .= '</tr><tr class="tr-border-bottom">';
                                            if ($subRowName2 != '') {
                                                $rowHtml .= '<td><b>' . $subRowName2 . '</b></td>';
                                            }
                                            foreach ($rowElements['location_sub_row'][$locationName][$subRowPropertyName2] as $subRowLocationValue) {
                                                $rowHtml .= '<td><b>' . $subRowLocationValue . '</b></td>';
                                            }
                                            $rowHtml .= '</tr>';
                                        }
                                        break;
                                    case 'objects':
                                        $rowHtml .= '<tr>';
                                        foreach ($rowElementsValues as $i => $arRow) {
                                            foreach ($arRow as $rowElementValue) {
                                                $rowHtml .= '<td>' . $rowElementValue . '</td>';
                                            }
                                            $rowHtml .= '</tr>';
                                            if (isset($rowElements['object_sub_row'][$locationName][$i])) {
                                                if ($subRowName1 != '') {
                                                    $rowHtml .= '<td><b>' . $subRowName1 . '</b></td>';
                                                }
                                                foreach ($rowElements['object_sub_row'][$locationName][$i][$subRowPropertyName1] as $subRowObjectValue) {
                                                    $rowHtml .= '<td>' . $subRowObjectValue . '</td>';
                                                }
                                                $rowHtml .= '</tr><tr class="tr-border-bottom">';
                                                if ($subRowName2 != '') {
                                                    $rowHtml .= '<td><b>' . $subRowName2 . '</b></td>';
                                                }
                                                foreach ($rowElements['object_sub_row'][$locationName][$i][$subRowPropertyName2] as $subRowObjectValue) {
                                                    $rowHtml .= '<td>' . $subRowObjectValue . '</td>';
                                                }
                                                $rowHtml .= '</tr><tr>';
                                            }
                                        }
                                        break;
                                }
                            }
                        }
                        $html .= $rowHtml;
                        unset($rowHtml);
                        break;
                }
            }
            $html .= '</table></div></div>';
            if ($html) {
                return $html;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //функция генерит шапку на странице с отчетами
    public function createTableHeader($arProps)
    {
        Loader::includeModule("iblock");

        if (isset($arProps) && !empty($arProps)) {

            $html = '<table class="blank-table blank-table--sm mb40">';

            if (isset($arProps['dates']) && !empty($arProps['dates'])) {
                $html .= '<tr><th>Дата</th>';
                $data_html = '<td>';
                if (isset($arProps['dates']["arrival_date"]) && !empty($arProps['dates']["arrival_date"])) {
                    $data_html .= $arProps['dates']["arrival_date"];
                }
                if (isset($arProps['dates']["departure_date"]) && !empty($arProps['dates']["departure_date"])) {
                    $data_html .= ' - ' . $arProps['dates']["departure_date"];
                }
                $data_html .= '</td>';
                $html .= $data_html . '</tr>';
            }
            if (isset($arProps['locations']) && !empty($arProps['locations'])) {
                if ($arProps['locations'][0] != 'all') {
                    $html .= '<tr><th>Локации</th><td>';
                    foreach ($arProps['locations'] as $k => $location_id) {
                        $res = CIBlockElement::GetByID($location_id);
                        if ($ar_res = $res->GetNext()) {
                            $html .= $ar_res['NAME'];
                            if ($k != array_key_last($arProps['locations'])) {
                                $html .= ', ';
                            }
                        }
                    }
                    $html .= '</td></tr>';
                } else {
                    $html .= '<tr><th>Локации</th><td>Все</td></tr>';
                }
                unset($ar_res, $res, $k);
            }
            if (isset($arProps['objects']) && !empty($arProps['objects'])) {
                if ($arProps['objects'][0] != 'all') {
                    $html .= '<tr><th>Объекты</th><td>';
                    foreach ($arProps['objects'] as $k => $object_id) {
                        $res = CIBlockElement::GetByID($object_id);
                        if ($ar_res = $res->GetNext()) {
                            $html .= $ar_res['NAME'];
                            if ($k != array_key_last($arProps['objects'])) {
                                $html .= ', ';
                            }
                        }
                    }
                    $html .= '</td></tr>';
                } else {
                    $html .= '<tr><th>Объекты</th><td>Все</td></tr>';
                }
                unset($ar_res, $res, $k);
            }
            if (isset($arProps['users']) && !empty($arProps['users'])) {
                if ($arProps['users'][0] != 'all') {
                    $html .= '<tr><th>Операторы</th><td>';
                    foreach ($arProps['users'] as $k => $user_id) {
                        $rsUser = CUser::GetByID($user_id);
                        $arUser = $rsUser->Fetch();
                        $html .= $arUser['LOGIN'];
                        if ($k != array_key_last($arProps['users'])) {
                            $html .= ', ';
                        }
                    }
                    $html .= '</td></tr>';
                } else {
                    $html .= '<tr><th>Операторы</th><td>Все</td></tr>';
                }
            }

            $html .= '</table>';

            return $html;

        } else {
            return false;
        }
    }

    //функция дергает данные из хл блока
    public function getStatData($arProps)
    {
        //собираем фильтр по прилетевшим значениям
        $filter = [];

        if (isset($arProps['dates']) && !empty($arProps['dates'])) {
            if (isset($arProps['dates']["arrival_date"]) && isset($arProps['dates']["departure_date"])) {
                if (!empty($arProps['dates']["arrival_date"])) {
                    $filter['>=UF_DATE_INSERT'] = $arProps['dates']["arrival_date"];
                }
                if (!empty($arProps['dates']["departure_date"])) {
                    $filter['<=UF_DATE_INSERT'] = $arProps['dates']["departure_date"];
                }
            } else {
                if (isset($arProps['dates']["arrival_date"]) && !empty($arProps['dates']["arrival_date"])) {
                    $filter['=UF_DATE_INSERT'] = $arProps['dates']["arrival_date"];
                }
            }
        }
        if (isset($arProps['locations']) && !empty($arProps['locations'])) {
            if ($arProps['locations'][0] != 'all') {
                $filter['=UF_LOCATION'] = $arProps['locations'];
            }
        }
        if (isset($arProps['objects']) && !empty($arProps['objects'])) {
            if ($arProps['objects'][0] != 'all') {
                $filter['=UF_OBJECT_ID'] = $arProps['objects'];
            }
        }
        if (isset($arProps['users']) && !empty($arProps['users'])) {
            if ($arProps['users'][0] != 'all') {
                $arUsers = [];
                foreach ($arProps['users'] as $user_id) {
                    $rsUser = CUser::GetByID($user_id);
                    $arUser = $rsUser->Fetch();
                    $arUsers[] = $arUser['LOGIN'];
                }
                if (!in_array('С сайта', $arUsers)) {
                    $arUsers[] = 'С сайта';
                }
                if (!empty($arUsers)) {
                    $filter['=UF_OPERATOR'] = $arUsers;
                }
            }
        }

        //делаем выборку из таблицы статистики
        Loader::includeModule("highloadblock");

        $result = [];

        $hlblock = HL\HighloadBlockTable::getById(HL_STATS)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();

        $data = $entity_data_class::getList(array(
            "select" => array("*"),
            "order" => array("ID" => "DESC"),
            "filter" => $filter
        ));

        while ($arData = $data->Fetch()) {
            $result[$arData['ID']] = $arData;
        }

        //отдаем результат
        if (!empty($result)) {
            return $result;
        } else {
            return false;
        }
    }

    //функция генерит файлик ексель из html таблиц и отдает ссылку на него
    public function getExcelAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (!empty($post)) {
            $arTables = $post;
            $htmlString = '';
            foreach ($arTables as $table) {
                foreach ($table as $tableData) {
                    $htmlString .= $tableData;
                }
            }
            if ($htmlString != '') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
                $spreadsheet = $reader->loadFromString($htmlString);
                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
                $writer->save('/site_stats.xls');
            }
            return AjaxJson::createSuccess([
                'data' => '/site_stats.xls',
            ]);
        } else {
            return AjaxJson::createError(null, false);
        }
    }

    //функция принимает
    public function getTableAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();

        //\Bitrix\Main\Diag\Debug::dumpToFile($post, 'getTableAction', 'class.txt');

        $stat_data = $this->getStatData($post);

        if (!empty($stat_data)) {

            $html = $this->createTableHeader($post);

            if (!empty($html)) {
                if (isset($post["settings"]) && !empty($post["settings"])) {
                    $tableCells = [];
                    $tableRows = [];
                    foreach ($post["settings"] as $tableType) {
                        switch ($tableType) {
                            case 'object_count':
                                $tableCells = $this->calculatePropertyValue('UF_OBJECT_ID', $stat_data);
                                $tableRows = $this->convertTableCells($tableCells, "COUNT");
                                $html .= $this->createObjectTable('Количество сданных объектов', 'Общее количество', $tableRows);
                                break;
                            case 'earnings':
                                $tableCells = $this->calculatePropertyValue('UF_ORDER_SUM', $stat_data);
                                $tableRows = $this->convertTableCells($tableCells, "ORDER_SUM");
                                $html .= $this->createObjectTable('Выручка', 'Общая выручка', $tableRows);
                                break;
                            case 'permits_count':
                                $tableCells = $this->calculatePropertyValue('UF_PERMIT_COUNT', $stat_data);
                                $tableRows = $this->convertTableCells($tableCells, ["BENEFIT_PERMIT_COUNT", "PERMIT_COUNT", "SUM"]);
                                $tableRows = $this->prepareTableData($tableRows, 'BENEFIT_PERMIT_COUNT', 'PERMIT_COUNT');
                                $html .= $this->createObjectTable(
                                    'Разрешения на посещения',
                                    'Разрешений всего',
                                    $tableRows,
                                    'Разрешение',
                                    'Льготное разрешение',
                                    "PERMIT_COUNT",
                                    "BENEFIT_PERMIT_COUNT"
                                );
                                break;
                            case 'stay_duration':
                                $tableCells = $this->calculatePropertyValue('UF_STAY_DURATION', $stat_data);
                                $tableRows = $this->convertTableCells($tableCells, ["COUPLE_DAY", "DAILY", "SUM"]);
                                $tableRows = $this->prepareTableData($tableRows, 'COUPLE_DAY', 'DAILY');
                                $html .= $this->createObjectTable(
                                    'Длительность пребывания',
                                    'Общее количество',
                                    $tableRows,
                                    'Суточное',
                                    'Дневное',
                                    'COUPLE_DAY',
                                    'DAILY'
                                );
                                break;
                        }
                    }
                }
            }
            return AjaxJson::createSuccess([
                'data' => $html,
            ]);
        } else {
            return AjaxJson::createError(null, false);
        }
    }

    public function getLocations(&$arResult, $params)
    {
        $locations = getLocationStructure()['LOCATION'];
        if($locations){
            foreach ($locations as $location){
                $arResult["LOCATIONS"][$location['ID']] = $location;
            }
        }
    }

    public function getObjects(&$arResult, $params)
    {
        $objects = getObjects();
        if($objects){
            foreach ($objects as $object){
                $arResult["OBJECTS"][$object['ID']] = $object;
            }
        }
    }

    public function getUsers(&$arResult, $params)
    {
        $filter = ["GROUPS_ID" => $params["USER_GROUPS"]];
        $rsUsers = CUser::GetList(($order = "desc"), $filter);
        while ($arUser = $rsUsers->Fetch()) {
            $arResult["USERS"][$arUser['ID']] = [
                'ID' => $arUser['ID'],
                'LOGIN' => $arUser['LOGIN'],
            ];
        }
    }

    public function init()
    {
        $this->getLocations($this->arResult, $this->arParams);
        $this->getObjects($this->arResult, $this->arParams);
        $this->getUsers($this->arResult, $this->arParams);

    }

    public function executeComponent()
    {
        $this->init();
        $this->includeComponentTemplate();
    }
}