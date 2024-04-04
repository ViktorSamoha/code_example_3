<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="page">
    <div class="page_content">

        <div class="form-block form-block--border" id="ajax-user-data">
            <div class="title-wrap">
                <h3 class="form-block_title">Личные данные</h3>
                <a href="" class="btn-edit">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M1.5 17.9997V15.731H16.5V17.9997H1.5ZM3.0375 13.7435V11.2497L10.0312 4.25599L12.525 6.74974L5.53125 13.7435H3.0375ZM13.35 5.92474L10.8563 3.43099L12.4313 1.85599C12.5688 1.69349 12.725 1.60911 12.9 1.60286C13.075 1.59661 13.25 1.68099 13.425 1.85599L14.8875 3.31849C15.05 3.48099 15.1312 3.65286 15.1312 3.83411C15.1312 4.01536 15.0625 4.18724 14.925 4.34974L13.35 5.92474Z"
                                fill="#ED8C00"/>
                    </svg>
                    <span id="edit-user-data">Редактировать</span>
                </a>
            </div>
            <table class="blank-table blank-table--auto">
                <tr>
                    <th>ФИО</th>
                    <td><?= $arResult['USER_DATA']['LAST_NAME'] . ' ' . $arResult['USER_DATA']['NAME'] . ' ' . $arResult['USER_DATA']['SECOND_NAME'] ?></td>
                </tr>
                <tr>
                    <th>Телефон</th>
                    <td><?= $arResult['USER_DATA']['PHONE'] ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= $arResult['USER_DATA']['EMAIL'] ?></td>
                </tr>
                <? if ($arResult['USER_DATA']['PREF_CATEGORY']): ?>
                    <tr>
                        <th>Льготная категория</th>
                        <td><?= $arResult['USER_DATA']['PREF_CATEGORY']['VALUE'] ?></td>
                    </tr>
                <? else: ?>
                    <tr>
                        <th>Льготная категория</th>
                        <td>Отсутствует</td>
                    </tr>
                <? endif; ?>
            </table>
        </div>

        <div class="form-block form-block--border" id="ajax-vehicle-list">
            <h3 class="form-block_title">Транспортные средства</h3>
            <? if (!empty($arResult['USER_DATA']['USER_TRANSPORT'])): ?>
                <? foreach ($arResult['USER_DATA']['USER_TRANSPORT'] as $arTransport): ?>
                    <div class="title-wrap title-wrap--flex-end">
                        <div class="title-wrap_right">
                            <button class="btn-edit" onclick="editVehicle(<?= $arTransport['ID'] ?>)">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                            d="M1.5 17.9997V15.731H16.5V17.9997H1.5ZM3.0375 13.7435V11.2497L10.0312 4.25599L12.525 6.74974L5.53125 13.7435H3.0375ZM13.35 5.92474L10.8563 3.43099L12.4313 1.85599C12.5688 1.69349 12.725 1.60911 12.9 1.60286C13.075 1.59661 13.25 1.68099 13.425 1.85599L14.8875 3.31849C15.05 3.48099 15.1312 3.65286 15.1312 3.83411C15.1312 4.01536 15.0625 4.18724 14.925 4.34974L13.35 5.92474Z"
                                            fill="#ED8C00"/>
                                </svg>
                                <span>Редактировать</span>
                            </button>
                            <button class="btn-edit js-open-r-modal" data-name="modal-delete-vehicle"
                                    onclick="deleteVehicle(<?= $arTransport['ID'] ?>)">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                            d="M5.79167 17.0833C5.47222 17.0833 5.19444 16.9652 4.95833 16.7291C4.72222 16.493 4.60417 16.2083 4.60417 15.8749V4.54159H3.75V3.60409H7.3125V3.02075H12.6875V3.60409H16.25V4.54159H15.3958V15.8749C15.3958 16.2083 15.2778 16.493 15.0417 16.7291C14.8056 16.9652 14.5278 17.0833 14.2083 17.0833H5.79167ZM7.9375 14.3749H8.89583V6.29159H7.9375V14.3749ZM11.1042 14.3749H12.0625V6.29159H11.1042V14.3749Z"
                                            fill="#EA9A00"/>
                                </svg>
                                <span>Удалить</span>
                            </button>
                        </div>
                    </div>
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
                <? endforeach; ?>
                <br>
            <? else: ?>
                <br>
            <? endif; ?>
            <a href="/user/add_transport/" class="btn-create">
                <svg width="14" height="16" viewBox="0 0 14 16" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M13.668 6.86914V8.41406H0.0234375V6.86914H13.668ZM7.61133 0.511719V15.0039H5.9707V0.511719H7.61133Z"
                            fill="#313131"/>
                </svg>
                <span>Добавить ТС</span>
            </a>
        </div>

    </div>
    <div class="page_aside">
        <div class="group">
            <h2 class="group_title">МОЯ ГРУППА</h2>
            <ul class="group_list" id="ajax-group-list">
                <? foreach ($arResult['USER_DATA']['USER_GROUP'] as $visitor): ?>
                    <li class="group_item">
                        <div class="group-item_title"><?= $visitor['LAST_NAME'] . ' ' . $visitor['NAME'] . ' ' . $visitor['SECOND_NAME'] ?></div>
                        <button class="group-item_btn"
                                onclick="deleteVisitor(<?= $visitor['ID'] ?>)" type="button">
                            <svg width="14" height="15" viewBox="0 0 14 15" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M2.79167 14.0833C2.47222 14.0833 2.19444 13.9652 1.95833 13.7291C1.72222 13.493 1.60417 13.2083 1.60417 12.8749V1.54159H0.75V0.604085H4.3125V0.020752H9.6875V0.604085H13.25V1.54159H12.3958V12.8749C12.3958 13.2083 12.2778 13.493 12.0417 13.7291C11.8056 13.9652 11.5278 14.0833 11.2083 14.0833H2.79167ZM4.9375 11.3749H5.89583V3.29159H4.9375V11.3749ZM8.10417 11.3749H9.0625V3.29159H8.10417V11.3749Z"
                                        fill="#EA9A00"/>
                            </svg>
                        </button>
                    </li>
                <? endforeach; ?>
            </ul>
            <a href="/user/add_visitor/" class="btn-create">
                <svg width="14" height="16" viewBox="0 0 14 16" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M13.668 6.86914V8.41406H0.0234375V6.86914H13.668ZM7.61133 0.511719V15.0039H5.9707V0.511719H7.61133Z"
                            fill="#313131"/>
                </svg>
                <span>Добавить посетителя</span>
            </a>
        </div>
    </div>
</div>
