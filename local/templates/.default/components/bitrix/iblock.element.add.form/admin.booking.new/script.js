let order_data = new Object(null);//данные со всех форм

let filter_data = new Object(null);//данные для фильтрации объектов

var myMap = null;
var objManager = null;

//переменные для расчета стоимости
let OBJECT_DATA = {
    OBJECT_COST: 0,
    OBJECT_DAILY_COST: 0,
    COST_PER_PERSON: 0,
    COST_PER_PERSON_ONE_DAY: 0,
    CAPACITY_ESTIMATED: 0,
    CAPACITY_MAXIMUM: 0,
    VISIT_PERMISSION_COST: 0,
    FIXED_COST: 0,
}

function switchTab() {
    var activeTab = document.querySelector('.tab.active');
    var id = Number(activeTab.dataset.id) + 1;
    activeTab.classList.add('done');
    switchTabsByBtn(id);
}

//функция выбирает действие по нажатию на кнопку
function doBtnAction(action) {
    if (action === 'order') {
        let period = $('#time-select-radio').find('input[name="radio"]:checked').attr('data-period');
        let arrival_time = $('#arrival-time-select').find('.custom-select_title').attr('data-selected-id');
        let departure_time = $('#departure-time-select').find('.custom-select_title').attr('data-selected-id');
        if (checkForm(
            'form[name="iblock_add"]',
            'input[name="PROPERTY[9][0]"]',
            'input[name="PROPERTY[10][0]"]',
            '#adult-quantity',
            '',
            '',
            '',
            period,
            arrival_time,
            departure_time
        )) {
            if ($('form[name="iblock_add"]').find('input[name="time_limit_value"]').val() === 'N') {
                checkBookingPossibility(
                    'input[name="object_id_inpt"]',
                    'input[name="PROPERTY[11][0][VALUE]"]',
                    'input[name="PROPERTY[12][0][VALUE]"]',
                    '#arrival-time-select',
                    '#departure-time-select',
                    null,
                    period
                ).then((resolve) => {
                    if (resolve === 'true') {
                        $('#map').html('');
                        initPreloader(false, '.lk_content');
                        switchTab();
                        let form = $('form[name="iblock_add"]');
                        addDataToObject(new FormData(form[0]), order_data);
                        order_data['PROPERTY[NAME][0]'] = "Бронь " + $('input[name="PROPERTY[9][0]"]').val() + ' ' + $('input[name="PROPERTY[10][0]"]').val();
                        order_data['PROPERTY[32][0]'] = $('#booking-sum-value').html();
                        order_data['PROPERTY[13][0]'] = $('#arrival-time-select').find('.custom-select_title').data('selectedId');
                        order_data['PROPERTY[14][0]'] = $('#departure-time-select').find('.custom-select_title').data('selectedId');
                        submitForm(form.attr('action'), order_data);
                        setTimeout(() => {
                            createDocument(order_data, '/ajax/orders/create_new_blank.php');
                            initReceiptMap(order_data['PROPERTY[21][0][VALUE]'], myMap, objManager);
                            destroyPreloader('.preloader', '.lk_content');
                        }, 2000);
                    } else {
                        pushFormError(['Данное время недоступно для бронирования']);
                    }
                })
            } else {
                $('#map').html('');
                initPreloader(false, '.lk_content');
                switchTab();
                let form = $('form[name="iblock_add"]');
                addDataToObject(new FormData(form[0]), order_data);
                order_data['PROPERTY[NAME][0]'] = "Бронь " + $('input[name="PROPERTY[9][0]"]').val() + ' ' + $('input[name="PROPERTY[10][0]"]').val();
                order_data['PROPERTY[32][0]'] = $('#booking-sum-value').html();
                order_data['PROPERTY[13][0]'] = $('#time-select').find('.custom-select_title').data('selectedId');
                order_data['PROPERTY[TIME_LIMIT]'] = $('input[name="time_limit_value"]').val();
                submitForm(form.attr('action'), order_data);
                setTimeout(() => {
                    createDocument(order_data, '/ajax/orders/create_new_blank.php');
                    initReceiptMap(order_data['PROPERTY[21][0][VALUE]'], myMap, objManager);
                    destroyPreloader('.preloader', '.lk_content');
                }, 2000);
            }
        }
    }
}

function tabBtnClick() {
    $('input[name="PROPERTY[21][0][VALUE]"]').change(function () {
        order_data['PROPERTY[21][0][VALUE]'] = $(this).val();
        OBJECT_DATA = getObjectCost(order_data['PROPERTY[21][0][VALUE]'], OBJECT_DATA);
        calendarSwitcher('#time-select-radio', 'input[name="PROPERTY[11][0][VALUE]"]', order_data['PROPERTY[21][0][VALUE]'], 'input[name="PROPERTY[12][0][VALUE]"]');
        $('form[name="iblock_add"]').append('<input type="hidden" name="PROPERTY[21][0][VALUE]" value="' + order_data['PROPERTY[21][0][VALUE]'] + '">');
        reinitBookingFormCalendar('input[name="PROPERTY[11][0][VALUE]"]', order_data['PROPERTY[21][0][VALUE]'], '.second-range-input', $('#time-select-radio input:checked').data('period'));
    });
    $('button[data-tab-action="order"]').click(function (e) {
        let btn_action = $(e.target).data('tabAction');
        doBtnAction(btn_action);
    });
}

//функция получает данные для фильтрации объектов
function getFilterData(data) {
    initPreloader(false, '.tabs-content_item[data-id="2"]');
    ajaxWrap('/ajax/orders/filter_order.php', data).then(
        function (data) {
            if (data) {
                let data_object = JSON.parse(data);
                if (Object.keys(data_object).length > 10) {
                    createSelect(data_object);
                } else {
                    createRadioList(data_object);
                }
                onObjectSelect();
                destroyPreloader('.preloader', '.tabs-content_item[data-id="2"]');
            } else {
                $('#object-select-block').html('Нет подходящих объектов');
                //onObjectSelect();
                destroyPreloader('.preloader', '.tabs-content_item[data-id="2"]');
            }
        }
    );
}

//функция добавляет данные в фильтр
function setFilterData() {
    let location_filter_block = $('#location-filter-block');
    let category_filter_block = $('#category-filter-block');
    let category_filter = $(category_filter_block).find('.custom-select_title')[0];
    let location_filter = $(location_filter_block).find('.custom-select_title')[0];

    let categoryObserver = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutationRecord) {
            let category_id = $(mutationRecord.target).attr('data-selected-id');
            filter_data.category_id = category_id;
            filter_data.location_id = $(location_filter_block).find('.custom-select_title').attr('data-selected-id');
            getFilterData(filter_data);
        });
    });
    let locationObserver = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutationRecord) {
            let location_id = $(mutationRecord.target).attr('data-selected-id');
            filter_data.location_id = location_id;
            $.ajax({
                url: '/ajax/orders/order_location_filter.php',
                method: 'post',
                data: filter_data,
                success: function (data) {
                    category_filter_block.find('.custom-select_body').html(data);
                }
            });
        });
    });
    $('input[name="filter-period"]').change(function () {
        filter_data.period = $(this).data('period');
        getFilterData(filter_data);
    });
    $('input[name="arrival-date-input"]').change(function () {
        filter_data.arrival_date = $(this).val();
        filter_data.category_id = $(category_filter_block).find('.custom-select_title').attr('data-selected-id');
        filter_data.location_id = $(location_filter_block).find('.custom-select_title').attr('data-selected-id');
        if ($('input[name="departure-date-input"]').val()) {
            filter_data.departure_date = $('input[name="departure-date-input"]').val();
            getFilterData(filter_data);
        }
    });
    categoryObserver.observe(category_filter, {attributes: true, attributeFilter: ['data-selected-id']});
    locationObserver.observe(location_filter, {attributes: true, attributeFilter: ['data-selected-id']});
}

//функция собирает значения полей формы и пересчитывает стоимость аренды
function RecalculateSum() {
    let form = $('form[name="iblock_add"]');
    let sum = form.find('#booking-sum-value');
    let time_limit_value = $('input[name="time_limit_value"]').val();
    if (time_limit_value && time_limit_value === 'Y') {
        sum.html(calculateFixedObjectSum(
            form.find('input[name="PROPERTY[16][0]"]').val(),
            form.find('input[name="PROPERTY[17][0]"]').val(),
            OBJECT_DATA.CAPACITY_MAXIMUM,
            OBJECT_DATA.CAPACITY_ESTIMATED,
            form.find('input[name="PROPERTY[15]"]:checked').val(),
            OBJECT_DATA.VISIT_PERMISSION_COST,
            OBJECT_DATA.FIXED_COST,
        ));
    } else {
        sum.html(calculateOrderSum(
            OBJECT_DATA.OBJECT_COST,
            OBJECT_DATA.OBJECT_DAILY_COST,
            OBJECT_DATA.COST_PER_PERSON,
            OBJECT_DATA.COST_PER_PERSON_ONE_DAY,
            form.find('input[name="PROPERTY[16][0]"]').val(),
            OBJECT_DATA.CAPACITY_ESTIMATED,
            OBJECT_DATA.CAPACITY_MAXIMUM,
            form.find('input[name="PROPERTY[17][0]"]').val(),
            OBJECT_DATA.VISIT_PERMISSION_COST,
            form.find('input[name="PROPERTY[15]"]:checked').val(),
            $('#time-select-radio').find('input[name="radio"]:checked').data('period'),
            calculateOrderPeriod(form.find('input[name="PROPERTY[11][0][VALUE]"]').val(), form.find('input[name="PROPERTY[12][0][VALUE]"]').val())
        ));
    }
    if ($('#time-select-radio').find('input[name="radio"]:checked').data('period') === 'day') {
        insertCostData(
            'input[name="PROPERTY[43][0]"]',
            'input[name="PROPERTY[44][0]"]',
            OBJECT_DATA.OBJECT_DAILY_COST,
            OBJECT_DATA.VISIT_PERMISSION_COST,
            form.find('input[name="PROPERTY[16][0]"]').val(),
            form.find('input[name="PROPERTY[17][0]"]').val()
        );
    } else {
        insertCostData(
            'input[name="PROPERTY[43][0]"]',
            'input[name="PROPERTY[44][0]"]',
            OBJECT_DATA.OBJECT_COST,
            OBJECT_DATA.VISIT_PERMISSION_COST,
            form.find('input[name="PROPERTY[16][0]"]').val(),
            form.find('input[name="PROPERTY[17][0]"]').val()
        );
    }
    //reinitBookingFormCalendar('input[name="PROPERTY[11][0][VALUE]"]', $('input[name="PROPERTY[21][0][VALUE]"]:checked').val(), 'input[name="PROPERTY[12][0][VALUE]"]', $('#time-select-radio').find('input[name="radio"]:checked').data('period'));
}

function insertObjectId(id) {
    let form = $('form[name="iblock_add"]');
    let id_input = form.find('input[name="object_id_inpt"]');
    if (id_input.length > 0) {
        id_input.attr('value', id);
    } else {
        form.append('<input type="hidden" name="object_id_inpt" value="' + id + '">');
    }
}

function insertObjectType(type) {
    let form = $('form[name="iblock_add"]');
    let id_input = form.find('input[name="time_limit_value"]');
    if (id_input.length > 0) {
        id_input.attr('value', type);
    } else {
        form.append('<input type="hidden" name="time_limit_value" value="' + type + '">');
    }
}

function onObjectSelect() {
    clearErrors();
    let radio_input = $('input[name="PROPERTY[21][0][VALUE]"]');
    if (radio_input.length !== 0) {
        radio_input.change(function () {
            if ($(this).val()) {
                let object_id = $(this).val();
                let time_limit_value = $(this).data('timeLimitValue');
                let car_possibility = $(this).data('carPossibilityValue');
                let car_capacity = $(this).attr('data-car-capacity-value');
                insertObjectId(object_id);
                ajaxWrap('/ajax/objects/booking_form_html.php', {
                    form_type: 'admin_booking_new',
                    time_limit_value: time_limit_value,
                    object_id: object_id,
                }).then(
                    function (html) {
                        if (html) {
                            $('#booking-date-time-block').html(html);
                            if (time_limit_value === 'N') {
                                reinitCustomSelect('#arrival-time-select');
                                reinitCustomSelect('#departure-time-select');
                                editTimeSelectBlock('radio', object_id);
                                $('input[name="PROPERTY[11][0][VALUE]"]').val('');
                                $('input[name="PROPERTY[12][0][VALUE]"]').val('');
                                calendarSwitcher('#time-select-radio', 'input[name="PROPERTY[11][0][VALUE]"]', object_id, 'input[name="PROPERTY[12][0][VALUE]"]');
                                reinitBookingFormCalendar('input[name="PROPERTY[11][0][VALUE]"]', object_id, 'input[name="PROPERTY[12][0][VALUE]"]', $('#time-select-radio input:checked').data('period'));
                                resetSelectValue('#arrival-time-select');
                                resetSelectValue('#departure-time-select');
                                onChangeDateTimeValues(
                                    'input[name="object_id_inpt"]',
                                    'input[name="PROPERTY[11][0][VALUE]"]',
                                    'input[name="PROPERTY[12][0][VALUE]"]',
                                    '#arrival-time-select',
                                    '#departure-time-select',
                                    '#time-select-radio input[name="radio"]:checked',
                                    false);
                            } else {
                                $('input[name="PROPERTY[11][0][VALUE]"]').val('');
                                reinitCustomSelect('#time-select');
                                resetSelectValue('#time-select');
                                reinitBookingFormCalendar(
                                    'input[name="PROPERTY[11][0][VALUE]"]',
                                    object_id,
                                    '',
                                    'day',
                                    true,
                                    false,
                                    true);
                                onChangeDateTimeValues(
                                    'input[name="object_id_inpt"]',
                                    'input[name="PROPERTY[11][0][VALUE]"]',
                                    '',
                                    '#time-select',
                                    '',
                                    '#time-select-radio input[name="radio"]:checked',
                                    false,
                                    time_limit_value,
                                    $('#time-start').val(),
                                    $('#time-end').val()
                                );
                            }
                            insertObjectType(time_limit_value);
                            OBJECT_DATA = getObjectCost(object_id, OBJECT_DATA);
                            $('#car-no').prop('checked', true);
                            $('#car-yes').prop('checked', false);
                            $('#car-detail-hidden').hide();
                            if (car_possibility === 'Y') {
                                $('#car-possibility-block').show();
                            } else {
                                $('#car-possibility-block').hide();
                                $('#car-no').attr('checked', true);
                                $('#car-detail-hidden').hide();
                                $('#car-quantity').val(1);
                                drawCarNumberInput(1, $('input[name="guest-car-prop-number"]').val(), '#cars-list');
                            }
                            if (car_capacity) {
                                $('input[name="CAR_CAPACITY"]').val(car_capacity);
                            } else {
                                $('input[name="CAR_CAPACITY"]').val('');
                            }
                            $('#booking-params').show();
                        }
                    });

            }
        });
    } else {
        let object_select_observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutationRecord) {
                let object_id = $(mutationRecord.target).attr('data-selected-id');
                let time_limit_value = $('#object-select').find('.custom-select_body').find(`.custom-select_item[data-id="${object_id}"]`).data('timeLimitValue');
                let car_possibility = $('#object-select').find('.custom-select_body').find(`.custom-select_item[data-id="${object_id}"]`).data('carPossibilityValue');
                let car_capacity = $('#object-select').find('.custom-select_body').find(`.custom-select_item[data-id="${object_id}"]`).data('carCapacityValue');
                ajaxWrap('/ajax/objects/booking_form_html.php', {
                    form_type: 'admin_booking_new',
                    time_limit_value: time_limit_value,
                    object_id: object_id,
                }).then(
                    function (html) {
                        if (html) {
                            $('#booking-date-time-block').html(html);
                            if (time_limit_value === 'N') {
                                reinitCustomSelect('#arrival-time-select');
                                reinitCustomSelect('#departure-time-select');
                                editTimeSelectBlock('select', object_id);
                                $('input[name="PROPERTY[11][0][VALUE]"]').val('');
                                $('input[name="PROPERTY[12][0][VALUE]"]').val('');
                                resetSelectValue('#arrival-time-select');
                                resetSelectValue('#departure-time-select');
                                calendarSwitcher('#time-select-radio', 'input[name="PROPERTY[11][0][VALUE]"]', order_data['PROPERTY[21][0][VALUE]'], 'input[name="PROPERTY[12][0][VALUE]"]');
                                reinitBookingFormCalendar('input[name="PROPERTY[11][0][VALUE]"]', object_id, 'input[name="PROPERTY[12][0][VALUE]"]', $('#time-select-radio input:checked').data('period'));
                                onChangeDateTimeValues(
                                    'input[name="object_id_inpt"]',
                                    'input[name="PROPERTY[11][0][VALUE]"]',
                                    'input[name="PROPERTY[12][0][VALUE]"]',
                                    '#arrival-time-select',
                                    '#departure-time-select',
                                    '#time-select-radio input[name="radio"]:checked',
                                    false);
                            } else {
                                reinitCustomSelect('#time-select');
                                $('input[name="PROPERTY[11][0][VALUE]"]').val('');
                                resetSelectValue('#time-select');
                                reinitBookingFormCalendar(
                                    'input[name="PROPERTY[11][0][VALUE]"]',
                                    object_id,
                                    '',
                                    'day',
                                    true,
                                    false,
                                    true);
                                onChangeDateTimeValues(
                                    'input[name="object_id_inpt"]',
                                    'input[name="PROPERTY[11][0][VALUE]"]',
                                    '',
                                    '#time-select',
                                    '',
                                    '#time-select-radio input[name="radio"]:checked',
                                    false,
                                    time_limit_value,
                                    $('#time-start').val(),
                                    $('#time-end').val()
                                );
                            }
                            onInputchange();
                            insertObjectType(time_limit_value);
                            order_data['PROPERTY[21][0][VALUE]'] = object_id;
                            OBJECT_DATA = getObjectCost(order_data['PROPERTY[21][0][VALUE]'], OBJECT_DATA);
                            insertObjectId(object_id);
                            $('#car-no').prop('checked', true);
                            $('#car-yes').prop('checked', false);
                            $('#car-detail-hidden').hide();
                            if (car_possibility === 'Y') {
                                $('#car-possibility-block').show();
                            } else {
                                $('#car-possibility-block').hide();
                                $('#car-no').attr('checked', true);
                                $('#car-detail-hidden').hide();
                                $('#car-quantity').val(1);
                                drawCarNumberInput(1, $('input[name="guest-car-prop-number"]').val(), '#cars-list');
                            }
                            if (car_capacity) {
                                $('input[name="CAR_CAPACITY"]').val(car_capacity);
                            } else {
                                $('input[name="CAR_CAPACITY"]').val('');
                            }
                            $('#booking-params').show();
                        }
                    });
            });
        });
        object_select_observer.observe($('#object-select').find('.custom-select_title')[0], {
            attributes: true,
            attributeFilter: ['data-selected-id']
        });
    }

}

function onFilterTimeChange() {
    let filterNode = $('#time-filter-block');
    let filterBtn = $('#set-filter');
    filterBtn.click(function (e) {
        e.preventDefault();
        initPreloader(false, '.tabs-content_item[data-id="2"]');
        $('#booking-params').hide();
        filter_data.category_id = $('#object-location-filter').find('.custom-select_title').attr('data-selected-id');
        filter_data.location_id = $('#object-category-filter').find('.custom-select_title').attr('data-selected-id');
        filter_data.period = $('#filter-period-block').find('input[type="radio"]:checked').attr('pariod');
        filter_data.arrival_date = $('#date-filter-block').find('#arrival-date-filter').val();
        filter_data.departure_date = $('#date-filter-block').find('#departure-date-filter').val();
        filter_data.arrival_time = filterNode.find('#filter-arr-time').find('.custom-select_title').attr('data-selected-id');
        filter_data.departure_time = filterNode.find('#filter-dep-time').find('.custom-select_title').attr('data-selected-id');
        ajaxWrap(
            '/ajax/orders/filter_order.php',
            filter_data
        ).then(
            function (data) {
                if (data) {
                    let data_object = JSON.parse(data);
                    if (Object.keys(data_object).length > 10) {
                        createSelect(data_object);
                    } else {
                        createRadioList(data_object);
                    }
                    onObjectSelect();
                    destroyPreloader('.preloader', '.tabs-content_item[data-id="2"]');
                } else {
                    $('#object-select-block').html('Нет подходящих объектов');
                    destroyPreloader('.preloader', '.tabs-content_item[data-id="2"]');
                }
            }
        );
    });
}

$(document).ready(function () {
    onObjectSelect();
    resetInputClass('form[name="iblock_add"]');
    onInputchange();
    onChangeDateTimeValues(
        'input[name="object_id_inpt"]',
        'input[name="PROPERTY[11][0][VALUE]"]',
        'input[name="PROPERTY[12][0][VALUE]"]',
        '#arrival-time-select',
        '#departure-time-select',
        '#time-select-radio input[name="radio"]:checked',
        false);
    printBlank();
    tabBtnClick();
    //setFilterData();
    onFilterTimeChange();
    carLogicBlock(
        '#car-radio-group',
        '#car-detail-hidden',
        '#car-quantity',
        $('input[name="guest-car-prop-number"]').val(),
        '#cars-list',
        'input[name="CAR_CAPACITY"]'
    );
});