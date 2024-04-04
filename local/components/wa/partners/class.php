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

class WAPartners extends CBitrixComponent implements Controllerable
{

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'deletePartner' => [
                'prefilters' => [],
            ],

        ];
    }

    public function deletePartnerAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['id'])) {
            Loader::includeModule("highloadblock");
            $hlblock = HL\HighloadBlockTable::getById(HL_PARTNERS)->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            if ($entity_data_class::Delete($post['id'])) {
                $arResult = [];
                $data = $entity_data_class::getList(array(
                    "select" => array("*"),
                    "order" => array("ID" => "ASC"),
                    "filter" => []
                ));
                while($arData = $data->Fetch()){
                    $arResult[] = $arData;
                }
                return AjaxJson::createSuccess([
                    'data' => $arResult,
                ]);
            }
        } else {
            return AjaxJson::createError(null, 'Ошибка - отсутствует id пользователя');
        }
    }

    public function init()
    {
        Loader::includeModule("highloadblock");

        //достаем из hl блока "Партнеры" список партнеров
        $hlblock = HL\HighloadBlockTable::getById(HL_PARTNERS)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();

        $data = $entity_data_class::getList(array(
            "select" => array("*"),
            "order" => array("ID" => "ASC"),
            "filter" => array()
        ));

        while ($arData = $data->Fetch()) {
            $this->arResult['PARTNERS'][] = [
                'ID' => $arData['ID'],
                'NAME' => $arData['UF_NAME'],
                'PARTNER_EMAIL' => $arData['UF_PARTNER_EMAIL'],
                'TELEGRAM_API' => $arData['UF_TELEGRAM_API'],
                'CHAT_ID' => $arData['UF_CHAT_ID'],
            ];
        }
    }

    public function executeComponent()
    {
        $this->init();
        $this->includeComponentTemplate();
    }
}