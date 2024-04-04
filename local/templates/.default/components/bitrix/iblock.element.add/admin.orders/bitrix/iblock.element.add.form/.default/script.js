let order_data = new Object(null);//данные со всех форм

let filter_data = new Object(null);

var myMap = null;
var objManager = null;

let function_init_counter = 0;

//переменные для расчета стоимости
/*let OBJECT_COST = 0;
let OBJECT_DAILY_COST = 0;
let COST_PER_PERSON = 0;
let COST_PER_PERSON_ONE_DAY = 0;
let CAPACITY_ESTIMATED = 0;
let CAPACITY_MAXIMUM = 0;*/

let OBJECT_DATA = {
    OBJECT_COST: 0,
    OBJECT_DAILY_COST: 0,
    COST_PER_PERSON: 0,
    COST_PER_PERSON_ONE_DAY: 0,
    CAPACITY_ESTIMATED: 0,
    CAPACITY_MAXIMUM: 0,
    VISIT_PERMISSION_COST: 0,
}

//функция собирает значения полей формы и пересчитывает стоимость аренды
function RecalculateSum() {
    let form = $('form[name="iblock_add"]');
    let sum = form.find('#order-sum-value');
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
        $('input[name="radio"]:checked').data('period'),
        calculateOrderPeriod(form.find('input[name="PROPERTY[11][0][VALUE]"]').val(), form.find('input[name="PROPERTY[12][0][VALUE]"]').val())
    ));
}

function switchTab() {
    var activeTab = document.querySelector('.tab.active');
    var id = Number(activeTab.dataset.id) + 1;
    activeTab.classList.add('done');
    switchTabsByBtn(id);
}

function doBtnAction() {
    let form = $('form[name="iblock_add"]');
    addDataToObject(new FormData(form[0]), order_data);
    order_data['PROPERTY[32][0]'] = $('#order-sum-value').html();
    if ($('form[name="iblock_add"]').find('input[name="time_limit_value"]').val()) {
        if ($('form[name="iblock_add"]').find('input[name="time_limit_value"]').val() === 'N') {
            order_data['PROPERTY[13][0]'] = $('#arrival-time-select').find('.custom-select_title').data('selectedId');
            order_data['PROPERTY[14][0]'] = $('#departure-time-select').find('.custom-select_title').data('selectedId');
        } else {
            order_data['PROPERTY[13][0]'] = $('#time-select').find('.custom-select_title').data('selectedId');
            order_data['PROPERTY[TIME_LIMIT]'] = $('input[name="time_limit_value"]').val();
        }
    } else {
        order_data['PROPERTY[13][0]'] = $('#arrival-time-select').find('.custom-select_title').data('selectedId');
        order_data['PROPERTY[14][0]'] = $('#departure-time-select').find('.custom-select_title').data('selectedId');
    }
    order_data['PROPERTY[21][0][VALUE]'] = $('#object_id').val();
    submitForm(form.attr('action'), order_data);
    initPreloader(false, '.lk_content');
    switchTab();
    createDocument(order_data, '/ajax/orders/create_blank.php');
    initReceiptMap(order_data['PROPERTY[21][0][VALUE]'], myMap, objManager);
    destroyPreloader('.preloader', '.lk_content');
}

function tabBtnClick() {
    $('button[data-tab-action="order"]').click(function () {
        let order_id = $('input[name="PROPERTY[39][0]"]').val();
        doFormAction(order_id);
    });
}

function getFilterData(data) {
    $('#booking-params').hide();
    $.ajax({
        url: '/ajax/orders/filter_order.php',
        method: 'post',
        data: data,
        success: function (data) {
            if (data) {
                //$('#objects-list').html(data);
                let data_object = JSON.parse(data);
                if (Object.keys(data_object).length > 10) {
                    createSelect(data_object);
                    onObjectSelect();
                } else {
                    createRadioList(data_object);
                    onObjectSelect();
                }
            }
        }
    });
}

function setFilterData() {
    let filter = $('#object-category-filter').find('.custom-select_title')[0];
    let categoryObserver = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutationRecord) {
            let category_id = $(mutationRecord.target).attr('data-selected-id');
            filter_data.category_id = category_id;
            //console.log(filter_data);
            getFilterData(filter_data);
        });
    });
    $('input[name="arrival-date-input"]').change(function () {
        filter_data.arrival_date = $(this).val();
        filter_data.departure_date = $('input[name="departure-date-input"]').val();
        getFilterData(filter_data);
        //console.log(filter_data);
    });
    categoryObserver.observe(filter, {attributes: true, attributeFilter: ['data-selected-id']});
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
    let radio_input = $('input[name="PROPERTY[21][0][VALUE]"]');
    if (radio_input.length !== 0) {
        radio_input.change(function () {
            if ($(this).val()) {
                let object_id = $(this).val();
                let time_limit_value = $(this).data('timeLimitValue');
                let car_possibility = $(this).data('carPossibilityValue');
                let car_capacity = $(this).attr('data-car-capacity-value');
                $('#object_id').val(object_id);
                $('#time_limit_value').val(time_limit_value);
                ajaxWrap('/ajax/objects/booking_form_html.php', {
                    form_type: 'admin_booking_new',
                    time_limit_value: time_limit_value,
                    object_id: object_id
                }).then(
                    function (html) {
                        if (html) {
                            if (time_limit_value === 'N') {
                                reinitBookingFormCalendar('input[name="PROPERTY[11][0][VALUE]"]', $(this).val(), 'input[name="PROPERTY[12][0][VALUE]"]');
                            } else {
                                reinitCustomSelect('#time-select');
                                resetSelectValue('#arrival-time-select');
                                flatpickr.localize(flatpickr.l10ns.ru);
                                flatpickr($('input[name="PROPERTY[11][0][VALUE]"]'), {
                                    dateFormat: "d.m.Y",
                                    allowInput: "false",
                                    allowInvalidPreload: true,
                                    disableMobile: "true",
                                    minDate: "today",
                                    mode: "single",
                                });
                            }
                        }
                    });
                OBJECT_DATA = getObjectCost($(this).val(), OBJECT_DATA);
                insertObjectType(time_limit_value);
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
    } else {
        let object_select_observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutationRecord) {
                let object_id = $(mutationRecord.target).attr('data-selected-id');
                let time_limit = $(mutationRecord.target).attr('data-time-limit-value');
                let car_possibility = $(mutationRecord.target).attr('data-car-possibility-value');
                let car_capacity = $(mutationRecord.target).attr('data-car-capacity-value');
                ajaxWrap('/ajax/objects/booking_form_html.php', {
                    form_type: 'admin_booking_new',
                    time_limit_value: time_limit,
                    object_id: object_id
                }).then(
                    function (html) {
                        if (html) {
                            if (time_limit === 'N') {
                                calendarSwitcher('#time-select-radio', 'input[name="PROPERTY[11][0][VALUE]"]', order_data['PROPERTY[21][0][VALUE]'], 'input[name="PROPERTY[12][0][VALUE]"]');
                                reinitBookingFormCalendar('input[name="PROPERTY[11][0][VALUE]"]', object_id, 'input[name="PROPERTY[12][0][VALUE]"]');
                            } else {
                                reinitCustomSelect('#time-select');
                                resetSelectValue('#arrival-time-select');
                                flatpickr.localize(flatpickr.l10ns.ru);
                                flatpickr($('input[name="PROPERTY[11][0][VALUE]"]'), {
                                    dateFormat: "d.m.Y",
                                    allowInput: "false",
                                    allowInvalidPreload: true,
                                    disableMobile: "true",
                                    minDate: "today",
                                    mode: "single",
                                });
                            }
                        }
                    });
                order_data['PROPERTY[21][0][VALUE]'] = object_id;
                $('#object_id').val(object_id);
                OBJECT_DATA = getObjectCost(order_data['PROPERTY[21][0][VALUE]'], OBJECT_DATA);
                insertObjectType(time_limit);
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
            });
        });
        object_select_observer.observe($('#object-select').find('.custom-select_title')[0], {
            attributes: true,
            attributeFilter: ['data-selected-id']
        });
    }
}

function onOrderStatusSelectChange() {
    let order_status_select_observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutationRecord) {
            let order_status = $(mutationRecord.target).attr('data-selected-id');
            order_data['PROPERTY[40][0][VALUE]'] = order_status;
        });
    });
    order_status_select_observer.observe($('#order-status-select').find('.custom-select_title')[0], {
        attributes: true,
        attributeFilter: ['data-selected-id']
    });
}

function _setFormTime() {
    function_init_counter = function_init_counter + 1;
    let object_id = $('#object_id').val();
    let user_arr_date = $('input[name="PROPERTY[11][0][VALUE]"]').val();
    let user_dep_date = $('input[name="PROPERTY[12][0][VALUE]"]').val();
    let period = $('input[name="radio"]:checked').data('period');
    banSelector('#arrival-time-select');
    banSelector('#departure-time-select');
    ajaxWrap('/ajax/objects/get_arrival_date_time.php', {
        object_id: object_id,
        user_date: user_arr_date
    }).then((a_data) => {
        ajaxWrap('/ajax/objects/get_departure_date_time.php', {
            object_id: object_id,
            user_date: user_dep_date,
            arrival_date: user_arr_date
        }).then((d_data) => {
            let _arrival_time = [];
            let _departure_time = [];
            if (a_data) {
                _arrival_time = JSON.parse(a_data);
            }
            if (d_data) {
                _departure_time = JSON.parse(d_data);
            }
            ajaxWrap('/ajax/objects/calc_form_time.php', {
                arrival_time: _arrival_time,
                departure_time: _departure_time,
                period_value: period
            }).then((result) => {
                //console.log(JSON.parse(result));
                if (result) {
                    let DATA = JSON.parse(result);
                    if (DATA.ERROR) {
                        if (function_init_counter > 1) {
                            pushFormError([DATA.ERROR]);
                        } else {
                            ajaxWrap('/ajax/objects/simply_set_time.php', {
                                arrival_time: _arrival_time,
                                departure_time: _departure_time,
                                period_value: period
                            }).then((result) => {
                                if (result) {
                                    let DATA = JSON.parse(result);
                                    let html = '';
                                    for (const [num, time] of Object.entries(DATA.ARRIVAL_TIME)) {
                                        let [h, m, s] = time.split(':');
                                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                                    }
                                    $('#arrival-time-select').find('.custom-select_body').html(html);
                                    html = '';
                                    for (const [num, time] of Object.entries(DATA.DEPARTURE_TIME)) {
                                        let [h, m, s] = time.split(':');
                                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                                    }
                                    $('#departure-time-select').find('.custom-select_body').html(html);
                                    banSelector('#arrival-time-select', false);
                                    banSelector('#departure-time-select', false);
                                }
                            });
                        }
                    } else {
                        let html = '';
                        for (const [num, time] of Object.entries(DATA.ARRIVAL_TIME)) {
                            let [h, m, s] = time.split(':');
                            html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                        }
                        $('#arrival-time-select').find('.custom-select_body').html(html);
                        html = '';
                        for (const [num, time] of Object.entries(DATA.DEPARTURE_TIME)) {
                            let [h, m, s] = time.split(':');
                            html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                        }
                        $('#departure-time-select').find('.custom-select_body').html(html);
                        banSelector('#arrival-time-select', false);
                        banSelector('#departure-time-select', false);
                    }
                }
            });
        });
    });
}

function doFormAction(order_id) {
    if (checkForm(
        'form[name="iblock_add"]',
        'input[name="PROPERTY[9][0]"]',
        'input[name="PROPERTY[10][0]"]',
        '#adult-quantity',
    )) {
        if ($('form[name="iblock_add"]').find('input[name="time_limit_value"]').val() === 'N') {
            checkBookingPossibility(
                '#object_id',
                'input[name="PROPERTY[11][0][VALUE]"]',
                'input[name="PROPERTY[12][0][VALUE]"]',
                '#arrival-time-select',
                '#departure-time-select',
                order_id
            ).then((resolve) => {
                if (resolve === 'true') {
                    ajaxWrap(
                        '/ajax/orders/delete_order.php',
                        {order_id: $('#order_id').val()}
                    ).then(function (result) {
                        if (result) {
                            if (result === 'true') {
                                doBtnAction();
                            }
                        }
                    });
                } else {
                    pushFormError(['Данное время недоступно для бронирования']);
                }
            });
        } else {
            ajaxWrap(
                '/ajax/orders/delete_order.php',
                {order_id: $('#order_id').val()}
            ).then(function (result) {
                if (result) {
                    if (result === 'true') {
                        doBtnAction();
                    }
                }
            });
        }
    }
}

$(window).on('load', function () {
    _setFormTime();
    let object_id = $('form[name="iblock_add"]').find('input[name="PROPERTY[21][0][VALUE]"]:checked').val();
    if (!object_id) {
        object_id = $('#object_id').val();
    }
    if ($('#time_limit_value').val() == 'Y') {
        flatpickr.localize(flatpickr.l10ns.ru);
        flatpickr($('input[name="PROPERTY[11][0][VALUE]"]'), {
            dateFormat: "d.m.Y",
            allowInput: "false",
            allowInvalidPreload: true,
            disableMobile: "true",
            minDate: "today",
            mode: "single",
        });
    } else {
        reinitBookingFormCalendar('input[name="PROPERTY[11][0][VALUE]"]', $('#object_id').val(), 'input[name="PROPERTY[12][0][VALUE]"]', 'couple', false, true);
        calendarSwitcher('#time-select-radio', 'input[name="PROPERTY[11][0][VALUE]"]', object_id, 'input[name="PROPERTY[12][0][VALUE]"]', $('form[name="iblock_add"]').find('input[name="radio"]:checked').data('period'));
        onChangeDateTimeValues(
            'input[name="PROPERTY[21][0][VALUE]"]:checked',
            'input[name="PROPERTY[11][0][VALUE]"]',
            'input[name="PROPERTY[12][0][VALUE]"]',
            '#arrival-time-select',
            '#departure-time-select',
            '#time-select-radio input[name="radio"]:checked',
            false);
    }
    OBJECT_DATA = getObjectCost(object_id, OBJECT_DATA);
    resetInputClass('form[name="iblock_add"]');
    onInputchange();
    onObjectSelect();
    tabBtnClick();
    setFilterData();
    printBlank();
    onOrderStatusSelectChange();
    carLogicBlock(
        '#car-radio-group',
        '#car-detail-hidden',
        '#car-quantity',
        $('input[name="guest-car-prop-number"]').val(),
        '#cars-list',
        'input[name="CAR_CAPACITY"]'
    );
});