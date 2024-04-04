"use strict";

//функция отвечает за увеличение/ уменьшение числа на инпуте с кнопками + и -
function inputValueCalculator() {
    $('.input-counter_btn').click((e) => {
        let btn_action = $(e.currentTarget).data('action');
        let btn_target_input = $(e.currentTarget).data('inputId');
        let input = $('#' + btn_target_input);
        let input_value = input.val();
        let max_input_value = input.attr('max');
        if (btn_action === 'plus') {
            if (max_input_value) {
                if (input_value !== max_input_value) {
                    input_value++;
                }
            } else {
                input_value++;
            }
        } else {
            if (input_value >= 1) {
                input_value = input_value - 1;
            }
        }
        input.val(input_value).trigger("change");
    });
}

//функция запускает "загрузку" на html блоке
function initPreloader(preloaderclass, target) {
    switch (preloaderclass) {
        case false:
            $(target).find('.preloader').addClass('active');
            break;
        case 'green':
            let preloader = $(target).find('.preloader');
            preloader.addClass('preloader--green').addClass('active');
            break;

    }
}

function clearErrors() {
    let warn_node = $('.form-warn-message');
    warn_node.html('');
}

function ajaxWrap(ajax_url, data_object) {
    return $.ajax({
        url: ajax_url,
        method: 'post',
        data: data_object,
    });
}

function banSelector(selector, action = true) {
    if (action) {
        $(selector).addClass('loading');
    } else {
        $(selector).removeClass('loading');
    }
}

function resetSelectValue(select_selector) {
    let select_node = $(select_selector).find('.custom-select_title');
    select_node.attr('data-selected-id', '');
    select_node.html(select_node.data('defaultValue'));
    setDefaultSelectBodyHtml(select_selector);
}

function setDefaultSelectBodyHtml(select_selector) {
    let select_body = $(select_selector).find('.custom-select_body');
    let default_select_body_html =
        '<div class="custom-select_item" data-id="8:00">8:00</div>\n' +
        '<div class="custom-select_item" data-id="9:00">9:00</div>\n' +
        '<div class="custom-select_item" data-id="10:00">10:00</div>\n' +
        '<div class="custom-select_item" data-id="11:00">11:00</div>\n' +
        '<div class="custom-select_item" data-id="12:00">12:00</div>\n' +
        '<div class="custom-select_item" data-id="13:00">13:00</div>\n' +
        '<div class="custom-select_item" data-id="14:00">14:00</div>\n' +
        '<div class="custom-select_item" data-id="15:00">15:00</div>\n' +
        '<div class="custom-select_item" data-id="16:00">16:00</div>\n' +
        '<div class="custom-select_item" data-id="17:00">17:00</div>\n' +
        '<div class="custom-select_item" data-id="18:00">18:00</div>\n' +
        '<div class="custom-select_item" data-id="19:00">19:00</div>\n' +
        '<div class="custom-select_item" data-id="20:00">20:00</div>\n' +
        '<div class="custom-select_item" data-id="21:00">21:00</div>\n' +
        '<div class="custom-select_item" data-id="22:00">22:00</div>\n' +
        '<div class="custom-select_item" data-id="23:00">23:00</div>';
    select_body.html(default_select_body_html);
}

//функция отправляет на проверку даты и время бронирвоания
function onChangeDateTimeValues(object_id_selector, arrival_date_input_selector, departure_date_input_selector, arrival_time_selector, departure_time_selector, booking_type_selector, change_time = true, time_limit_value = null, tl_am = null, tl_pm = null, user_form = false) {
    let departure_time;
    let ar_time = ['8:00', '9:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00',
        '19:00', '20:00', '21:00', '22:00', '23:00'];
    $(arrival_date_input_selector).change(function () {
        let ar_input_val = $(this).val();
        let dep_input_val = $(departure_date_input_selector).val();
        let object_id = $(object_id_selector).val();

        if ((ar_input_val !== null || ar_input_val !== '') && (dep_input_val !== null || dep_input_val !== '')) {
            if (object_id !== null || object_id !== '' || typeof object_id !== 'undefined') {
                resetSelectValue(arrival_time_selector);
                resetSelectValue(departure_time_selector);
                setFormTime(object_id, ar_input_val, dep_input_val, time_limit_value, tl_am, tl_pm, user_form);
            }
        } else {
            if ((ar_input_val !== null || ar_input_val !== '')) {
                if (object_id !== null || object_id !== '' || typeof object_id !== 'undefined') {
                    resetSelectValue(arrival_time_selector);
                    banSelector(arrival_time_selector);
                    ajaxWrap('/ajax/objects/get_arrival_date_time.php', {
                        object_id: object_id,
                        user_date: ar_input_val
                    }).then((a_data) => {

                        let _arrival_time = [];
                        let _departure_time = [];
                        if (a_data) {
                            _arrival_time = JSON.parse(a_data);
                        }
                        ajaxWrap('/ajax/objects/calc_form_time.php', {
                            arrival_time: _arrival_time,
                            departure_time: _departure_time,
                            period_value: $(booking_type_selector).data('period'),
                            user_form: user_form
                        }).then((result) => {
                            if (result) {
                                let DATA = JSON.parse(result);
                                if (DATA.ERROR) {
                                    pushFormError([DATA.ERROR]);
                                    banSelector(arrival_time_selector);
                                } else {
                                    let html = '';
                                    for (const [num, time] of Object.entries(DATA.ARRIVAL_TIME)) {
                                        let [h, m, s] = time.split(':');
                                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                                    }
                                    $(arrival_time_selector).find('.custom-select_body').html(html);
                                    banSelector(arrival_time_selector, false);
                                }
                            }
                        });
                    });
                }
            }
        }
        /*
                if (ar_input_val !== null || ar_input_val !== '') {
                    if (object_id !== null || object_id !== '' || typeof object_id !== 'undefined') {
                        resetSelectValue(arrival_time_selector);
                        resetSelectValue(departure_time_selector);
                        get_arrival_date_time(object_id, ar_input_val);
                    }
                }
                if (dep_input_val !== null || dep_input_val !== '') {
                    if (object_id !== null || object_id !== '' || typeof object_id !== 'undefined') {
                        get_departure_date_time(object_id, dep_input_val, ar_input_val);
                        //resetSelectValue(departure_time_selector);
                    }
                }
                */
        //НЕ УДАЛЯТЬ - МБ ПОНАДОБИТСЯ!
        //old-but-gold version
        // if (dep_input_val !== null || dep_input_val !== '') {
        //     if (object_id !== null || object_id !== '' || typeof object_id !== 'undefined') {
        //         get_departure_date_time(object_id, dep_input_val, arrival_time_selector, booking_type_selector);
        //         resetSelectValue(departure_time_selector);
        //     }
        // }
        onArrivalTimeChange(object_id, dep_input_val, ar_input_val, user_form);
        //clearErrors();
    });

    $(booking_type_selector).change(function () {
        resetSelectValue(arrival_time_selector);
        resetSelectValue(departure_time_selector);
    });

    function onArrivalTimeChange(object_id, user_date, user_arrival_date, user_form) {
        let arrival_time_select_observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutationRecord) {
                let arrival_time = $(mutationRecord.target).attr('data-selected-id');
                //setDepartureTime(arrival_time);
                if (user_date) {
                    setDepartureTime(arrival_time, object_id, user_date, user_arrival_date, user_form);
                }
            });
        });
        let arrival_time_select = $(arrival_time_selector).find('.custom-select_title')[0];
        if (arrival_time_select) {
            arrival_time_select_observer.observe(arrival_time_select, {
                attributes: true,
                attributeFilter: ['data-selected-id']
            });
        }
    }

    function setDepartureTimeValue(time) {
        $(departure_time_selector).find('.custom-select_title').attr('data-selected-id', time);
        $(departure_time_selector).find('.custom-select_title').html(time);
        $(departure_time_selector).find('.custom-select_body').html(`<div class="custom-select_item" data-id="${time}">${time}</div>`);
    }

    function setFormTime(object_id, user_arr_date, user_dep_date, time_limit_value, tl_am, tl_pm, user_form) {
        banSelector(arrival_time_selector);
        ajaxWrap('/ajax/objects/get_arrival_date_time.php', {
            object_id: object_id,
            user_date: user_arr_date
        }).then((a_data) => {
            banSelector(departure_time_selector);
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
                    period_value: $(booking_type_selector).data('period'),
                    time_limit_value: time_limit_value,
                    tl_am: tl_am,
                    tl_pm: tl_pm,
                    user_form: user_form
                }).then((result) => {
                    //console.log(JSON.parse(result));
                    if (result) {
                        let DATA = JSON.parse(result);
                        if (DATA.ERROR) {
                            pushFormError([DATA.ERROR]);
                            banSelector(arrival_time_selector);
                            banSelector(departure_time_selector);
                        } else {
                            let html = '';
                            if (DATA.ARRIVAL_TIME) {
                                for (const [num, time] of Object.entries(DATA.ARRIVAL_TIME)) {
                                    let [h, m, s] = time.split(':');
                                    html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                                }
                                $(arrival_time_selector).find('.custom-select_body').html(html);
                                banSelector(arrival_time_selector, false);
                            }
                            if (DATA.DEPARTURE_TIME) {
                                html = '';
                                for (const [num, time] of Object.entries(DATA.DEPARTURE_TIME)) {
                                    let [h, m, s] = time.split(':');
                                    html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                                }
                                $(departure_time_selector).find('.custom-select_body').html(html);
                                banSelector(departure_time_selector, false);
                            }
                        }
                    }
                });
            });
        });
    }

    function get_arrival_date_time(object_id, user_date) {
        banSelector(arrival_time_selector);
        ajaxWrap('/ajax/objects/get_arrival_date_time.php', {
            object_id: object_id,
            user_date: user_date
        }).then((data) => {
            if (data) {
                departure_time = JSON.parse(data);
                let html = '';
                let can_book = false;
                if ($(booking_type_selector).data('period') === 'couple') {
                    for (const [num, time] of Object.entries(JSON.parse(data))) {
                        let [h, m, s] = time.split(':');
                        if (Number(h) === 23) {
                            can_book = true;
                        }
                    }
                    if (can_book) {
                        for (const [num, time] of Object.entries(JSON.parse(data))) {
                            let [h, m, s] = time.split(':');
                            if (Number(h) <= 23) {
                                can_book = false;
                            } else {
                                can_book = true;
                            }
                        }
                    }
                } else {
                    if (Object.entries(JSON.parse(data)).length > 1) {
                        can_book = true;
                    }
                }
                if (can_book) {
                    for (const [num, time] of Object.entries(JSON.parse(data))) {
                        let [h, m, s] = time.split(':');
                        if ($(booking_type_selector).data('period') === 'day') {
                            if (Number(h) <= 23) {
                                html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                            }
                        } else {
                            html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                        }
                    }
                    $(arrival_time_selector).find('.custom-select_body').html(html);
                    banSelector(arrival_time_selector, false);
                } else {
                    pushFormError(['Бронирование на данный диапазон не возможно']);
                    banSelector(arrival_time_selector);
                    banSelector(departure_time_selector);
                    /*$(arrival_time_selector).find('.custom-select_body').html('');
                    $(departure_time_selector).find('.custom-select_body').html('');*/
                }
            } else {
                if ($(booking_type_selector).data('period') === 'day') {
                    let html = '';
                    for (const time of ar_time) {
                        let [h, m] = time.split(':');
                        if (Number(h) <= 23) {
                            html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                        }
                    }
                    $(arrival_time_selector).find('.custom-select_body').html(html);
                    banSelector(arrival_time_selector, false);
                    setDepartureTimeValue('23:00');
                } else {
                    resetSelectValue(arrival_time_selector);
                    resetSelectValue(departure_time_selector);
                    let dep_input_val = $(departure_date_input_selector).val();
                    let object_id = $(object_id_selector).val();
                    get_departure_date_time(object_id, dep_input_val, arrival_time_selector, booking_type_selector);
                }
            }
            //НЕ УДАЛЯТЬ - МБ ПОНАДОБИТСЯ!
            //old-but-gold version
            /*if (data) {
                departure_time = JSON.parse(data);
                let html = '';
                for (const [num, time] of Object.entries(JSON.parse(data))) {
                    let [h, m, s] = time.split(':');
                    if ($(booking_type_selector).data('period') === 'day') {
                        if (Number(h) <= 20) {
                            html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                        } else {
                            pushFormError(['невозможно забронировать на выбранное время']);
                        }
                    } else {
                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                    }
                }
                $(arrival_time_selector).find('.custom-select_body').html(html);
            } else {
                if ($(booking_type_selector).data('period') === 'day') {
                    let html = '';
                    for (const time of ar_time) {
                        let [h, m] = time.split(':');
                        if (Number(h) <= 20) {
                            html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                        }
                    }
                    $(arrival_time_selector).find('.custom-select_body').html(html);
                } else {
                    let html = '';
                    for (const time of ar_time) {
                        let [h, m] = time.split(':');
                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                    }
                    $(arrival_time_selector).find('.custom-select_body').html(html);
                }
            }*/
        });

        /*$.ajax({
            url: '/ajax/objects/get_arrival_date_time.php',
            method: 'post',
            data: {object_id: object_id, user_date: user_date},
            success: function (data) {
                if (data) {
                    departure_time = JSON.parse(data);
                    let html = '';
                    let can_book = false;
                    for (const [num, time] of Object.entries(JSON.parse(data))) {
                        let [h, m, s] = time.split(':');
                        if (Number(h) === 20) {
                            can_book = true;
                        }
                    }
                    if (can_book) {
                        for (const [num, time] of Object.entries(JSON.parse(data))) {
                            let [h, m, s] = time.split(':');
                            if (Number(h) <= 20) {
                                can_book = false;
                            } else {
                                can_book = true;
                            }
                        }
                    }
                    if (can_book) {
                        for (const [num, time] of Object.entries(JSON.parse(data))) {
                            let [h, m, s] = time.split(':');
                            if ($(booking_type_selector).data('period') === 'day') {
                                if (Number(h) <= 20) {
                                    html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                                }
                            } else {
                                html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                            }
                        }
                        $(arrival_time_selector).find('.custom-select_body').html(html);
                    } else {
                        pushFormError(['Бронирование на данный диапазон не возможно']);
                        $(arrival_time_selector).find('.custom-select_body').html('');
                        $(departure_time_selector).find('.custom-select_body').html('');
                    }
                } else {
                    if ($(booking_type_selector).data('period') === 'day') {
                        let html = '';
                        for (const time of ar_time) {
                            let [h, m] = time.split(':');
                            if (Number(h) <= 20) {
                                html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                            }
                        }
                        $(arrival_time_selector).find('.custom-select_body').html(html);
                        setDepartureTimeValue('20:00');
                    } else {
                        resetSelectValue(arrival_time_selector);
                        resetSelectValue(departure_time_selector);
                        let dep_input_val = $(departure_date_input_selector).val();
                        let object_id = $(object_id_selector).val();
                        get_departure_date_time(object_id, dep_input_val, arrival_time_selector, booking_type_selector);
                    }
                }
                //НЕ УДАЛЯТЬ - МБ ПОНАДОБИТСЯ!
                //old-but-gold version
                /!*if (data) {
                    departure_time = JSON.parse(data);
                    let html = '';
                    for (const [num, time] of Object.entries(JSON.parse(data))) {
                        let [h, m, s] = time.split(':');
                        if ($(booking_type_selector).data('period') === 'day') {
                            if (Number(h) <= 20) {
                                html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                            } else {
                                pushFormError(['невозможно забронировать на выбранное время']);
                            }
                        } else {
                            html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                        }
                    }
                    $(arrival_time_selector).find('.custom-select_body').html(html);
                } else {
                    if ($(booking_type_selector).data('period') === 'day') {
                        let html = '';
                        for (const time of ar_time) {
                            let [h, m] = time.split(':');
                            if (Number(h) <= 20) {
                                html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                            }
                        }
                        $(arrival_time_selector).find('.custom-select_body').html(html);
                    } else {
                        let html = '';
                        for (const time of ar_time) {
                            let [h, m] = time.split(':');
                            html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                        }
                        $(arrival_time_selector).find('.custom-select_body').html(html);
                    }
                }*!/
            }
        });*/
    }

    function get_departure_date_time(object_id, user_date, user_arrival_date) {
        banSelector(departure_time_selector);
        ajaxWrap('/ajax/objects/get_departure_date_time.php', {
            object_id: object_id,
            user_date: user_date,
            arrival_date: user_arrival_date
        }).then((data) => {
            if (data) {
                let html = '';
                for (const [num, time] of Object.entries(JSON.parse(data))) {
                    let [h, m, s] = time.split(':');
                    //if ($(booking_type_selector).data('period') === 'couple') {
                    if (Number(h) >= 8 && Number(h) <= 23) {
                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                    }
                    //}
                }
                $(departure_time_selector).find('.custom-select_body').html(html);
                banSelector(departure_time_selector, false);
                if ($(booking_type_selector).data('period') === 'couple') {
                    banSelector(arrival_time_selector);
                    let html = '';
                    for (const [num, time] of Object.entries(JSON.parse(data))) {
                        let [h, m, s] = time.split(':');
                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                    }
                    banSelector(arrival_time_selector, false);
                    /*if (limitArrivalTime(data).length > 0) {
                        banSelector(arrival_time_selector);
                        $(arrival_time_selector).find('.custom-select_body').html(limitArrivalTime(data));
                        banSelector(arrival_time_selector, false);
                    } else {
                        banSelector(arrival_time_selector);
                        //$(arrival_time_selector).find('.custom-select_body').html('');
                        pushFormError(['Данное время недоступно для бронирования']);
                    }*/
                }
            } else {
                let html = '';
                for (const time of ar_time) {
                    let [h, m] = time.split(':');
                    html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                }
                $(departure_time_selector).find('.custom-select_body').html(html);
                banSelector(departure_time_selector, false);
            }
            //НЕ УДАЛЯТЬ - МБ ПОНАДОБИТСЯ!
            //old-but-gold version
            /*if (data) {
                let html = '';
                for (const [num, time] of Object.entries(JSON.parse(data))) {
                    let [h, m, s] = time.split(':');
                    if ($(booking_type_selector).data('period') === 'day') {
                        if (Number(h) <= 20) {
                            html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                        }
                    } else {
                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                    }
                }
                $(departure_time_selector).find('.custom-select_body').html(html);
            } else {
                if ($(booking_type_selector).data('period') === 'day') {
                    let html = '';
                    for (const time of ar_time) {
                        let [h, m] = time.split(':');
                        if (Number(h) <= 20) {
                            html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                        }
                    }
                    $(departure_time_selector).find('.custom-select_body').html(html);
                } else {
                    let html = '';
                    for (const time of ar_time) {
                        let [h, m] = time.split(':');
                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                    }
                    $(departure_time_selector).find('.custom-select_body').html(html);
                }
            }*/
        });
        /*$.ajax({
            url: '/ajax/objects/get_departure_date_time.php',
            method: 'post',
            data: {object_id: object_id, user_date: user_date},
            success: function (data) {
                if (data) {
                    let html = '';
                    for (const [num, time] of Object.entries(JSON.parse(data))) {
                        let [h, m, s] = time.split(':');
                        if ($(booking_type_selector).data('period') === 'couple') {
                            if (Number(h) > 8 && Number(h) < 20) {
                                html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                            }
                        }
                    }
                    $(arrival_time_selector).find('.custom-select_body').html(html);
                } else {
                    let html = '';
                    for (const time of ar_time) {
                        let [h, m] = time.split(':');
                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                    }
                    $(arrival_time_selector).find('.custom-select_body').html(html);
                }
                //НЕ УДАЛЯТЬ - МБ ПОНАДОБИТСЯ!
                //old-but-gold version
                /!*if (data) {
                    let html = '';
                    for (const [num, time] of Object.entries(JSON.parse(data))) {
                        let [h, m, s] = time.split(':');
                        if ($(booking_type_selector).data('period') === 'day') {
                            if (Number(h) <= 20) {
                                html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                            }
                        } else {
                            html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                        }
                    }
                    $(departure_time_selector).find('.custom-select_body').html(html);
                } else {
                    if ($(booking_type_selector).data('period') === 'day') {
                        let html = '';
                        for (const time of ar_time) {
                            let [h, m] = time.split(':');
                            if (Number(h) <= 20) {
                                html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                            }
                        }
                        $(departure_time_selector).find('.custom-select_body').html(html);
                    } else {
                        let html = '';
                        for (const time of ar_time) {
                            let [h, m] = time.split(':');
                            html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                        }
                        $(departure_time_selector).find('.custom-select_body').html(html);
                    }
                }*!/
            }
        });*/
    }

    function limitArrivalTime(data) {
        let ar_arrival_time = [];
        let ar_departure_time = [];
        let html = '';
        $(arrival_time_selector).find('.custom-select_body').find('.custom-select_item').each(function () {
            if ($(this).data('id')) {
                let [h, m, s] = $(this).data('id').split(':');
                ar_arrival_time.push(h);
            }
        });
        for (const [num, time] of Object.entries(JSON.parse(data))) {
            let [h, m, s] = time.split(':');
            ar_departure_time.push(h);
        }
        let difference = ar_arrival_time.filter(x => !ar_departure_time.includes(x));
        let allowed_time = ar_arrival_time.filter(x => !difference.includes(x));
        for (const element of allowed_time) {
            html += `<div class="custom-select_item" data-id="${element}:00">${element}:00</div>`;
        }
        return html;
    }

    function setDepartureTime(arrival_time, object_id, user_date, user_arrival_date, user_form = false) {
        if ($(booking_type_selector).data('period') === 'day') {
            if (change_time) {
                banSelector(departure_time_selector);
                let html = '';
                html += `<div class="custom-select_item" data-id="23:00">23:00</div>`;
                $(departure_time_selector).find('.custom-select_body').html(html);
                $(departure_time_selector).find('.custom-select_title').attr('data-selected-id', '23:00');
                $(departure_time_selector).find('.custom-select_title').html('23:00');
                banSelector(departure_time_selector, false);
            } else {
                resetSelectValue(departure_time_selector);
            }
        } else {
            if (arrival_time) {
                if (user_form) {
                    banSelector(departure_time_selector);
                    let html = '';
                    let [u_h, u_m] = arrival_time.split(':');
                    let new_time = `${Number(u_h) - 1}:${u_m}`
                    html += `<div class="custom-select_item" data-id="${new_time}">${new_time}</div>`;
                    $(departure_time_selector).find('.custom-select_body').html(html);
                    $(departure_time_selector).find('.custom-select_title').attr('data-selected-id', new_time);
                    $(departure_time_selector).find('.custom-select_title').html(new_time);
                    banSelector(departure_time_selector, false);
                } else {
                    get_departure_date_time(object_id, user_date, user_arrival_date);
                }
            }
        }

        //НЕ УДАЛЯТЬ - МБ ПОНАДОБИТСЯ!

        /*if (departure_time) {
            let html = '';
            for (const [num, time] of Object.entries(departure_time)) {
                let [h, m, s] = time.split(':');
                let [u_h, u_m] = arrival_time.split(':');
                if ($(booking_type_selector).data('period') === 'day') {
                    if ((Number(h) > Number(u_h)) && Number(h) <= 20) {
                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                    }
                } else {
                    if (Number(h) > Number(u_h)) {
                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                    }
                }
            }
            $(departure_time_selector).find('.custom-select_body').html(html);
        } else {
            if ($(booking_type_selector).data('period') === 'day') {
                let html = '';
                for (const time of ar_time) {
                    let [h, m] = time.split(':');
                    let [u_h, u_m] = arrival_time.split(':');
                    if ((Number(h) > Number(u_h)) && Number(h) <= 20) {
                        html += `<div class="custom-select_item" data-id="${h}:${m}">${h}:${m}</div>`;
                    }
                }
                $(departure_time_selector).find('.custom-select_body').html(html);
            }
        }*/
    }
}

//функция проверяет строку на наличие спец символов
function isValid(_str) {
    return /^[a-zA-Zа-яА-Я0-9]+$/.test(_str.replace(/\s/g, ""));
}

//функция спрашивает сервер есть ли запись в бд с такими же данными
function checkBookingPossibility(object_id_selector, a_date_selector, d_date_selector, a_time_selector, d_time_selector, order_id = null, booking_period = null) {
    if (object_id_selector && a_date_selector && d_date_selector && a_time_selector && d_time_selector) {
        let ajax_data = {};
        let object_id = $(object_id_selector).val();
        let a_date = $(a_date_selector).val();
        let d_date = $(d_date_selector).val();
        let a_time = $(a_time_selector).find('.custom-select_title').data('selectedId');
        let d_time = $(d_time_selector).find('.custom-select_title').data('selectedId');
        if (order_id) {
            ajax_data.order_id = order_id;
        }
        if (object_id) {
            if (booking_period) {
                if (booking_period === 'day') {
                    let [a_h, a_m] = a_time.split(':');
                    let [d_h, d_m] = d_time.split(':');
                    if (parseInt(a_h) >= parseInt(d_h)) {
                        console.log('time');
                        pushFormError(['Ошибка заполнения формы. Неверно указано время заезда\ выезда']);
                        return false;
                    }
                }
            }
            if (a_date && d_date) {
                if (a_time && d_time) {
                    let a_date_time = a_date + ' ' + a_time + ':00';
                    let d_date_time = d_date + ' ' + d_time + ':00';
                    ajax_data.object_id = object_id;
                    ajax_data.arr_date_time = a_date_time;
                    ajax_data.dep_date_time = d_date_time;

                    return $.ajax({
                        url: '/ajax/orders/check_booking_possibility.php',
                        method: 'post',
                        data: ajax_data
                    });
                } else {
                    console.log('time');
                    pushFormError(['Ошибка заполнения формы. Данное время недоступно для бронирования']);
                    return false;
                }
            } else {
                console.log('date');
                pushFormError(['Ошибка заполнения формы. Данные даты заняты']);
                return false;
            }
        } else {
            console.log('obj_id');
            pushFormError(['Ошибка заполнения формы. Выберите объект для бронирования']);
            return false;
        }
    } else {
        console.log('any');
        pushFormError(['Ошибка заполнения формы']);
        return false;
    }
}

//функция проверки полей формы
function checkForm(
    form_selector,
    name_input_selector,
    second_name_input_selector,
    guest_count_input_selector,
    stay_ch_selector = '',
    pers_data_confirm_selector = '',
    offer_confirm_selector = '',
    booking_period = '',
    arrival_time = '',
    departure_time = '',
) {
    let return_flag = true;
    let warn_msg = [];
    //проверка обязательных полей
    let ar_req_inputs = $(form_selector).find('input:required');
    ar_req_inputs.each(function () {
        if ($(this).val() != null) {
            if ($(this).attr('type') === 'tel') {
                if ($(this).val().length != 16) {
                    return_flag = false;
                    $(this).parent().addClass('empty-field');
                    warn_msg.push('Некорректный телефон');
                }
            } else if ($(this).attr('type') === 'email') {
                const EMAIL_REGEXP = /^(([^<>()[\].,;:\s@"]+(\.[^<>()[\].,;:\s@"]+)*)|(".+"))@(([^<>()[\].,;:\s@"]+\.)+[^<>()[\].,;:\s@"]{2,})$/iu;
                if (!EMAIL_REGEXP.test($(this).val())) {
                    return_flag = false;
                    $(this).parent().addClass('empty-field');
                    warn_msg.push('Некорректный email');
                }
            } else {
                if ($(this).val() === '') {
                    $(this).parent().addClass('empty-field');
                    return_flag = false;
                }
            }
        }
    });
    if (!return_flag) {
        warn_msg.push('Заполните обязательные поля');
    }
    //проверка на запрещенные символы
    if (name_input_selector !== '' && second_name_input_selector !== '') {
        let name = $(name_input_selector).val();
        let second_name = $(second_name_input_selector).val();
        if (name && second_name) {
            if (!isValid(name)) {
                $(name_input_selector).parent().addClass('empty-field');
                return_flag = false;
                warn_msg.push('Недопустимый символ в поле "Имя"');
            }
            if (!isValid(second_name)) {
                $(second_name_input_selector).parent().addClass('empty-field');
                return_flag = false;
                warn_msg.push('Недопустимый символ в поле "Фамилия"');
            }
        }
    }
    //проверка корректности времени
    if (booking_period !== '' && arrival_time !== '' && departure_time !== '') {
        if (booking_period === 'day') {
            let [a_h, a_m] = arrival_time.split(':');
            let [d_h, d_m] = departure_time.split(':');
            if (parseInt(a_h) >= parseInt(d_h)) {
                return_flag = false;
                warn_msg.push('Некорректно указан диапазон времени');
            }
        }
    }
    //проверка количества гостей
    if (Number($(guest_count_input_selector).val()) === 0) {
        $(guest_count_input_selector).parent().addClass('empty-field');
        return_flag = false;
        warn_msg.push('Укажите количество гостей');
    }
    if (stay_ch_selector && pers_data_confirm_selector && offer_confirm_selector) {
        //проверка соглашений
        if (!$(stay_ch_selector).is(":checked")) {
            return_flag = false;
            warn_msg.push('Необходимо согласиться с условиями пребывания на территории парка');
        }
        if (!$(pers_data_confirm_selector).is(":checked")) {
            return_flag = false;
            warn_msg.push('Необходимо согласиться на обработку персональных данных');
        }
        if (!$(offer_confirm_selector).is(":checked")) {
            return_flag = false;
            warn_msg.push('Необходимо подтвердить ознакомление с договором оферты');
        }
    }
    //если есть ошибки - выводим их
    if (!return_flag) {
        pushFormError(warn_msg);
    }

    return return_flag;
}

function pushFormError(errors) {
    let warn_node = $('.form-warn-message');
    let warn_html = '';
    for (let msg of errors) {
        warn_html += `<span>${msg}</span>`;
    }
    warn_node.html(warn_html);
}

function isMobile() {
    if (navigator.userAgent.match(/Android/i)
        || navigator.userAgent.match(/webOS/i)
        || navigator.userAgent.match(/iPhone/i)
        || navigator.userAgent.match(/iPad/i)
        || navigator.userAgent.match(/iPod/i)
        || navigator.userAgent.match(/BlackBerry/i)
        || navigator.userAgent.match(/Windows Phone/i)) {
        return true;
    } else {
        return false;
    }
}

//функция смотрит на изменение инпутов и запускает пересчет стоимости аренды
function onInputchange() {
    let inputs = $('form[name="iblock_add"]').find('input');
    inputs.each(function () {
        let input_type = $(this).attr('type');
        if (input_type === 'text' || input_type === 'radio') {
            $(this).change(function () {
                RecalculateSum();
            });
        }
    });
}

//функция считает разницу в днях между датами
function calculateOrderPeriod(arr_date, dep_date) {
    let ar_date1 = arr_date.split(".");
    let ar_date2 = dep_date.split(".");
    let date1 = new Date(Date.UTC(ar_date1[2], ar_date1[1] - 1, ar_date1[0]));
    let date2 = new Date(Date.UTC(ar_date2[2], ar_date2[1] - 1, ar_date2[0]));
    let diffDays = 1;
    let timeDiff = Math.abs(date2.getTime() - date1.getTime());
    if (Math.ceil(timeDiff / (1000 * 3600 * 24)) > 0) {
        diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
    }
    return parseInt(diffDays);
}

//функция считает стоимость аренды
function calculateOrderSum(
    _object_cost,
    _object_daily_cost,
    _cost_per_person,
    _cost_per_person_one_day,
    _people_count,
    _capacity_estimated,
    _capacity_max,
    _benifit_count,
    _permition_cost,
    _is_permit,
    _booking_type,
    _day_count
) {
    let SUM = 0; //итоговая сумма
    let OBJECT_COST = 0; //стоимость объекта
    let over_max_count_price = parseInt(_cost_per_person); //переплата за превышение лимита человек (суточно)
    let over_max_count_price_one_day = parseInt(_cost_per_person_one_day); //переплата за превышение лимита человек (день)
    let people_count = parseInt(_people_count); //количество людей
    let max_people_free_count = parseInt(_capacity_estimated); //максимальное количество людей без переплаты
    let max_people_count = parseInt(_capacity_max); //максимальное количество людей
    let benefit_people_count = parseInt(_benifit_count); //льготники
    let permition_cost = parseInt(_permition_cost); //стоимость разрешения
    let over_max_count = 0; //число людей превышающее максимум
    let OVER_PRICE = 0; //переплата
    let PERMIT_PRICE = 0; //стоимость разрешения
    let booking_type = _booking_type; //тип бронирования (несколько суток\ дневное пребывание)
    let object_cost = parseInt(_object_cost);//Стоимость суточного пребывания
    let object_daily_cost = parseInt(_object_daily_cost); //Стоимость дневного пребывания
    let booking_period = parseInt(_day_count); //количество дней аренды
    if (people_count <= max_people_count) {
        if (benefit_people_count <= people_count) {
            let is_overlimit = max_people_free_count - people_count;
            /*
            * 2 - разрешение не получено
            * 1 - получено
            * */
            if (parseInt(_is_permit) === 2) {
                PERMIT_PRICE = (people_count - benefit_people_count) * permition_cost;
            } else {
                PERMIT_PRICE = 0;
            }
            /*
            * day - дневное пребывание
            * couple - посуточное пребывание
            * */
            if (booking_type === 'day') {
                if (typeof object_daily_cost === 'number') {
                    OBJECT_COST = object_daily_cost;
                    if (is_overlimit < 0) {
                        over_max_count = Math.abs(is_overlimit);
                        OVER_PRICE = over_max_count_price_one_day * over_max_count;
                    }
                } else {
                    OBJECT_COST = 0;
                    console.log('ошибка: поле "Стоимость дневного пребывания" не является числом!');
                }
            } else {
                if (typeof object_cost === 'number') {
                    OBJECT_COST = object_cost;
                    if (is_overlimit < 0) {
                        over_max_count = Math.abs(is_overlimit);
                        OVER_PRICE = over_max_count_price * over_max_count;
                    }
                } else {
                    OBJECT_COST = 0;
                    console.log('ошибка: поле "Стоимость суточного пребывания" не является числом!');
                }
            }
            SUM = (OBJECT_COST * booking_period) + (OVER_PRICE * booking_period) + PERMIT_PRICE;
        } else {
            SUM = 0;
            pushFormError(['Введено неверное количество людей!']);
            let beneficiaries_input = $('#beneficiaries-quantity');
            beneficiaries_input.val(beneficiaries_input.val() - 1).trigger("change");
        }
    } else {
        SUM = 0;
        pushFormError(['Превышено максимально допустимое число людей на объекте!']);
        $('#adult-quantity').val(max_people_count).trigger("change");
    }
    return SUM;
}

function calculateFixedObjectSum(_people_count, _benifit_count, _capacity_max, _capacity_estimated, _is_permit, _permition_cost, _object_cost) {
    let people_count = parseInt(_people_count); //количество людей
    let benefit_people_count = parseInt(_benifit_count); //льготники
    let max_people_count = parseInt(_capacity_max); //максимальное количество людей
    let PERMIT_PRICE = 0; //стоимость разрешения
    let permition_cost = parseInt(_permition_cost); //стоимость разрешения
    let SUM = 0; //итоговая сумма
    let OBJECT_COST = parseInt(_object_cost);//Стоимость суточного пребывания

    if (people_count <= max_people_count) {
        if (benefit_people_count > people_count) {
            pushFormError(['Введено неверное количество людей!']);
            let beneficiaries_input = $('#beneficiaries-quantity');
            beneficiaries_input.val(beneficiaries_input.val() - 1).trigger("change");
        }
    } else {
        pushFormError(['Превышено максимально допустимое число людей на объекте!']);
        $('#adult-quantity').val(max_people_count).trigger("change");
    }
    if (parseInt(_is_permit) === 2) {
        PERMIT_PRICE = (people_count - benefit_people_count) * permition_cost;
    } else {
        PERMIT_PRICE = 0;
    }
    return OBJECT_COST + PERMIT_PRICE;
}

//функция отправляет данные заказа на ajax файл и печатает форму из ответа
function createDocument(data, url) {
    $.ajax({
        url: url,
        method: 'post',
        data: data,
        success: function (data) {
            $('#blank').html(data);
            initEditBtn();
        }
    });
}

//функция отправки формы
function submitForm(url, data) {
    $.ajax({
        url: url,
        method: 'post',
        data: data,
        success: function (data) {
            //console.log(data);
        }
    });
}

//функция получает данные объекта для расчета стоимости
function getObjectCost(object_id, object_data) {
    $.ajax({
        url: '/ajax/objects/get_object_cost.php',
        method: 'post',
        data: {object_id: object_id},
        success: function (data) {
            if (data) {
                let object_values = JSON.parse(data);
                object_data.OBJECT_COST = object_values.OBJECT_COST;
                object_data.OBJECT_DAILY_COST = object_values.OBJECT_DAILY_COST;
                object_data.COST_PER_PERSON = object_values.COST_PER_PERSON;
                object_data.COST_PER_PERSON_ONE_DAY = object_values.COST_PER_PERSON_ONE_DAY;
                object_data.CAPACITY_ESTIMATED = object_values.CAPACITY_ESTIMATED;
                object_data.CAPACITY_MAXIMUM = object_values.CAPACITY_MAXIMUM;
                object_data.VISIT_PERMISSION_COST = object_values.VISIT_PERMISSION_COST;
                object_data.FIXED_COST = object_values.FIXED_COST;
                if (object_values.FIXED_COST !== 0) {
                    $('#booking-sum-value').html(object_values.FIXED_COST);
                } else {
                    $('#booking-sum-value').html(object_values.OBJECT_COST);//сразу выводим стоимость аренды
                }
            }
        }
    });
    return object_data;
}

//функция добавляет данные одного объекта в другой
function addDataToObject(data, object) {
    for (let [name, value] of data) {
        object[name] = value;
    }
}

//функция генерит массив радиокнопок
function destroyPreloader(preloaderclass, target) {
    $(target).find(preloaderclass).removeClass('active');
}

//функция удаляет категорию
function deleteCategory(id) {
    BX.ajax.runComponentAction(
        'wa:admin',
        'deleteCategory',
        {
            mode: 'class',
            dataType: 'json',
            data: {id: id}
        }
    ).then(
        function (response) {
            if (response) {
                //console.log(response.data.data);
                refreshCategorySelect();
            }
        }
    ).catch(
        function (response) {
            //popup.error(response.errors.pop().message);
        }
    )
}

//функция удаляет локацию
function deleteLocation(id) {
    BX.ajax.runComponentAction(
        'wa:admin',
        'deleteLocation',
        {
            mode: 'class',
            dataType: 'json',
            data: {id: id}
        }
    ).then(
        function (response) {
            if (response) {
                //console.log(response.data.data);
                refreshLocationSelect();
            }
        }
    ).catch(
        function (response) {
            //popup.error(response.errors.pop().message);
        }
    )
}

//функция удаляет характеристику объекта
function deleteCharacteristic(xml_id) {
    BX.ajax.runComponentAction(
        'wa:admin',
        'deleteCharacteristic',
        {
            mode: 'class',
            dataType: 'json',
            data: {xml_id: xml_id}
        }
    ).then(
        function (response) {
            if (response) {
                //console.log(response.data.data);
            }
        }
    ).catch(
        function (response) {
            //popup.error(response.errors.pop().message);
        }
    )
}

//функция печати бланка
function printBlank() {
    $('#print-blank').click(function (e) {
        if (navigator.userAgent.includes('Safari')) {
            try {
                // Print for Safari browser
                document.execCommand('print', false, null);
            } catch {
                window.print();
            }
        } else {
            window.print();
        }
    });
}

//функция вешается на кнопку редактирования заказа
function editOrder() {
    $('#edit-order').click(function () {
        let order_id = $(this).data('orderId');
        let is_booking_fast = $(this).data('bookingFast');
        if (order_id) {
            if (typeof is_booking_fast !== 'undefined' && is_booking_fast === true) {
                window.location.href = '/admin/orders/?edit=Y&CODE=' + order_id + '&fast=Y';
            } else {
                window.location.href = '/admin/orders/?edit=Y&CODE=' + order_id;
            }
        }
    });
}

//функция инициализирует возможность редактирования заказа после оформления
function initEditBtn() {
    let order_id = $('#blank').find('#order-id').html();
    if (order_id) {
        $('#edit-order').attr('data-order-id', order_id);
        editOrder();
    }
}

//функция убирает класс с ошибкой с инпута
function resetInputClass(form_selector) {
    let arr_inputs = $(form_selector).find('input');
    arr_inputs.each(function () {
        $(this).focus(function () {
            if ($(this).parent().hasClass('empty-field')) {
                $(this).parent().removeClass('empty-field');
            }
        });
    });
    $('.input-counter_btn').click(function () {
        if ($(this).parent().hasClass('empty-field')) {
            $(this).parent().removeClass('empty-field');
        }
    });
}

//функция запускает действия при переключении типа бронирования
function calendarSwitcher(radio_group_selector, date_input_selector, object_id, second_date_input_selector) {
    let inputs = $(radio_group_selector).find('input[name="radio"]');
    inputs.unbind('change');
    inputs.change(function () {
        if (object_id !== '') {
            $(date_input_selector).val('');
            $(second_date_input_selector).val('');
            reinitBookingFormCalendar(date_input_selector, object_id, second_date_input_selector, $(this).data('period'));
            if ($(this).data('period') === 'day') {
                $('.second-range-input').prop('readonly', true);
            } else {
                $('.second-range-input').prop('readonly', false);
            }
        } else {
            pushFormError(['Выберите объект']);
        }
    });
}

//функция присваивает классы со стилями дням в календаре во время их генерации
function setCalendarDayClass(day, rent_data) {
    if (!day.classList.contains('nextMonthDay') && !day.classList.contains('prevMonthDay') && !day.classList.contains('flatpickr-disabled')) {
        for (const [date, date_props] of Object.entries(rent_data)) {
            let [d, m, y] = date.split('.');
            if (Number(day) === Number($(d).html())) {
                $(day).addClass(date_props.class);
            }
        }
    }
}

//функция присваивает классы со стилями дням в календаре
function insertBookedDates(calendar, rent_data) {
    let days_matrix = calendar.days.childNodes;
    let disable_dates = [];
    for (const [date, date_props] of Object.entries(rent_data)) {
        if (date_props.status === "disabled") {
            disable_dates.push(date);
        }
    }
    if (disable_dates.length > 0) {
        calendar.config.disable = disable_dates;
    }
    for (const node of days_matrix) {
        var classList = node.classList;
        if (!classList.contains('prevMonthDay') && !classList.contains('flatpickr-disabled') && !classList.contains('nextMonthDay')) {
            insertClass(node);
        }
    }

    function insertClass(node) {
        let node_year = parseInt(node.dateObj.getFullYear());
        let node_month = parseInt(node.dateObj.getMonth(), 10) + 1;
        let node_day = parseInt(node.dateObj.getDate());
        let node_date = new Date(node_year, node_month, node_day);
        for (const [date, date_props] of Object.entries(rent_data)) {
            let [day, month, year] = date.split('.');
            let this_date = new Date(parseInt(year), parseInt(month, 10), parseInt(day, 10));
            if (this_date.toString() === node_date.toString()) {
                $(node).addClass(date_props.class);
            }
        }
    }

}

function disableHalfDate(instance) {
    let allDays = instance.days.getElementsByClassName('flatpickr-day');
    let i = false;
    for (let elDay of allDays) {
        elDay.addEventListener('mouseenter', function () {
            if (this.classList.contains("inRange") && this.classList.contains("first-half-day")) {
                for (let el of allDays) {
                    i ? el.classList.add('notAllowed') : el.classList.remove('notAllowed');
                    if (el === this) {
                        i = true;
                    }
                }
            }
            if (this.classList.contains("endRange")) {
                for (let el of allDays) {
                    if (el.classList.contains("inRange") && el.classList.contains("second-half-day")) {
                        for (let el of allDays) {
                            i ? el.classList.add('notAllowed') : el.classList.remove('notAllowed');
                            if (el === this) {
                                i = true;
                            }
                            ;
                        }
                        el.classList.add('notAllowed');
                    }
                }
            }
            if (this.classList.contains("endRange")) {
                for (let el of allDays) {
                    if (el.classList.contains("endRange") && el.classList.contains("second-half-day")) {
                        for (let el of allDays) {
                            i ? el.classList.add('notAllowed') : el.classList.remove('notAllowed');
                            if (el === this) {
                                i = true;
                            }
                            ;
                        }
                        el.classList.add('notAllowed');
                    }
                }
            }
        });
    }
}

function checkDepartureDate(instance, selectedDates, dep_date_input) {
    if (typeof selectedDates[1] !== 'undefined') {
        if (selectedDates[0] !== '' && selectedDates[1] !== '') {
            if (selectedDates[0].toString() === selectedDates[1].toString()) {
                $(dep_date_input).val('');
                pushFormError(['Дата выезда не может равняться дате заезда!']);
            } else {
                clearErrors();
            }
        }
    }
    let allDays = instance.days.getElementsByClassName('flatpickr-day');
    for (let elDay of allDays) {
        if (elDay.classList.contains("selected") || elDay.classList.contains("inRange")) {
            if (elDay.classList.contains("inRange") && (elDay.classList.contains("first-half-day") || elDay.classList.contains("second-half-day"))) {
                $(dep_date_input).val('');
                pushFormError(['Бронирование на данный диапазон не возможно']);
            }
        }

    }
}

//функция получает забронированные даты объекта и рестартит календарик
function reinitBookingFormCalendar(
    date_input_selector,
    object_id,
    second_date_input_selector,
    period = 'couple',
    is_user_form = false,
    is_edit_order = false,
    unset_day = false,
    is_route = false,
    daily_traffic = false,
) {
    if (object_id) {
        let ajax_data = {
            object_id: object_id,
            user_form: is_user_form,
            period: period
        };
        if (is_edit_order) {
            let a_date = $(date_input_selector).val();
            let d_date = $('.m-input-date-block').find(second_date_input_selector).val();
            let date_ajax_data = {
                unset_arrival_date: a_date,
                unset_departure_date: d_date,
            }
            ajax_data = Object.assign(ajax_data, date_ajax_data);
        }
        if (unset_day) {
            let date_ajax_data = {
                unset_date: unset_day,
            }
            ajax_data = Object.assign(ajax_data, date_ajax_data);
        }
        if (is_route) {
            let date_ajax_data = {
                is_route: is_route,
                daily_traffic: daily_traffic
            }
            ajax_data = Object.assign(ajax_data, date_ajax_data);
        }
        ajaxWrap('/ajax/objects/get_object_rent_dates.php', ajax_data).then((data) => {
            let input = $(date_input_selector);
            let secondInput = $('.m-input-date-block').find(second_date_input_selector)[0];
            if (data) {
                let object_rent_data = JSON.parse(data);
                if (is_user_form) {
                    $(input).val('');
                    $(secondInput).val('');
                }
                if (object_rent_data != null) {
                    switch (period) {
                        case 'couple':
                            flatpickr.localize(flatpickr.l10ns.ru);
                            flatpickr(input, {
                                dateFormat: "d.m.Y",
                                allowInput: "false",
                                allowInvalidPreload: true,
                                disableMobile: "true",
                                minDate: "today",
                                //disable: object_rent_data.disable_dates,
                                "plugins": [new rangePlugin({
                                    input: secondInput
                                })],
                                "onDayCreate": [
                                    function (dObj, dStr, fp, dayElem) {
                                        setCalendarDayClass(dayElem, object_rent_data);
                                    }
                                ],
                                "onOpen": [
                                    function (selectedDates, dateStr, instance) {
                                        insertBookedDates(instance, object_rent_data)
                                    }],
                                "onChange": [
                                    function (selectedDates, dateStr, instance) {
                                        insertBookedDates(instance, object_rent_data);
                                        disableHalfDate(instance);
                                        checkDepartureDate(instance, selectedDates, secondInput);
                                    }
                                ],
                                "onMonthChange": [
                                    function (selectedDates, dateStr, instance) {
                                        insertBookedDates(instance, object_rent_data)
                                    }
                                ],
                                "onYearChange": [
                                    function (selectedDates, dateStr, instance) {
                                        insertBookedDates(instance, object_rent_data)
                                    }
                                ],
                            });
                            break;
                        case 'day':
                            flatpickr.localize(flatpickr.l10ns.ru);
                            flatpickr(input, {
                                dateFormat: "d.m.Y",
                                allowInput: "false",
                                allowInvalidPreload: true,
                                disableMobile: "true",
                                minDate: "today",
                                mode: "single",
                                "onChange": [
                                    function (selectedDates, dateStr, instance) {
                                        insertBookedDates(instance, object_rent_data);
                                        $(secondInput).val($(input).val());
                                        disableHalfDate(instance);
                                    }
                                ],
                                "onDayCreate": [
                                    function (dObj, dStr, fp, dayElem) {
                                        setCalendarDayClass(dayElem, object_rent_data);
                                    }
                                ],
                                "onOpen": [
                                    function (selectedDates, dateStr, instance) {
                                        insertBookedDates(instance, object_rent_data)
                                    }],
                                "onMonthChange": [
                                    function (selectedDates, dateStr, instance) {
                                        insertBookedDates(instance, object_rent_data)
                                    }
                                ],
                                "onYearChange": [
                                    function (selectedDates, dateStr, instance) {
                                        insertBookedDates(instance, object_rent_data)
                                    }
                                ],
                            });
                            break;
                    }
                } else {
                    flatpickr.localize(flatpickr.l10ns.ru);
                    flatpickr(input, {
                        dateFormat: "d.m.Y",
                        allowInput: "false",
                        allowInvalidPreload: true,
                        disableMobile: "true",
                        minDate: "today",
                        "plugins": [new rangePlugin({
                            input: secondInput
                        })],
                    });
                }
            } else {
                flatpickr.localize(flatpickr.l10ns.ru);
                flatpickr(input, {
                    dateFormat: "d.m.Y",
                    allowInput: "false",
                    allowInvalidPreload: true,
                    disableMobile: "true",
                    minDate: "today",
                    "plugins": [new rangePlugin({
                        input: secondInput
                    })],
                });
            }
        });
    }
}

//функция инициализирует карту на квитанции об оплате
function initReceiptMap(object_id, myMap, objManager) {
    $.ajax({
        url: '/ajax/objects/get_object_map_data.php',
        method: 'post',
        data: {object_id: object_id},
        success: function (data) {
            let map_point = JSON.parse(data);
            ymaps.ready(function () {
                if (myMap === null) {
                    if (objManager === null) {
                        objManager = new ymaps.ObjectManager(
                            {
                                clusterize: true,
                                gridSize: 40,
                            }
                        );
                    }
                    myMap = new ymaps.Map('map', {
                        center: map_point.features[0].geometry.coordinates,
                        zoom: 11
                    }, {
                        searchControlProvider: 'yandex#search'
                    }),
                        myMap.geoObjects.add(objManager);
                    objManager.add(map_point);
                } else {
                    myMap.geoObjects.add(objManager);
                    objManager.add(map_point);
                }
            });
        }
    });
}

//функция перезапускает кастомный drop down
function reinitCustomSelect(select_selector) {
    let select = document.querySelector(select_selector);
    var closingSortSelect = function closingSortSelect(e) {
        if (!e.composedPath().includes(select)) {
            select.classList.remove('active');
            document.removeEventListener('click', closingSortSelect);
        }
    };
    if (select) {
        select.addEventListener('click', function (e) {
            var selectHead = select.querySelector('.custom-select_head');
            var currentOption = this.querySelector('.custom-select_title');

            if (e.composedPath().includes(selectHead)) {
                this.classList.toggle('active');
            }

            if (e.target.classList.contains('custom-select_item')) {
                currentOption.textContent = e.target.textContent;
                currentOption.dataset.selectedId = e.target.dataset.id; //custom

                this.classList.remove('active');
            } //закрытие при клике вне элемента


            if (this.classList.contains('active')) {
                document.addEventListener('click', closingSortSelect);
            } else {
                document.removeEventListener('click', closingSortSelect);
            }
        });
    }
}

//функция генерит массив радиокнопок
function createRadioList(data) {
    let radio_list_body = $('#objects-list');
    if (radio_list_body.length !== 0) {
        radio_list_body.html('');
        for (const [key, value] of Object.entries(data)) {
            let html = '<div class="radio">';
            if (value.TIME_INTERVAL) {
                if (value.TIME_INTERVAL.TYPE && value.TIME_INTERVAL.VALUE) {
                    html += `<input type="radio" id="radio_${key}" name="PROPERTY[21][0][VALUE]" value="${key}" 
                                data-time-interval="${value.TIME_INTERVAL.TYPE}" 
                                data-time-value="${value.TIME_INTERVAL.VALUE}" 
                                data-time-limit-value="${value.TIME_LIMIT}"  
                                data-car-possibility-value="${value.CAR_POSSIBILITY}"
                                data-car-capacity-value="${value.CAR_CAPACITY}"
                                >`;
                } else {
                    html += `<input type="radio" id="radio_${key}" name="PROPERTY[21][0][VALUE]" value="${key}" 
                                data-time-limit-value="${value.TIME_LIMIT}"
                                data-car-possibility-value="${value.CAR_POSSIBILITY}"
                                data-car-capacity-value="${value.CAR_CAPACITY}"
                                >`;
                }
            } else {
                html += `<input type="radio" id="radio_${key}" name="PROPERTY[21][0][VALUE]" value="${key}" 
                            data-time-limit-value="${value.TIME_LIMIT}"  
                            data-car-possibility-value="${value.CAR_POSSIBILITY}"
                            data-car-capacity-value="${value.CAR_CAPACITY}"
                            >`;
            }
            html += `<label for="radio_${key}"><div class="radio_text">${value.NAME}</div></label></div>`;
            radio_list_body.append(html);
        }
    } else {
        let parent_block = $('#object-select-block');
        parent_block.html('');
        let parent_block_html = '<div class="form-block">' +
            '<h3 class="form-block_title">Выберите доступный объект</h3>' +
            '<div class="radio-list" id="objects-list">';
        for (const [key, value] of Object.entries(data)) {
            let html = '<div class="radio">';
            if (value.TIME_INTERVAL) {
                if (value.TIME_INTERVAL.TYPE && value.TIME_INTERVAL.VALUE) {
                    html += `<input type="radio" id="radio_${key}" name="PROPERTY[21][0][VALUE]" value="${key}" 
                                data-time-interval="${value.TIME_INTERVAL.TYPE}" 
                                data-time-value="${value.TIME_INTERVAL.VALUE}" 
                                data-time-limit-value="${value.TIME_LIMIT}" 
                                data-car-possibility-value="${value.CAR_POSSIBILITY}"
                                data-car-capacity-value="${value.CAR_CAPACITY}"
                                >`;
                } else {
                    html += `<input type="radio" id="radio_${key}" name="PROPERTY[21][0][VALUE]" value="${key}" 
                        data-time-limit-value="${value.TIME_LIMIT}" 
                        data-car-possibility-value="${value.CAR_POSSIBILITY}"
                        data-car-capacity-value="${value.CAR_CAPACITY}"
                        >`;
                }
            } else {
                html += `<input type="radio" id="radio_${key}" name="PROPERTY[21][0][VALUE]" value="${key}" 
                            data-time-limit-value="${value.TIME_LIMIT}" 
                            data-car-possibility-value="${value.CAR_POSSIBILITY}"
                            data-car-capacity-value="${value.CAR_CAPACITY}"
                            >`;
            }
            html += `<label for="radio_${key}"><div class="radio_text">${value.NAME}</div></label></div>`;
            parent_block_html += html;
        }
        parent_block_html += '</div></div>';
        parent_block.html(parent_block_html);
    }
}

//функция генерит кастомный drop down
function createSelect(data) {
    let select_list_body = $('#object-select').find('.custom-select_body');
    if (select_list_body.length !== 0) {
        select_list_body.html('');
        for (const [key, value] of Object.entries(data)) {
            let html = '';
            if (value.TIME_INTERVAL) {
                if (value.TIME_INTERVAL.TYPE && value.TIME_INTERVAL.VALUE) {
                    html = `<div class="custom-select_item" data-id="${key}" 
                        data-time-interval="${value.TIME_INTERVAL.TYPE}" 
                        data-time-value="${value.TIME_INTERVAL.VALUE}" 
                        data-time-limit-value="${value.TIME_LIMIT}" 
                        data-car-possibility-value="${value.CAR_POSSIBILITY}"
                        data-car-capacity-value="${value.CAR_CAPACITY}"
                        >${value.NAME}</div>`;
                } else {
                    html = `<div class="custom-select_item" data-id="${key}" 
                        data-time-limit-value="${value.TIME_LIMIT}"  
                        data-car-possibility-value="${value.CAR_POSSIBILITY}"
                        data-car-capacity-value="${value.CAR_CAPACITY}"
                        >${value.NAME}</div>`;
                }
            } else {
                html = `<div class="custom-select_item" data-id="${key}" 
                    data-time-limit-value="${value.TIME_LIMIT}"  
                    data-car-possibility-value="${value.CAR_POSSIBILITY}"
                    data-car-capacity-value="${value.CAR_CAPACITY}"
                    >${value.NAME}</div>`;
            }
            select_list_body.append(html);
        }
    } else {
        let parent_block = $('#object-select-block');
        parent_block.html('');
        let parent_block_html = '<div class="form-block form-block--mb30">' +
            '<h3 class="form-block_title">Выберите доступный объект</h3>' +
            '<div class="select-block select-block--lg">' +
            '<div class="custom-select" id="object-select"> <div class="custom-select_head">' +
            '<span class="custom-select_title" data-selected-id="">Выберите доступный объект</span>' +
            '<svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none" ' +
            'xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L13 1" stroke="#000"/>' +
            '</svg></div><div class="custom-select_body">';
        for (const [key, value] of Object.entries(data)) {
            let html = '';
            if (value.TIME_INTERVAL) {
                if (value.TIME_INTERVAL.TYPE && value.TIME_INTERVAL.VALUE) {
                    html = `<div class="custom-select_item" data-id="${key}" 
                        data-time-interval="${value.TIME_INTERVAL.TYPE}" 
                        data-time-value="${value.TIME_INTERVAL.VALUE}" 
                        data-time-limit-value="${value.TIME_LIMIT}"  
                        data-car-possibility-value="${value.CAR_POSSIBILITY}"
                        data-car-capacity-value="${value.CAR_CAPACITY}"
                        >${value.NAME}</div>`;
                } else {
                    html = `<div class="custom-select_item" data-id="${key}"
                        data-time-limit-value="${value.TIME_LIMIT}"  
                        data-car-possibility-value="${value.CAR_POSSIBILITY}"
                        data-car-capacity-value="${value.CAR_CAPACITY}"
                        >${value.NAME}</div>`;
                }
            } else {
                html = `<div class="custom-select_item" data-id="${key}" 
                    data-time-limit-value="${value.TIME_LIMIT}"  
                    data-car-possibility-value="${value.CAR_POSSIBILITY}"
                    data-car-capacity-value="${value.CAR_CAPACITY}"
                    >${value.NAME}</div>`;
            }
            parent_block_html += html;
        }
        parent_block_html += '</div></div></div></div>';
        parent_block.html(parent_block_html);
        reinitCustomSelect('#object-select');
    }
}

function openBookingModal(date = '') {
    if (date !== '') {
        let modal_node = $('section[data-name="modal-booking"]');
        modal_node.toggleClass('active');
        modal_node.find('form[name="iblock_add"]')[0].reset();
        calendarSwitcher('#service-cost', 'input[name="PROPERTY[11][0][VALUE]"]', $('#input-object-id').val(), 'input[name="PROPERTY[12][0][VALUE]"]');
        $('form[name="iblock_add"]').find('input[name="PROPERTY[11][0][VALUE]"]').val(date);
        $('form[name="iblock_add"]').find('input[name="PROPERTY[11][0][VALUE]"]').trigger('change');
        $('#service-cost').find('input[name="radio"]').each(function () {
            $(this).change(function () {
                RecalculateSum();
            });
        });
    } else {
        $('section[data-name="modal-booking"]').find('form[name="iblock_add"]')[0].reset();
        calendarSwitcher('#service-cost', 'input[name="PROPERTY[11][0][VALUE]"]', $('#input-object-id').val(), 'input[name="PROPERTY[12][0][VALUE]"]');
        $('#service-cost').find('input[name="radio"]').each(function () {
            $(this).change(function () {
                RecalculateSum();
            });
        });
    }
}

function onCalendarDateClick() {
    let calendar_node = $('.calendar_wrap');
    let month_node = calendar_node.find('.c-month_body');
    month_node.find('span').click(function (e) {
        let day_node = $(e.target);
        if (typeof day_node.attr('class') != 'undefined') {
            openBookingModal(day_node.data('date'));
        }
    });

}

/*function switchTab() {
    let activeTab = $('.tab.active');
    let id = Number(activeTab.data('id')) + 1;
    switchTabsByBtn(id);
}*/

function custom_openModal() {
    $('.js-open-r-modal').click(function (e) {
        let modal_name = $(e.currentTarget).data('name');
        let modal_node = $('.r-modal[data-name="' + modal_name + '"]');
        modal_node.addClass('active');
        modal_node.find('.r-modal-close-btn').click(function () {
            modal_node.removeClass('active');
        });
    });
}

function switchTabsByBtn(id) {
    $('.tab[data-id="' + (Number(id) - 1) + '"]').removeClass('active');
    $('.tab[data-id="' + id + '"]').addClass('active');
    $('.tabs-content_item[data-id="' + (Number(id) - 1) + '"]').removeClass('active');
    $('.tabs-content_item[data-id="' + id + '"]').addClass('active');
}

function refreshCharacteristics() {
    $.ajax({
        url: '/ajax/objects/refresh_characteristics.php',
        method: 'post',
        success: function (data) {
            $('#characteristic-select').html(data);
            onDeleteCharacteristic();
        }
    });

}

function onDeleteCharacteristic() {
    $('.btn-delete').click(function () {
        let id = $(this).data('itemId');
        $('.r-modal[data-name="modal-delete-characteristic"]').addClass('active');
        $('#modal-delete-characteristic-confirm').click(function (e) {
            deleteCharacteristic(id);
            refreshCharacteristics();
            $('.r-modal[data-name="modal-delete-characteristic"]').removeClass('active');
        });
    });
}

function refreshLocationSelect() {
    $.ajax({
        url: '/ajax/objects/refresh_locations.php',
        method: 'post',
        success: function (data) {
            $('#location-select-body').html(data);
            onDeleteLocation();
        }
    });
}

function refreshCategorySelect() {
    $.ajax({
        url: '/ajax/objects/refresh_category.php',
        method: 'post',
        success: function (data) {
            $('#category-select-body').html(data);
            onDeleteLocation();
        }
    });
}

function refreshPartnerSelect() {
    $.ajax({
        url: '/ajax/objects/refresh_partner.php',
        method: 'post',
        success: function (data) {
            $('#partner-select-body').html(data);
        }
    });
}

function onDeleteLocation() {
    $('.select-btn-delete').click(function () {
        let id = $(this).data('itemId');
        if ($(this).data('selectType') === 'category') {
            $('.r-modal[data-name="modal-delete-category"]').addClass('active');
            $('#modal-delete-category-confirm').click(function (e) {
                deleteCategory(id);
                $('.r-modal[data-name="modal-delete-category"]').removeClass('active');
            });
        } else if ($(this).data('selectType') === 'location') {
            $('.r-modal[data-name="modal-delete-location"]').addClass('active');
            $('#modal-delete-location-confirm').click(function (e) {
                deleteLocation(id);
                $('.r-modal[data-name="modal-delete-location"]').removeClass('active');
            });
        }
    });
}

function insertCostData(
    object_cost_selector,
    visit_permission_cost_selector,
    object_cost,
    visit_permission_cost,
    person_count,
    benefit_person_count) {

    let visit_permission_cost_value = visit_permission_cost;
    if (benefit_person_count && benefit_person_count !== 0) {
        if (person_count && person_count > 0) {
            visit_permission_cost_value = (person_count - benefit_person_count) * visit_permission_cost;
        }
    }

    $(object_cost_selector).val(object_cost);
    $(visit_permission_cost_selector).val(visit_permission_cost_value);
}


function editTimeSelectBlock(selector_type, id) {

    let type = null;
    let value = null;

    if (selector_type === 'radio') {
        type = $('#objects-list').find('input[type="radio"]:checked').data('timeInterval');
        value = $('#objects-list').find('input[type="radio"]:checked').data('timeValue');
    } else {
        type = $('#object-select').find('.custom-select_body').find(`div[data-id="${id}"]`).data('timeInterval');
        value = $('#object-select').find('.custom-select_body').find(`div[data-id="${id}"]`).data('timeValue');
    }

    let time_select_node = $('#time-select-radio');
    if (type === 'single') {
        if (typeof value == null) {
            time_select_node.find('#time-select-day').show();
            time_select_node.find('#time-select-couple').show();
            time_select_node.find('#time-select-day').find('input[name="radio"]').prop('checked', false);
            time_select_node.find('#time-select-couple').find('input[name="radio"]').prop('checked', true);
        } else {
            if (value === 3) {
                time_select_node.find('#time-select-day').hide();
                time_select_node.find('#time-select-day').find('input[name="radio"]').prop('checked', false);
                time_select_node.find('#time-select-couple').find('input[name="radio"]').prop('checked', true);
            } else {
                time_select_node.find('#time-select-couple').hide();
                time_select_node.find('#time-select-couple').find('input[name="radio"]').prop('checked', false);
                time_select_node.find('#time-select-day').find('input[name="radio"]').prop('checked', true);
            }
        }
    } else {
        time_select_node.find('#time-select-day').show();
        time_select_node.find('#time-select-couple').show();
        time_select_node.find('#time-select-day').find('input[name="radio"]').prop('checked', false);
        time_select_node.find('#time-select-couple').find('input[name="radio"]').prop('checked', true);
    }
}

//добавляем маску на инпут с телефоном при нажатии на него
function onTelInputChange() {
    $('input[type="tel"]').on("focus", function () {
        var cleavePhone = new Cleave(this, {
            prefix: '+7',
            delimiter: '-',
            blocks: [2, 3, 3, 2, 2],
            numericOnly: true
        });
    });
}


function drawCarNumberInput(quantity, propertyNumber, inputListSelector, req = false) {
    let inputsList = $(inputListSelector).find('input[type="text"]');
    let arrCarId = [];
    if (inputsList) {
        inputsList.each(function () {
            if ($(this).val() && $(this).val() !== '') {
                arrCarId.push($(this).val());
            }
        });
    }
    if (quantity) {
        let html = '';
        let req_html = '';
        let req_prop = '';
        if (req) {
            req_html = '<span class="color-red">*</span>';
            req_prop = 'required';
        }
        for (let i = 1; i <= quantity; i++) {
            if (arrCarId.length !== 0) {
                if (typeof arrCarId[i - 1] !== 'undefined') {
                    html += `<div class="input">
                    <label for="PROPERTY[${propertyNumber}][${i - 1}]" class="input-label">Номер автомобиля ${i} ${req_html}</label>
                    <input type="text" name="PROPERTY[${propertyNumber}][${i - 1}]" size="30" value="${arrCarId[i - 1]}" ${req_prop}></div>`;
                } else {
                    html += `<div class="input">
                    <label for="PROPERTY[${propertyNumber}][${i - 1}]" class="input-label">Номер автомобиля ${i} ${req_html}</label>
                    <input type="text" name="PROPERTY[${propertyNumber}][${i - 1}]" size="30" ${req_prop}></div>`;
                }
            } else {
                html += `<div class="input">
                    <label for="PROPERTY[${propertyNumber}][${i - 1}]" class="input-label">Номер автомобиля ${i} ${req_html}</label>
                    <input type="text" name="PROPERTY[${propertyNumber}][${i - 1}]" size="30" ${req_prop}></div>`;
            }
        }
        if (html !== '') {
            $(inputListSelector).html('').append(html);
        }
    }
}

function onCarQuantityChange(quantityInputSelector, propertyNumber, inputListSelector, carMaxCapacitySelector, req) {
    let carQuantity = 0;
    let carMaxCapacity = 0;
    $(quantityInputSelector).change(function () {
        carQuantity = $(this).val();
        carMaxCapacity = $(carMaxCapacitySelector).val();
        if (carMaxCapacity) {
            if (carQuantity >= carMaxCapacity) {
                $(this).val(carMaxCapacity);
                drawCarNumberInput(carMaxCapacity, propertyNumber, inputListSelector, req);
                pushFormError([`Максимально разрешенное количество автомобилей на объекте = ${carMaxCapacity}`]);
            } else {
                if (carQuantity === '0') {
                    $(this).val('1');
                }
                drawCarNumberInput(carQuantity, propertyNumber, inputListSelector, req);
            }
        } else {
            if (carQuantity === '0') {
                $(this).val('1');
            }
            drawCarNumberInput(carQuantity, propertyNumber, inputListSelector, req);
        }
    });
}

function onCarRadioChange(radioGroupSelector, detailBlockSelector, propertyNumber, inputListSelector, req) {
    let carRadioInputs = $(radioGroupSelector).find('input[type="radio"]');
    let carDetailBlock = $(detailBlockSelector);
    let carInputList = $(inputListSelector);
    carRadioInputs.each(function () {
        $(this).change(function (e) {
            let r_input = $(e.currentTarget);
            if (r_input.attr('value') === '1') {
                carDetailBlock.show();
                drawCarNumberInput(1, propertyNumber, inputListSelector, req)
            } else {
                carDetailBlock.hide();
                carInputList.html('');
            }
        });
    });
}

function carLogicBlock(radioGroupSelector, detailBlockSelector, quantityInputSelector, propertyNumber, inputListSelector, carMaxCapacitySelector, req = false) {
    onCarRadioChange(radioGroupSelector, detailBlockSelector, propertyNumber, inputListSelector, req);
    onCarQuantityChange(quantityInputSelector, propertyNumber, inputListSelector, carMaxCapacitySelector, req);
}

function onCarPossibilityChange(yesBtnId) {
    $('#car-radio-list').find('input[type="radio"]').each(function () {
        $(this).change(function () {
            if ($(this).attr('id') === yesBtnId) {
                $('#car-capacity-block').show();
            } else {
                $('#car-capacity-block').hide();
                $('#car-capacity-block').find('#car-capacity').attr('value', 0);
            }
        });
    });
}

//функция которая, проверяет объект на наличие значений
function isEmpty(obj) {
    for (const prop in obj) {
        if (Object.hasOwn(obj, prop)) {
            return false;
        }
    }
    return true;
}

//функция которая, удаляет повторяющиеся значения из массива
function arrayUnique(arr) {
    let unique = arr.filter(function (itm, i, a) {
        return i === a.indexOf(itm);
    });
    return unique;
}

//функция, которая сбрасывает ошибки с инпутов и очищает пулл сообщений об ошибке
function resetErrors() {
    let inputs = $(document).find('input');
    $.each(inputs, function (index, input) {
        $(input).on("change", function () {
            let parent = $(this).parent();
            if (parent.hasClass('empty-field')) {
                parent.removeClass('empty-field');
            }
            $('#form-errors').html('');
        });
    });
}

//функция прокидывает параметр в GET
function archive() {
    let url = new URL(window.location.href);
    let is_archive = url.searchParams.get('ARCHIVE');
    if (is_archive) {
        if (is_archive === 'Y') {
            url.searchParams.set('ARCHIVE', 'N');
        } else {
            url.searchParams.set('ARCHIVE', 'Y');
        }
    } else {
        url.searchParams.set('ARCHIVE', 'Y');
    }
    window.location.href = url;
}

//логика формы бронирования в модалке
function modalBookingRulesConfirm(checkbox) {
    let checkBoxParentNode = $(checkbox).closest('.booking-block_btns');
    let btn = checkBoxParentNode.find('.primary-btn');
    if ($(checkbox).is(':checked')) {
        btn.removeClass('primary-btn--disabled');
    } else {
        btn.addClass('primary-btn--disabled');
    }
}

function modalBookingOpenModal(modal) {
    modal.find('#js-open-visiting-modal').click(function (e) {
        e.preventDefault();
        let modal_name = $(e.currentTarget).data('name');
        let modal_node = $('.r-modal[data-name="' + modal_name + '"]');
        modal_node.addClass('active');
        modal_node.find('.r-modal-close-btn').click(function () {
            modal_node.removeClass('active');
        });
    });
    modal.find('#js-open-personal-modal').click(function (e) {
        e.preventDefault();
        let modal_name = $(e.currentTarget).data('name');
        let modal_node = $('.r-modal[data-name="' + modal_name + '"]');
        modal_node.addClass('active');
        modal_node.find('.r-modal-close-btn').click(function () {
            modal_node.removeClass('active');
        });
    });
    modal.find('#js-open-offer-confirm').click(function (e) {
        e.preventDefault();
        let modal_name = $(e.currentTarget).data('name');
        let modal_node = $('.r-modal[data-name="' + modal_name + '"]');
        modal_node.addClass('active');
        modal_node.find('.r-modal-close-btn').click(function () {
            modal_node.removeClass('active');
        });
    });
}

function onPermitRadioChange(modal) {
    let radio = modal.find('input[name="permit"]');
    let btn = modal.find('.permit-block_top').find('.primary-btn');
    let input = modal.find('.permit-block').find('.input');
    radio.each(function () {
        $(this).on('change', function () {
            if ($(this).is(':checked')) {
                if ($(this).val() === 'no') {
                    if (btn) {
                        btn.removeClass('hidden');
                        if (input) {
                            input.addClass('hidden');
                        }
                    }
                } else {
                    if (btn) {
                        btn.addClass('hidden');
                        if (input) {
                            input.removeClass('hidden');
                        }
                    }
                }
            }
        })
    });
}

function onTransportPermitRadioChange(modal) {
    let radio = modal.find('input[name="transport-permit"]');
    let btn = modal.find('#transport-permit-block').find('.primary-btn');
    let input = modal.find('#transport-permit-block').find('.input');
    radio.each(function () {
        $(this).on('change', function () {
            if ($(this).is(':checked')) {
                if ($(this).val() === 'no') {
                    if (btn) {
                        btn.removeClass('hidden');
                        if (input) {
                            input.addClass('hidden');
                        }
                    }
                } else {
                    if (btn) {
                        btn.addClass('hidden');
                        if (input) {
                            input.removeClass('hidden');
                        }
                    }
                }
            }
        })
    });
}

function onModalCarQuantityChange(modal) {
    let input = modal.find('#modal-booking-car-quantity');
    let list = modal.find('#modal-booking-cars-list');
    input.change(function () {
        if ($(this).val()) {
            BX.ajax.runComponentAction(
                'wa:booking',
                'drawCarNumberInput',
                {
                    mode: 'class',
                    dataType: 'json',
                    data: {'COUNT': $(this).val()}
                }
            ).then(
                function (response) {
                    if (response) {
                        list.html(response.data.html);
                    }
                }
            ).catch(
                function (response) {
                    //popup.error(response.errors.pop().message);
                }
            )
        }
    });
}

function modalBookingOnCarRadioChange(modal) {
    let radioNode = modal.find('#modal-booking-car-radio-group');
    radioNode.find('input[name="car-radio"]').each(function () {
        $(this).change(function () {
            if ($(this).is(':checked')) {
                if ($(this).val() === 'yes') {
                    modal.find('#modal-booking-car-detail-hidden').show();
                } else {
                    modal.find('#modal-booking-car-detail-hidden').hide();
                }
            }
        });
    });
    onModalCarQuantityChange(modal);
}

function modalBookingCheckErrors(form) {
    if (form) {
        let arErrors = [];
        form.find('input').each(function () {
            if ($(this).prop('required')) {
                if (!$(this).val()) {
                    let parent = $(this).parent();
                    parent.addClass('empty-field');
                    arErrors.push('Заполните обязательные поля!');
                }
            } else {
                if ($(this).prop('name') === 'permit' && $(this).is(':checked')) {
                    if ($(this).val() === 'yes') {
                        let permit_number = form.find('input[name="VISITING_PERMISSION_ID"]');
                        if (!permit_number.val()) {
                            permit_number.parent().addClass('empty-field');
                            arErrors.push('Заполните номер разрешения на посещение!');
                        }
                    } else {
                        let permit_btn = $(this).closest('.permit-block_top').find('.primary-btn');
                        if (permit_btn.html() !== 'Добавлено') {
                            arErrors.push('Необходимо добавить разрешение на посещение!');
                        }
                    }
                }
                if ($(this).prop('name') === 'car-radio' && $(this).is(':checked') && $(this).val() === 'yes') {
                    form.find('#modal-booking-cars-list').find('input').each(function () {
                        if (!$(this).val()) {
                            let parent = $(this).parent();
                            parent.addClass('empty-field');
                            arErrors.push('Заполните номер транспортного средства!');
                        }
                    });
                }
                if ($(this).prop('name') === 'transport-permit' && $(this).is(':checked')) {
                    if ($(this).val() === 'yes') {
                        let permit_number = form.find('input[name="TRANSPORT_PERMISSION_ID"]');
                        if (!permit_number.val()) {
                            permit_number.parent().addClass('empty-field');
                            arErrors.push('Заполните номер транспортного средства!');
                        }
                    } else {
                        let permit_btn = $(this).closest('.permit-block_top').find('.primary-btn');
                        if (permit_btn.html() !== 'Добавлено') {
                            arErrors.push('Необходимо добавить разрешение на транспортное средство!');
                        }
                    }
                }
            }
        });
        if (!form.find('#modal-booking-arrival-time-select').find('.custom-select_title').data('selectedId')) {
            arErrors.push('Необходимо указать время заезда!');
        }
        if (!form.find('#modal-booking-departure-time-select').find('.custom-select_title').data('selectedId')) {
            arErrors.push('Необходимо указать время выезда!');
        }
        if (!form.find('#modal-booking-stay-confirm').is(':checked')) {
            arErrors.push('Необходимо согласиться с условиями пребывания на территории парка!');
        }
        if (!form.find('#modal-booking-personal-data-confirm').is(':checked')) {
            arErrors.push('Необходимо дать согласие на обработку персональных данных!');
        }
        if (!form.find('#modal-booking-offer-confirm').is(':checked')) {
            arErrors.push('Необходимо согласиться с договором оферты!');
        }
        if (!isEmpty(arErrors)) {
            arErrors = arrayUnique(arErrors);
            pushFormError(arErrors);
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}

function bookingModalAdd2Basket() {
    clearErrors();
    let form = $('#modal-booking').find('form');
    let formData = new FormData(form.get(0));
    let orderCost = form.find('#modal-booking-price').html();
    if (orderCost) {
        formData.append('PRICE', orderCost);
    }
    if (modalBookingCheckErrors(form)) {
        /*for (let [name, value] of formData) {
            console.log(`${name} = ${value}`);
        }*/
        BX.ajax.runComponentAction(
            'wa:booking',
            'add2Basket',
            {
                mode: 'class',
                dataType: 'json',
                data: formData
            }
        ).then(
            function (response) {
                if (response) {
                    console.log(response);
                    $('#modal-booking').removeClass('active');
                }
            }
        ).catch(
            function (response) {
                //popup.error(response.errors.pop().message);
            }
        )
    }
}

function bookingModalAddPermission(btn) {
    $('#modal-booking').find('form').append('<input type="hidden" name="ADD_PERMISSION" value="Y">');
    if (btn) {
        if (!$(btn).attr('disabled')) {
            $(btn).html('Добавлено').attr('onclick', '');
        }
    }
}

function bookingModalAddTransportPermission(btn) {
    $('#modal-booking').find('form').append('<input type="hidden" name="ADD_TRANSPORT_PERMISSION" value="Y">');
    if (btn) {
        if (!$(btn).attr('disabled')) {
            $(btn).html('Добавлено').attr('onclick', '');
        }
    }
}

function modalBookingRecalculateSum(form, isRoute = false) {
    if (form) {
        let sum = form.find('#modal-booking-price');
        let is_permit = 1;
        if (form.find('input[name="permit"]:checked').val()) {
            if (form.find('input[name="permit"]:checked').val() === 'no') {
                is_permit = 2;
            }
        }
        if (isRoute) {
            sum.html(recalculateRoutePrice(
                form.find('input[name="OBJECT_COST"]').val(),
                form.find('#modal-booking-guest-quantity').val(),
                form.find('#modal-booking-beneficiaries-quantity').val(),
                is_permit,
                form.find('input[name="PERMIT_COST"]').val()
            ));
        } else {
            let time_limit_value = $('input[name="TIME_UNLIMIT_OBJECT"]').val();
            if (time_limit_value && time_limit_value === 'Y') {
                sum.html(calculateFixedObjectSum(
                    form.find('#modal-booking-guest-quantity').val(),
                    form.find('#modal-booking-beneficiaries-quantity').val(),
                    form.find('input[name="CAPACITY_MAXIMUM"]').val(),
                    form.find('input[name="CAPACITY_ESTIMATED"]').val(),
                    is_permit,
                    form.find('input[name="PERMIT_COST"]').val(),
                    form.find('input[name="FIXED_COST"]').val()
                ));
            } else {
                sum.html(calculateOrderSum(
                    form.find('input[name="OBJECT_COST"]').val(),
                    form.find('input[name="OBJECT_DAILY_COST"]').val(),
                    form.find('input[name="COST_PER_PERSON"]').val(),
                    form.find('input[name="COST_PER_PERSON_ONE_DAY"]').val(),
                    form.find('#modal-booking-guest-quantity').val(),
                    form.find('input[name="CAPACITY_ESTIMATED"]').val(),
                    form.find('input[name="CAPACITY_MAXIMUM"]').val(),
                    form.find('#modal-booking-beneficiaries-quantity').val(),
                    form.find('input[name="PERMIT_COST"]').val(),
                    is_permit,
                    form.find('input[name="BOOKING_PERIOD"]').val(),
                    calculateOrderPeriod(form.find('input[name="ARRIVAL_DATE"]').val(), form.find('input[name="DEPARTURE_DATE"]').val())
                ));
            }
        }
    }
}

function modalBookingInputChange(isRoute = false) {
    let form = $('#modal-booking').find('form');
    let inputs = form.find('input');
    inputs.each(function () {
        let input_type = $(this).attr('type');
        if (input_type === 'text' || input_type === 'radio') {
            $(this).change(function () {
                modalBookingRecalculateSum(form, isRoute);
            });
        }
    });
}

//оплата заказа
function payOrder() {
    BX.ajax.runComponentAction(
        'wa:basket',
        'payOrder',
        {
            mode: 'class',
            dataType: 'json',
            data: {}
        }
    ).then(
        function (response) {
            if (response) {
                if (response.status === 'success') {
                    console.log(response.data);
                }
            }
        }
    ).catch(
        function (response) {
            //popup.error(response.errors.pop().message);
        }
    )

}

function objectFormLogic() {
    let modal = $('#modal-booking');
    let modal_block = modal.find('.modal_block');
    let info = modal.find('.booking-block_info');
    let form = modal.find('.booking-block_form');
    info.css('display', 'none');
    modal_block[0].scrollTo(0, 0);
    form.css('display', 'block');
    modalBookingOpenModal(modal);
    onPermitRadioChange(modal);
    onTransportPermitRadioChange(modal);
    inputValueCalculator();
    modalBookingOnCarRadioChange(modal);
    custom_openModal();
    reinitBookingFormCalendar(
        'input[name="ARRIVAL_DATE"]',
        modal.find('input[name="LOCATION_ID"]').val(),
        'input[name="DEPARTURE_DATE"]',
        modal.find('input[name="BOOKING_PERIOD"]').val(),
        true,
        false,
        false);
    onChangeDateTimeValues(
        'input[name="LOCATION_ID"]',
        'input[name="ARRIVAL_DATE"]',
        'input[name="DEPARTURE_DATE"]',
        '#modal-booking-arrival-time-select',
        '#modal-booking-departure-time-select',
        '',
        false,
        '',
        0,
        0,
        true
    );
    reinitCustomSelect('#modal-booking-arrival-time-select');
    reinitCustomSelect('#modal-booking-departure-time-select');
    resetErrors();
    modalBookingInputChange();
}

function modalBookingOnGuestCountChangeAction() {
    let modal = $('#modal-booking');
    let permission = modal.find('input[name="permit"]:checked').val();
    let bookingObjectPrice = modal.find('input[name="OBJECT_COST"]').val();
    let guestQuantity = modal.find('#modal-booking-guest-quantity');
    let benefitQuantity = modal.find('#modal-booking-beneficiaries-quantity');
    guestQuantity.change(function () {
        let max = $(this).attr('max');
        if ($(this).val() >= max) {
            pushFormError(['Достигнуто максимально число посетителей на объекте!']);
        }
        if ($(this).val() < benefitQuantity.val()) {
            benefitQuantity.val($(this).val());
            pushFormError(['Количество льготников не должно превышать количество посетителей!']);
        }
    });
    benefitQuantity.change(function () {
        if ($(this).val() > guestQuantity.val()) {
            $(this).val(guestQuantity.val());
            pushFormError(['Количество льготников не должно превышать количество посетителей!']);
        }
    });
}

function routeFormLogic() {
    let modal = $('#modal-booking');
    let modal_block = modal.find('.modal_block');
    let info = modal.find('.booking-block_info');
    let form = modal.find('.booking-block_form');
    info.css('display', 'none');
    modal_block[0].scrollTo(0, 0);
    form.css('display', 'block');
    modalBookingOpenModal(modal);
    onPermitRadioChange(modal);
    onTransportPermitRadioChange(modal);
    inputValueCalculator();
    modalBookingOnCarRadioChange(modal);
    custom_openModal();
    reinitBookingFormCalendar(
        'input[name="ARRIVAL_DATE"]',
        modal.find('input[name="LOCATION_ID"]').val(),
        'input[name="DEPARTURE_DATE"]',
        modal.find('input[name="BOOKING_PERIOD"]').val(),
        false,
        false,
        false,
        true,
        modal.find('input[name="DAILY_TRAFFIC"]').val()
    );
    resetErrors();
    modalBookingInputChange(true);
    modalBookingOnGuestCountChangeAction();
}

function modalBookingShowForm(isRoute = false) {
    if (isRoute) {
        routeFormLogic();
    } else {
        objectFormLogic();
    }
}

//конец логики модалки

//функция, которая убирает активность у модалки
function closeModal(modal_selector, data_name) {
    document.querySelector(modal_selector + '[data-name="' + data_name + '"]').classList.remove('active');
}

//функция открывает форму бронирования
function callBookingModal(elementId) {
    if (elementId) {
        ajaxWrap('/ajax/booking/callBookingForm.php', {'ELEMENT_ID': elementId}).then(
            function (html) {
                let modal = $('#modal-booking');
                modal.replaceWith(html);
                $('#modal-booking').addClass('active');
            }
        );
    }
}

//функция открывает окно с корзиной
function openBasketModal() {
    ajaxWrap('/ajax/booking/openBasket.php', {}).then(
        function (html) {
            $('main').after(html);
        }
    );
}

//функция удаляет позицию из корзины
function deleteFromBasket(id) {
    if (id) {
        BX.ajax.runComponentAction(
            'wa:basket',
            'deleteFromBasket',
            {
                mode: 'class',
                dataType: 'json',
                data: {ID: id}
            }
        ).then(
            function (response) {
                if (response) {
                    if (response.status === 'success') {
                        ajaxWrap('/ajax/booking/refreshBasket.php', {}).then(function (html) {
                            if (html) {
                                let modal = $('.r-modal[data-name="modal-basket"]');
                                let table = modal.find('#ajax-table');
                                table.html(html);
                            }
                        });
                    }
                }
            }
        ).catch(
            function (response) {
                //popup.error(response.errors.pop().message);
            }
        )
    }
}

function checkRouteBookingPossibility(objectId, arrivalDate, departureDate, peopleCount, dailyTraffic) {
    if (objectId
        && arrivalDate
        && departureDate
        && peopleCount
        && dailyTraffic) {
        ajaxWrap(
            '/ajax/booking/checkRouteBookingPossibility.php',
            {
                OBJECT_ID: objectId,
                ARRIVAL_DATE: arrivalDate,
                DEPARTURE_DATE: departureDate,
                PEOPLE_COUNT: peopleCount,
                DAILY_TRAFFIC: dailyTraffic,
            }).then(function (response) {
            if (response === '1') {
                return true;
            } else {
                return false;
            }
        });
    } else {
        return false;
    }
}

function recalculateRoutePrice(
    price,
    peopleCount,
    benefitCount,
    permission,
    permissionCost
) {
    if (price && peopleCount && permission && permissionCost) {
        let PERMIT_PRICE = 0;
        if (permission == 2) {
            PERMIT_PRICE = (parseInt(peopleCount) - parseInt(benefitCount)) * parseInt(permissionCost);
        }
        return parseInt(peopleCount) * price + PERMIT_PRICE;
    }
}

$(document).ready(function () {
    inputValueCalculator();
    onTelInputChange();
    $('#js-open-offer').click(function (e) {
        e.preventDefault();
        let modal_name = $(e.currentTarget).data('name');
        let modal_node = $('.r-modal[data-name="' + modal_name + '"]');
        modal_node.addClass('active');
        modal_node.find('.r-modal-close-btn').click(function () {
            modal_node.removeClass('active');
        });
    });
});