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

class UserList extends CBitrixComponent implements Controllerable
{
    public $arrResult = [];

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'deleteUser' => [
                'prefilters' => [],
            ],

        ];
    }

    public function deleteUserAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['user_id'])) {
            if (CUser::Delete($post['user_id'])) {
                $this->getUserList();
                return AjaxJson::createSuccess([
                    'data' => $this->arResult,
                ]);
            } else {
                return AjaxJson::createError(null, 'Ошибка удаления пользователя');
            }
        } else {
            return AjaxJson::createError(null, 'Ошибка - отсутствует id пользователя');
        }
    }

    public function getUserList()
    {
        $rsUsers = CUser::GetList(
            $by,
            $order = "desc",
            ['GROUPS_ID' => [SITE_ADMIN_GROUP_ID, SITE_OPERATOR_GROUP_ID, SITE_RESERV_GROUP_ID]],
            ['SELECT' => ['UF_USER_LOCATIONS', 'UF_USER_OBJECTS']]
        );
        while ($arUser = $rsUsers->GetNext()) {
            $this->arResult['USERS'][$arUser['ID']] = [
                'ID' => $arUser['ID'],
                'LOGIN' => $arUser['LOGIN'],
                'PASSWORD' => $arUser['PASSWORD'],
                'CHECKWORD' => $arUser['CHECKWORD'],
                'ACTIVE' => $arUser['ACTIVE'],
                'EMAIL' => $arUser['EMAIL'],
                'DATE_REGISTER' => $arUser['DATE_REGISTER'],
            ];
            if (isset($arUser['UF_USER_LOCATIONS'])) {
                foreach ($arUser['UF_USER_LOCATIONS'] as $location_id) {
                    $arLocation = CIBlockElement::GetByID($location_id);
                    if ($Location = $arLocation->GetNext()) {
                        $this->arResult['USERS'][$arUser['ID']]['LOCATIONS'][] = [
                            "ID" => $Location['ID'],
                            "NAME" => $Location['NAME']
                        ];
                    }
                }
            }
            if (isset($arUser['UF_USER_OBJECTS'])) {
                foreach ($arUser['UF_USER_OBJECTS'] as $object_id) {
                    $arObject = CIBlockElement::GetByID($object_id);
                    if ($Object = $arObject->GetNext()) {
                        $this->arResult['USERS'][$arUser['ID']]['OBJECTS'][] = [
                            "ID" => $object_id,
                            "NAME" => $Object['NAME']
                        ];
                    }
                }
            }
        }
    }

    public function executeComponent()
    {
        $this->getUserList();
        $this->IncludeComponentTemplate($this->componentPage);
    }
}