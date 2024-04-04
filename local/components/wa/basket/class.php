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
use Bitrix\Main\Web\Cookie;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class WABasket extends CBitrixComponent implements Controllerable
{

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'deleteFromBasket' => [
                'prefilters' => [],
            ],
        ];
    }

    public function payOrderAction(): AjaxJson
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $cookie = $request->getCookie("basket");
        if ($cookie) {
            $basket = unserialize($cookie);
            if ($basket && is_array($basket)) {
                //TODO:СДЕЛАТЬ ОПЛАТУ ЗАКАЗА
                return AjaxJson::createSuccess([
                    'basket' => $basket
                ]);
            } else {
                return AjaxJson::createError(null, 'нет значений!');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function deleteFromBasketAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (!empty($post['ID'])) {
            $cookie = Application::getInstance()->getContext()->getRequest()->getCookie("basket");
            if ($cookie) {
                $basket = unserialize($cookie);
                if ($basket && is_array($basket)) {
                    foreach ($basket as $i => $basketItem) {
                        if ($basketItem['ID'] == $post['ID']) {
                            unset($basket[$i]);
                        }
                    }
                    $newCookie = new Cookie("basket", serialize($basket));
                    Application::getInstance()->getContext()->getResponse()->addCookie($newCookie);
                    return AjaxJson::createSuccess();
                } else {
                    return AjaxJson::createError(null, 'Корзина не является массивом!');
                }
            } else {
                return AjaxJson::createError(null, 'Не удалось получить корзину!');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function getBasketData(&$arResult)
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $cookie = $request->getCookie("basket");
        if ($cookie) {
            $basket = unserialize($cookie);
            if ($basket && is_array($basket)) {
                $arResult['BASKET_DATA'] = $basket;
            }
        }
    }

    public function executeComponent()
    {
        $this->getBasketData($this->arResult);
        $this->includeComponentTemplate();
    }
}