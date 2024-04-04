</main>

<div class="r-modal" data-name="modal-delete-visitor">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Удалить посетителя?</h2>
        <div class="block-success">
            <div class="block-success_text">
                <p>Вы уверены, что хотите удалить посетителя?</p>
            </div>
            <div class="block-success_btns">
                <button class="block-success-btn block-success-btn--green js-close-modal" type="button">нет</button>
                <button class="block-success-btn block-success-btn--yellow" type="button" id="delete-visitor-confirm">
                    да
                </button>
            </div>
        </div>
    </div>
</div>

<div class="r-modal" data-name="modal-delete-vehicle">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Удалить транспортное средство?</h2>
        <div class="block-success">
            <div class="block-success_text">
                <p>Вы уверены, что хотите удалить транспортное средство?</p>
            </div>
            <div class="block-success_btns">
                <button class="block-success-btn block-success-btn--green js-close-modal" type="button">нет</button>
                <button class="block-success-btn block-success-btn--yellow" type="button" id="delete-vehicle-confirm">
                    да
                </button>
            </div>
        </div>
    </div>
</div>

<section class="modal" data-name="modal-success-permission-notification">
    <div class="modal_block">
        <button class="modal-close-btn">
            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="2.82843" width="4" height="42.0722" transform="rotate(-45 0 2.82843)" fill="white"/>
                <rect x="3" y="32.8284" width="4" height="42.0722" transform="rotate(-135 3 32.8284)" fill="white"/>
            </svg>
        </button>
        <h3 class="modal_h3">Разрешение успешно оформлено!</h3>
        <div class="m-btn-group">
            <a href="/user/" class="primary-btn primary-btn--xl primary-btn--center">Вернуться в личный кабинет</a>
            <a href="/user/" target="_blank" class="primary-btn primary-btn--xl primary-btn--center primary-btn--green" id="blank-link">Квитанция</a>
        </div>
    </div>
</section>

<?

use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(ASSETS . 'js/lib/swiper.min.js');
Asset::getInstance()->addJs(ASSETS . 'js/lib/fancybox.umd.js');
Asset::getInstance()->addJs(ASSETS . 'js/lib/flatpickr.min.js');
Asset::getInstance()->addJs(ASSETS . 'js/lib/flatpickr-ru.js');
Asset::getInstance()->addJs(ASSETS . 'js/lib/rangePlugin.js');
Asset::getInstance()->addJs(ASSETS . 'js/lib/cleave.min.js');
Asset::getInstance()->addJs(ASSETS . 'js/lib/jquery.min.js');
Asset::getInstance()->addJs(ASSETS . 'js/app/main.js');
Asset::getInstance()->addJs(DEFAULT_TEMPLATE . 'js/custom.js');
?>

<!--modal-add-visitor-->
<?
$APPLICATION->IncludeComponent(
    "wa:user.add.visitor",
    "add.visitor.modal",
    [],
    false
);
?>
<!--modal-add-visitor-->

</body>

</html>