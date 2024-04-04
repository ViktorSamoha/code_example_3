<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

use Bitrix\Main\Diag\Debug;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class AdminBookingList extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();
        return [
            'unsetObjectRent' => [
                'prefilters' => [],
            ],
            'deleteOrder' => [
                'prefilters' => [],
            ],
        ];
    }

    public function deleteOrderAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['ORDER_ID']) && isset($post['HLB_ORDER_ID'])) {
            if (Loader::includeModule("highloadblock")) {
                if ($post['IS_ROUTE']) {
                    $hlblock = HL\HighloadBlockTable::getById(HL_ROUTE_BOOKING_ID)->fetch();
                } else {
                    $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
                }
                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();
                $entity_data_class::Delete($post['HLB_ORDER_ID']);
            } else {
                return AjaxJson::createError(null, 'Ошибка подключения модуля highloadblock');
            }
            if (Loader::IncludeModule("iblock")) {
                /*global $DB;
                $DB->StartTransaction();
                if (!CIBlockElement::Delete($post['ORDER_ID'])) {
                    $DB->Rollback();
                    return AjaxJson::createError(null, 'Ошибка удаления элемента!');
                } else {
                    $DB->Commit();
                    return AjaxJson::createSuccess();
                }*/
                $el = new CIBlockElement;
                $res = $el->Update($post['ORDER_ID'], ["ACTIVE" => "N"]);
                if ($res) {
                    return AjaxJson::createSuccess();
                } else {
                    return AjaxJson::createError(null, 'Ошибка удаления элемента!');
                }
            } else {
                return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function unsetObjectRentAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['ID']) && isset($post['HLB_ORDER_ID']) && isset($post['ARRIVAL_DATE']) && isset($post['DEPARTURE_DATE'])) {
            if (Loader::includeModule("highloadblock")) {
                $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();
                $data = $entity_data_class::getList(array(
                    "select" => array("*"),
                    "order" => array("ID" => "DESC"),
                    "filter" => array("UF_OBJECT_ID" => $post['ID'], 'ID' => $post['HLB_ORDER_ID'])
                ));
                while ($arData = $data->Fetch()) {
                    $hlb_record_id = $arData['ID'];

                    /* $hl_arrival_date = DateTime::createFromFormat('d.m.Y H:i:s', $arData['UF_ARRIVAL_DATE']);
                     if (isset($arData['UF_DEPARTURE_DATE']) && $arData['UF_DEPARTURE_DATE'] != '') {
                         $hl_departure_date = DateTime::createFromFormat('d.m.Y H:i:s', $arData['UF_DEPARTURE_DATE']);
                     } else {
                         $hl_departure_date = '';
                     }
                     $arrival_date = DateTime::createFromFormat('d.m.Y H:i:s', \Bitrix\Main\Type\DateTime::createFromUserTime($post['ARRIVAL_DATE']));
                     if (!empty($post['DEPARTURE_DATE']) && !is_null($post['DEPARTURE_DATE']) && $post['DEPARTURE_DATE'] != " ") {
                         $departure_date = DateTime::createFromFormat('d.m.Y H:i:s', \Bitrix\Main\Type\DateTime::createFromUserTime($post['DEPARTURE_DATE']));
                     } else {
                         $departure_date = '';
                     }
                     if ($hl_arrival_date == $arrival_date && $departure_date == $hl_departure_date) {
                         $hlb_record_id = $arData['ID'];
                     }*/

                }
                if ($hlb_record_id) {
                    $entity_data_class::Delete($hlb_record_id);
                    if (!isset($arData['UF_DEPARTURE_DATE']) || $arData['UF_DEPARTURE_DATE'] == '') {
                        if (Loader::IncludeModule("iblock")) {
                            $res = CIBlockElement::SetPropertyValuesEx($post['ID'], false, array('CAN_BOOK' => 36,));
                            if (is_null($res)) {
                                $res = CIBlockElement::SetPropertyValuesEx($post['ORDER_ID'], false, array('ARCHIVE' => 'Y'));
                                if (is_null($res)) {
                                    return AjaxJson::createSuccess();
                                } else {
                                    return AjaxJson::createError(null, 'Ошибка добавления элемента в архив');
                                }
                            } else {
                                return AjaxJson::createError(null, 'Ошибка смены статуса объекта');
                            }
                        } else {
                            return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
                        }
                    } else {
                        return AjaxJson::createError(null, 'нет значений!');
                    }
                } else {
                    return AjaxJson::createError(null, 'Отсутствует запись в таблице забронированных объектов!');
                }
            } else {
                return AjaxJson::createError(null, 'Ошибка подключения модуля highloadblock');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function filterOrders(&$orders)
    {
        if ($orders) {
            if (Loader::includeModule("highloadblock")) {
                $arHlbOrderId = [];
                $arFoundOrders = [];
                $arNeedleOrderId = [];
                $arRouteHlbOrderId = [];
                foreach ($orders as $orderId => $orderData) {
                    if ($orderData['IS_ROUTE']) {
                        $arRouteHlbOrderId[$orderId] = $orderData['HLBLOCK_ORDER_ID'];
                    } else {
                        $arHlbOrderId[$orderId] = $orderData['HLBLOCK_ORDER_ID'];
                    }
                }
                if (!empty($arHlbOrderId)) {
                    $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
                    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                    $entity_data_class = $entity->getDataClass();
                    $data = $entity_data_class::getList(array(
                        "select" => array("ID"),
                        "order" => array("ID" => "DESC"),
                        "filter" => array("ID" => $arHlbOrderId)
                    ));
                    while ($arData = $data->Fetch()) {
                        $arFoundOrders[] = $arData["ID"];
                    }
                }
                if (!empty($arRouteHlbOrderId)) {
                    $hlblock = HL\HighloadBlockTable::getById(HL_ROUTE_BOOKING_ID)->fetch();
                    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                    $entity_data_class = $entity->getDataClass();
                    $data = $entity_data_class::getList(array(
                        "select" => array("ID"),
                        "order" => array("ID" => "DESC"),
                        "filter" => array("ID" => $arRouteHlbOrderId)
                    ));
                    while ($arData = $data->Fetch()) {
                        $arFoundOrders[] = $arData["ID"];
                    }
                }
                $arResult = array_intersect(array_merge($arRouteHlbOrderId, $arHlbOrderId), $arFoundOrders);
                foreach ($arResult as $orderId => $hlbId) {
                    $arNeedleOrderId[] = $orderId;
                }
                foreach ($orders as $orderId => $orderData) {
                    if (!in_array($orderId, $arNeedleOrderId)) {
                        unset($orders[$orderId]);
                    }
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getOrderList(&$arResult, $params)
    {
        $arOrders = [];
        $now = date('Y-m-d');
        $arSelect = array(
            "ID",
            'NAME',
            'PROPERTY_NAME',
            'PROPERTY_SURNAME',
            'PROPERTY_ARRIVAL_DATE',
            'PROPERTY_DEPARTURE_DATE',
            'PROPERTY_CHECK_IN_TIME',
            'PROPERTY_DEPARTURE_TIME',
            'PROPERTY_PERMISSION',
            'PROPERTY_ADULTS',
            'PROPERTY_BENIFICIARIES',
            'PROPERTY_BOOKING_OBJECT',
            'PROPERTY_BOOKING_TYPE',
            'PROPERTY_GUEST_CARS',
            'PROPERTY_HLBLOCK_ORDER_ID',
            'PROPERTY_ARCHIVE',
        );
        $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, 'ACTIVE'=>'Y');
        if (isset($params['FILTER_VALUE']) && !empty($params['FILTER_VALUE'])) {
            $arFilter['ID'] = $params['FILTER_VALUE']['ID'];
            $arFilter['>=DATE_CREATE'] = $params['FILTER_VALUE']['DATE'];
            if ($params['FILTER_VALUE']['ARCHIVE'] && $params['FILTER_VALUE']['ARCHIVE'] == 'Y') {
                //$arFilter['<=PROPERTY_DEPARTURE_DATE'] = $now;
                $arFilter['=PROPERTY_ARCHIVE'] = 'Y';
            } else {
                $arFilter['>=PROPERTY_DEPARTURE_DATE'] = $now;
            }
        } else {
            $arFilter['>=PROPERTY_DEPARTURE_DATE'] = $now;
        }
        $res = CIBlockElement::GetList(["ID" => 'DESC'], $arFilter, false, [], $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arOrders[] = [
                'ID' => $arFields['ID'],
                'NAME' => $arFields['NAME'],
                'USER_FIO' => $arFields['PROPERTY_NAME_VALUE'] . ' ' . $arFields['PROPERTY_SURNAME_VALUE'],
                'OBJECT_ID' => $arFields['PROPERTY_BOOKING_OBJECT_VALUE'],
                'BOOKING_TYPE' => $arFields['PROPERTY_BOOKING_TYPE_VALUE'],
                'ARRIVAL_DATE' => $arFields['PROPERTY_ARRIVAL_DATE_VALUE'],
                'DEPARTURE_DATE' => $arFields['PROPERTY_DEPARTURE_DATE_VALUE'],
                'CHECK_IN_TIME' => $arFields['PROPERTY_CHECK_IN_TIME_VALUE'],
                'DEPARTURE_TIME' => $arFields['PROPERTY_DEPARTURE_TIME_VALUE'],
                'TRANSPORT' => $arFields['PROPERTY_GUEST_CARS_VALUE'],
                'GUEST_COUNT' => $arFields['PROPERTY_ADULTS_VALUE'],
                'BENEFICIARIES' => $arFields['PROPERTY_BENIFICIARIES_VALUE'],
                'HLBLOCK_ORDER_ID' => $arFields['PROPERTY_HLBLOCK_ORDER_ID_VALUE'],
            ];
        }
        if (!empty($arOrders)) {

            foreach ($arOrders as &$order) {
                if (checkRoute($order['OBJECT_ID'])) {
                    $order['IS_ROUTE'] = true;
                } else {
                    $order['IS_ROUTE'] = false;
                }
            }
            if (!isset($params['FILTER_VALUE']['ARCHIVE']) || $params['FILTER_VALUE']['ARCHIVE'] == 'N') {
                $this->filterOrders($arOrders);
            }
            $currentDateTime = new DateTime();
            $currentDate = $currentDateTime->format("d.m.Y");
            $currentTime = $currentDateTime->format("H:i:s");
            foreach ($arOrders as $i => &$arOrder) {
                $arObject = getObjectById($arOrder['OBJECT_ID']);
                if ($arObject) {
                    $arOrder['OBJECT_NAME'] = $arObject['NAME'];
                    if ($arObject['SECTIONS']) {
                        foreach ($arObject['SECTIONS'] as $objectSection) {
                            if ($objectSection['DEPTH_LEVEL'] == 3) {
                                $arOrder['OBJECT_LOCATION'] = $objectSection['NAME'];
                                $arOrder['OBJECT_LOCATION_ID'] = $objectSection['ID'];
                            }
                        }
                    }
                }
                if ($params['FILTER_VALUE']['LOCATION_ID']) {
                    if ($arOrder['OBJECT_LOCATION_ID'] != $params['FILTER_VALUE']['LOCATION_ID']) {
                        unset($arOrders[$i]);
                    }
                }
                if ($params['FILTER_VALUE']['OBJECT_NAME']) {
                    if (!strpos(mb_strtolower($arOrder['OBJECT_NAME']), mb_strtolower($params['FILTER_VALUE']['OBJECT_NAME']))) {
                        unset($arOrders[$i]);
                    }
                }
                if (!empty($arOrder['TRANSPORT'])) {
                    $arTransportData = [];
                    foreach ($arOrder['TRANSPORT'] as $transportId) {
                        $arTransportData[] = getVehicleById($transportId);
                    }
                    if (!empty($arTransportData)) {
                        $arOrder['TRANSPORT'] = $arTransportData;
                    }
                }
                if ($arOrder['DEPARTURE_DATE'] == $currentDate) {
                    if (getTimeDiff($arOrder['DEPARTURE_TIME'], $currentTime) <= 2) {
                        $arOrder['RED'] = true;
                    }
                }
            }
            $arResult['ORDERS'] = $arOrders;
        }
    }

    public function executeComponent()
    {
        $this->getOrderList($this->arResult, $this->arParams);
        $this->IncludeComponentTemplate($this->componentPage);
    }
}