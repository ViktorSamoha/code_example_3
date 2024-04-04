<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8"/>
    <? $APPLICATION->ShowHead() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><? $APPLICATION->ShowTitle() ?></title>

    <? $APPLICATION->SetAdditionalCSS(ASSETS . "css/swiper.min.css"); ?>
    <? $APPLICATION->SetAdditionalCSS(ASSETS . "css/fancybox.css"); ?>
    <? $APPLICATION->SetAdditionalCSS(ASSETS . "css/flatpickr.min.css"); ?>
    <? $APPLICATION->SetAdditionalCSS(ASSETS . "css/main.css?rev=973216c712956166cb66ad77433a422d"); ?>
    <? $APPLICATION->AddHeadScript(ASSETS . 'js/lib/jquery.min.js'); ?>
    <? $APPLICATION->SetAdditionalCSS("/local/templates/.default/assets/css/custom.css"); ?>

</head>

<body>

<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>

<main class="main <?= defined("ERROR_404") ? "main--404" : "" ?>">
