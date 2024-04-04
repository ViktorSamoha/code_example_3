//функция добавляет название бронирования для инфоблока
function setIblockRecordName() {
    let form = $('form[name="iblock_add"]');
    let user_name = form.find('#input-user-name').val();
    let user_surname = form.find('#input-user-surname').val();
    let input_name = form.find('#iblock-record-name');
    let iblock_record_title = 'Бронь ' + user_name + ' ' + user_surname;
    let time_limit_value = form.find('input[name="PROPERTY[TIME_LIMIT]"]').val();
    input_name.val(iblock_record_title);
    form.find('input[name="PROPERTY[32][0]"]').attr('value', $('#booking-sum-value').html());//стоимость заказа
    if (time_limit_value === 'N') {
        form.find('input[name="PROPERTY[13][0]"]').attr('value', $('#arrival-time-select').find('.custom-select_title').data('selectedId'));
        form.find('input[name="PROPERTY[14][0]"]').attr('value', $('#departure-time-select').find('.custom-select_title').data('selectedId'));
    } else {
        form.find('input[name="PROPERTY[13][0]"]').attr('value', $('#time-select').find('.custom-select_title').data('selectedId'));
    }
    form.submit();
}

//функция собирает значения полей формы и пересчитывает стоимость аренды
function RecalculateSum() {
    let form = $('form[name="iblock_add"]');
    let sum = form.find('#booking-sum-value');
    let time_limit_value = form.find('input[name="PROPERTY[TIME_LIMIT]"]').val();
    if (time_limit_value === 'N') {
        sum.html(calculateOrderSum(
            form.find('input[name="OBJECT_COST"]').val(),
            form.find('input[name="OBJECT_DAILY_COST"]').val(),
            form.find('input[name="COST_PER_PERSON"]').val(),
            form.find('input[name="COST_PER_PERSON_ONE_DAY"]').val(),
            form.find('input[name="PROPERTY[16][0]"]').val(),
            form.find('input[name="CAPACITY_ESTIMATED"]').val(),
            form.find('input[name="CAPACITY_MAXIMUM"]').val(),
            form.find('input[name="PROPERTY[17][0]"]').val(),
            form.find('input[name="VISIT_PERMISSION_COST"]').val(),
            form.find('input[name="PROPERTY[15]"]:checked').val(),
            form.find('input[name="radio"]:checked').data('period'),
            calculateOrderPeriod(form.find('input[name="PROPERTY[11][0][VALUE]"]').val(), form.find('input[name="PROPERTY[12][0][VALUE]"]').val())
        ));
        insertCostData(
            'input[name="PROPERTY[43][0]"]',
            'input[name="PROPERTY[44][0]"]',
            form.find('input[name="OBJECT_COST"]').val(),
            form.find('input[name="VISIT_PERMISSION_COST"]').val(),
            form.find('input[name="PROPERTY[16][0]"]').val(),
            form.find('input[name="PROPERTY[17][0]"]').val()
        );
    } else {
        sum.html(calculateFixedObjectSum(
            form.find('input[name="PROPERTY[16][0]"]').val(),
            form.find('input[name="PROPERTY[17][0]"]').val(),
            form.find('input[name="CAPACITY_MAXIMUM"]').val(),
            form.find('input[name="CAPACITY_ESTIMATED"]').val(),
            form.find('input[name="PROPERTY[15]"]:checked').val(),
            form.find('input[name="VISIT_PERMISSION_COST"]').val(),
            form.find('input[name="fixed-cost"]').val()
        ));
    }
}

function openModal() {
    $('#js-open-visiting-modal').click(function (e) {
        e.preventDefault();
        let modal_name = $(e.currentTarget).data('name');
        let modal_node = $('.r-modal[data-name="' + modal_name + '"]');
        modal_node.addClass('active');
        modal_node.find('.r-modal-close-btn').click(function () {
            modal_node.removeClass('active');
        });
    });
    $('#js-open-personal-modal').click(function (e) {
        e.preventDefault();
        let modal_name = $(e.currentTarget).data('name');
        let modal_node = $('.r-modal[data-name="' + modal_name + '"]');
        modal_node.addClass('active');
        modal_node.find('.r-modal-close-btn').click(function () {
            modal_node.removeClass('active');
        });
    });
    $('#js-open-offer-confirm').click(function (e) {
        e.preventDefault();
        let modal_name = $(e.currentTarget).data('name');
        let modal_node = $('.r-modal[data-name="' + modal_name + '"]');
        modal_node.addClass('active');
        modal_node.find('.r-modal-close-btn').click(function () {
            modal_node.removeClass('active');
        });
    });
}

function insertObjectPriceValue() {
    let form = $('form[name="iblock_add"]');
    let sum = form.find('#booking-sum-value');
    let time_limit_value = form.find('input[name="PROPERTY[TIME_LIMIT]"]').val();
    let fixed_cost_value = form.find('input[name="fixed-cost"]').val();
    if (time_limit_value && time_limit_value === 'Y') {
        if (fixed_cost_value) {
            sum.html(fixed_cost_value);
        } else {
            sum.html(0);
        }
    }
}

function _createInputDate(input, secondInput) {
    flatpickr.localize(flatpickr.l10ns.ru);
    flatpickr(input, {
        dateFormat: "d.m.Y",
        allowInput: "true",
        allowInvalidPreload: true,
        disableMobile: "true",
        minDate: "today",
        onChange: function (selectedDates, dateStr, instance) {

            let allDays = instance.days.getElementsByClassName('flatpickr-day');
            let i = false;

            for (let elDay of allDays) {

                elDay.addEventListener('mouseenter', function () {
                    if (this.classList.contains("endRange") && this.classList.contains("first-half-day")) {
                        for (let el of allDays) {
                            i ? el.classList.add('notAllowed') : el.classList.remove('notAllowed');
                            if (el === this) {
                                i = true;
                            }
                            ;
                        }
                    }
                    if (this.classList.contains("endRange")) {
                        for (let el of allDays) {
                            if (el.classList.contains("inRange") && el.classList.contains("first-half-day")) {
                                for (let el of allDays) {
                                    i ? el.classList.add('notAllowed') : el.classList.remove('notAllowed');
                                    if (el === this) {
                                        i = true;
                                    }
                                    ;
                                }
                            }
                        }
                    }
                })
            }
        },
        "plugins": [new rangePlugin({
            input: secondInput,
        })]
    });
}

$(document).ready(function () {
    openModal();
    resetInputClass('form[name="iblock_add"]');
    //onChangeTimeValues(['input[name="PROPERTY[13][0]"]', 'input[name="PROPERTY[14][0]"]']);
    $('#bookig-action-button').click(function (e) {
        e.preventDefault();
        let time_limit_value = $('form[name="iblock_add"]').find('input[name="PROPERTY[TIME_LIMIT]"]').val();
        if (checkForm(
            'form[name="iblock_add"]',
            '#input-user-name',
            '#input-user-surname',
            '#adult-quantity',
            '#stay-confirm',
            '#personal-data-confirm',
            '#offer-confirm'
        )) {
            if (time_limit_value === 'N') {
                checkBookingPossibility(
                    '#input-object-id',
                    'input[name="PROPERTY[11][0][VALUE]"]',
                    'input[name="PROPERTY[12][0][VALUE]"]',
                    '#arrival-time-select',
                    '#departure-time-select'
                ).then((resolve) => {
                    if (resolve === 'true') {
                        setIblockRecordName();
                    } else {
                        pushFormError(['Данное время недоступно для бронирования']);
                    }
                })
            } else {
                setIblockRecordName();
            }
        }
    });
    $('#booking-button').click(function () {
        let time_limit_value = $(this).attr('data-time-limit-value');
        let car_possibility = $(this).attr('data-car-possibility');
        let car_capacity = $(this).attr('data-car-capacity');
        let fixed_price = $(this).attr('data-fixed-price-value');
        let form = $('form[name="iblock_add"]');
        let objectId = $(this).attr('data-object-id');
        ajaxWrap('/ajax/objects/booking_form_html.php', {
            form_type: 'bron_object_booking',
            time_limit_value: time_limit_value,
            object_id: objectId
        }).then(
            function (html) {
                if (html) {
                    $('#date-time-select-block').html(html);
                    if (time_limit_value === 'Y') {
                        $('#date-time-select-block').css("width", "100%");
                        reinitCustomSelect('#time-select');
                        reinitBookingFormCalendar(
                            'input[name="PROPERTY[11][0][VALUE]"]',
                            objectId,
                            '',
                            'day',
                            true,
                            false,
                            true);
                        onChangeDateTimeValues('#input-object-id', 'input[name="PROPERTY[11][0][VALUE]"]', '', '#time-select', '', 'input[name="radio"]:checked', false, time_limit_value, $('#time-start').val(), $('#time-end').val(), true);
                    } else {
                        reinitCustomSelect('#arrival-time-select');
                        reinitCustomSelect('#departure-time-select');
                        $('input[name="PROPERTY[11][0][VALUE]"]').val('');
                        $('input[name="PROPERTY[12][0][VALUE]"]').val('');
                        reinitBookingFormCalendar('input[name="PROPERTY[11][0][VALUE]"]', objectId, 'input[name="PROPERTY[12][0][VALUE]"]', 'couple', true);
                        onChangeDateTimeValues('#input-object-id', 'input[name="PROPERTY[11][0][VALUE]"]', 'input[name="PROPERTY[12][0][VALUE]"]', '#arrival-time-select', '#departure-time-select', 'input[name="radio"]:checked', true, null, null, null, true);
                        resetSelectValue('#arrival-time-select');
                        resetSelectValue('#departure-time-select');
                    }
                    if (form.find('input[name="PROPERTY[TIME_LIMIT]"]').length) {
                        form.find('input[name="PROPERTY[TIME_LIMIT]"]').val(time_limit_value);
                    } else {
                        form.append(`<input type="hidden" name="PROPERTY[TIME_LIMIT]" value="${time_limit_value}">`);
                    }
                    if (form.find('input[name="fixed-cost"]').length) {
                        form.find('input[name="fixed-cost"]').val(fixed_price);
                    } else {
                        form.append(`<input type="hidden" name="fixed-cost" value="${fixed_price}">`);
                    }
                    if (form.find('input[name="CAR_POSSIBILITY"]').length) {
                        form.find('input[name="CAR_POSSIBILITY"]').val(car_possibility);
                    } else {
                        form.append(`<input type="hidden" name="CAR_POSSIBILITY" value="${car_possibility}">`);
                    }
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
                        drawCarNumberInput(1, $('input[name="guest-car-prop-number"]').val(), '#cars-list', false);
                    }
                    if (car_capacity) {
                        if (form.find('input[name="CAR_CAPACITY"]').length) {
                            form.find('input[name="CAR_CAPACITY"]').val(car_capacity);
                        } else {
                            form.append(`<input type="hidden" name="CAR_CAPACITY" value="${car_capacity}">`);
                        }
                    } else {
                        if (form.find('input[name="CAR_CAPACITY"]').length) {
                            form.find('input[name="CAR_CAPACITY"]').val('');
                        }
                    }
                    insertObjectPriceValue();
                    carLogicBlock(
                        '#car-radio-group',
                        '#car-detail-hidden',
                        '#car-quantity',
                        $('input[name="guest-car-prop-number"]').val(),
                        '#cars-list',
                        'input[name="CAR_CAPACITY"]',
                        true
                    );
                }
            });
        onInputchange();
        openBookingModal();
        clearErrors();
    });

});