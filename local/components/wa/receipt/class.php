<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Iblock\Component\Tools;

use Bitrix\Main\Diag\Debug;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class WAReceipt extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();
        return [
            '' => [
                'prefilters' => [],
            ],
        ];
    }


    /*public function init()
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
            $componentPage = 'default';
        }
        if (empty($componentPage)) {
            \Bitrix\Iblock\Component\Tools::process404(
                trim($this->arParams['MESSAGE_404']) ?: 'Элемент или раздел инфоблока не найден',
                true,
                true,
                true,
                ''
            );
            return;
        }
        CComponentEngine::InitComponentVariables(
            $componentPage,
            null,
            array(),
            $arVariables
        );
        $this->componentPage = $componentPage;
    }

    public function executeComponent()
    {
        $this->init();
        $this->IncludeComponentTemplate($this->componentPage);
    }*/


    protected array $arComponentVariables = [
        "SECTION",
    ];

    public function executeComponent()
    {
        Loader::includeModule('iblock');

        if ($this->arParams["SEF_MODE"] === "Y") {
            $componentPage = $this->sefMode();
        }

        //Отдать 404 статус если не найден шаблон
        if (!$componentPage) {
            Tools::process404(
                $this->arParams["MESSAGE_404"],
                ($this->arParams["SET_STATUS_404"] === "Y"),
                ($this->arParams["SET_STATUS_404"] === "Y"),
                ($this->arParams["SHOW_404"] === "Y"),
                $this->arParams["FILE_404"]
            );
        }

        $this->IncludeComponentTemplate($componentPage);
    }

    protected function sefMode()
    {
        //Значение алиасов по умолчанию.
        $arDefaultVariableAliases404 = [];

        /**
         * Значение масок для шаблонов по умолчанию. - маски без корневого раздела,
         * который указывается в $arParams["SEF_FOLDER"]
         */
        $arDefaultUrlTemplates404 = [
            "user" => "user/#ELEMENT_CODE#/",
            "transport" => "transport/#ELEMENT_CODE#/",
        ];

        //В этот массив будут заполнены переменные, которые будут найдены по маске шаблонов url
        $arVariables = [];

        $engine = new CComponentEngine($this);
        //Нужно добавлять для парсинга SECTION_CODE_PATH и SMART_FILTER_PATH (жадные шаблоны)
        $engine->addGreedyPart("#SECTION_CODE_PATH#");
        $engine->addGreedyPart("#SMART_FILTER_PATH#");
        $engine->setResolveCallback(["CIBlockFindTools", "resolveComponentEngine"]);

        //Объединение дефолтных параметров масок шаблонов и алиасов. Параметры из настроек заменяют дефолтные.
        $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $this->arParams["SEF_URL_TEMPLATES"]);
        $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $this->arParams["VARIABLE_ALIASES"]);

        //Поиск шаблона
        $componentPage = $engine->guessComponentPath(
            $this->arParams["SEF_FOLDER"],
            $arUrlTemplates,
            $arVariables
        );

        //Проброс значений переменных из алиасов.
        CComponentEngine::initComponentVariables($componentPage, $this->arComponentVariables, $arVariableAliases, $arVariables);
        $this->arResult = [
            "VARIABLES" => $arVariables,
            "ALIASES" => $arVariableAliases
        ];

        return $componentPage;
    }

}