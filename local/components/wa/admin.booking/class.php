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

class WAAdminBooking extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();
        return [
            'formObjectFilter' => [
                'prefilters' => [],
            ],
            'onObjectSelect' => [
                'prefilters' => [],
            ],
            'recalculateOrderPrice' => [
                'prefilters' => [],
            ],
            'addCarIdField' => [
                'prefilters' => [],
            ],
            'saveOrder' => [
                'prefilters' => [],
            ],

        ];
    }

    public function insertOrderInToHlBlock(&$arOrder, $timeUnlimit, $deleteId = false, $isRoute = false)
    {
        if ($arOrder) {
            if (Loader::includeModule("highloadblock")) {
                if ($isRoute) {
                    $hlblock = HL\HighloadBlockTable::getById(HL_ROUTE_BOOKING_ID)->fetch();
                    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                    $entity_data_class = $entity->getDataClass();
                    $data = array(
                        "UF_OBJECT_ID" => $arOrder['BOOKING_OBJECT'],
                        "UF_ARRIVAL_DATE" => $arOrder['ARRIVAL_DATE'],
                        "UF_DEPARTURE_DATE" => $arOrder['DEPARTURE_DATE'],
                        "UF_PEOPLE_COUNT" => $arOrder['ADULTS'],
                    );
                    if ($deleteId) {
                        $entity_data_class::Delete($deleteId);
                    }
                    $result = $entity_data_class::add($data);
                    if (!$result->isSuccess()) {
                        \Bitrix\Main\Diag\Debug::dumpToFile(implode(', ', $result->getErrors()), 'insertOrderInToHlBlock', 'booking_error_log.txt');
                    } else {
                        $arOrder['HLBLOCK_ORDER_ID'] = $result->getId();
                    }
                } else {
                    $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
                    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                    $entity_data_class = $entity->getDataClass();
                    if ($deleteId) {
                        $entity_data_class::Delete($deleteId);
                    }
                    if ($timeUnlimit) {
                        if ($timeUnlimit == 'Да') {
                            $data = array(
                                "UF_OBJECT_ID" => $arOrder['BOOKING_OBJECT'],
                                "UF_ARRIVAL_DATE" => $arOrder['ARRIVAL_DATE'] . ' ' . $arOrder['CHECK_IN_TIME'],
                                "UF_DEPARTURE_DATE" => $arOrder['ARRIVAL_DATE'] . '23:00:00',
                            );
                            $arOrder['DEPARTURE_DATE'] = $arOrder['ARRIVAL_DATE'];
                            $arOrder['DEPARTURE_TIME'] = '22:00';
                        } else {
                            $data = array(
                                "UF_OBJECT_ID" => $arOrder['BOOKING_OBJECT'],
                                "UF_ARRIVAL_DATE" => $arOrder['ARRIVAL_DATE'] . ' ' . $arOrder['CHECK_IN_TIME'],
                                "UF_DEPARTURE_DATE" => $arOrder['DEPARTURE_DATE'] . ' ' . $arOrder['DEPARTURE_TIME'],
                            );
                        }
                        $result = $entity_data_class::add($data);
                        if (!$result->isSuccess()) {
                            \Bitrix\Main\Diag\Debug::dumpToFile(implode(', ', $result->getErrors()), 'insertOrderInToHlBlock', 'booking_error_log.txt');
                        } else {
                            $arOrder['HLBLOCK_ORDER_ID'] = $result->getId();
                        }
                    }
                }
            }
        }
    }

    public function saveOrderAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (!empty($post)) {
            $arProps = [];
            if (isset($post['BOOKING_OBJECT_ID']) && !empty($post['BOOKING_OBJECT_ID'])) {
                $arProps['BOOKING_OBJECT'] = $post['BOOKING_OBJECT_ID'];
            }
            if (isset($post['ARRIVAL_DATE']) && !empty($post['ARRIVAL_DATE'])) {
                $arProps['ARRIVAL_DATE'] = $post['ARRIVAL_DATE'];
            }
            if (isset($post['DEPARTURE_DATE']) && !empty($post['DEPARTURE_DATE'])) {
                $arProps['DEPARTURE_DATE'] = $post['DEPARTURE_DATE'];
            }
            if (isset($post['CHECK_IN_TIME']) && !empty($post['CHECK_IN_TIME'])) {
                $arProps['CHECK_IN_TIME'] = $post['CHECK_IN_TIME'];
            }
            if (isset($post['DEPARTURE_TIME']) && !empty($post['DEPARTURE_TIME'])) {
                $arProps['DEPARTURE_TIME'] = $post['DEPARTURE_TIME'];
            }
            if (isset($post['BOOKING_COST']) && !empty($post['BOOKING_COST'])) {
                $arProps['BOOKING_COST'] = $post['BOOKING_COST'];
            }
            if (isset($post['PERMISSION']) && !empty($post['PERMISSION'])) {
                $arProps['PERMISSION'] = $post['PERMISSION'];
            }
            if (isset($post['GUESTS_COUNT']) && !empty($post['GUESTS_COUNT'])) {
                $arProps['ADULTS'] = $post['GUESTS_COUNT'];
            }
            if (isset($post['BENIFICIARIES_COUNT']) && !empty($post['BENIFICIARIES_COUNT'])) {
                $arProps['BENIFICIARIES'] = $post['BENIFICIARIES_COUNT'];
            }
            if (isset($post['car-radio']) && $post['car-radio'] == 1) {
                if (isset($post['GUEST_CARS'])) {
                    for ($i = 0; $i < $post['GUEST_CARS']; $i++) {
                        $arProps['GUEST_CARS'][] = [
                            'VALUE' => $post['GUEST_CARS_K_' . ($i + 1)]
                        ];
                    }
                }
            }
            if (isset($post['ADMIN']) && !empty($post['ADMIN'])) {
                $arProps['BOOKING_TYPE'] = $post['ADMIN'];
            }
            if (isset($post['NAME']) && !empty($post['NAME'])) {
                $arProps['NAME'] = $post['NAME'];
            }
            if (isset($post['LAST_NAME']) && !empty($post['LAST_NAME'])) {
                $arProps['SURNAME'] = $post['LAST_NAME'];
            }
            if (isset($post['PHONE']) && !empty($post['PHONE'])) {
                $arProps['PHONE'] = $post['PHONE'];
            }
            if (isset($post['EMAIL']) && !empty($post['EMAIL'])) {
                $arProps['EMAIL'] = $post['EMAIL'];
            }
            if (isset($post['BOOKING_OBJECT_PRICE']) && !empty($post['BOOKING_OBJECT_PRICE'])) {
                $arProps['OBJECT_RENT_COST'] = $post['BOOKING_OBJECT_PRICE'];
            }
            if ($post['ORDER_ID']) {
                if (!empty($arProps)) {
                    if (Loader::includeModule("iblock")) {
                        if ($post['TIME_UNLIMIT_OBJECT']) {
                            $db_props = CIBlockElement::GetProperty(IB_BOOKING_LIST, $post['ORDER_ID'], array("sort" => "asc"), array("CODE" => "HLBLOCK_ORDER_ID"));
                            if ($ar_props = $db_props->Fetch()) {
                                $oldHlbId = $ar_props["VALUE"];
                            }
                            if ($oldHlbId) {
                                if ($post['IS_ROUTE']) {
                                    $this->insertOrderInToHlBlock($arProps, $post['TIME_UNLIMIT_OBJECT'], $oldHlbId, $post['IS_ROUTE']);
                                } else {
                                    $this->insertOrderInToHlBlock($arProps, $post['TIME_UNLIMIT_OBJECT'], $oldHlbId);
                                }
                            }
                        } elseif ($post['IS_ROUTE']) {
                            $db_props = CIBlockElement::GetProperty(IB_BOOKING_LIST, $post['ORDER_ID'], array("sort" => "asc"), array("CODE" => "HLBLOCK_ORDER_ID"));
                            if ($ar_props = $db_props->Fetch()) {
                                $oldHlbId = $ar_props["VALUE"];
                            }
                            if ($oldHlbId) {
                                $this->insertOrderInToHlBlock($arProps, $post['TIME_UNLIMIT_OBJECT'], $oldHlbId, $post['IS_ROUTE']);
                            }
                        }
                        CIBlockElement::SetPropertyValuesEx($post['ORDER_ID'], IB_BOOKING_LIST, $arProps);
                        $res = CIBlockElement::GetByID($post['ORDER_ID']);
                        $element_code = $link = false;
                        if ($ar_res = $res->GetNext()) {
                            $element_code = $ar_res['CODE'];
                        }
                        if ($element_code) {
                            $link = '/receipt/order/' . $element_code . '/';
                        }
                        if ($arProps['EMAIL']) {
                            $arObject = getObjectById($arProps['BOOKING_OBJECT']);
                            sendEmail(
                                'NEW_ORDER_NOTIFICATION',
                                13,
                                [
                                    'EMAIL' => $arProps['EMAIL'],
                                    'LOCATION' => $arObject['SECTIONS'][2]['NAME'],
                                    'OBJECT' => $arObject['NAME'],
                                    'USER_FIO' => $arProps['SURNAME'] . ' ' . $arProps['NAME'],
                                    'USER_PHONE' => $arProps['PHONE'],
                                    'BOOKING_FROM' => $arProps['ARRIVAL_DATE'] . ' ' . $arProps['CHECK_IN_TIME'],
                                    'BOOKING_TO' => $arProps['DEPARTURE_DATE'] . ' ' . $arProps['DEPARTURE_TIME'],
                                    'RECEIPT_LINK' => $link,
                                ]);
                        }
                        return AjaxJson::createSuccess([
                            'blank_link' => $link
                        ]);
                    } else {
                        return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
                    }
                } else {
                    return AjaxJson::createError(null, 'Пустой массив свойств');
                }
            } else {
                if (!empty($arProps)) {
                    if (Loader::includeModule("iblock")) {
                        global $USER;
                        $now = time();
                        $el = new CIBlockElement;
                        if ($post['BOOKING_FAST']) {
                            $elementName = "Быстрая бронь " . $post['NAME'] . ' ' . $post['LAST_NAME'];
                        } else {
                            $elementName = "Бронь " . $post['NAME'] . ' ' . $post['LAST_NAME'];
                        }
                        $hash_string = $elementName . $now;
                        $order_unique_code = stringToHash($hash_string);
                        $arProps['QR_CODE'] = getQrCode($order_unique_code, 'order');
                        if ($post['TIME_UNLIMIT_OBJECT']) {
                            $this->insertOrderInToHlBlock($arProps, $post['TIME_UNLIMIT_OBJECT']);
                        } elseif ($post['IS_ROUTE']) {
                            $this->insertOrderInToHlBlock($arProps, $post['TIME_UNLIMIT_OBJECT'], false, $post['IS_ROUTE']);
                        }
                        //TODO:СДЕЛАТЬ ССЫЛКУ НА ОПЛАТУ
                        $arLoadProductArray = array(
                            "MODIFIED_BY" => $USER->GetID(),
                            "IBLOCK_SECTION_ID" => false,
                            "IBLOCK_ID" => IB_BOOKING_LIST,
                            "PROPERTY_VALUES" => $arProps,
                            'ACTIVE_FROM' => $post['ACTIVE_FROM'],
                            'CODE' => $order_unique_code,
                            "NAME" => $elementName,
                            "ACTIVE" => "Y",
                        );
                        if ($newOrderId = $el->Add($arLoadProductArray)) {
                            $res = CIBlockElement::GetByID($newOrderId);
                            $element_code = $link = false;
                            if ($ar_res = $res->GetNext()) {
                                $element_code = $ar_res['CODE'];
                            }
                            if ($element_code) {
                                $link = '/receipt/order/' . $element_code . '/';
                            }
                            if ($link) {
                                if ($arProps['EMAIL']) {
                                    $arObject = getObjectById($arProps['BOOKING_OBJECT']);
                                    sendEmail(
                                        'NEW_ORDER_NOTIFICATION',
                                        13,
                                        [
                                            'EMAIL' => $arProps['EMAIL'],
                                            'LOCATION' => $arObject['SECTIONS'][2]['NAME'],
                                            'OBJECT' => $arObject['NAME'],
                                            'USER_FIO' => $arProps['SURNAME'] . ' ' . $arProps['NAME'],
                                            'USER_PHONE' => $arProps['PHONE'],
                                            'BOOKING_FROM' => $arProps['ARRIVAL_DATE'] . ' ' . $arProps['CHECK_IN_TIME'],
                                            'BOOKING_TO' => $arProps['DEPARTURE_DATE'] . ' ' . $arProps['DEPARTURE_TIME'],
                                            'RECEIPT_LINK' => $link,
                                        ]);
                                }
                                //TODO:СДЕЛАТЬ ИНФОРМИРОВАНИЕ В ТГ
                                return AjaxJson::createSuccess([
                                    'blank_link' => $link
                                ]);
                            } else {
                                return AjaxJson::createError(null, 'Ошибка формирования ссылки');
                            }
                        } else {
                            return AjaxJson::createError(null, $el->LAST_ERROR);
                        }
                    } else {
                        return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
                    }
                } else {
                    return AjaxJson::createError(null, 'Пустой массив свойств');
                }
            }
        } else {
            return AjaxJson::createError(null, 'Нет значений');
        }
    }

    public function addCarIdFieldAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['COUNT'])) {
            $html = '';
            for ($i = 0; $i < $post['COUNT']; $i++) {
                $html .= '
                    <div class="input">
                                <label for="GUEST_CARS_K_' . ($i + 1) . '" class="input-label">Номер
                                    автомобиля ' . ($i + 1) . '</label>
                                <input type="text" name="GUEST_CARS_K_' . ($i + 1) . '" size="30"
                                       value="">
                            </div>
                ';
            }
            if ($html != '') {
                return AjaxJson::createSuccess([
                    'html' => $html,
                ]);
            } else {
                return AjaxJson::createError(null, 'Нет значений');
            }
        } else {
            return AjaxJson::createError(null, 'Нет значений');
        }
    }

    public function recalculateOrderPriceAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['PERMISSION']) && isset($post['PRICE']) && isset($post['GUESTS']) && isset($post['BENEFIT'])) {
            $sum = $post['GUESTS'] - $post['BENEFIT'];
            if ($post['PERMISSION'] == 2) {
                $sum *= VISIT_PERMISSION_COST;
            }
            $sum += $post['PRICE'];
            return AjaxJson::createSuccess([
                'sum' => $sum,
            ]);
        } else {
            return AjaxJson::createError(null, 'Нет значений');
        }
    }

    /*    public function checkRoute($objectId)
        {
            if ($objectId) {
                $sections = getObjectSections($objectId);
                if ($sections) {
                    $result = false;
                    foreach ($sections as $section) {
                        if ($section['ID'] == TOURISTS_ROUTS_SECTION_ID) {
                            $result = true;
                        }
                    }
                    return $result;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }*/

    public function getObjectTimeFormBlock($type, $isRoute)
    {
        $html = false;
        if ($type) {
            switch ($type) {
                case 'Нет':
                    $html = '<div class="input-group" id="booking-period">
                        <div class="radio-group" id="time-select-radio">
                            <div class="radio">
                                <input type="radio" id="radio_07" data-period="couple"
                                       name="radio" checked>
                                <label for="radio_07">
                                    <div class="radio_text">На несколько суток</div>
                                </label>
                            </div>
                            <div class="radio">
                                <input type="radio" id="radio_08" data-period="day"
                                       name="radio">
                                <label for="radio_08">
                                    <div class="radio_text">Дневное пребывание</div>
                                </label>
                            </div>
                        </div>
                        <div class="input input--sm">
                                    <label for="" class="input-label">Дата оформления <span class="color-red">*</span></label>
                                    <input type="text" value="' . date('d.m.Y') . '" name="ACTIVE_FROM" class="input-date" required="" readonly="">
                                </div>
                    </div>
                    <div class="input-group">
                        <div class="m-input-dates m-input-dates--md js-input-date-group">
                            <div class="m-input-date-block">
                                <label for="" class="input-label">Дата заезда <span
                                            class="color-red">*</span></label>
                                <input type="text" class="input-date" name="ARRIVAL_DATE"
                                       size="25"
                                       value=""
                                       autocomplete="off">
                            </div>
                            <div class="m-input-date-block">
                                <label for="" class="input-label">Дата выезда <span
                                            class="color-red">*</span></label>
                                <input type="text" class="input-date second-range-input"
                                       name="DEPARTURE_DATE"
                                       size="25"
                                       value=""
                                       autocomplete="off">
                            </div>
                        </div>
                        <div class="m-input-dates m-input-dates--md">
                            <div class="m-input-date-block">
                                <label for="">Время заезда</label>
                                <div class="custom-select custom-select--sm" id="arrival-time-select">
                                    <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время заезда"
                                          data-selected-id="">Время заезда</span>
                                        <svg class="custom-select_icon" width="14" height="8"
                                             viewBox="0 0 14 8"
                                             fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                                        </svg>
                                    </div>
                                    <div class="custom-select_body">
                                        <div class="custom-select_item" data-id="8:00">8:00</div>
                                        <div class="custom-select_item" data-id="9:00">9:00</div>
                                        <div class="custom-select_item" data-id="10:00">10:00</div>
                                        <div class="custom-select_item" data-id="11:00">11:00</div>
                                        <div class="custom-select_item" data-id="12:00">12:00</div>
                                        <div class="custom-select_item" data-id="13:00">13:00</div>
                                        <div class="custom-select_item" data-id="14:00">14:00</div>
                                        <div class="custom-select_item" data-id="15:00">15:00</div>
                                        <div class="custom-select_item" data-id="16:00">16:00</div>
                                        <div class="custom-select_item" data-id="17:00">17:00</div>
                                        <div class="custom-select_item" data-id="18:00">18:00</div>
                                        <div class="custom-select_item" data-id="19:00">19:00</div>
                                        <div class="custom-select_item" data-id="20:00">20:00</div>
                                        <div class="custom-select_item" data-id="21:00">21:00</div>
                                        <div class="custom-select_item" data-id="22:00">22:00</div>
                                        <div class="custom-select_item" data-id="23:00">23:00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="m-input-date-block">
                                <label for="">Время выезда</label>
                                <div class="custom-select custom-select--sm" id="departure-time-select">
                                    <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время выезда"
                                          data-selected-id="">Время выезда</span>
                                        <svg class="custom-select_icon" width="14" height="8"
                                             viewBox="0 0 14 8"
                                             fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                                        </svg>
                                    </div>
                                    <div class="custom-select_body">
                                        <div class="custom-select_item" data-id="8:00">8:00</div>
                                        <div class="custom-select_item" data-id="9:00">9:00</div>
                                        <div class="custom-select_item" data-id="10:00">10:00</div>
                                        <div class="custom-select_item" data-id="11:00">11:00</div>
                                        <div class="custom-select_item" data-id="12:00">12:00</div>
                                        <div class="custom-select_item" data-id="13:00">13:00</div>
                                        <div class="custom-select_item" data-id="14:00">14:00</div>
                                        <div class="custom-select_item" data-id="15:00">15:00</div>
                                        <div class="custom-select_item" data-id="16:00">16:00</div>
                                        <div class="custom-select_item" data-id="17:00">17:00</div>
                                        <div class="custom-select_item" data-id="18:00">18:00</div>
                                        <div class="custom-select_item" data-id="19:00">19:00</div>
                                        <div class="custom-select_item" data-id="20:00">20:00</div>
                                        <div class="custom-select_item" data-id="21:00">21:00</div>
                                        <div class="custom-select_item" data-id="22:00">22:00</div>
                                        <div class="custom-select_item" data-id="23:00">23:00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                    break;
                case 'Да':
                    $html = '<div class="form-block form-block--mb30">
                        <h3 class="form-block_title">Дата и время заезда</h3>
                        <div id="date-time-select-block">
                            <div class="input-group">
                            <div class="input">
                                    <label for="" class="input-label">Дата оформления <span class="color-red">*</span></label>
                                    <input type="text" value="' . date('d.m.Y') . '" name="ACTIVE_FROM" class="input-date" required="" readonly="">
                                </div>
                                <div class="m-input-dates m-input-dates--md">
                                    <div class="m-input-date-block">
                                        <label for="" class="input-label">Дата заезда</label>
                                        <input type="text" class="input-date flatpickr-input active" name="ARRIVAL_DATE" size="25" required="" autocomplete="off" readonly="readonly">
                                    </div>
                                    <div class="m-input-date-block">
                                        <label for="" class="input-label">Время заезда</label>
                                        <div class="custom-select custom-select--sm" id="arrival-time-select">
                                            <div class="custom-select_head">
                                                    <span class="custom-select_title" data-default-value="Время заезда" data-selected-id="">Время заезда</span>
                                                <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 1L7 7L13 1" stroke="#000"></path>
                                                </svg>
                                            </div>
                                            <div class="custom-select_body"><div class="custom-select_item" data-id="8:00">8:00</div>
                                            <div class="custom-select_item" data-id="9:00">9:00</div>
                                            <div class="custom-select_item" data-id="10:00">10:00</div>
                                            <div class="custom-select_item" data-id="11:00">11:00</div>
                                            <div class="custom-select_item" data-id="12:00">12:00</div>
                                            <div class="custom-select_item" data-id="13:00">13:00</div>
                                            <div class="custom-select_item" data-id="14:00">14:00</div>
                                            <div class="custom-select_item" data-id="15:00">15:00</div>
                                            <div class="custom-select_item" data-id="16:00">16:00</div>
                                            <div class="custom-select_item" data-id="17:00">17:00</div>
                                            <div class="custom-select_item" data-id="18:00">18:00</div>
                                            <div class="custom-select_item" data-id="19:00">19:00</div>
                                            <div class="custom-select_item" data-id="20:00">20:00</div>
                                            <div class="custom-select_item" data-id="21:00">21:00</div>
                                            <div class="custom-select_item" data-id="22:00">22:00</div>
                                            <div class="custom-select_item" data-id="23:00">23:00</div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                    break;

            }
        } else {
            if ($isRoute) {
                $html = '
                    <div class="input-group">
                    <div class="input input--md">
                                    <label for="" class="input-label">Дата оформления <span class="color-red">*</span></label>
                                    <input type="text" value="' . date('d.m.Y') . '" name="ACTIVE_FROM" class="input-date" required="" readonly="">
                                </div>
                        <div class="m-input-dates m-input-dates--md js-input-date-group">
                            <div class="m-input-date-block">
                                <label for="" class="input-label">Дата заезда <span
                                            class="color-red">*</span></label>
                                <input type="text" class="input-date" name="ARRIVAL_DATE"
                                       size="25"
                                       value=""
                                       autocomplete="off">
                            </div>
                            <div class="m-input-date-block">
                                <label for="" class="input-label">Дата выезда <span
                                            class="color-red">*</span></label>
                                <input type="text" class="input-date second-range-input"
                                       name="DEPARTURE_DATE"
                                       size="25"
                                       value=""
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>';
            }
        }
        return $html;
    }

    public function setObjectPrice(&$arObject, $isRoute = false)
    {
        if ($arObject) {
            if ($isRoute) {
                if ($arObject['OBJECT_COST']) {
                    $arObject['BOOKING_OBJECT_PRICE'] = $arObject['OBJECT_COST'];
                }
                $arObject['BOOKING_OBJECT_PERIOD'] = 'couple';
            } else {
                if ($arObject['TIME_INTERVAL'] && is_array($arObject['TIME_INTERVAL']) && count($arObject['TIME_INTERVAL']) > 0) {
                    if (isset($arObject['TIME_INTERVAL'][PROPERTY_TIME_INTERVAL_COUPLE])) {
                        if ($arObject['OBJECT_COST']) {
                            $arObject['BOOKING_OBJECT_PRICE'] = $arObject['OBJECT_COST'];
                        }
                        $arObject['BOOKING_OBJECT_PERIOD'] = 'couple';
                    } else {
                        if ($arObject['OBJECT_DAILY_COST']) {
                            $arObject['BOOKING_OBJECT_PRICE'] = $arObject['OBJECT_DAILY_COST'];
                        }
                        $arObject['BOOKING_OBJECT_PERIOD'] = 'day';
                    }
                }
                if ($arObject['TIME_UNLIMIT'] && $arObject['TIME_UNLIMIT'] == 'Да') {
                    if ($arObject['FIXED_COST']) {
                        $arObject['BOOKING_OBJECT_PRICE'] = $arObject['FIXED_COST'];
                    }
                    $arObject['BOOKING_OBJECT_PERIOD'] = 'day';
                }
            }
        }
    }

    public function onObjectSelectAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['OBJECT_ID'])) {
            if (Loader::includeModule("iblock")) {
                $arData = [];
                $arSelect = array(
                    "ID",
                    "NAME",
                    'PROPERTY_TIME_INTERVAL',
                    'PROPERTY_CAPACITY_MAXIMUM',
                    'PROPERTY_CAPACITY_ESTIMATED',
                    'PROPERTY_PRICE',
                    'PROPERTY_TIME_UNLIMIT_OBJECT',
                    'PROPERTY_COST_PER_PERSON',
                    'PROPERTY_OBJECT_COST',
                    'PROPERTY_OBJECT_DAILY_COST',
                    'PROPERTY_COST_PER_PERSON_ONE_DAY',
                    'PROPERTY_FIXED_COST',
                    'PROPERTY_CAR_POSSIBILITY',
                    'PROPERTY_CAR_CAPACITY',
                    'PROPERTY_DAILY_TRAFFIC',
                );
                $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, 'ID' => $post['OBJECT_ID']);
                $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arData = [
                        'ID' => $arFields['ID'],
                        'TIME_INTERVAL' => $arFields['PROPERTY_TIME_INTERVAL_VALUE'],
                        'CAPACITY_MAXIMUM' => $arFields['PROPERTY_CAPACITY_MAXIMUM_VALUE'],
                        'CAPACITY_ESTIMATED' => $arFields['PROPERTY_CAPACITY_ESTIMATED_VALUE'],
                        'PRICE' => $arFields['PROPERTY_PRICE_VALUE'],
                        'TIME_UNLIMIT' => $arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'],
                        'COST_PER_PERSON' => $arFields['PROPERTY_COST_PER_PERSON_VALUE'],
                        'OBJECT_COST' => $arFields['PROPERTY_OBJECT_COST_VALUE'],
                        'OBJECT_DAILY_COST' => $arFields['PROPERTY_OBJECT_DAILY_COST_VALUE'],
                        'COST_PER_PERSON_ONE_DAY' => $arFields['PROPERTY_COST_PER_PERSON_ONE_DAY_VALUE'],
                        'FIXED_COST' => $arFields['PROPERTY_FIXED_COST_VALUE'],
                        'CAR_POSSIBILITY' => $arFields['PROPERTY_CAR_POSSIBILITY_VALUE'],
                        'CAR_CAPACITY' => $arFields['PROPERTY_CAR_CAPACITY_VALUE'],
                        'DAILY_TRAFFIC' => $arFields['PROPERTY_DAILY_TRAFFIC_VALUE'],
                    ];
                }
                if (!empty($arData)) {
                    $isRoute = checkRoute($arData['ID']);
                    if ($isRoute) {
                        $arData['IS_ROUTE'] = true;
                    }
                    $this->setObjectPrice($arData, $isRoute);
                    $formHtmlBlock = $this->getObjectTimeFormBlock($arData['TIME_UNLIMIT'], $isRoute);
                    return AjaxJson::createSuccess([
                        'params' => $arData,
                        'form_html' => $formHtmlBlock
                    ]);
                } else {
                    return AjaxJson::createError(null, 'Нет значений');
                }
            } else {
                return AjaxJson::createError(null, 'Не удалось подключить модуль iblock');
            }
        } else {
            return AjaxJson::createError(null, 'Нет значений');
        }
    }

    public function formObjectFilterAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['LOCATION_ID']) || isset($post['CATEGORY_ID']) || isset($post['ARRIVAL_DATE']) || isset($post['DEPARTURE_DATE'])) {
            if (Loader::includeModule("iblock")) {
                $arObjects = [];
                $arObjId = [];
                $arFilter = array('IBLOCK_ID' => IB_LOCATIONS, 'GLOBAL_ACTIVE' => 'Y');
                if (isset($post['LOCATION_ID'])) {
                    $arFilter['SECTION_ID'] = $post['LOCATION_ID'];
                } elseif (isset($post['CATEGORY_ID'])) {
                    $arFilter['SECTION_ID'] = $post['CATEGORY_ID'];
                    $db_list = CIBlockSection::GetList(array(), $arFilter, true);
                    while ($ar_result = $db_list->GetNext()) {
                        $arSectionFilterId[] = $ar_result['ID'];
                    }
                    if (isset($arSectionFilterId) && !empty($arSectionFilterId)) {
                        $arFilter['SECTION_ID'] = $arSectionFilterId;
                    }
                }
                if (isset($post['PERIOD'])) {
                    if ($post['PERIOD'] == 'day') {
                        $arFilter['=PROPERTY_TIME_INTERVAL'] = PROPERTY_TIME_INTERVAL_DAY;
                        $period_filter = 'day';
                    } else {
                        $arFilter['=PROPERTY_TIME_INTERVAL'] = PROPERTY_TIME_INTERVAL_COUPLE;
                        $period_filter = 'couple';
                    }
                }
                $arSelect = array("ID", "NAME", "PROPERTY_TIME_UNLIMIT_OBJECT", "PROPERTY_CAR_POSSIBILITY", "PROPERTY_CAR_CAPACITY");
                $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $prop = CIBlockElement::GetProperty(IB_LOCATIONS, $arFields['ID'], array("sort" => "asc"), array("CODE" => "TIME_INTERVAL"));
                    $arPropVal = [];
                    while ($prop_ob = $prop->GetNext()) {
                        if (isset($prop_ob['VALUE']) && isset($prop_ob['VALUE_ENUM'])) {
                            $arPropVal[] = [
                                'ID' => $prop_ob['VALUE'],
                                'NAME' => $prop_ob['VALUE_ENUM'],
                            ];
                        }
                    }
                    $arObjects[$arFields['ID']] = [
                        'ID' => $arFields['ID'],
                        'NAME' => $arFields['NAME']
                    ];
                    if (!empty($arPropVal)) {
                        if (count($arPropVal) == 2) {
                            $arObjects[$arFields['ID']]["TIME_INTERVAL"]['TYPE'] = 'multi';
                            $arObjects[$arFields['ID']]["TIME_INTERVAL"]['VALUE'] = 'null';
                        } else {
                            $arObjects[$arFields['ID']]["TIME_INTERVAL"]['TYPE'] = 'single';
                            $arObjects[$arFields['ID']]["TIME_INTERVAL"]['VALUE'] = $arPropVal[0]['ID'];
                        }
                    }
                    if (isset($arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'])) {
                        if ($arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'] == 'Да') {
                            $arObjects[$arFields['ID']]['TIME_LIMIT'] = 'Y';
                        } else {
                            $arObjects[$arFields['ID']]['TIME_LIMIT'] = 'N';
                        }
                    } else {
                        $arObjects[$arFields['ID']]['TIME_LIMIT'] = 'N';
                    }
                    if (isset($arFields['PROPERTY_CAR_POSSIBILITY_VALUE'])) {
                        if ($arFields['PROPERTY_CAR_POSSIBILITY_VALUE'] == 'Да') {
                            $arObjects[$arFields['ID']]['CAR_POSSIBILITY'] = 'Y';
                        } else {
                            $arObjects[$arFields['ID']]['CAR_POSSIBILITY'] = 'N';
                        }
                    } else {
                        $arObjects[$arFields['ID']]['CAR_POSSIBILITY'] = 'N';
                    }
                    if (isset($arFields['PROPERTY_CAR_CAPACITY_VALUE'])) {
                        $arObjects[$arFields['ID']]['CAR_CAPACITY'] = $arFields['PROPERTY_CAR_CAPACITY_VALUE'];
                    } else {
                        $arObjects[$arFields['ID']]['CAR_CAPACITY'] = '';
                    }
                    $arObjId[] = $arFields['ID'];
                }
            } else {
                return AjaxJson::createError(null, 'Не удалось подключить модуль iblock');
            }

            //\Bitrix\Main\Diag\Debug::dumpToFile($arObjects, $varName = '$arObjects', $fileName = 'formObjectFilterAction_log.txt');

            if (Loader::includeModule("highloadblock")) {
                if ((isset($post['ARRIVAL_DATE']) && !empty($post['ARRIVAL_DATE'])) && (isset($post['DEPARTURE_DATE']) && !empty($post['DEPARTURE_DATE'])) && !empty($arObjects)) {
                    $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
                    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                    $entity_data_class = $entity->getDataClass();
                    $now = new DateTime();
                    $first_half_day = [
                        0 => '8:00:00',
                        1 => '9:00:00',
                        2 => '10:00:00',
                        3 => '11:00:00',
                        4 => '12:00:00',
                        5 => '13:00:00',
                        6 => '14:00:00',
                    ];
                    $second_half_day = [
                        7 => '15:00:00',
                        8 => '16:00:00',
                        9 => '17:00:00',
                        10 => '18:00:00',
                        11 => '19:00:00',
                        12 => '20:00:00',
                        13 => '21:00:00',
                        14 => '22:00:00',
                    ];
                    $arItems = $arObjId;
                    $arItemsDates = [];
                    $obj_in_hl = [];
                    $_unset = [];
                    $data = $entity_data_class::getList(array(
                        "select" => array("*"),
                        "order" => array("ID" => "DESC"),
                        "filter" => array("UF_OBJECT_ID" => $arObjId)
                    ));
                    while ($arData = $data->Fetch()) {
                        if (in_array($arData["UF_OBJECT_ID"], $arObjId)) {
                            $obj_in_hl[] = $arData["UF_OBJECT_ID"];
                        }
                        $arDates = _get_dates($arData['UF_ARRIVAL_DATE'], $arData['UF_DEPARTURE_DATE']);
                        foreach ($arDates as $date) {
                            $booked_dates[] = $date;
                        }
                        if (isset($arItemsDates[$arData['UF_OBJECT_ID']])) {
                            $arItemsDates[$arData['UF_OBJECT_ID']] = array_merge($arItemsDates[$arData['UF_OBJECT_ID']], createSelectedDateArr($booked_dates));
                        } else {
                            $arItemsDates[$arData['UF_OBJECT_ID']] = createSelectedDateArr($booked_dates);
                        }
                        unset($arDates, $booked_dates);
                    }
                    if (isset($post['ARRIVAL_TIME'])) {
                        if (isset($post['DEPARTURE_TIME'])) {
                            $filter_dates = _get_dates($post['ARRIVAL_DATE'] . ' ' . $post['ARRIVAL_TIME'] . ':00', $post['DEPARTURE_DATE'] . ' ' . $post['DEPARTURE_TIME'] . ':00');
                        } else {
                            $filter_dates = _get_dates($post['ARRIVAL_DATE'] . ' ' . $post['ARRIVAL_TIME'] . ':00', $post['DEPARTURE_DATE'] . ' 00:00:00');
                        }
                    } elseif (isset($post['DEPARTURE_TIME'])) {
                        $filter_dates = _get_dates($post['ARRIVAL_DATE'] . ' 00:00:00', $post['DEPARTURE_DATE'] . ' ' . $post['DEPARTURE_TIME'] . ':00');
                    } else {
                        $filter_dates = _get_dates($post['ARRIVAL_DATE'] . ' 00:00:00', $post['DEPARTURE_DATE'] . ' 00:00:00');
                    }
                    $filter_dates = createSelectedDateArr($filter_dates);
                    $filter_date_count = count($filter_dates);
                    $un_unset = [];
                    foreach ($arItemsDates as $item_id => $date_time) {
                        foreach ($date_time as $date => $time) {
                            foreach ($filter_dates as $f_date => $f_date_time) {
                                if (DateTime::createFromFormat('d.m.Y', $f_date)->format('d.m.Y') == DateTime::createFromFormat('d.m.Y', $date)->format('d.m.Y')) {
                                    $_unset[$item_id][] = $date;
                                }
                            }
                        }
                    }
                    foreach ($un_unset as $id) {
                        foreach ($_unset as $u_id => $unset_date) {
                            if ($u_id == $id) {
                                unset($_unset[$u_id]);
                            }
                        }
                    }
                    foreach ($_unset as $id => $unset_date) {
                        if (isset($period_filter)) {
                            continue;
                        } else {
                            if (count($unset_date) != $filter_date_count) {
                                unset($_unset[$id]);
                            }
                        }
                    }
                    if (!empty($_unset)) {
                        foreach ($_unset as $id => $unset_dates) {
                            foreach ($arItems as $i => $item_id) {
                                if ($item_id == $id) {
                                    unset($arItems[$i]);
                                }
                            }
                        }
                    }
                    foreach ($arObjects as &$obj) {
                        if (!in_array($obj['ID'], $arItems)) {
                            unset($arObjects[$obj['ID']]);
                        }
                    }
                }
            } else {
                return AjaxJson::createError(null, 'Не удалось подключить модуль highloadblock');
            }
            if (isset($arObjects) && !empty($arObjects)) {

                $html = '<div class="form-block form-block--mb30">
                    <h3 class="form-block_title">Выберите доступный объект</h3>
                    <div class="select-block select-block--lg">
                        <div class="custom-select" id="object-select">
                            <div class="custom-select_head">
                                <span class="custom-select_title" data-selected-id="">Выберите доступный объект</span>
                                <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                     fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L7 7L13 1" stroke="#000"/>
                                </svg>
                            </div>
                            <div class="custom-select_body">';
                foreach ($arObjects as $object):
                    $html .= '<div class="custom-select_item"
                                         data-id="' . $object['ID'] . '"
                                         >' . $object['NAME'] . '</div>';
                endforeach;
                $html .= '</div>
                        </div>
                    </div>
                </div>';
                return AjaxJson::createSuccess([
                    'html' => $html,
                ]);
            } else {
                return AjaxJson::createError(null, 'Нет значений');
            }
        } else {
            return AjaxJson::createError(null, 'Нет значений');
        }
    }

    public function getOrderData($params, &$arResult)
    {
        if ($params['ORDER_ID']) {
            if (Loader::includeModule("iblock")) {
                $arOrder = [];
                $arSelect = array(
                    "ID",
                    "NAME",
                    "PROPERTY_BOOKING_OBJECT",
                    "DATE_ACTIVE_FROM",
                    "PROPERTY_ARRIVAL_DATE",
                    "PROPERTY_DEPARTURE_DATE",
                    "PROPERTY_CHECK_IN_TIME",
                    "PROPERTY_DEPARTURE_TIME",
                    "PROPERTY_NAME",
                    "PROPERTY_SURNAME",
                    "PROPERTY_PERMISSION",
                    "PROPERTY_ADULTS",
                    "PROPERTY_BENIFICIARIES",
                    "PROPERTY_EMAIL",
                    "PROPERTY_PHONE",
                    "PROPERTY_IS_PAYED",
                    "PROPERTY_BOOKING_COST",
                    "PROPERTY_GUEST_CARS",
                );
                $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, 'ID' => $params['ORDER_ID']);
                $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arOrder = [
                        'ID' => $arFields['ID'],
                        'DATE_INSERT' => $arFields['DATE_ACTIVE_FROM'],
                        'BOOKING_OBJECT' => getObjectById($arFields['PROPERTY_BOOKING_OBJECT_VALUE']),
                        'ARRIVAL_DATE' => $arFields['PROPERTY_ARRIVAL_DATE_VALUE'],
                        'DEPARTURE_DATE' => $arFields['PROPERTY_DEPARTURE_DATE_VALUE'],
                        'CHECK_IN_TIME' => $arFields['PROPERTY_CHECK_IN_TIME_VALUE'],
                        'DEPARTURE_TIME' => $arFields['PROPERTY_DEPARTURE_TIME_VALUE'],
                        'USER_NAME' => $arFields['PROPERTY_NAME_VALUE'],
                        'USER_SURNAME' => $arFields['PROPERTY_SURNAME_VALUE'],
                        'USER_EMAIL' => $arFields['PROPERTY_EMAIL_VALUE'],
                        'USER_PHONE' => $arFields['PROPERTY_PHONE_VALUE'],
                        'PERMISSION' => $arFields['PROPERTY_PERMISSION_VALUE'],
                        'GUESTS_COUNT' => $arFields['PROPERTY_ADULTS_VALUE'],
                        'BENIFICIARIES_COUNT' => $arFields['PROPERTY_BENIFICIARIES_VALUE'],
                        'IS_PAYED' => $arFields['PROPERTY_IS_PAYED_VALUE'],
                        'BOOKING_COST' => $arFields['PROPERTY_BOOKING_COST_VALUE'],
                        'GUEST_CARS' => $arFields['PROPERTY_GUEST_CARS_VALUE'],
                    ];
                }
                if (!empty($arOrder)) {
                    if (checkRoute($arOrder['BOOKING_OBJECT']['ID'])) {
                        $arOrder['BOOKING_OBJECT']['IS_ROUTE'] = true;
                    } else {
                        $arOrder['BOOKING_OBJECT']['IS_ROUTE'] = false;
                    }
                    $arResult['ORDER_DATA'] = $arOrder;
                    $arResult['LOCATION_OBJECTS'] = getObjects($arOrder['BOOKING_OBJECT']['SECTIONS'][2]['ID']);
                }
            }
        }
    }

    public function executeComponent()
    {
        if ($this->arParams['ORDER_ID']) {
            $this->getOrderData($this->arParams, $this->arResult);
        } elseif ($this->arParams['OBJECT_ID']) {
            $this->getObjectData($this->arParams, $this->arResult);
        } else {
            $this->arResult['OBJECTS'] = getObjects();
        }
        $this->arResult['ADMIN_DATA'] = getUserData();
        $this->arResult['LOCATIONS_STRUCTURE'] = getLocationStructure();
        $this->IncludeComponentTemplate($this->componentPage);
    }
}