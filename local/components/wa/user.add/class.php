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

class userAdd extends CBitrixComponent implements Controllerable
{
    public $arrResult = [];

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'addNewUser' => [
                'prefilters' => [],
            ],
            'getLocationObjects' => [
                'prefilters' => [],
            ],
        ];
    }

    public function getLocationObjectsAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['locations'])) {
            $locationObjects = getObjects($post['locations']);
            if ($locationObjects) {
                $html = '';
                foreach ($locationObjects as $locationObject) {
                    $html .= '<div class="custom-select_item">
                        <div class="checkbox checkbox-w-btn">
                        <input type="checkbox" id="checkbox_' . $locationObject['ID'] . '" value="' . $locationObject['ID'] . '" name="UF_USER_OBJECTS[]">
                        <label for="checkbox_' . $locationObject['ID'] . '">
                        <div class="checkbox_text">' . $locationObject['NAME'] . '</div>
                        </label></div></div>';
                }
                if ($html != '') {
                    return AjaxJson::createSuccess([
                        'html' => $html,
                    ]);
                } else {
                    return AjaxJson::createError(null, 'Ошибка генерации списка объектов');
                }
            } else {
                return AjaxJson::createError(null, 'Объекты не найдены');
            }
        } else {
            return AjaxJson::createError(null, 'не заполнены обязательные поля');
        }
    }

    public function addNewUserAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['USER_LOGIN'])
            && isset($post['USER_EMAIL'])
            && isset($post['USER_PASSWORD'])
        ) {
            $LOGIN = $post['USER_LOGIN'];
            $EMAIL = $post['USER_EMAIL'];
            $PASSWORD = $post['USER_PASSWORD'];
            $CONFIRM_PASSWORD = $post['USER_CONFIRM_PASSWORD'];
            $ROLE = $post['USER_ROLE'];
            $OBJECTS = $post['UF_USER_OBJECTS'];
            $LOCATIONS = $post['UF_USER_LOCATIONS'];
            $user = new CUser;
            $arFields = array(
                "LOGIN" => $LOGIN,
                "EMAIL" => $EMAIL,
                "ACTIVE" => "Y",
                "PASSWORD" => $PASSWORD,
                "CONFIRM_PASSWORD" => $CONFIRM_PASSWORD,
                "GROUP_ID" => [$ROLE],
                "UF_USER_OBJECTS" => $OBJECTS,
                "UF_USER_LOCATIONS" => $LOCATIONS,
            );
            if ($newUserId = $user->Add($arFields)) {
                return AjaxJson::createSuccess([
                    'NEW_USER_ID' => $newUserId,
                ]);
            } else {
                return AjaxJson::createError(null, $user->LAST_ERROR);
            }
        } else {
            return AjaxJson::createError(null, 'не заполнены обязательные поля');
        }
    }

    public function init()
    {
        $this->arResult['LOCATIONS'] = getLocationStructure();
        $this->arResult['OBJECTS'] = getObjects();
    }

    public function executeComponent()
    {
        $this->init();
        $this->IncludeComponentTemplate($this->componentPage);
    }
}