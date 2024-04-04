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

class AdminVisitorsList extends CBitrixComponent implements Controllerable
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

    public function getUserList(&$arResult, $params)
    {
        $arVisitors = [];
        $arParams["FIELDS"] = array("ID", "NAME", "LAST_NAME", "SECOND_NAME", 'WORK_PHONE', 'EMAIL');
        $filter = array("ACTIVE" => "Y", "GROUPS_ID" => VISITOR_PERMISSION_GROUP);
        if (isset($params['FILTER_VALUE']) && !empty($params['FILTER_VALUE'])) {
            $filter = array_merge($filter, $params['FILTER_VALUE']);
        }
        $rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), $filter, $arParams);
        while ($res = $rsUsers->GetNext()) {
            $arVisitors[] = $res;
        }
        if (!empty($arVisitors)) {
            $arResult['VISITORS_LIST'] = $arVisitors;
        }
    }

    public function executeComponent()
    {
        $this->getUserList($this->arResult, $this->arParams);
        $this->IncludeComponentTemplate($this->componentPage);
    }
}