<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<form action="" class="orders-filter">
    <div class="orders-filter_wrap">
        <div class="input input--flex orders-filter_input--md">
            <label for="" class="input-label">Фамилия</label>
            <input type="text" value="<?= $arParams['FILTER_LAST_NAME_VALUE'] ?>" name="LAST_NAME">
        </div>
        <div class="input input--flex">
            <label for="" class="input-label">Телефон</label>
            <input type="text" value="<?= $arParams['FILTER_PHONE_VALUE'] ?>" name="WORK_PHONE">
        </div>
        <div class="input input--flex">
            <label for="" class="input-label">E-mail</label>
            <input type="email" value="<?= $arParams['FILTER_EMAIL_VALUE'] ?>" name="EMAIL">
        </div>
        <input type="button" value="Применить" class="gray-btn" id="set-filter">
    </div>
    <div class="orders-filter_wrap">
        <a href="/admin/visitor_add/" class="btn-create">
            <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                        d="M13.668 6.86914V8.41406H0.0234375V6.86914H13.668ZM7.61133 0.511719V15.0039H5.9707V0.511719H7.61133Z"
                        fill="#313131"/>
            </svg>
            <span>Создать</span>
        </a>
    </div>
</form>