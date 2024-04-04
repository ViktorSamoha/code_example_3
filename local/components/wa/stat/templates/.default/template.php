<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

?>
<form action="" class="form-create-report" id="report-config-form">
    <div class="form-block">
        <h3 class="form-block_title">Укажите параметры для формирования отчета</h3>
        <div class="input-group">
            <div class="input input--md">
                <label for="" class="input-label">Дата или диапазон дат</label>
                <input type="text" class="input-date c-input" id="arrival-date">
                <input type="text" class="input-date c-input" id="departure-date">
            </div>
            <div class="select-block">
                <span class="input-label input-label--mb input-label--gray">Локация</span>
                <div class="custom-select" id="location-select">
                    <div class="custom-select_head">
                        <span class="custom-select_title">Выберите локацию</span>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? if (!empty($arResult['LOCATIONS'])): ?>
                            <div class="custom-select_item">
                                <div class="checkbox checkbox-w-btn">
                                    <input type="checkbox"
                                           id="location_checkbox_0"
                                           value="all"
                                           name="LOCATIONS[]"
                                    >
                                    <label for="location_checkbox_0">
                                        <div class="checkbox_text">Все локации</div>
                                    </label>
                                </div>
                            </div>
                            <? foreach ($arResult['LOCATIONS'] as $location_id => $location): ?>
                                <div class="custom-select_item">
                                    <div class="checkbox checkbox-w-btn">
                                        <input type="checkbox"
                                               id="<?= 'location_checkbox_' . $location_id ?>"
                                               value="<?= $location_id ?>"
                                               name="LOCATIONS[]"
                                        >
                                        <label for="<?= 'location_checkbox_' . $location_id ?>">
                                            <div class="checkbox_text"><?= $location['NAME'] ?></div>
                                        </label>
                                    </div>
                                </div>
                            <? endforeach; ?>
                        <? endif; ?>
                    </div>
                </div>
            </div>
            <div class="select-block">
                <span class="input-label input-label--mb input-label--gray">Объект</span>
                <div class="custom-select" id="object-select">
                    <div class="custom-select_head">
                        <span class="custom-select_title">Выберите объект</span>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? if (!empty($arResult['OBJECTS'])): ?>
                            <div class="custom-select_item">
                                <div class="checkbox checkbox-w-btn">
                                    <input type="checkbox"
                                           id="object_checkbox_0"
                                           value="all"
                                           name="OBJECTS[]"
                                    >
                                    <label for="object_checkbox_0">
                                        <div class="checkbox_text">Все объекты</div>
                                    </label>
                                </div>
                            </div>
                            <? foreach ($arResult['OBJECTS'] as $object_id => $object): ?>
                                <div class="custom-select_item">
                                    <div class="checkbox checkbox-w-btn">
                                        <input type="checkbox"
                                               id="<?= 'object_checkbox_' . $object_id ?>"
                                               value="<?= $object_id ?>"
                                               name="OBJECTS[]"
                                        >
                                        <label for="<?= 'object_checkbox_' . $object_id ?>">
                                            <div class="checkbox_text"><?= $object['NAME'] ?></div>
                                        </label>
                                    </div>
                                </div>
                            <? endforeach; ?>
                        <? endif; ?>
                    </div>
                </div>
            </div>
            <div class="select-block">
                <span class="input-label input-label--mb input-label--gray">Оператор</span>
                <div class="custom-select" id="user-select">
                    <div class="custom-select_head">
                        <span class="custom-select_title">Выберите оператора</span>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? if (!empty($arResult['OBJECTS'])): ?>
                            <div class="custom-select_item">
                                <div class="checkbox checkbox-w-btn">
                                    <input type="checkbox"
                                           id="user_checkbox_0"
                                           value="all"
                                           name="USERS[]"
                                    >
                                    <label for="user_checkbox_0">
                                        <div class="checkbox_text">Все операторы</div>
                                    </label>
                                </div>
                            </div>
                            <? foreach ($arResult['USERS'] as $user_id => $user): ?>
                                <div class="custom-select_item">
                                    <div class="checkbox checkbox-w-btn">
                                        <input type="checkbox"
                                               id="<?= 'user_checkbox_' . $user_id ?>"
                                               value="<?= $user_id ?>"
                                               name="USERS[]"
                                        >
                                        <label for="<?= 'user_checkbox_' . $user_id ?>">
                                            <div class="checkbox_text"><?= $user['LOGIN'] ?></div>
                                        </label>
                                    </div>
                                </div>
                            <? endforeach; ?>
                        <? endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-block">
        <h3 class="form-block_title">Данные, которые необходимо вывести в отчете</h3>
        <div class="checkbox-list" id="report-settings">
            <div class="checkbox">
                <input type="checkbox" id="checkbox_01" value="object_count" name="SETTINGS[]">
                <label for="checkbox_01">
                    <div class="checkbox_text">Количество сданных объектов</div>
                </label>
            </div>
            <div class="checkbox">
                <input type="checkbox" id="checkbox_02" value="earnings" name="SETTINGS[]">
                <label for="checkbox_02">
                    <div class="checkbox_text">Выручка</div>
                </label>
            </div>
            <div class="checkbox">
                <input type="checkbox" id="checkbox_03" value="permits_count" name="SETTINGS[]">
                <label for="checkbox_03">
                    <div class="checkbox_text">Количество выданных разрешений</div>
                </label>
            </div>
            <div class="checkbox">
                <input type="checkbox" id="checkbox_04" value="stay_duration" name="SETTINGS[]">
                <label for="checkbox_04">
                    <div class="checkbox_text">Длительность пребывания</div>
                </label>
            </div>
        </div>
    </div>
    <button class="primary-btn primary-btn--xl" id="generate-report">Сформировать отчет</button>
    <div class="form-warn-message"></div>
</form>

<div id="ajax" style="display: none">
    <div id="ajax-content"></div>
    <div class="group-btn">
        <a href="/admin/stat/" class="gray-btn group-btn_item" type="button">Назад</a>
        <a href="#" class="primary-btn group-btn_item" id="get-excel" type="button">Скачать excel</a>
    </div>
</div>