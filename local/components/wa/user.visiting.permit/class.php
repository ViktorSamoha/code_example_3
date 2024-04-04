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
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\QROutputInterface;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class userVisitingPermit extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'recalculatePermissionPrice' => [
                'prefilters' => [],
            ],
            'registerUserPermission' => [
                'prefilters' => [],
            ],
        ];
    }

    public function getUserData(&$arResult)
    {
        global $USER;
        $userId = $USER->GetID();
        $userRecord = checkUserRecord($userId);
        $rsUser = CUser::GetByID($userId);
        $arUser = $rsUser->Fetch();
        $arResult['USER_NAME'] = $arUser['NAME'];
        $arResult['USER_SECOND_NAME'] = $arUser['SECOND_NAME'];
        $arResult['USER_LAST_NAME'] = $arUser['LAST_NAME'];
        if ($arUser['WORK_PHONE']) {
            $arResult['USER_PHONE'] = $arUser['WORK_PHONE'];
        }
        if ($userRecord) {
            $arResult['USER_RECORD_ID'] = $userRecord;
            $arVisitorsId = array();
            $res = CIBlockElement::GetProperty(IB_USERS, $userRecord, "sort", "asc", array("CODE" => "USER_GROUP"));
            while ($ob = $res->GetNext()) {
                if ($ob['VALUE']) {
                    $arVisitorsId[] = $ob['VALUE'];
                }
            }
            if (!empty($arVisitorsId)) {
                $arVisitors = [];
                $arSelect = array(
                    "ID",
                    "PROPERTY_U_LAST_NAME",
                    "PROPERTY_U_NAME",
                    "PROPERTY_U_SECOND_NAME",
                    "PROPERTY_U_PREFERENTIAL_CATEGORY",
                    "PROPERTY_U_LOCATION",
                    "PROPERTY_U_PREF_DOC_NUMBER",
                    "PROPERTY_U_PREF_DOC_DATE",
                );
                $arFilter = array("IBLOCK_ID" => IB_VISITORS, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", 'ID' => $arVisitorsId);
                $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arVisitors[] = [
                        'ID' => $arFields['ID'],
                        'NAME' => $arFields['PROPERTY_U_NAME_VALUE'],
                        'LAST_NAME' => $arFields['PROPERTY_U_LAST_NAME_VALUE'],
                        'SECOND_NAME' => $arFields['PROPERTY_U_SECOND_NAME_VALUE'],
                        'PREFERENTIAL_CATEGORY' => $arFields['PROPERTY_U_PREFERENTIAL_CATEGORY_VALUE'],
                        'LOCATION' => $arFields['PROPERTY_U_LOCATION_VALUE'],
                        'PREF_DOC_NUMBER' => $arFields['PROPERTY_U_PREF_DOC_NUMBER_VALUE'],
                        'PREF_DOC_DATE' => $arFields['PROPERTY_U_PREF_DOC_DATE_VALUE'],
                    ];
                }
                if (!empty($arVisitors)) {
                    $arResult['USER_GROUP'] = $arVisitors;
                }
            }
        }
    }

    public function getUserGroupData($userRecordId)
    {
        if ($userRecordId) {
            $arVisitorsId = array();
            $res = CIBlockElement::GetProperty(IB_USERS, $userRecordId, "sort", "asc", array("CODE" => "USER_GROUP"));
            while ($ob = $res->GetNext()) {
                $arVisitorsId[] = $ob['VALUE'];
            }
            if (!empty($arVisitorsId)) {
                $arVisitors = [];
                $arSelect = array(
                    "ID",
                    "PROPERTY_U_LAST_NAME",
                    "PROPERTY_U_NAME",
                    "PROPERTY_U_SECOND_NAME",
                    "PROPERTY_U_PREFERENTIAL_CATEGORY",
                    "PROPERTY_U_LOCATION",
                    "PROPERTY_U_PREF_DOC_NUMBER",
                    "PROPERTY_U_PREF_DOC_DATE",
                );
                $arFilter = array("IBLOCK_ID" => IB_VISITORS, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", 'ID' => $arVisitorsId);
                $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arVisitors[] = [
                        'ID' => $arFields['ID'],
                        'NAME' => $arFields['PROPERTY_U_NAME_VALUE'],
                        'LAST_NAME' => $arFields['PROPERTY_U_LAST_NAME_VALUE'],
                        'SECOND_NAME' => $arFields['PROPERTY_U_SECOND_NAME_VALUE'],
                        'PREFERENTIAL_CATEGORY' => $arFields['PROPERTY_U_PREFERENTIAL_CATEGORY_VALUE'],
                        'LOCATION' => $arFields['PROPERTY_U_LOCATION_VALUE'],
                        'PREF_DOC_NUMBER' => $arFields['PROPERTY_U_PREF_DOC_NUMBER_VALUE'],
                        'PREF_DOC_DATE' => $arFields['PROPERTY_U_PREF_DOC_DATE_VALUE'],
                    ];
                }
                if (!empty($arVisitors)) {
                    return $arVisitors;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getUserPropsData(&$arResult)
    {
        if (Loader::includeModule("iblock")) {
            global $USER;
            $arPrefCategories = [];
            $arUserLocations = [];
            $property_enums = CIBlockPropertyEnum::GetList(array("DEF" => "DESC", "SORT" => "ASC"), array("IBLOCK_ID" => IB_VISITORS, "CODE" => "U_PREFERENTIAL_CATEGORY"));
            while ($enum_fields = $property_enums->GetNext()) {
                if ($enum_fields) {
                    $arPrefCategories[] = [
                        'ID' => $enum_fields['ID'],
                        'VALUE' => $enum_fields['VALUE'],
                        //'XML_ID' => $enum_fields['XML_ID'],
                    ];
                }
            }
            unset($property_enums, $enum_fields);
            $property_enums = CIBlockPropertyEnum::GetList(array("DEF" => "DESC", "SORT" => "ASC"), array("IBLOCK_ID" => IB_VISITORS, "CODE" => "U_LOCATION"));
            while ($enum_fields = $property_enums->GetNext()) {
                if ($enum_fields) {
                    $arUserLocations[] = [
                        'ID' => $enum_fields['ID'],
                        'VALUE' => $enum_fields['VALUE'],
                        //'XML_ID' => $enum_fields['XML_ID'],
                    ];
                }
            }
            if ($arPrefCategories) {
                $arResult['PREF_CATEGORIES'] = $arPrefCategories;
            }
            if ($arPrefCategories) {
                $arResult['USER_LOCATIONS'] = $arUserLocations;
            }
            //$arResult['PARENT_USER_ID'] = $USER->GetID();
        }
    }

    public function setVisitorCounter($arParams, &$arResult)
    {
        if ($arParams['VISITOR_COUNTER']) {
            $arResult['VISITOR_COUNTER'] = $arParams['VISITOR_COUNTER'];
        }
    }

    public function recalculatePermissionPriceAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (!empty($post)) {
            $count = ($post['count'] + 1) * VISIT_PERMISSION_COST;
            return AjaxJson::createSuccess([
                'price' => $count,
            ]);
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function getUserGroup($arData)
    {
        if (!empty($arData)) {
            if (is_array($arData)) {
                $arVisitors = [];
                foreach ($arData as $fieldName => $fieldValue) {
                    if ($fieldName != "VISITORS_COUNT" && strpos($fieldName, 'VISITOR') !== false) {
                        list($propertyName, $counter) = explode('_K_', $fieldName);
                        if ($propertyName == 'VISITOR_ID') {
                            $needleCounter = $counter;
                            $visitorId = $fieldValue;
                            $arVisitors[$visitorId]['ID'] = $visitorId;
                        } else {
                            if ($counter == $needleCounter) {
                                $arVisitors[$visitorId][$propertyName] = $fieldValue;
                            } else {
                                if ($counter > $needleCounter) {
                                    $arVisitors['new_visitor_' . $counter][$propertyName] = $fieldValue;
                                }
                            }
                        }
                    }
                }
                if (!empty($arVisitors)) {
                    return $arVisitors;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function updateUserGroup($userRecordId, $arUserGroup)
    {
        if ($userRecordId && is_array($arUserGroup)) {
            $_return = false;
            $now = date('d.m.Y');
            Loader::includeModule("iblock");
            global $USER;
            foreach ($arUserGroup as $elementId => $elementFields) {
                $el = new CIBlockElement;
                $PROP = array();
                $PROP['U_LAST_NAME'] = $elementFields['VISITOR_LAST_NAME'];
                $PROP['U_NAME'] = $elementFields['VISITOR_NAME'];
                if (isset($elementFields['VISITOR_SECOND_NAME']) && !empty($elementFields['VISITOR_SECOND_NAME'])) {
                    $PROP['U_SECOND_NAME'] = $elementFields['VISITOR_SECOND_NAME'];
                }
                if (isset($elementFields['VISITOR_PREF_CATEGORY']) && !empty($elementFields['VISITOR_PREF_CATEGORY'])) {
                    $PROP['U_PREFERENTIAL_CATEGORY'] = $elementFields['VISITOR_PREF_CATEGORY'];
                }
                if (isset($elementFields['VISITOR_LOCATION']) && !empty($elementFields['VISITOR_LOCATION'])) {
                    $PROP['U_LOCATION'] = $elementFields['VISITOR_LOCATION'];
                }
                if (isset($elementFields['VISITOR_PREF_DOC_NUMBER']) && !empty($elementFields['VISITOR_PREF_DOC_NUMBER'])) {
                    $PROP['U_PREF_DOC_NUMBER'] = $elementFields['VISITOR_PREF_DOC_NUMBER'];
                }
                if (isset($elementFields['VISITOR_PREF_DOC_DATE']) && !empty($elementFields['VISITOR_PREF_DOC_DATE'])) {
                    $PROP['U_PREF_DOC_DATE'] = $elementFields['VISITOR_PREF_DOC_DATE'];
                }
                $arLoadProductArray = array(
                    "MODIFIED_BY" => $USER->GetID(),
                    "IBLOCK_SECTION_ID" => false,
                    "IBLOCK_ID" => IB_VISITORS,
                    "PROPERTY_VALUES" => $PROP,
                    "NAME" => $PROP['U_LAST_NAME'] . ' ' . $PROP['U_NAME'] . ' ' . $PROP['U_SECOND_NAME'],
                    "ACTIVE" => "Y",
                );
                if (strpos($elementId, 'new_visitor_') !== false) {
                    if ($element_id = $el->Add($arLoadProductArray)) {
                        $userGroupPropertyValues = array();
                        $res = CIBlockElement::GetProperty(IB_USERS, $userRecordId, "sort", "asc", array("CODE" => "USER_GROUP"));
                        while ($ob = $res->GetNext()) {
                            $userGroupPropertyValues[] = $ob['VALUE'];
                        }
                        $userGroupPropertyValues[] = $element_id;
                        CIBlockElement::SetPropertyValuesEx($userRecordId, false, array('USER_GROUP' => $userGroupPropertyValues));
                    } else {
                        $_return = false;
                        $err_msg = 'Ошибка добавления посетителя' . ' time= ' . $now;
                        \Bitrix\Main\Diag\Debug::dumpToFile($err_msg, $varName = 'error', $fileName = 'userVisitingPermit_error_log.txt');
                    }
                } else {
                    if ($element_id = $el->Update($elementId, $arLoadProductArray)) {
                        $_return = true;
                    } else {
                        $_return = false;
                        $err_msg = 'Ошибка обновления посетителя ' . $elementId . ' time= ' . $now;
                        \Bitrix\Main\Diag\Debug::dumpToFile($err_msg, $varName = 'error', $fileName = 'userVisitingPermit_error_log.txt');
                    }
                }
            }
            return $_return;
        } else {
            return false;
        }
    }

    public function setUserGroup($fields, $docs)
    {
        if ($fields) {
            $arUserGroup = [];
            foreach ($fields as $propertyName => $propertyValue) {
                if (is_numeric(strpos($propertyName, '_USER_'))) {
                    list($newUserPropName, $i) = explode('_USER_', $propertyName);
                    $arUserGroup[$i][$newUserPropName] = $propertyValue;
                }
            }
            if ($docs) {
                foreach ($docs as $docName => $docValue) {
                    if (is_numeric(strpos($docName, '_USER_'))) {
                        list($newUserDocPropName, $posValue) = explode('_USER_', $docName);
                        list($i, $filePos) = explode('_FVAL_', $posValue);
                        $arUserGroup[$i][$newUserDocPropName][$filePos] = $docValue;
                    }
                }
            }
            if (!empty($arUserGroup)) {
                if (Loader::includeModule("iblock")) {
                    $arVisitors = [];
                    foreach ($arUserGroup as $visitor) {
                        $el = new CIBlockElement;
                        $PROP = [
                            'U_LAST_NAME' => $visitor["LAST_NAME"],
                            'U_NAME' => $visitor["NAME"],
                            'U_SECOND_NAME' => $visitor["SECOND_NAME"] ? $visitor["SECOND_NAME"] : '',
                            'U_PREFERENTIAL_CATEGORY' => $visitor["PREFERENTIAL_CATEGORY"] ? $visitor["PREFERENTIAL_CATEGORY"] : '',
                            'U_LOCATION' => $visitor["LOCATION"] ? $visitor["LOCATION"] : '',
                            'U_PREF_DOC_NUMBER' => $visitor["PREF_DOC_NUMBER"] ? $visitor["PREF_DOC_NUMBER"] : '',
                            'U_PREF_DOC_DATE' => $visitor["PREF_DOC_DATE"] ? $visitor["PREF_DOC_DATE"] : '',
                        ];
                        if (isset($visitor["PREF_DOCS"]) && is_array($visitor["PREF_DOCS"])) {
                            foreach ($visitor["PREF_DOCS"] as $pos => $arFile) {
                                $PROP['U_PREF_DOCS']['n' . $pos] = ['VALUE' => $arFile];
                            }
                        }
                        $arLoadProductArray = array(
                            "IBLOCK_SECTION_ID" => false,
                            "IBLOCK_ID" => IB_VISITORS,
                            "PROPERTY_VALUES" => $PROP,
                            "NAME" => $visitor['LAST_NAME'] . ' ' . $visitor['NAME'] . ' ' . $visitor['SECOND_NAME'],
                            "ACTIVE" => "Y",
                        );
                        if ($element_id = $el->Add($arLoadProductArray)) {
                            $arVisitors[] = $element_id;
                        }
                    }
                    if (!empty($arVisitors)) {
                        return $arVisitors;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function registerUser($fields, $docs)
    {
        if (isset($fields) && !empty($fields)) {
            if ($fields["NAME"] && $fields["LAST_NAME"] && $fields["EMAIL"]) {
                $newUserData = registerNewUser([
                    'NAME' => $fields["NAME"],
                    'LAST_NAME' => $fields["LAST_NAME"],
                    'SECOND_NAME' => $fields["SECOND_NAME"],
                    'EMAIL' => $fields["EMAIL"],
                    'PHONE' => $fields["PHONE"],
                ]);
                if ($newUserData) {
                    if ($newUserData['ID']) {
                        if ($docs) {
                            foreach ($docs as $docName => $docValue) {
                                if (!is_numeric(strpos($docName, '_USER_'))) {
                                    list($newUserDocPropName, $filePos) = explode('_FVAL_', $docName);
                                    //$newUserData[$newUserDocPropName][$filePos] = $docValue;
                                    $newUserData[$newUserDocPropName]['n' . $filePos] = ['VALUE' => $docValue];
                                }
                            }
                        }
                        $arUserProps = [
                            'PREF_CATEGORY' => $fields["PREF_CATEGORY"] ? getPrefCategoryById($fields["PREF_CATEGORY"])['VALUE'] : '',
                            'LOCATION' => $fields["LOCATION"] ? getPrefUserLocationById($fields["LOCATION"])['VALUE'] : '',
                            'PREF_DOC_NUMBER' => $fields["PREF_DOC_NUMBER"] ? $fields["PREF_DOC_NUMBER"] : '',
                            'PREF_DOC_DATE' => $fields["PREF_DOC_DATE"] ? $fields["PREF_DOC_DATE"] : '',
                            'PREF_DOCS' => $newUserData["PREF_DOCS"] ? $newUserData["PREF_DOCS"] : '',
                        ];
                        $userGroup = $this->setUserGroup($fields, $docs);
                        $userRecordId = addUserRecord($newUserData['ID'], $userGroup, false, $arUserProps);
                        if ($userRecordId) {
                            return [
                                'USER_ID' => $newUserData['ID'],
                                'USER_RECORD_ID' => $userRecordId,
                            ];
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function registerUserPermissionAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        $files = $this->request->getFileList()->toArray();
        if (!empty($post)) {
            global $USER;
            $now = time();
            $userId = $arUser = $elementName = $userRecordId = false;
            if ($post['USER_RECORD_ID']) {
                $userId = $USER->GetID();
                $arUser = getUserData($userId);
                $userRecordId = $post['USER_RECORD_ID'];
                $elementName = 'Разрешение на посещение [' . $userId . '] на период с ' . $post['USER_ARRIVAL_DATE'] . ' по ' . $post['USER_DEPARTURE_DATE'] . ' [' . $now . ']';
            } else {
                $userData = $this->registerUser($post, $files);
                if ($userData) {
                    $userId = $userData["USER_ID"];
                    $userRecordId = $userData["USER_RECORD_ID"];
                    $arUser = getUserData($userId);
                    $elementName = 'Разрешение на посещение [' . $userId . '] на период с ' . $post['USER_ARRIVAL_DATE'] . ' по ' . $post['USER_DEPARTURE_DATE'] . ' [' . $now . ']';
                    $userGroup = getUserGroupDataByUserId($userId);
                    if ($userGroup) {
                        $arVisitors = [];
                        foreach ($userGroup as $visitor) {
                            $arVisitors[] = $visitor['ID'];
                        }
                    }
                }
            }
            if ($userId && $arUser && $elementName && $userRecordId) {
                if (Loader::includeModule("iblock")) {
                    $el = new CIBlockElement;
                    $hash_string = $elementName . $now;
                    $permission_unique_code = stringToHash($hash_string);
                    $PROP = array();
                    $PROP['USER_RECORD_ID'] = $userRecordId;
                    $PROP['USER_FIO'] = $arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME'];
                    $PROP['USER_PHONE'] = $arUser['PHONE'];
                    $PROP['ARRIVAL_DATE'] = $post['USER_ARRIVAL_DATE'];
                    $PROP['DEPARTURE_DATE'] = $post['USER_DEPARTURE_DATE'];
                    $PROP['ROUTE'] = '';
                    $PROP['QR_CODE'] = getQrCode($permission_unique_code, 'user');
                    //TODO:СДЕЛАТЬ ССЫЛКУ НА ОПЛАТУ
                    $payment_link = '';
                    $PROP['PRICE'] = $post['PRICE'];
                    $PROP['BOOKING_TYPE'] = 'Онлайн';
                    if ($post['VISITORS']) {
                        if (is_array($post['VISITORS'])) {
                            foreach ($post['VISITORS'] as $arVisitorId) {
                                $PROP['USER_GROUP'][] = $arVisitorId;
                            }
                        } else {
                            $PROP['USER_GROUP'][] = $post['VISITORS'];
                        }
                    } elseif ($arVisitors) {
                        $PROP['USER_GROUP'] = $arVisitors;
                    }
                    $arLoadProductArray = array(
                        "MODIFIED_BY" => $userId,
                        "IBLOCK_SECTION_ID" => false,
                        "IBLOCK_ID" => IB_PERMISSION,
                        'CODE' => $permission_unique_code,
                        "PROPERTY_VALUES" => $PROP,
                        "NAME" => $elementName,
                        "ACTIVE" => "Y",
                    );
                    if ($permissionId = $el->Add($arLoadProductArray)) {
                        return AjaxJson::createSuccess([
                            'permission_id' => $permissionId,
                            'payment_link' => $payment_link,
                            'blank_link' => 'https://' . $_SERVER['HTTP_HOST'] . '/receipt/user/' . $permission_unique_code . '/',
                        ]);
                    } else {
                        return AjaxJson::createError([
                            'error_msg' => $el->LAST_ERROR,
                        ]);
                    }
                } else {
                    return AjaxJson::createError(null, 'Ошибка подключения модуля iblock!');
                }
            } else {
                return AjaxJson::createError(null, 'нет значений!');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function executeComponent()
    {
        $this->getUserData($this->arResult);
        $this->getUserPropsData($this->arResult);
        $this->setVisitorCounter($this->arParams, $this->arResult);
        $this->IncludeComponentTemplate($this->componentPage);
    }
}