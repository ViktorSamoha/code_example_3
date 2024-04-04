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

class User extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();
        return [
            'createCategory' => [
                'prefilters' => [],
            ],
        ];
    }

    public function init()
    {
        Loader::includeModule("iblock");
        $arVariables = array();
        $componentPage = false;
        $componentPage = CComponentEngine::ParseComponentPath(
            $this->arParams['SEF_FOLDER'],
            $this->arParams['SEF_URL_TEMPLATES'],
            $arVariables
        );
        if ($componentPage === false && parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) == $this->arParams['SEF_FOLDER']) {
            $componentPage = 'personal';
        }
        $this->componentPage = $componentPage;
    }

    public function executeComponent()
    {
        $this->init();
        $this->IncludeComponentTemplate($this->componentPage);
    }
}