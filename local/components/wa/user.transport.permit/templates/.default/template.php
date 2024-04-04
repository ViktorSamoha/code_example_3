<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<form action="" class="form-transport-permit">
    <div class="form-block">
        <h3 class="form-block_title">Данные о ТС</h3>
        <div class="lk-select-wrap">
            <? if ($arResult['USER_VEHICLES']): ?>
                <div class="select-block">
                    <div class="input-label input-label--mb input-label--gray">Выбрать доступные транспортные средства
                    </div>
                    <div class="custom-select">
                        <div class="custom-select_head">
                            <span class="custom-select_title" id="user-vehicle"></span>
                            <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L7 7L13 1" stroke="#000"/>
                            </svg>
                        </div>
                        <div class="custom-select_body">
                            <? foreach ($arResult['USER_VEHICLES'] as $userVehicle): ?>
                                <? if ($userVehicle['POWER']): ?>
                                    <div class="custom-select_item"
                                         data-id="<?= $userVehicle['ID'] ?>"><?= $userVehicle['VEHICLE_TYPE'] . ' ' . $userVehicle['MODEL'] ?>
                                    </div>
                                <? else: ?>
                                    <div class="custom-select_item"
                                         data-id="<?= $userVehicle['ID'] ?>"><?= $userVehicle['VEHICLE_TYPE'] . ' ' . $userVehicle['MODEL'] ?>
                                        - свыше 10 л. с.
                                    </div>
                                <? endif; ?>
                            <? endforeach; ?>
                        </div>
                    </div>
                </div>
            <? else: ?>
                нет доступных транспортных средств
            <? endif; ?>
            <a href="/user/add_transport/" class="btn-create" type="button">
                <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M13.668 6.86914V8.41406H0.0234375V6.86914H13.668ZM7.61133 0.511719V15.0039H5.9707V0.511719H7.61133Z"
                            fill="#313131"/>
                </svg>
                <span>Добавить TC</span>
            </a>
        </div>

    </div>

    <div class="form-block">
        <h3 class="form-block_title">Данные о маршруте</h3>
        <div class="select-block">
            <div class="input-label input-label--mb input-label--gray">Выбрать маршрут</div>
            <div class="custom-select">
                <div class="custom-select_head">
                    <span class="custom-select_title" id="route"></span>
                    <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                    </svg>
                </div>
                <div class="custom-select_body">
                    <? foreach ($arResult['ROUTS'] as $route): ?>
                        <div class="custom-select_item" data-id="<?= $route['ID'] ?>"><?= $route['NAME'] ?></div>
                    <? endforeach; ?>
                </div>
            </div>
        </div>
        <div id="route-map"></div>
    </div>

    <div class="form-block">
        <h3 class="form-block_title">Срок пребывания</h3>
        <div class="m-input-dates m-input-dates--md js-input-date-group">
            <div class="m-input-date-block">
                <label for="" class="input-label">Дата заезда<span class="color-red">*</span></label>
                <input type="text" class="input-date" name="ARRIVAL_DATE">
            </div>
            <div class="m-input-date-block">
                <label for="" class="input-label">Дата выезда<span class="color-red">*</span></label>
                <input type="text" class="input-date second-range-input" name="DEPARTURE_DATE">
            </div>
        </div>
    </div>
    <div class="form-block">
        <h3 class="form-block_title">Разрешение на посещение</h3>

        <div class="radio-group">
            <div class="radio">
                <input type="radio" id="visit-permission-yes" name="visit-permission" value="yes" checked>
                <label for="visit-permission-yes">
                    <div class="radio_text">Есть</div>
                </label>
            </div>
            <div class="radio">
                <input type="radio" id="visit-permission-no" name="visit-permission" value="no">
                <label for="visit-permission-no">
                    <div class="radio_text">Нет</div>
                </label>
            </div>
        </div>
        <div class="input input--lg" id="visit-permission-input">
            <label for="">Номер разрешения на посещение</label>
            <input type="text" value="" name="PERMISSION_CODE">
        </div>

    </div>
    <div class="form-block border-top">
        <div class="checkbox-list">
            <div class="checkbox">
                <input type="checkbox" id="personal-data-confirm">
                <label for="personal-data-confirm">
                    <div class="checkbox_text">Даю согласие на обработку персональных данных</div>
                </label>
            </div>
            <div class="checkbox">
                <input type="checkbox" id="visiting-rules-confirm">
                <label for="visiting-rules-confirm">
                    <div class="checkbox_text">Я согласен с правилами посещения территории нац. парка</div>
                </label>
            </div>
        </div>
    </div>
    <button class="primary-btn primary-btn--lg" id="get-permission">Оформить</button>
    <br>
    <div id="form-errors"></div>
</form>