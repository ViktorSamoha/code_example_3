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

class UserEdit extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'saveUserData' => [
                'prefilters' => [],
            ],
            'getLocationObjects' => [
                'prefilters' => [],
            ],
        ];
    }

    public function saveUserDataAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['USER_ID'])) {
            $user = new CUser;
            $arErrors = [];
            $fields = [];

            if (isset($post['USER_LOGIN']) && !empty($post['USER_LOGIN'])) {
                $fields['LOGIN'] = $post['USER_LOGIN'];
            }
            if (isset($post['USER_EMAIL']) && !empty($post['USER_EMAIL'])) {
                $fields['EMAIL'] = $post['USER_EMAIL'];
            }
            if (isset($post['USER_ROLE']) && !empty($post['USER_ROLE'])) {
                $fields['GROUP_ID'] = [$post['USER_ROLE']];
            }

            if (!empty($post['USER_NEW_PASSWORD']) && !empty($post['USER_CONFIRM_NEW_PASSWORD'])
            ) {
                if ($post['USER_NEW_PASSWORD'] == $post['USER_CONFIRM_NEW_PASSWORD']) {
                    $fields['PASSWORD'] = $post['USER_NEW_PASSWORD'];
                    $fields['CONFIRM_PASSWORD'] = $post['USER_CONFIRM_NEW_PASSWORD'];
                } else {
                    $arErrors[] = 'Новый пароль не совпадает';
                }
            }

            if (isset($post['UF_USER_LOCATIONS']) && !empty($post['UF_USER_LOCATIONS'])) {
                $fields['UF_USER_LOCATIONS'] = $post['UF_USER_LOCATIONS'];
            }

            if (isset($post['UF_USER_OBJECTS']) && !empty($post['UF_USER_OBJECTS'])) {
                $fields['UF_USER_OBJECTS'] = $post['UF_USER_OBJECTS'];
            }

            if (empty($arErrors)) {
                if ($user->Update($post['USER_ID'], $fields)) {
                    return AjaxJson::createSuccess([
                        'data' => 'Данные пользователя успешно сохранены!',
                    ]);
                } else {
                    return AjaxJson::createSuccess([
                        'data' => $user->LAST_ERROR,
                    ]);
                }
            } else {
                return AjaxJson::createError([
                    'data' => $arErrors,
                ]);
            }
        } else {
            return AjaxJson::createError([
                'data' => 'Отсутствует идентификатор пользователя!',
            ]);
        }
    }

    public function getLocationObjectsAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['locations'])) {

            Loader::includeModule("iblock");
            $arSelect = array("ID", "NAME",);
            $arFilter = array("IBLOCK_ID" => IB_OBJECT, "PROPERTY_LOCATION" => $post['locations']);
            $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arResult[] = $arFields;
            }
            if (isset($post['objects'])) {
                foreach ($arResult as &$object) {
                    foreach ($post['objects'] as $user_object) {
                        if ($object['ID'] == $user_object) {
                            $object['CONDITION'] = 'checked';
                        }
                    }
                }
            }
            return AjaxJson::createSuccess([
                'data' => $arResult,
            ]);
        } else {
            return AjaxJson::createError([
                'data' => 'не заполнены обязательные поля',
            ]);
        }
    }

    public function getUserData()
    {
        if (isset($this->arParams['USER_ID'])) {
            $rsUsers = CUser::GetList($by, $order = "desc", ['ID' => $this->arParams['USER_ID']], ['SELECT' => ['UF_USER_LOCATIONS', 'UF_USER_OBJECTS']]);
            while ($arUser = $rsUsers->GetNext()) {
                $this->arResult['USER'] = [
                    'ID' => $arUser['ID'],
                    'LOGIN' => $arUser['LOGIN'],
                    'CHECKWORD' => $arUser['CHECKWORD'],
                    'ACTIVE' => $arUser['ACTIVE'],
                    'EMAIL' => $arUser['EMAIL'],
                    'DATE_REGISTER' => $arUser['DATE_REGISTER'],
                    'GROUPS' => CUser::GetUserGroup($this->arParams['USER_ID'])
                ];
                if (isset($arUser['UF_USER_LOCATIONS'])) {
                    foreach ($arUser['UF_USER_LOCATIONS'] as $location_id) {
                        $arLocation = CIBlockElement::GetByID($location_id);
                        if ($Location = $arLocation->GetNext()) {
                            $this->arResult['USER']['LOCATIONS'][] = [
                                "ID" => $Location['ID'],
                                "NAME" => $Location['NAME'],
                            ];
                        }
                    }
                }
                if (isset($arUser['UF_USER_OBJECTS'])) {
                    foreach ($arUser['UF_USER_OBJECTS'] as $object_id) {
                        $arObject = CIBlockElement::GetByID($object_id);
                        if ($Object = $arObject->GetNext()) {
                            $this->arResult['USER']['OBJECTS'][] = [
                                "ID" => $object_id,
                                "NAME" => $Object['NAME'],
                            ];
                        }
                    }
                }
            }
        } else {
            $this->arResult['ERRORS'][] = 'empty user id';
        }
    }

    public function executeComponent()
    {

        $this->getUserData();

        $this->IncludeComponentTemplate($this->componentPage);

    }
}