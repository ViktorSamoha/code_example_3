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

class WAAdminVisitorAdd extends CBitrixComponent implements Controllerable
{

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'addUser' => [
                'prefilters' => [],
            ],
        ];
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

    public function addUserRecord($arData, $userId, $arFiles)
    {
        if ($arData && $userId) {
            if (Loader::includeModule("iblock")) {
                global $USER;
                $el = new CIBlockElement;
                $PROP = array();
                $PROP['USER_ID'] = $userId;
                if (isset($arData['PREF_CATEGORY'])) {
                    $prefCategory = getPrefCategoryById($arData['PREF_CATEGORY']);
                    if ($prefCategory) {
                        $PROP['PREF_CATEGORY'] = $prefCategory['VALUE'];
                    }
                }
                if (isset($arData['LOCATION'])) {
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
                $arLoadProductArray = array(
                    "MODIFIED_BY" => $USER->GetID(),
                    "IBLOCK_SECTION_ID" => false,
                    "IBLOCK_ID" => IB_USERS,
                    "PROPERTY_VALUES" => $PROP,
                    "NAME" => $arData['LAST_NAME'] . ' ' . $arData['NAME'] . ' ' . $arData['SECOND_NAME'],
                    "ACTIVE" => "Y",
                );
                $newUserRecordId = $el->Add($arLoadProductArray);
                if ($newUserRecordId) {
                    return $newUserRecordId;
                } else {
                    return $el->LAST_ERROR;
                }
            }
        } else {
            return false;
        }

    }

    public function addUserAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        $files = $this->request->getFileList()->toArray();
        if (isset($post['LAST_NAME']) && isset($post['NAME']) && isset($post['PHONE']) && isset($post['EMAIL'])) {
            $user = new CUser;
            $userPasswd = generateRandomString();
            $arFields = array(
                "NAME" => $post['NAME'],
                "LAST_NAME" => $post['LAST_NAME'],
                "SECOND_NAME" => $post['SECOND_NAME'] ? $post['SECOND_NAME'] : '',
                "EMAIL" => $post['EMAIL'],
                "LOGIN" => $post['EMAIL'],
                "WORK_PHONE" => $post['PHONE'],
                "ACTIVE" => "Y",
                "GROUP_ID" => array(3, 10),
                "PASSWORD" => $userPasswd,
                "CONFIRM_PASSWORD" => $userPasswd,
            );
            $newUserId = $user->Add($arFields);
            if (intval($newUserId) > 0) {
                $userRecordId = $this->addUserRecord($post, $newUserId, $files);
                if (intval($userRecordId) > 0) {
                    return AjaxJson::createSuccess([
                        'new_user_id' => $newUserId,
                        'new_user_record_id' => $userRecordId,
                        'user_page_link' => '/admin/user_profile/?ID=' . $newUserId,
                    ]);
                } else {
                    return AjaxJson::createError(null, $userRecordId);
                }
            } else {
                return AjaxJson::createError(null, $user->LAST_ERROR);
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function executeComponent()
    {

        $this->getUserPropsData($this->arResult);
        $this->includeComponentTemplate();
    }

}
