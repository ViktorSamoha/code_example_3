<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
$archive = false;
if ($_GET['ARCHIVE'] && $_GET['ARCHIVE'] == 'Y') {
    $archive = true;
}
?>
<form action="" class="orders-filter">
    <div class="orders-filter_wrap">
        <div class="input input--flex">
            <label for="" class="input-label">Уникальный код</label>
            <input type="text" value="<?= $arParams['FILTER_ID_VALUE'] ?>" name="ID">
        </div>
        <div class="input input--flex orders-filter_input--md">
            <label for="" class="input-label">ФИО</label>
            <input type="text" value="<?= $arParams['FILTER_USER_FIO_VALUE'] ?>" name="USER_FIO">
        </div>
        <div class="input input--flex">
            <label for="" class="input-label">Дата</label>
            <input type="text" value="<?= $arParams['FILTER_DATE_VALUE'] ?>" class="input-date js-input-date"
                   name="DATE">
        </div>
    </div>
    <div class="orders-filter_wrap">
        <input type="button" value="Применить" class="gray-btn" id="set-filter">
        <a href="javascript:void(0);" onclick="archive();"
           class="rounded-btn rounded-btn--sm <?= !$archive ? 'rounded-btn--gray' : '' ?>">Архив
            заказов</a>
    </div>
</form>