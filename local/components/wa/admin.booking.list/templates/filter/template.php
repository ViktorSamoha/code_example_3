<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<form action="" class="orders-filter">
    <div class="orders-filter_wrap">
        <div class="input input--flex">
            <label for="" class="input-label">Уникальный код</label>
            <input type="text" value="<?= $arParams['FILTER_ID_VALUE'] ?>" name="ID">
        </div>
        <div class="input input--flex">
            <label for="" class="input-label">Дата оформления</label>
            <input type="text" value="<?= $arParams['FILTER_DATE_VALUE'] ?>" class="c-input-date"
                   name="DATE">
        </div>
        <div class="input input--flex">
            <label for="search-field-fio" class="input-label">ФИО</label>
            <input type="text" value="<?= $arParams['FILTER_USER_FIO_VALUE'] ?>" name="USER_FIO">
        </div>
        <div class="input input--flex">
            <label for="search-field-car-id" class="input-label">Номер машины</label>
            <input type="text" value="<?= $arParams['FILTER_VEHICLE_ID_VALUE'] ?>" name="VEHICLE_ID">
        </div>
        <div class="input input--flex">
            <label for="search-field-car-id" class="input-label">Название объекта</label>
            <input type="text" value="<?= $arParams['FILTER_OBJECT_NAME_VALUE'] ?>" name="OBJECT_NAME">
        </div>
        <? if (count($arResult['LOCATIONS']) > 1): ?>
            <div class="select-block" id="user-location-filter">
                <div class="custom-select">
                    <div class="custom-select_head">
                        <? if ($arResult['SELECTED_LOCATION']): ?>
                            <span class="custom-select_title"
                                  data-selected-id="<?= $arResult['SELECTED_LOCATION']['ID'] ?>"><?= $arResult['SELECTED_LOCATION']['NAME'] ?></span>
                        <? else: ?>
                            <span class="custom-select_title"
                                  data-selected-id="">Выберите локацию</span>
                        <? endif; ?>
                        <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                             fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#000"/>
                        </svg>
                    </div>
                    <div class="custom-select_body">
                        <? foreach ($arResult['LOCATIONS'] as $userLocation): ?>
                            <div class="custom-select_item"
                                 data-id="<?= $userLocation['ID'] ?>"><?= $userLocation['NAME'] ?>
                            </div>
                        <? endforeach; ?>
                        <div class="custom-select_item"
                             data-id="">Все локации
                        </div>
                    </div>
                </div>
            </div>
        <? endif; ?>
    </div>
    <div class="orders-filter_wrap">
        <input type="button" value="Применить" class="gray-btn" id="set-filter">
        <? if ($arResult['USER']['IS_ADMIN'] && in_array(7, $arResult['USER']['GROUPS'])): ?>
            <a href="javascript:void(0);" onclick="archive();"
               class="rounded-btn rounded-btn--sm <?= !$arResult['ARCHIVE'] ? 'rounded-btn--gray' : '' ?>">Архив
                заказов</a>
        <? endif; ?>
    </div>
</form>