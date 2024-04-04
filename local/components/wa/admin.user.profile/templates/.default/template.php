<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if ($arResult['USER_FIELDS']): ?>
    <form action="" class="form-visiting-permit">
        <div class="form-block">
            <h3 class="form-block_title">Данные посетителя</h3>
            <div class="input-group">
                <!-- class input--disabled чтобы нельзя было изменить значения -->
                <div class="input input--sm input--disabled">
                    <label for="" class="input-label">Фамилия<span class="color-red">*</span></label>
                    <input type="text" value="<?= $arResult['USER_FIELDS']['LAST_NAME'] ?>">
                </div>
                <div class="input input--sm input--disabled">
                    <label for="" class="input-label">Имя<span class="color-red">*</span></label>
                    <input type="text" value="<?= $arResult['USER_FIELDS']['NAME'] ?>">
                </div>
                <div class="input input--sm input--disabled">
                    <label for="" class="input-label">Отчество</label>
                    <input type="text" value="<?= $arResult['USER_FIELDS']['SECOND_NAME'] ?>">
                </div>
                <div class="input input--sm input--disabled">
                    <label for="" class="input-label">Телефон</label>
                    <input type="text" value="<?= $arResult['USER_FIELDS']['PHONE'] ?>">
                </div>
                <div class="input input--sm input--disabled">
                    <label for="" class="input-label">E-mail</label>
                    <input type="text" value="<?= $arResult['USER_FIELDS']['EMAIL'] ?>">
                </div>
                <div class="input input--sm"></div>
            </div>
        </div>
        <? if ($arResult['USER_FIELDS']['USER_RECORD_DATA']['PREF_CATEGORY']): ?>
            <div class="form-block-w-dop-inputs js-show-dop-inputs">
                <div class="input input--sm">
                    <label for="" class="input-label">Льготная категория</label>
                    <input type="text"
                           value="<?= $arResult['USER_FIELDS']['USER_RECORD_DATA']['PREF_CATEGORY'] ?>"
                           readonly>
                </div>
                <div class="dop-inputs_item ">
                    <? if ($arResult['USER_FIELDS']['USER_RECORD_DATA']['PREF_CATEGORY'] == "Проживающие в близлежащих населенных пунктах"): ?>
                        <div class="input input--sm">
                            <label for="" class="input-label">Населенный пункт</label>
                            <input type="text"
                                   value="<?= $arResult['USER_FIELDS']['USER_RECORD_DATA']['LOCATION'] ?>"
                                   readonly>
                        </div>
                    <? endif; ?>
                    <div class="input-group">
                        <div class="input input--sm">
                            <label for="" class="input-label">Номер док-та подтверждающего
                                льготу</label>
                            <input type="text"
                                   value="<?= $arResult['USER_FIELDS']['USER_RECORD_DATA']['PREF_DOC_NUMBER'] ?>"
                                   readonly>
                        </div>
                        <div class="input input--sm">
                            <label for="" class="input-label">Дата выдачи</label>
                            <input type="text" class="input-date"
                                   value="<?= $arResult['USER_FIELDS']['USER_RECORD_DATA']['PREF_DOC_DATE'] ?>"
                                   readonly>
                        </div>
                        <div class="input input--sm input--message"></div>
                    </div>
                </div>

            </div>
        <? endif; ?>
        <? if ($arResult['USER_TRANSPORT']): ?>
            <div class="form-block">
                <h3 class="form-block_title">Транспортные средства посетителя</h3>
                <? foreach ($arResult['USER_TRANSPORT'] as $arTransport): ?>
                    <table class="blank-table blank-table--auto">
                        <tr>
                            <th>Тип транспортного средства</th>
                            <td><?= $arTransport['VEHICLE_TYPE'] ?></td>
                        </tr>
                        <tr>
                            <th>Права</th>
                            <td><?= 'Серия: ' . $arTransport['DRIVING_LICENSE_SERIES'] . ' Номер: ' . $arTransport['DRIVING_LICENSE_NUMBER'] ?></td>
                        </tr>
                        <tr>
                            <th>Модель</th>
                            <td><?= $arTransport['MODEL'] ?></td>
                        </tr>
                        <tr>
                            <th>Дата тех. осмотра</th>
                            <td><?= $arTransport['INSPECTION_DATE'] ?></td>
                        </tr>
                        <? if ($arTransport['BLOCKED'] == 'Да'): ?>
                            <tr>
                                <th style="color: red">Транспортное средство заблокировано!</th>
                                <td></td>
                            </tr>
                        <? endif; ?>
                    </table>

                    <br>
                <? endforeach; ?>
            </div>
        <? endif; ?>
        <? if ($arResult['USER_GROUP']): ?>
            <div class="form-block">
                <h3 class="form-block_title">Группа посетителя</h3>
                <? foreach ($arResult['USER_GROUP'] as $arVisitor): ?>
                    <div class="input-group">
                        <div class="input input--sm input--disabled">
                            <label for="" class="input-label">Фамилия</label>
                            <input type="text" value="<?= $arVisitor['LAST_NAME'] ?>">
                        </div>
                        <div class="input input--sm input--disabled">
                            <label for="" class="input-label">Имя</label>
                            <input type="text" value="<?= $arVisitor['NAME'] ?>">
                        </div>
                        <div class="input input--sm input--disabled">
                            <label for="" class="input-label">Отчество</label>
                            <input type="text" value="<?= $arVisitor['SECOND_NAME '] ?>">
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
        <? endif; ?>
    </form>
<? endif; ?>