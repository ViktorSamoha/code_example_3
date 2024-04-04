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

class AdminLocationList extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'setSectionActivity' => [
                'prefilters' => [],
            ],
            'setSectionPartner' => [
                'prefilters' => [],
            ],

        ];
    }

    public function setSectionPartnerAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['SECTION_ID']) && isset($post['ACTION']) && isset($post['PARTNER_ID'])) {
            if (Loader::includeModule("iblock")) {
                $sectionPartners = [];
                $db_list = CIBlockSection::GetList([], ["IBLOCK_ID" => IB_LOCATIONS, 'ID' => $post['SECTION_ID']], true, ['ID', 'IBLOCK_ID','UF_PARTNERS']);
                while ($ar_result = $db_list->GetNext()) {
                    $sectionPartners = $ar_result['UF_PARTNERS'];
                }
                switch ($post['ACTION']) {
                    case 'set':
                        $sectionPartners[] = $post['PARTNER_ID'];
                        break;
                    case 'delete':
                        if (!empty($sectionPartners)) {
                            foreach ($sectionPartners as $k => $pertnerId) {
                                if ($pertnerId == $post['PARTNER_ID']) {
                                    unset($sectionPartners[$k]);
                                }
                            }
                        }
                        break;
                }
                $bs = new CIBlockSection;
                $arFields = array(
                    "UF_PARTNERS" => $sectionPartners,
                );
                if ($bs->Update($post['SECTION_ID'], $arFields)) {
                    return AjaxJson::createSuccess();
                } else {
                    return AjaxJson::createError(null, $bs->LAST_ERROR);
                }

            } else {
                return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function setSectionActivityAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['SECTION_ID']) && isset($post['ACTION'])) {
            if (Loader::includeModule("iblock")) {
                $activeCondition = 'Y';
                switch ($post['ACTION']) {
                    case 'activate':
                        $activeCondition = 'Y';
                        break;
                    case 'deactivate':
                        $activeCondition = 'N';
                        break;
                }
                $bs = new CIBlockSection;
                $arFields = array(
                    "ACTIVE" => $activeCondition,
                );
                if ($bs->Update($post['SECTION_ID'], $arFields)) {
                    return AjaxJson::createSuccess();
                } else {
                    return AjaxJson::createError(null, $bs->LAST_ERROR);
                }

            } else {
                return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function getSectionList(&$arResult, $params)
    {
        if (Loader::includeModule("iblock")) {
            $arSections = [];
            $arFilter = array('IBLOCK_ID' => IB_LOCATIONS);
            if ($params['PARENT_SECTION_ID']) {
                $arFilter['SECTION_ID'] = $params['PARENT_SECTION_ID'];
            }
            $db_list = CIBlockSection::GetList([], $arFilter, true, ['ID', 'IBLOCK_ID', 'NAME', 'ACTIVE', 'UF_PARTNERS']);
            while ($ar_result = $db_list->GetNext()) {
                $arSections[] = [
                    'ID' => $ar_result['ID'],
                    'NAME' => $ar_result['NAME'],
                    'ACTIVE' => $ar_result['ACTIVE'],
                    'ELEMENT_CNT' => $ar_result['ELEMENT_CNT'],
                    'SELECTED_PARTNERS' => !empty($ar_result['UF_PARTNERS']) ? getPartnersList($ar_result['UF_PARTNERS']) : false,
                ];
            }
            if (!empty($arSections)) {
                $arResult['SECTIONS'] = $arSections;
            }
        }
    }

    public function executeComponent()
    {
        $this->getSectionList($this->arResult, $this->arParams);
        $this->IncludeComponentTemplate($this->componentPage);
    }
}