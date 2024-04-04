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

use Bitrix\Main\Diag\Debug;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class WAUserPersonal extends CBitrixComponent implements Controllerable
{

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'saveUserData' => [
                'prefilters' => [],
            ],
            'deleteVisitor' => [
                'prefilters' => [],
            ],
            'deleteVehicle' => [
                'prefilters' => [],
            ],
        ];
    }

    public function deleteVehicleAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['VEHICLE_ID'])) {
            if (CModule::IncludeModule("iblock")) {
                global $DB;
                $DB->StartTransaction();
                if (!CIBlockElement::Delete($post['VEHICLE_ID'])) {
                    $DB->Rollback();
                    return AjaxJson::createError(null, 'Ошибка удаления транспортного средства!');
                } else {
                    $DB->Commit();
                    return AjaxJson::createSuccess([
                        'msg' => 'Транспортное средство успешно удалено!',
                    ]);
                }
            } else {
                return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
            }
        } else {
            return AjaxJson::createError(null, 'Отсутствует значение id транспортного средства!');
        }
    }

    public function deleteVisitorAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['VISITOR_ID'])) {
            if (CModule::IncludeModule("iblock")) {
                global $DB;
                $DB->StartTransaction();
                if (!CIBlockElement::Delete($post['VISITOR_ID'])) {
                    $DB->Rollback();
                    return AjaxJson::createError(null, 'Ошибка удаления посетителя!');
                } else {
                    $DB->Commit();
                    return AjaxJson::createSuccess([
                        'msg' => 'Посетитель успешно удален!',
                    ]);
                }
            } else {
                return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
            }
        } else {
            return AjaxJson::createError(null, 'Отсутствует значение id посетителя!');
        }
    }

    public function updateUserRecord($arData, $arFiles)
    {
        if ($arData) {
            global $USER;
            $userId = $USER->GetID();
            $userRecordId = checkUserRecord($userId);
            if ($userRecordId) {
                if (Loader::includeModule("iblock")) {
                    $PROP = array();
                    if (isset($arData['PREF_CATEGORY']) && !empty($arData['PREF_CATEGORY'])) {
                        $prefCategory = getPrefCategoryById($arData['PREF_CATEGORY']);
                        if ($prefCategory) {
                            $PROP['PREF_CATEGORY'] = $prefCategory['VALUE'];
                        }

                    }
                    if (isset($arData['LOCATION']) && !empty($arData['LOCATION'])) {
                        $prefLocation = getPrefUserLocationById($arData['LOCATION']);
                        if ($prefLocation) {
                            $PROP['LOCATION'] = $prefLocation['VALUE'];
                        }
                    }
                    if (isset($arData['PREF_DOC_NUMBER']) && !empty($arData['PREF_DOC_NUMBER'])) {
                        $PROP['PREF_DOC_NUMBER'] = $arData['PREF_DOC_NUMBER'];
                    }
                    if (isset($arData['PREF_DOC_DATE']) && !empty($arData['PREF_DOC_DATE'])) {
                        $PROP['PREF_DOC_DATE'] = $arData['PREF_DOC_DATE'];
                    }
                    if (!empty($arFiles)) {
                        foreach ($arFiles as $propName => $arFile) {
                            list($propertyName, $filePos) = explode('_FVAL_', $propName);
                            $PROP[$propertyName]['n' . $filePos] = ['VALUE' => $arFile];
                        }
                    }
                    CIBlockElement::SetPropertyValuesEx($userRecordId, IB_USERS, $PROP);
                }
            }
        }
    }

    public function saveUserDataAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        $files = $this->request->getFileList()->toArray();
        if (isset($post['USER_ID'])) {
            $user = new CUser;
            $fields = array(
                "NAME" => $post['NAME'],
                "LAST_NAME" => $post['LAST_NAME'],
                "SECOND_NAME" => $post['SECOND_NAME'],
                "EMAIL" => $post['EMAIL'],
                "WORK_PHONE" => $post['PHONE'],
            );
            $this->updateUserRecord($post, $files);
            if (!$user->Update($post['USER_ID'], $fields)) {
                return AjaxJson::createError(null, $user->LAST_ERROR);
            } else {
                return AjaxJson::createSuccess([
                    'msg' => 'Данные обновлены успешно!',
                ]);
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    /*    public function getUserTransport($userId)
        {
            if ($userId) {
                $arUserTransport = [];
                $arSelect = array("ID", "PROPERTY_USER_TRANSPORT");
                $arFilter = array("IBLOCK_ID" => IB_USERS, "PROPERTY_USER_ID" => $userId);
                $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    if ($arFields) {
                        $arUserTransport[$arFields['ID']] = $arFields['PROPERTY_USER_TRANSPORT_VALUE'];
                    }
                }
                if (!empty($arUserTransport)) {
                    $temp = [];
                    foreach ($arUserTransport as $arTransportId) {
                        $arSelect = array(
                            "ID",
                            "PROPERTY_VEHICLE_TYPE",
                            "PROPERTY_DRIVING_LICENSE_SERIES",
                            "PROPERTY_DRIVING_LICENSE_NUMBER",
                            "PROPERTY_MODEL",
                            "PROPERTY_INSPECTION_DATE",
                            "PROPERTY_BLOCKED",
                        );
                        $arFilter = array("IBLOCK_ID" => IB_TRANSPORT, "ID" => $arTransportId);
                        $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                        while ($ob = $res->GetNextElement()) {
                            $arFields = $ob->GetFields();
                            $element = [
                                'ID' => $arFields['ID'],
                                'VEHICLE_TYPE' => getUserVehicleType($arFields['PROPERTY_VEHICLE_TYPE_VALUE']),
                                'DRIVING_LICENSE_SERIES' => $arFields['PROPERTY_DRIVING_LICENSE_SERIES_VALUE'],
                                'DRIVING_LICENSE_NUMBER' => $arFields['PROPERTY_DRIVING_LICENSE_NUMBER_VALUE'],
                                'MODEL' => $arFields['PROPERTY_MODEL_VALUE'],
                                'INSPECTION_DATE' => $arFields['PROPERTY_INSPECTION_DATE_VALUE'],
                                'BLOCKED' => $arFields['PROPERTY_BLOCKED_VALUE'],
                            ];
                            $temp[] = $element;
                        }
                    }
                    return $temp;
                }
            } else {
                return false;
            }
        }

        public function getUserGroupData($userId)
        {
            if ($userId) {
                $arUserGroup = [];
                $arSelect = array("ID", "PROPERTY_USER_GROUP");
                $arFilter = array("IBLOCK_ID" => IB_USERS, "PROPERTY_USER_ID" => $userId);
                $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    if ($arFields) {
                        $arUserGroup[$arFields['ID']] = $arFields['PROPERTY_USER_GROUP_VALUE'];
                    }
                }
                if (!empty($arUserGroup)) {
                    $temp = [];
                    foreach ($arUserGroup as $groupElements) {
                        if (!empty($groupElements)) {
                            $arSelect = array("ID", "PROPERTY_U_LAST_NAME", "PROPERTY_U_NAME", "PROPERTY_U_SECOND_NAME");
                            $arFilter = array("IBLOCK_ID" => IB_VISITORS, "ID" => $groupElements);
                            $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                            while ($ob = $res->GetNextElement()) {
                                $arFields = $ob->GetFields();
                                $groupElement = [
                                    'ID' => $arFields['ID'],
                                    'NAME' => $arFields['PROPERTY_U_LAST_NAME_VALUE'],
                                    'LAST_NAME' => $arFields['PROPERTY_U_NAME_VALUE'],
                                    'SECOND_NAME' => $arFields['PROPERTY_U_SECOND_NAME_VALUE'],
                                ];
                                $temp[] = $groupElement;
                            }
                        } else {
                            return false;
                        }
                    }
                    return $temp;
                }
            } else {
                return false;
            }
        }*/

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
                    ];
                }
            }
            if ($arPrefCategories) {
                $arResult['PREF_CATEGORIES'] = $arPrefCategories;
            }
            if ($arPrefCategories) {
                $arResult['USER_LOCATIONS'] = $arUserLocations;
            }
        }
    }

    public function executeComponent()
    {
        $userData = getUserData();
        if ($userData) {
            /*$userData['USER_GROUP'] = $this->getUserGroupData($userData["ID"]);
            $userData['USER_TRANSPORT'] = $this->getUserTransport($userData["ID"]);*/
            $userData['USER_GROUP'] = getUserGroupDataByUserId($userData["ID"]);
            $userData['USER_TRANSPORT'] = getUserTransportByUserId($userData["ID"]);
            $this->arResult['USER_DATA'] = $userData;
        }
        $this->getUserPropsData($this->arResult);
        $this->includeComponentTemplate();
    }
}