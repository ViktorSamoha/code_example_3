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

class WAUserAddVisitor extends CBitrixComponent implements Controllerable
{

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'addUserGroupElement' => [
                'prefilters' => [],
            ],

        ];
    }

    public function addGroupElementToUser($elementId, $userId)
    {
        if ($elementId && $userId) {
            $userRecordId = checkUserRecord($userId);
            if ($userRecordId) {
                $userGroupValues = [];
                $res = CIBlockElement::GetProperty(IB_USERS, $userRecordId, "sort", "asc", array("CODE" => "USER_GROUP"));
                while ($ob = $res->GetNext()) {
                    $userGroupValues[] = $ob['VALUE'];
                }
                if (!empty($userGroupValues)) {
                    $userGroupValues[] = $elementId;
                    CIBlockElement::SetPropertyValuesEx($userRecordId, false, array('USER_GROUP' => $userGroupValues));
                } else {
                    CIBlockElement::SetPropertyValuesEx($userRecordId, false, array('USER_GROUP' => $elementId));
                }
                return true;
            } else {
                return addUserRecord($userId, $elementId, false);
            }
        } else {
            return false;
        }
    }

    public function addUserGroupElementAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        $files = $this->request->getFileList()->toArray();
        if (isset($post['U_NAME']) && isset($post['U_LAST_NAME'])) {
            Loader::includeModule("iblock");
            global $USER;
            $el = new CIBlockElement;
            $PROP = array();
            $PROP['U_LAST_NAME'] = $post['U_LAST_NAME'];
            $PROP['U_NAME'] = $post['U_NAME'];
            if (isset($post['U_SECOND_NAME']) && !empty($post['U_SECOND_NAME'])) {
                $PROP['U_SECOND_NAME'] = $post['U_SECOND_NAME'];
            }
            if (isset($post['U_PREFERENTIAL_CATEGORY']) && !empty($post['U_PREFERENTIAL_CATEGORY'])) {
                $PROP['U_PREFERENTIAL_CATEGORY'] = $post['U_PREFERENTIAL_CATEGORY'];
            }
            if (isset($post['U_LOCATION']) && !empty($post['U_LOCATION'])) {
                $PROP['U_LOCATION'] = $post['U_LOCATION'];
            }
            if (isset($post['U_PREF_DOC_NUMBER']) && !empty($post['U_PREF_DOC_NUMBER'])) {
                $PROP['U_PREF_DOC_NUMBER'] = $post['U_PREF_DOC_NUMBER'];
            }
            if (isset($post['U_PREF_DOC_DATE']) && !empty($post['U_PREF_DOC_DATE'])) {
                $PROP['U_PREF_DOC_DATE'] = $post['U_PREF_DOC_DATE'];
            }
            if (!empty($files)) {
                foreach ($files as $propName => $arFile) {
                    list($propertyName, $filePos) = explode('_FVAL_', $propName);
                    $PROP[$propertyName]['n' . $filePos] = ['VALUE' => $arFile];
                }
            }
            $arLoadProductArray = array(
                "MODIFIED_BY" => $USER->GetID(),
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID" => IB_VISITORS,
                "PROPERTY_VALUES" => $PROP,
                "NAME" => $post['U_LAST_NAME'] . ' ' . $post['U_NAME'] . ' ' . $PROP['U_SECOND_NAME'],
                "ACTIVE" => "Y",
            );
            if ($element_id = $el->Add($arLoadProductArray)) {
                if ($this->addGroupElementToUser($element_id, $USER->GetID())) {
                    return AjaxJson::createSuccess([
                        'new_visitor_id' => $element_id,
                    ]);
                } else {
                    return AjaxJson::createError(null, 'Ошибка добавления посетителя');
                }
            } else {
                return AjaxJson::createError(null, $el->LAST_ERROR);
            }
        } else {
            return AjaxJson::createError(null, 'не заполнены обязательные поля!');
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
            $userRecordId = checkUserRecord($USER->GetID());
            if ($userRecordId) {
                $arResult['USER_RECORD_ID'] = $userRecordId;
            }
        }
    }

    public function executeComponent()
    {
        $this->getUserPropsData($this->arResult);
        $this->includeComponentTemplate();
    }
}