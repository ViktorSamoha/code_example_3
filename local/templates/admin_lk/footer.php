</main>
<?

$locationStructure = getLocationStructure();

?>
<div class="r-modal" data-name="modal-add-category">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Создать категорию</h2>
        <form action="" class="add-characteristic-form form" id="add-category-form">
            <div class="select-block">
                <span class="input-label input-label--mb input-label--gray">Выберите раздел</span>
                <div class="custom-select">
                    <div class="custom-select_head">
                        <span class="custom-select_title">Раздел</span>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? foreach ($locationStructure['CATEGORY'] as $category): ?>
                            <div class="custom-select_item"
                                 data-id="<?= $category['ID'] ?>"><?= $category['NAME'] ?></div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
            <br>
            <div class="input input--lg">
                <label for="input-category-name" class="input-label">Название категории</label>
                <input type="text" name="category_name" id="input-category-name">
            </div>
            <div class="example">
                <div class="example_title">Загрузите иконку категории. Иконка в формате png или svg 40х40 пикселей, как
                    в
                    примере:
                </div>
                <div class="example-icons">
                    <div class="example-icons_item">
                        <img src="<?= ASSETS ?>images/category_01.svg" alt="">
                    </div>
                    <div class="example-icons_item">
                        <img src="<?= ASSETS ?>images/category_02.svg" alt="">
                    </div>
                    <div class="example-icons_item">
                        <img src="<?= ASSETS ?>images/category_03.svg" alt="">
                    </div>
                </div>
            </div>
            <div class="input-file-icons">
                <div class="input-file input-file-icon">
                    <input id="input-category-file-active" type="file" name="active_icon_file" accept=".svg, .png">
                    <label for="input-category-file-active">
                        <div class="input-file_icon">
                            <img src="<?= ASSETS ?>images/input-file_icon.svg" alt="">
                        </div>
                        <span class="input-file_title"
                              data-title="Загрузить активную иконку">Загрузить иконку</span>
                    </label>
                </div>
                <!--<div class="input-file input-file-icon">
                    <input id="input-category-file-inactive" type="file" name="inactive_icon_file" accept=".svg, .png">
                    <label for="input-category-file-inactive">
                        <div class="input-file_icon">
                            <img src="images/input-file_icon.svg" alt="">
                        </div>
                        <span class="input-file_title" data-title="Загрузить неактивную иконку">Загрузить неактивную иконку</span>
                    </label>
                </div>-->
            </div>
            <div class="add-characteristic-form_bottom">
                <button class="primary-btn primary-btn--xl" id="create-category">Создать категорию</button>
            </div>
            <div id="result-msg"></div>
        </form>
        <div class="block-result hidden">
            <div class="block-result_item block-success" id="category-done">
                <!-- class hidden -->
                <div class="block-success_text">
                    <p>Категория успешно добавлена. </p>
                    <p>Нажмите “Закрыть”, чтобы выйти или “Создать категорию”, если хотите добавить еще одну
                        категорию?</p>
                </div>
                <div class="block-success_btns">
                    <button class="block-success-btn block-success-btn--green js-close-modal" type="button">закрыть
                    </button>
                    <button class="block-success-btn block-success-btn--yellow js-open-form" type="button">Создать еще
                        категорию
                    </button>
                </div>
            </div>
            <div class="block-result_item block-success hidden" id="category-error">
                <!-- class hidden -->
                <div class="block-success_text"></div>
                <div class="block-success_btns">
                    <button class="block-success-btn block-success-btn--green" type="button">закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-add-partner">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Добавить партнера</h2>
        <form action="" class="add-characteristic-form form" id="add-partner-form">
            <div class="input input--lg">
                <label for="input-partner-name" class="input-label">Наименование партнера</label>
                <input type="text" name="PARTNER_NAME" id="input-partner-name">
            </div>
            <div class="input input--lg">
                <label for="input-partner-email" class="input-label">E-mail партнера</label>
                <input type="text" name="PARTNER_EMAIL" id="input-partner-email">
            </div>
            <div class="input input--lg">
                <label for="input-partner-telegram-api" class="input-label">Телеграм API партнера</label>
                <input type="text" name="PARTNER_TELEGRAM_API" id="input-partner-telegram-api">
            </div>
            <div class="input input--lg">
                <label for="input-partner-telegram-chat-id" class="input-label">ID Телеграм чата</label>
                <input type="text" name="PARTNER_CHAT_ID" id="input-partner-telegram-chat-id">
            </div>
            <div class="add-characteristic-form_bottom">
                <button class="primary-btn primary-btn--xl" id="add-partner">Добавить партнера</button>
            </div>
            <div id="result-msg"></div>
        </form>
        <div class="block-result hidden">
            <div class="block-result_item block-success" id="partner-done">
                <!-- class hidden -->
                <div class="block-success_text">
                    <p>Новый партнер успешно добавлен.</p>
                    <p>Нажмите “Закрыть”, чтобы выйти или “Добавить партнера”, если хотите добавить еще одного
                        партнера?</p>
                </div>
                <div class="block-success_btns">
                    <button class="block-success-btn block-success-btn--green js-close-modal" type="button">закрыть
                    </button>
                    <button class="block-success-btn block-success-btn--yellow js-open-form" type="button">Добавить
                        партнера
                    </button>
                </div>
            </div>
            <div class="block-result_item block-success hidden" id="partner-error">
                <!-- class hidden -->
                <div class="block-success_text"></div>
                <div class="block-success_btns">
                    <button class="block-success-btn block-success-btn--green" type="button">закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-add-characteristic">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>

        <h2 class="modal_title">Создать характеристику</h2>

        <form action="" class="add-characteristic-form form" id="add-characteristic-form">

            <div class="input input--lg">
                <label for="input-characteristic-name" class="input-label">Название характеристики</label>
                <input type="text" name="characteristic_name" id="input-characteristic-name">
            </div>

            <div class="example">
                <div class="example_title">Загрузите иконку категории. Иконка в формате png или svg 48х48 пикселей, как
                    в
                    примере:
                </div>
                <div class="example-icons">
                    <div class="example-icons_item">
                        <img src="<?= ASSETS ?>images/characteristic_01.svg" alt="">
                    </div>
                    <div class="example-icons_item">
                        <img src="<?= ASSETS ?>images/characteristic_02.svg" alt="">
                    </div>
                    <div class="example-icons_item">
                        <img src="<?= ASSETS ?>images/characteristic_03.svg" alt="">
                    </div>
                </div>
            </div>

            <div class="input-file input-file-icon">
                <input id="input-characteristic-file" type="file" name="characteristic_file" accept=".svg, .png">
                <label for="input-characteristic-file">
                    <div class="input-file_icon">
                        <img src="<?= ASSETS ?>images/input-file_icon.svg" alt="">
                    </div>
                    <span class="input-file_title" data-title="Загрузить иконку">Загрузить иконку</span>
                </label>
            </div>
            <div class="add-characteristic-form_bottom">
                <button class="primary-btn primary-btn--xl" id="add-characteristic-btn">Создать характеристику</button>
            </div>
        </form>
        <div class="block-result hidden">
            <div class="block-result_item block-success" id="characteristic-done">
                <!-- class hidden -->
                <div class="block-success_text">
                    <p>Характеристика успешно добавлена. </p>
                    <p>Нажмите “Закрыть”, чтобы выйти или “Создать еще характеристику”, если хотите добавить еще одну
                        характеристику?</p>
                </div>
                <div class="block-success_btns">
                    <button class="block-success-btn block-success-btn--green js-close-modal" type="button">закрыть
                    </button>
                    <button class="block-success-btn block-success-btn--yellow js-open-form" type="button">Создать еще
                        характеристику
                    </button>
                </div>
            </div>
            <div class="block-result_item block-success hidden" id="characteristic-error">
                <!-- class hidden -->
                <div class="block-success_text"></div>
                <div class="block-success_btns">
                    <button class="block-success-btn block-success-btn--green" type="button">закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-put-on-map">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>

        <h2 class="modal_title">Отметить на карте</h2>
        <h4 class="modal_subtitle">Введите координаты или отметьте на карте</h4>

        <form action="" class="add-characteristic-form form" id="add-coords-form">

            <div class="input input--lg">
                <label for="object-coords" class="input-label">Координаты через запятую </label>
                <input type="text" name="coords" id="object-coords">
            </div>

            <div class="f-map" id="map">

            </div>

            <div class="add-characteristic-form_bottom">
                <button class="primary-btn primary-btn--xl" id="save-coords-btn">Сохранить отметку</button>
            </div>
        </form>
        <div class="block-result hidden">
            <div class="block-result_item block-success" id="coords-done">
                <!-- class hidden -->
                <div class="block-success_text">
                    <p>Координаты успешно добавлены. </p>
                </div>
                <div class="block-success_btns">
                    <button class="block-success-btn block-success-btn--green js-close-modal" type="button">закрыть
                    </button>
                </div>
            </div>
            <div class="block-result_item block-success hidden" id="coords-error">
                <!-- class hidden -->
                <div class="block-success_text"></div>
                <div class="block-success_btns">
                    <button class="block-success-btn block-success-btn--green" type="button">закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-route-on-map">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Отметить на карте</h2>
        <h4 class="modal_subtitle">Укажите маршрут с помощью линии</h4>
        <form action="" class="add-characteristic-form form" id="add-route-form">
            <div class="f-map" id="route-map"></div>
            <div class="add-characteristic-form_bottom">
                <button class="primary-btn primary-btn--xl" id="save-route-btn">Сохранить маршрут</button>
            </div>
        </form>
        <div class="block-result hidden">
            <div class="block-result_item block-success" id="route-done">
                <!-- class hidden -->
                <div class="block-success_text">
                    <p>Маршрут успешно добавлен. </p>
                </div>
                <div class="block-success_btns">
                    <button class="block-success-btn block-success-btn--green js-close-modal" type="button">закрыть
                    </button>
                </div>
            </div>
            <div class="block-result_item block-success hidden" id="coords-error">
                <!-- class hidden -->
                <div class="block-success_text"></div>
                <div class="block-success_btns">
                    <button class="block-success-btn block-success-btn--green" type="button">закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-add-location">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>

        <h2 class="modal_title">Создать локацию</h2>

        <form action="" class="add-characteristic-form form" id="add-location-form">
            <div class="select-block">
                <span class="input-label input-label--mb input-label--gray">Выберите категорию</span>
                <div class="custom-select">
                    <div class="custom-select_head">
                        <span class="custom-select_title">Категория</span>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? foreach ($locationStructure['TYPE'] as $category): ?>
                            <div class="custom-select_item"
                                 data-id="<?= $category['ID'] ?>"><?= $category['NAME'] ?></div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
            <br>
            <div class="input input--lg">
                <label for="input-location-name" class="input-label">Название локации</label>
                <input type="text" name="location_name" id="input-location-name">
            </div>

            <div class="add-characteristic-form_bottom">
                <button class="primary-btn primary-btn--xl" id="add-location-btn">Создать локацию</button>
            </div>
        </form>
        <div class="block-result hidden">
            <div class="block-result_item block-success" id="location-done">
                <!-- class hidden -->
                <div class="block-success_text">
                    <p>Локация успешно добавлена. </p>
                    <p>Нажмите “Закрыть”, чтобы выйти или “Создать еще локацию”, если хотите добавить еще одну
                        локацию?</p>
                </div>
                <div class="block-success_btns">
                    <button class="block-success-btn block-success-btn--green js-close-modal" type="button">закрыть
                    </button>
                    <button class="block-success-btn block-success-btn--yellow js-open-form" type="button">Создать еще
                        локацию
                    </button>
                </div>
            </div>
            <div class="block-result_item block-success hidden" id="location-error">
                <!-- class hidden -->
                <div class="block-success_text"></div>
                <div class="block-success_btns">
                    <button class="block-success-btn block-success-btn--green" type="button">закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-cancel-reservation">
    <div class="r-modal_block">
        <button class="r-modal-close-btn" onclick="closeModal('.r-modal', 'modal-cancel-reservation')">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Отменить бронь?</h2>
        <div class="block-success">
            <div class="block-success_text">
                <p>Вы уверены, что хотите отменить бронь у данного объекта?</p>
            </div>
            <div class="block-success_btns">
                <button class="block-success-btn block-success-btn--green js-close-modal" type="button"
                        onclick="closeModal('.r-modal', 'modal-cancel-reservation')">нет
                </button>
                <button class="block-success-btn block-success-btn--yellow" type="button" id="object-order-cancel-btn">
                    да
                </button>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-delete-order">
    <div class="r-modal_block">
        <button class="r-modal-close-btn" onclick="closeModal('.r-modal', 'modal-delete-order')">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Удалить заказ?</h2>
        <div class="block-success">
            <div class="block-success_text">
                <p>Вы уверены, что хотите удалить заказ?</p>
            </div>
            <div class="block-success_btns">
                <button class="block-success-btn block-success-btn--green js-close-modal" type="button"
                        onclick="closeModal('.r-modal', 'modal-delete-order')">нет
                </button>
                <button class="block-success-btn block-success-btn--yellow" type="button"
                        id="modal-delete-order-confirm">
                    да
                </button>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-delete-object">
    <div class="r-modal_block">
        <button class="r-modal-close-btn" onclick="closeModal('.r-modal', 'modal-delete-object')">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Удалить объект?</h2>
        <div class="block-success">
            <div class="block-success_text">
                <p>Вы уверены, что хотите удалить объект?</p>
            </div>
            <div class="block-success_btns">
                <button class="block-success-btn block-success-btn--green js-close-modal" type="button"
                        onclick="closeModal('.r-modal', 'modal-delete-object')">нет
                </button>
                <button class="block-success-btn block-success-btn--yellow" type="button"
                        id="modal-delete-object-confirm">
                    да
                </button>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-delete-location">
    <div class="r-modal_block">
        <button class="r-modal-close-btn" onclick="closeModal('.r-modal', 'modal-delete-location')">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Удалить локацию?</h2>
        <div class="block-success">
            <div class="block-success_text">
                <p>Вы уверены, что хотите удалить локацию?</p>
            </div>
            <div class="block-success_btns">
                <button class="block-success-btn block-success-btn--green js-close-modal" type="button"
                        onclick="closeModal('.r-modal', 'modal-delete-location')">нет
                </button>
                <button class="block-success-btn block-success-btn--yellow" type="button"
                        id="modal-delete-location-confirm">
                    да
                </button>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-delete-category">
    <div class="r-modal_block">
        <button class="r-modal-close-btn" onclick="closeModal('.r-modal', 'modal-delete-category')">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Удалить категорию?</h2>
        <div class="block-success">
            <div class="block-success_text">
                <p>Вы уверены, что хотите удалить категорию?</p>
            </div>
            <div class="block-success_btns">
                <button class="block-success-btn block-success-btn--green js-close-modal" type="button"
                        onclick="closeModal('.r-modal', 'modal-delete-category')">нет
                </button>
                <button class="block-success-btn block-success-btn--yellow" type="button"
                        id="modal-delete-category-confirm">
                    да
                </button>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-delete-characteristic">
    <div class="r-modal_block">
        <button class="r-modal-close-btn" onclick="closeModal('.r-modal', 'modal-delete-characteristic')">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Удалить характеристику?</h2>
        <div class="block-success">
            <div class="block-success_text">
                <p>Вы уверены, что хотите удалить характеристику?</p>
            </div>
            <div class="block-success_btns">
                <button class="block-success-btn block-success-btn--green js-close-modal" type="button"
                        onclick="closeModal('.r-modal', 'modal-delete-characteristic')">нет
                </button>
                <button class="block-success-btn block-success-btn--yellow" type="button"
                        id="modal-delete-characteristic-confirm">
                    да
                </button>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-delete-user">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Удалить пользователя?</h2>
        <div class="block-success">
            <div class="block-success_text">
                <p>Вы уверены, что хотите удалить данного пользователя?</p>
            </div>
            <div class="block-success_btns">
                <button class="block-success-btn block-success-btn--green js-close-modal" type="button">нет</button>
                <button class="block-success-btn block-success-btn--yellow" type="button" id="user-delete-btn">
                    да
                </button>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-delete-partner">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Удалить партнера?</h2>
        <div class="block-success">
            <div class="block-success_text">
                <p>Вы уверены, что хотите удалить данного партнера?</p>
            </div>
            <div class="block-success_btns">
                <button class="block-success-btn block-success-btn--green js-close-modal" type="button">нет</button>
                <button class="block-success-btn block-success-btn--yellow" type="button" id="partner-delete-btn">
                    да
                </button>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-benefit">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Категории льготников</h2>
        <div class="modal-text">
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => "/includes/footer/modal_benefit_text.php"
                )
            );
            ?>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-block-user-vehicle">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Заблокировать транспортное средство</h2>
        <div class="block-success">
            <div class="block-success_text">
                <p>Вы уверены, что хотите заблокировать транспортное средство?</p>
            </div>
            <div class="block-success_btns">
                <button class="block-success-btn block-success-btn--green js-close-modal" type="button">нет</button>
                <button class="block-success-btn block-success-btn--yellow" type="button"
                        id="block-user-vehicle-confirm">
                    да
                </button>
            </div>
        </div>
    </div>
</div>
<div class="r-modal" data-name="modal-unblock-user-vehicle">
    <div class="r-modal_block">
        <button class="r-modal-close-btn">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="3.11719" width="4.40903" height="30.8632" transform="rotate(-45 0 3.11719)" fill="#313131"/>
                <rect x="3.11719" y="24.9414" width="4.40903" height="30.8632" transform="rotate(-135 3.11719 24.9414)"
                      fill="#313131"/>
            </svg>
        </button>
        <h2 class="modal_title">Разблокировать транспортное средство</h2>
        <div class="block-success">
            <div class="block-success_text">
                <p>Вы уверены, что хотите разблокировать транспортное средство?</p>
            </div>
            <div class="block-success_btns">
                <button class="block-success-btn block-success-btn--green js-close-modal" type="button">нет</button>
                <button class="block-success-btn block-success-btn--yellow" type="button"
                        id="unblock-user-vehicle-confirm">
                    да
                </button>
            </div>
        </div>
    </div>
</div>
<section class="modal" data-name="modal-success-transport-permission-notification">
    <div class="modal_block">
        <button class="modal-close-btn"
                onclick="closeModal('.modal', 'modal-success-transport-permission-notification')">
            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="2.82843" width="4" height="42.0722" transform="rotate(-45 0 2.82843)" fill="white"/>
                <rect x="3" y="32.8284" width="4" height="42.0722" transform="rotate(-135 3 32.8284)" fill="white"/>
            </svg>
        </button>
        <h3 class="modal_h3">Разрешение на транспортное средство успешно оформлено!</h3>
        <div class="m-btn-group">
            <a href="/admin/transport_permission_list/" class="primary-btn primary-btn--xl primary-btn--center">Вернуться
                в список разрешений</a>
            <a href="/admin/transport_permission_list/"
               class="primary-btn primary-btn--xl primary-btn--center primary-btn--green" id="blank-link"
               target="_blank">Квитанция</a>
        </div>
    </div>
</section>
<section class="modal" data-name="modal-success-user-permission-notification">
    <div class="modal_block">
        <button class="modal-close-btn" onclick="closeModal('.modal', 'modal-success-user-permission-notification')">
            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="2.82843" width="4" height="42.0722" transform="rotate(-45 0 2.82843)" fill="white"/>
                <rect x="3" y="32.8284" width="4" height="42.0722" transform="rotate(-135 3 32.8284)" fill="white"/>
            </svg>
        </button>
        <h3 class="modal_h3">Разрешение на посещение успешно оформлено!</h3>
        <div class="m-btn-group">
            <a href="/admin/permission_list/" class="primary-btn primary-btn--xl primary-btn--center">Вернуться в
                список</a>
            <a href="/admin/permission_list/" class="primary-btn primary-btn--xl primary-btn--center primary-btn--green"
               target="_blank"
               id="blank-link">Квитанция</a>
        </div>
    </div>
</section>
<section class="modal" data-name="modal-success-user-registration-notification">
    <div class="modal_block">
        <button class="modal-close-btn" onclick="closeModal('.modal', 'modal-success-user-registration-notification')">
            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="2.82843" width="4" height="42.0722" transform="rotate(-45 0 2.82843)" fill="white"/>
                <rect x="3" y="32.8284" width="4" height="42.0722" transform="rotate(-135 3 32.8284)" fill="white"/>
            </svg>
        </button>
        <h3 class="modal_h3">Новый посетитель успешно зарегистрирован!</h3>
        <div class="m-btn-group">
            <a href="/admin/visitors/" class="primary-btn primary-btn--xl primary-btn--center">Вернуться в
                список</a>
            <a href="/admin/visitors/" class="primary-btn primary-btn--xl primary-btn--center primary-btn--green"
               id="user-page-link">Карточка пользователя</a>
        </div>
    </div>
</section>
<section class="modal" data-name="modal-success-order-edit-notification">
    <div class="modal_block">
        <button class="modal-close-btn" onclick="closeModal('.modal', 'modal-success-order-edit-notification')">
            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="2.82843" width="4" height="42.0722" transform="rotate(-45 0 2.82843)" fill="white"/>
                <rect x="3" y="32.8284" width="4" height="42.0722" transform="rotate(-135 3 32.8284)" fill="white"/>
            </svg>
        </button>
        <h3 class="modal_h3">Изменения успешно внесены в заказ!</h3>
        <div class="m-btn-group">
            <a href="/admin/orders/" class="primary-btn primary-btn--xl primary-btn--center">Вернуться в
                список</a>
            <a href="/admin/orders/" class="primary-btn primary-btn--xl primary-btn--center primary-btn--green"
               id="order-blank">Квитанция</a>
        </div>
    </div>
</section>
<section class="modal" data-name="modal-success-order-notification">
    <div class="modal_block">
        <button class="modal-close-btn" onclick="closeModal('.modal', 'modal-success-order-notification')">
            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="2.82843" width="4" height="42.0722" transform="rotate(-45 0 2.82843)" fill="white"/>
                <rect x="3" y="32.8284" width="4" height="42.0722" transform="rotate(-135 3 32.8284)" fill="white"/>
            </svg>
        </button>
        <h3 class="modal_h3">Заказ успешно оформлен!</h3>
        <div class="m-btn-group">
            <a href="/admin/orders/" class="primary-btn primary-btn--xl primary-btn--center">Вернуться в
                список</a>
            <a href="/admin/orders/" class="primary-btn primary-btn--xl primary-btn--center primary-btn--green"
               id="order-blank">Квитанция</a>
        </div>
    </div>
</section>
<section class="modal" data-name="modal-success-object-edit-notification">
    <div class="modal_block">
        <button class="modal-close-btn" onclick="document.querySelector('.modal').classList.remove('active')">
            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="2.82843" width="4" height="42.0722" transform="rotate(-45 0 2.82843)" fill="white"/>
                <rect x="3" y="32.8284" width="4" height="42.0722" transform="rotate(-135 3 32.8284)" fill="white"/>
            </svg>
        </button>
        <h3 class="modal_h3">Изменения успешно внесены!</h3>
        <div class="m-btn-group">
            <a href="/admin/objects_list/" class="primary-btn primary-btn--xl primary-btn--center">Вернуться в
                список</a>
        </div>
    </div>
</section>
<section class="modal" data-name="modal-success-object-add-notification">
    <div class="modal_block">
        <button class="modal-close-btn" onclick="document.querySelector('.modal').classList.remove('active')">
            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="2.82843" width="4" height="42.0722" transform="rotate(-45 0 2.82843)" fill="white"/>
                <rect x="3" y="32.8284" width="4" height="42.0722" transform="rotate(-135 3 32.8284)" fill="white"/>
            </svg>
        </button>
        <h3 class="modal_h3">Объект успешно добавлен!</h3>
        <div class="m-btn-group">
            <a href="/admin/objects_list/" class="primary-btn primary-btn--xl primary-btn--center">Вернуться в
                список</a>
        </div>
    </div>
</section>
<section class="modal" data-name="modal-success-new-user-add-notification">
    <div class="modal_block">
        <button class="modal-close-btn" onclick="document.querySelector('.modal').classList.remove('active')">
            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="2.82843" width="4" height="42.0722" transform="rotate(-45 0 2.82843)" fill="white"/>
                <rect x="3" y="32.8284" width="4" height="42.0722" transform="rotate(-135 3 32.8284)" fill="white"/>
            </svg>
        </button>
        <h3 class="modal_h3">Новый пользователь успешно добавлен!</h3>
        <div class="m-btn-group">
            <a href="/admin/users/" class="primary-btn primary-btn--xl primary-btn--center">Вернуться в
                список</a>
            <a href="/admin/create_user/" class="primary-btn primary-btn--xl primary-btn--center">Добавить еще</a>
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
Asset::getInstance()->addJs(ASSETS . 'js/app/main.js');
Asset::getInstance()->addJs('/local/templates/admin_lk/lkScript.js');
Asset::getInstance()->addJs(DEFAULT_TEMPLATE . 'js/custom.js');
?>
</body>

</html>