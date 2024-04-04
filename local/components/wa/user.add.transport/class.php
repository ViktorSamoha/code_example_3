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

class userAddTransport extends CBitrixComponent implements Controllerable
{
    public $arrResult = [];

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'addVehicle' => [
                'prefilters' => [],
            ],
            'updateVehicle' => [
                'prefilters' => [],
            ],
        ];
    }

    public function addVehicleToUser($elementId, $userId)
    {
        if ($elementId && $userId) {
            $userRecordId = checkUserRecord($userId);
            if ($userRecordId) {
                $userVehiclePropertyValues = array();
                $res = CIBlockElement::GetProperty(IB_USERS, $userRecordId, "sort", "asc", array("CODE" => "USER_TRANSPORT"));
                while ($ob = $res->GetNext()) {
                    $userVehiclePropertyValues[] = $ob['VALUE'];
                }
                $userVehiclePropertyValues[] = $elementId;
                CIBlockElement::SetPropertyValuesEx($userRecordId, false, array('USER_TRANSPORT' => $userVehiclePropertyValues));
                return true;
            } else {
                return addUserRecord($userId, false, $elementId);
            }
        } else {
            return false;
        }
    }

    public function updateVehicleAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        $files = $this->request->getFileList()->toArray();
        if (!empty($post) && !empty($files)) {
            if (!empty($post['USER_VEHICLE_ID'])) {
                if (CModule::IncludeModule('iblock')) {
                    global $USER;
                    $el = new CIBlockElement;
                    $PROP = array();
                    $PROP['VEHICLE_TYPE'] = $post["VEHICLE_TYPE"];
                    $PROP['DRIVING_LICENSE_SERIES'] = $post["DRIVING_LICENSE_SERIES"];
                    $PROP['DRIVING_LICENSE_NUMBER'] = $post["DRIVING_LICENSE_NUMBER"];
                    $PROP['MODEL'] = $post["MODEL"];
                    $PROP['INSPECTION_DATE'] = $post["INSPECTION_DATE"];
                    if ($post["VEHICLE_POWER"] && $post["VEHICLE_POWER"] == "on") {
                        $PROP['VEHICLE_POWER'] = '24';
                    }
                    foreach ($files as $propName => $arFile) {
                        list($propertyName, $filePos) = explode('_FVAL_', $propName);
                        $PROP[$propertyName]['n' . $filePos] = ['VALUE' => $arFile];
                    }
                    $arLoadProductArray = array(
                        "MODIFIED_BY" => $USER->GetID(),
                        "PROPERTY_VALUES" => $PROP,
                    );
                    if ($el->Update($post['USER_VEHICLE_ID'], $arLoadProductArray)) {
                        return AjaxJson::createSuccess([
                            'data' => 'Транспортное средство успешно обновлено',
                        ]);
                    } else {
                        return AjaxJson::createError(null, 'Ошибка обновления транспорта');
                    }
                } else {
                    return AjaxJson::createError(null, 'Отсутствует модуль iblock');
                }
            } else {
                return AjaxJson::createError(null, 'Отсутствует id транспортного средства');
            }
        } else {
            return AjaxJson::createError(null, 'не заполнены обязательные поля!');
        }
    }

    public function addVehicleAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        $files = $this->request->getFileList()->toArray();
        if (!empty($post) && !empty($files)) {
            global $USER;
            $userId = $USER->GetID();
            $elementName = 'Транспорт пользователя[' . $userId . '] ' . $post["MODEL"] . ' тип: ' . $post["VEHICLE_TYPE"];
            if (CModule::IncludeModule('iblock')) {
                $el = new CIBlockElement;
                $PROP = array();
                $PROP['VEHICLE_TYPE'] = $post["VEHICLE_TYPE"];
                $PROP['DRIVING_LICENSE_SERIES'] = $post["DRIVING_LICENSE_SERIES"] ? $post["DRIVING_LICENSE_SERIES"] : '';
                $PROP['DRIVING_LICENSE_NUMBER'] = $post["DRIVING_LICENSE_NUMBER"] ? $post["DRIVING_LICENSE_NUMBER"] : '';
                $PROP['MODEL'] = $post["MODEL"];
                $PROP['INSPECTION_DATE'] = $post["INSPECTION_DATE"];
                if ($post["VEHICLE_REGISTER"] && $post["VEHICLE_REGISTER"] == "on") {
                    $PROP['IS_REGISTER'] = '67';
                }
                foreach ($files as $propName => $arFile) {
                    list($propertyName, $filePos) = explode('_FVAL_', $propName);
                    $PROP[$propertyName]['n' . $filePos] = ['VALUE' => $arFile];
                }
                $arLoadProductArray = array(
                    "MODIFIED_BY" => $userId,
                    "IBLOCK_SECTION_ID" => false,
                    "IBLOCK_ID" => IB_TRANSPORT,
                    "PROPERTY_VALUES" => $PROP,
                    "NAME" => $elementName,
                    "ACTIVE" => "Y",
                );
                if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
                    if ($this->addVehicleToUser($PRODUCT_ID, $userId)) {
                        return AjaxJson::createSuccess([
                            'data' => $PRODUCT_ID,
                        ]);
                    } else {
                        return AjaxJson::createError(null, 'Ошибка добавления транспорта');
                    }
                } else {
                    return AjaxJson::createError(null, $el->LAST_ERROR);
                }
            } else {
                return AjaxJson::createError(null, 'Отсутствует модуль iblock');
            }
        } else {
            return AjaxJson::createError(null, 'не заполнены обязательные поля!');
        }
    }

    public function getUserVehicle($vehicleId, &$arResult)
    {
        if ($vehicleId) {
            $arVehicle = [];
            $arSelect = array(
                "ID",
                "PROPERTY_VEHICLE_TYPE",
                "PROPERTY_DRIVING_LICENSE_SERIES",
                "PROPERTY_DRIVING_LICENSE_NUMBER",
                "PROPERTY_DRIVING_LICENSE_FILES",
                "PROPERTY_MODEL",
                "PROPERTY_TECHNICAL_PASSPORT",
                "PROPERTY_INSPECTION_DATE",
                "PROPERTY_INSPECTION_FILES",
                "PROPERTY_IS_REGISTER",
            );
            $arFilter = array("IBLOCK_ID" => IB_TRANSPORT, "ID" => $vehicleId);
            $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arVehicle = [
                    'ID' => $arFields['ID'],
                    'VEHICLE_TYPE' => $arFields['PROPERTY_VEHICLE_TYPE_VALUE'],
                    'DRIVING_LICENSE_SERIES' => $arFields['PROPERTY_DRIVING_LICENSE_SERIES_VALUE'],
                    'DRIVING_LICENSE_NUMBER' => $arFields['PROPERTY_DRIVING_LICENSE_NUMBER_VALUE'],
                    'MODEL' => $arFields['PROPERTY_MODEL_VALUE'],
                    'INSPECTION_DATE' => $arFields['PROPERTY_INSPECTION_DATE_VALUE'],
                ];
                if ($arFields['PROPERTY_DRIVING_LICENSE_FILES_VALUE']) {
                    $arFiles = [];
                    foreach ($arFields['PROPERTY_DRIVING_LICENSE_FILES_VALUE'] as $fileId) {
                        $file = CFile::GetFileArray($fileId);
                        if ($file) {
                            $arFiles[] = [
                                'ID' => $fileId,
                                'FILE_PATH' => $file['SRC'],
                                'FILE_TYPE' => $file['CONTENT_TYPE'],
                                'FILE_DATA' => $file,
                            ];
                        }
                    }
                    if (!empty($arFiles)) {
                        $arVehicle['DRIVING_LICENSE_FILES'] = $arFiles;
                    }
                }
                unset($arFiles, $fileId, $file);
                if ($arFields['PROPERTY_TECHNICAL_PASSPORT_VALUE']) {
                    $arFiles = [];
                    foreach ($arFields['PROPERTY_TECHNICAL_PASSPORT_VALUE'] as $fileId) {
                        $file = CFile::GetFileArray($fileId);
                        if ($file) {
                            $arFiles[] = [
                                'ID' => $fileId,
                                'FILE_PATH' => $file['SRC'],
                                'FILE_TYPE' => $file['CONTENT_TYPE'],
                                'FILE_DATA' => $file,
                            ];
                        }
                    }
                    if (!empty($arFiles)) {
                        $arVehicle['TECHNICAL_PASSPORT'] = $arFiles;
                    }
                }
                unset($arFiles, $fileId, $file);
                if ($arFields['PROPERTY_INSPECTION_FILES_VALUE']) {
                    $arFiles = [];
                    foreach ($arFields['PROPERTY_INSPECTION_FILES_VALUE'] as $fileId) {
                        $file = CFile::GetFileArray($fileId);
                        if ($file) {
                            $arFiles[] = [
                                'ID' => $fileId,
                                'FILE_PATH' => $file['SRC'],
                                'FILE_TYPE' => $file['CONTENT_TYPE'],
                                'FILE_DATA' => $file,
                            ];
                        }
                    }
                    if (!empty($arFiles)) {
                        $arVehicle['INSPECTION_FILES'] = $arFiles;
                    }
                }
                if ($arFields['PROPERTY_IS_REGISTER_VALUE']) {
                    $arVehicle['IS_REGISTER'] = [
                        'ID' => $arFields['PROPERTY_IS_REGISTER_ENUM_ID'],
                        'VALUE' => $arFields['PROPERTY_IS_REGISTER_VALUE']
                    ];
                }
            }
            if (!empty($arVehicle)) {
                $arResult['USER_VEHICLE'] = $arVehicle;
            }
        }
    }

    public function executeComponent()
    {
        $this->arResult['VEHICLE_TYPES'] = getVehicleTypes();
        if ($this->arParams['EDIT_VEHICLE'] == 'Y' && $this->arParams['VEHICLE_ID']) {
            $this->getUserVehicle($this->arParams['VEHICLE_ID'], $this->arResult);
        }
        $this->IncludeComponentTemplate($this->componentPage);
    }
}