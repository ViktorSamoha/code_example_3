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

class Admin extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'createCategory' => [
                'prefilters' => [],
            ],
            'createCharacteristic' => [
                'prefilters' => [],
            ],
            'createLocation' => [
                'prefilters' => [],
            ],
            'addPartner' => [
                'prefilters' => [],
            ],
            'deleteCharacteristic' => [
                'prefilters' => [],
            ],
            'deleteLocation' => [
                'prefilters' => [],
            ],
            'deleteCategory' => [
                'prefilters' => [],
            ],
        ];
    }

    public function createCategoryAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();//получаем данные формы
        $active_icon = $this->request->getFile('active_icon_file');
        $inactive_icon = $this->request->getFile('inactive_icon_file');
        if (isset($post['category_name']) && $post['category_name'] != '') {
            if (Loader::includeModule("iblock")) {
                $function_result = '';
                $ACTIVE = 'Y';
                $IBLOCK_ID = IB_LOCATIONS;
                $NAME = $post['category_name'];
                $bs = new CIBlockSection;
                $arFields = array(
                    "ACTIVE" => $ACTIVE,
                    "IBLOCK_ID" => $IBLOCK_ID,
                    "IBLOCK_SECTION_ID" => $post['IBLOCK_SECTION_ID'] ? $post['IBLOCK_SECTION_ID'] : false,
                    "CODE" => Cutil::translit($NAME, "ru", ["replace_space" => "_", "replace_other" => "_"]),
                    "NAME" => $NAME,
                    "UF_CB_ACTIVE_SVG_ICON" => $active_icon ? $active_icon : '',
                    "UF_CB_INACTIVE_SVG_ICON" => $inactive_icon ? $inactive_icon : '',
                );
                if ($ID > 0) {
                    $res = $bs->Update($ID, $arFields);
                } else {
                    $ID = $bs->Add($arFields);//добавляем раздел
                    $res = ($ID > 0);
                    $function_result = [
                        'ID' => $ID,
                        "NAME" => $NAME,
                        "CODE" => Cutil::translit($NAME, "ru", ["replace_space" => "_", "replace_other" => "_"]),
                    ];
                }
                if (!$res) {
                    return AjaxJson::createError(null, $bs->LAST_ERROR);
                } else {
                    return AjaxJson::createSuccess([
                        'data' => $function_result,
                    ]);
                }
            } else {
                return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
            }
        } else {
            return AjaxJson::createError(null, 'не заполнено обязательное поле имя');
        }
    }

    public function createCharacteristicAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();//получаем данные формы
        $characteristic_icon = $this->request->getFile('characteristic_file');
        if (isset($post['characteristic_name']) && $post['characteristic_name'] != '') {
            if (Loader::includeModule("highloadblock")) {
                $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_FEATURES)->fetch();
                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();
                $xml_id = Cutil::translit($post['characteristic_name'], "ru", ["replace_space" => "-", "replace_other" => "-"]);
                $data = array(
                    "UF_OF_NAME" => $post['characteristic_name'],
                    "UF_OF_ICON" => $characteristic_icon,
                    "UF_XML_ID" => $xml_id,
                );
                $result = $entity_data_class::add($data);
                if ($result->isSuccess()) {
                    $function_result = [
                        "XML_ID" => $xml_id,
                        "NAME" => $post['characteristic_name'],
                    ];
                    return AjaxJson::createSuccess([
                        'data' => $function_result,
                    ]);
                } else {
                    return AjaxJson::createError(null, 'Ошибка: ' . implode(', ', $result->getErrors()) . "");
                }
            } else {
                return AjaxJson::createError(null, 'ошибка подключения модуля highloadblock');
            }
        } else {
            return AjaxJson::createError(null, 'не заполнено обязательное поле имя');
        }
    }

    public function addPartnerAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();//получаем данные формы
        if ((isset($post['PARTNER_NAME']) && $post['PARTNER_NAME'] != '') && (isset($post['PARTNER_EMAIL']) || isset($post['PARTNER_TELEGRAM_API']))) {
            if (Loader::includeModule("highloadblock")) {
                $hlblock = HL\HighloadBlockTable::getById(HL_PARTNERS)->fetch();
                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();
                $data = array(
                    "UF_NAME" => $post['PARTNER_NAME'],
                );
                if (isset($post['PARTNER_EMAIL'])) {
                    $data['UF_PARTNER_EMAIL'] = $post['PARTNER_EMAIL'];
                }
                if (isset($post['PARTNER_TELEGRAM_API'])) {
                    $data['UF_TELEGRAM_API'] = $post['PARTNER_TELEGRAM_API'];
                    $data['UF_CHAT_ID'] = $post['PARTNER_CHAT_ID'];
                }
                $result = $entity_data_class::add($data);
                if ($result->isSuccess()) {
                    $function_result = [
                        'ID' => $result->getId(),
                        "NAME" => $post['PARTNER_NAME'],
                    ];
                    return AjaxJson::createSuccess([
                        'data' => $function_result,
                    ]);
                } else {
                    return AjaxJson::createError(null, 'Ошибка: ' . implode(', ', $result->getErrors()) . "");
                }
            } else {
                return AjaxJson::createError(null, 'ошибка подключения модуля highloadblock');
            }
        } else {
            return AjaxJson::createError(null, 'не заполнено обязательное поле "Наименование"');
        }
    }

    public function createLocationAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['location_name']) && $post['location_name'] != '') {
            if (Loader::includeModule("iblock")) {
                $bs = new CIBlockSection;
                $arFields = array(
                    "MODIFIED_BY" => $GLOBALS['USER']->GetID(),
                    "IBLOCK_SECTION_ID" => $post['IBLOCK_SECTION_ID'] ? $post['IBLOCK_SECTION_ID'] : false,
                    "IBLOCK_ID" => IB_LOCATIONS,
                    "NAME" => $post['location_name'],
                    "CODE" => Cutil::translit($post['location_name'], "ru", ["replace_space" => "_", "replace_other" => "_"]),
                    "ACTIVE" => "Y",
                );
                if ($ID = $bs->Add($arFields)) {
                    return AjaxJson::createSuccess([]);
                } else {
                    return AjaxJson::createError(null, $bs->LAST_ERROR);
                }
            } else {
                return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
            }
        } else {
            return AjaxJson::createError(null, 'не заполнено обязательное поле имя');
        }
    }

    public function deleteLocationAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['id'])) {
            global $DB;
            if (Loader::includeModule("iblock")) {
                //отвязываем дочерние объекты от удаляемого раздела
                $arSelect = array("ID");
                $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "SECTION_ID" => $post['id']);
                $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    CIBlockElement::SetElementSection($arFields['ID'], 0);
                }
                $DB->StartTransaction();
                if (!CIBlockSection::Delete($post['id'])) {
                    $DB->Rollback();
                    return AjaxJson::createError(null, 'Ошибка удаления раздела ' . $post['id']);
                } else {
                    $DB->Commit();
                    return AjaxJson::createSuccess([]);
                }
            } else {
                return AjaxJson::createError(null, 'Ошибка подключения модуля iblock');
            }
        } else {
            return AjaxJson::createError(null, 'не заполнено обязательное поле id');
        }
    }

    public function deleteCategoryAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['id'])) {
            if (Loader::includeModule("iblock")) {
                global $DB;
                //отвязываем дочерние объекты от удаляемого раздела
                $arSelect = array("ID");
                $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, "SECTION_ID" => $post['id']);
                $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    CIBlockElement::SetElementSection($arFields['ID'], 0);
                }
                $DB->StartTransaction();
                if (!CIBlockSection::Delete($post['id'])) {
                    $DB->Rollback();
                    return AjaxJson::createError(null, 'ошибка удаления раздела ' . $post['id']);
                } else {
                    $DB->Commit();
                    return AjaxJson::createSuccess([]);
                }
            } else {
                return AjaxJson::createError(null, 'ошибка подключения модуля iblock');
            }
        } else {
            return AjaxJson::createError(null, 'не заполнено обязательное поле id');
        }
    }

    public function deleteCharacteristicAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['xml_id'])) {
            if (Loader::includeModule("highloadblock")) {
                $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_FEATURES)->fetch();
                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();
                $data = $entity_data_class::getList(array(
                    "select" => array("*"),
                    "order" => array("ID" => "DESC"),
                    "filter" => array("UF_XML_ID" => $post['xml_id'])
                ));
                while ($arData = $data->Fetch()) {
                    $ELEMENT_ID = $arData['ID'];
                }
                $result = $entity_data_class::Delete($ELEMENT_ID);
                if ($result) {
                    return AjaxJson::createSuccess([]);
                } else {
                    return AjaxJson::createError(null, 'шибка удаления характеристики');
                }
            } else {
                return AjaxJson::createError(null, 'ошибка подключения модуля highloadblock');
            }
        } else {
            return AjaxJson::createError(null, 'не заполнено обязательное поле');
        }
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
            $componentPage = 'orders';
        }
        $this->componentPage = $componentPage;
    }

    public function executeComponent()
    {
        $this->init();
        $this->IncludeComponentTemplate($this->componentPage);
    }
}