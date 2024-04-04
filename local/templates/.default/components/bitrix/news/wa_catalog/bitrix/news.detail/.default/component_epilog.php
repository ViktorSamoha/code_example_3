<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;

if (!$this->__template) {
    $this->InitComponentTemplate();
}

$this->__template->SetViewTarget('aside_price');
?>
    <div class="aside-price">
        стоимость <span
                class="aside-price_value"><?= $arResult['PRICE'] ?> ₽</span>
        <span class="aside-price_subtitle">/ <?= $arResult['PRICE_TYPE'] ?> (1 чел.)</span>
    </div>
<?
$this->__template->EndViewTarget();

?>