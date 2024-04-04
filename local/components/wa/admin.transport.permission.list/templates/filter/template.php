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
        <div class="input input--flex">
            <label for="" class="input-label">Статус</label>
            <div class="custom-select">
                <div class="custom-select_head">
                    <span class="custom-select_title" id="filter-status-select"><?= $arResult['STATUS_VALUE'] ?></span>
                    <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L7 7L13 1" stroke="#000"></path>
                    </svg>
                </div>
                <div class="custom-select_body">
                    <div class="custom-select_item" data-id="all">Все</div>
                    <div class="custom-select_item" data-id="under-consideration">На рассмотрении</div>
                    <div class="custom-select_item" data-id="deny">Отказано</div>
                    <div class="custom-select_item" data-id="approve">Одобрено</div>
                    <div class="custom-select_item" data-id="blocked">Заблокирован</div>
                </div>
            </div>
        </div>
    </div>
    <div class="orders-filter_wrap">
        <input type="button" value="Применить" class="gray-btn" id="set-filter">
        <a href="/admin/transport_permission_add/" class="rounded-btn rounded-btn--sm">Оформить разрешение</a>
        <a href="javascript:void(0);" onclick="archive();"
           class="rounded-btn rounded-btn--sm <?= !$archive ? 'rounded-btn--gray' : '' ?>">Архив</a>
    </div>
</form>
