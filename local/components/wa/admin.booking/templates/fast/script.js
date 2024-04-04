function clearBookingParams() {
    $('input[name="ARRIVAL_DATE"]').val('');
    $('input[name="DEPARTURE_DATE"]').val('');
    $('#arrival-time-select').find('.custom-select_title').attr('data-selected-id', '').html('');
    $('#departure-time-select').find('.custom-select_title').attr('data-selected-id', '').html('');
}

function updateForm(params) {
    if (params) {
        if (params.CAPACITY_MAXIMUM) {
            let guestQuantity = $('#adult-quantity');
            let benefitQuantity = $('#beneficiaries-quantity');
            if (guestQuantity.val() > params.CAPACITY_MAXIMUM) {
                guestQuantity.attr('max', params.CAPACITY_MAXIMUM);
                guestQuantity.val(1);
                benefitQuantity.attr('max', params.CAPACITY_MAXIMUM);
                benefitQuantity.val(0);
                if (params.PRICE) {
                    $('#order-sum-value').val(params.PRICE);
                }
                pushFormError([`Максимальное число посетителей на объекте: ${params.CAPACITY_MAXIMUM}`]);
            }
        }
        if (params.ID) {
            $('input[name="BOOKING_OBJECT_ID"]').val(params.ID);
        }
        if (params.BOOKING_OBJECT_PRICE) {
            $('input[name="BOOKING_OBJECT_PRICE"]').val(params.BOOKING_OBJECT_PRICE);
            $('#order-sum-value').html(params.BOOKING_OBJECT_PRICE);
        }
        if (params.CAR_CAPACITY) {
            $('#car-quantity').attr('max', params.CAR_CAPACITY);
        }
        if (params.BOOKING_OBJECT_PERIOD) {
            $('input[name="BOOKING_OBJECT_PERIOD"]').val(params.BOOKING_OBJECT_PERIOD);
        }
        if (params.TIME_UNLIMIT) {
            $('input[name="TIME_UNLIMIT_OBJECT"]').val(params.TIME_UNLIMIT);
        }
        if (params.CAPACITY_MAXIMUM) {
            $('input[name="CAPACITY_MAXIMUM"]').val(params.CAPACITY_MAXIMUM);
        }
        if (params.CAPACITY_ESTIMATED) {
            $('input[name="CAPACITY_ESTIMATED"]').val(params.CAPACITY_ESTIMATED);
        }
        if (params.FIXED_COST) {
            $('input[name="FIXED_COST"]').val(params.FIXED_COST);
        }
        if (params.OBJECT_DAILY_COST) {
            $('input[name="OBJECT_DAILY_COST"]').val(params.OBJECT_DAILY_COST);
        }
        if (params.COST_PER_PERSON) {
            $('input[name="COST_PER_PERSON"]').val(params.COST_PER_PERSON);
        }
        if (params.COST_PER_PERSON_ONE_DAY) {
            $('input[name="COST_PER_PERSON_ONE_DAY"]').val(params.COST_PER_PERSON_ONE_DAY);
        }
        if (params.IS_ROUTE) {
            $('input[name="IS_ROUTE"]').val(params.IS_ROUTE);
        }
        if (params.DAILY_TRAFFIC) {
            $('input[name="DAILY_TRAFFIC"]').val(params.DAILY_TRAFFIC);
        }
    }
}

function onObjectSelectAction() {
    if ($('#object-select').find('.custom-select_title')[0]) {
        let object_select_observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutationRecord) {
                let object_id = $(mutationRecord.target).attr('data-selected-id');
                initPreloader(false, 'form[name="iblock_add"]');
                BX.ajax.runComponentAction(
                    'wa:admin.booking',
                    'onObjectSelect',
                    {
                        mode: 'class',
                        dataType: 'json',
                        data: {
                            OBJECT_ID: object_id,
                        }
                    }
                ).then(
                    function (response) {
                        destroyPreloader('.preloader', 'form[name="iblock_add"]');
                        if (response) {
                            if (response.status === 'success') {
                                if (response.data.form_html) {
                                    if (response.data.params) {
                                        updateForm(response.data.params);
                                    }
                                    $('#date-time-select-block').html(response.data.form_html);
                                    $('#booking-params').show();
                                    formLogic();
                                }
                            } else {
                                console.log(response);
                            }
                        }
                    }
                ).catch(
                    function (response) {
                        destroyPreloader('.preloader', 'form[name="iblock_add"]');
                        console.log(response);
                    }
                )

            });
        });
        object_select_observer.observe($('#object-select').find('.custom-select_title')[0], {
            attributes: true,
            attributeFilter: ['data-selected-id']
        });
    } else {
        if ($('#objects-list')) {
            $('#objects-list').find('input[type="radio"]').change(function () {
                let object_id = $(this).attr('value');
                $('input[name="ARRIVAL_DATE"]').val('');
                $('input[name="DEPARTURE_DATE"]').val('');
                reinitCustomSelect('#arrival-time-select');
                reinitCustomSelect('#departure-time-select');
                initPreloader(false, 'form[name="iblock_add"]');
                BX.ajax.runComponentAction(
                    'wa:admin.booking',
                    'onObjectSelect',
                    {
                        mode: 'class',
                        dataType: 'json',
                        data: {
                            OBJECT_ID: object_id,
                        }
                    }
                ).then(
                    function (response) {
                        destroyPreloader('.preloader', 'form[name="iblock_add"]');
                        if (response) {
                            if (response.status === 'success') {
                                console.log(response.data.params);

                                if (response.data.form_html) {
                                    if (response.data.params) {
                                        updateForm(response.data.params);
                                    }
                                    $('#date-time-select-block').html(response.data.form_html);
                                    $('#booking-params').show();
                                    formLogic();
                                }
                            } else {
                                console.log(response);
                            }
                        }
                    }
                ).catch(
                    function (response) {
                        destroyPreloader('.preloader', 'form[name="iblock_add"]');
                        console.log(response);
                    }
                )
            });
        }
    }
}

function setFilterAction() {
    initPreloader(false, 'form[name="iblock_add"]');
    let locationId = $('#form-location-filter').attr('data-selected-id');
    let categoryId = $('#form-category-filter').attr('data-selected-id');
    let arrivalDate = $('#form-arrival-date-filter').val();
    let departureDate = $('#form-departure-date-filter').val();
    let arrivalTime = $('#form-arr-time-filter').attr('data-selected-id');
    let departureTime = $('#form-dep-time-filter').attr('data-selected-id');
    let period = $('#form-period-block-filter').find('input[name="filter-period"]:checked').data('period');
    let filterData = new FormData();
    if (locationId) {
        filterData.append('LOCATION_ID', locationId);
    }
    if (categoryId) {
        filterData.append('CATEGORY_ID', categoryId);
    }
    if (arrivalDate) {
        filterData.append('ARRIVAL_DATE', arrivalDate);
    }
    if (departureDate) {
        filterData.append('DEPARTURE_DATE', departureDate);
    }
    if (arrivalTime) {
        filterData.append('ARRIVAL_TIME', arrivalTime);
    }
    if (departureTime) {
        filterData.append('DEPARTURE_TIME', departureTime);
    }
    if (period) {
        filterData.append('PERIOD', period);
    }
    /* for (let [name, value] of filterData) {
         console.log(`${name} = ${value}`);
     }*/
    BX.ajax.runComponentAction(
        'wa:admin.booking',
        'formObjectFilter',
        {
            mode: 'class',
            dataType: 'json',
            data: filterData
        }
    ).then(
        function (response) {
            if (response) {
                if (response.status === 'success') {
                    $('#object-select-block').html(response.data.html);
                    reinitCustomSelect('#object-select');
                    clearBookingParams();
                    onObjectSelectAction();
                } else {
                    console.log(response);
                }
            }
            destroyPreloader('.preloader', 'form[name="iblock_add"]');
        }
    ).catch(
        function (response) {
            console.log(response);
            destroyPreloader('.preloader', 'form[name="iblock_add"]');
        }
    )

}

function onGuestCountChangeAction() {
    let permission = $('input[name="PERMISSION"]:checked').val();
    let bookingObjectPrice = $('input[name="BOOKING_OBJECT_PRICE"]').val();
    let guestQuantity = $('#adult-quantity');
    let benefitQuantity = $('#beneficiaries-quantity');
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

function saveOrderAction() {
    initPreloader(false, 'form[name="iblock_add"]');
    let form = $('form[name="iblock_add"]');
    let formData = new FormData(form.get(0));
    let arrivalTime = $('#arrival-time-select').find('.custom-select_title').data('selectedId');
    let departureTime = $('#departure-time-select').find('.custom-select_title').data('selectedId');
    if (arrivalTime) {
        formData.append('CHECK_IN_TIME', arrivalTime);
    }
    if (departureTime) {
        formData.append('DEPARTURE_TIME', departureTime);
    }
    formData.append('BOOKING_COST', $('#order-sum-value').html().trim());
    formData.append('BOOKING_FAST', 'true');

    /*for (let [name, value] of formData) {
        console.log(`${name} = ${value}`);
    }*/
    let period = $('#time-select-radio').find('input[name="radio"]:checked').attr('data-period');
    if (checkForm(
        'form[name="iblock_add"]',
        'input[name="NAME"]',
        'input[name="LAST_NAME"]',
        '#adult-quantity',
        '',
        '',
        '',
        period,
        arrivalTime,
        departureTime
    )) {
        let isRoute = $('input[name="IS_ROUTE"]').val();
        if (isRoute === 'true') {
            ajaxWrap(
                '/ajax/booking/checkRouteBookingPossibility.php',
                {
                    OBJECT_ID: $('input[name="BOOKING_OBJECT_ID"]').val(),
                    ARRIVAL_DATE: $('input[name="ARRIVAL_DATE"]').val(),
                    DEPARTURE_DATE: $('input[name="DEPARTURE_DATE"]').val(),
                    PEOPLE_COUNT: $('#adult-quantity').val(),
                    DAILY_TRAFFIC: $('input[name="DAILY_TRAFFIC"]').val(),
                }).then(function (response) {
                if (response === '1') {
                    BX.ajax.runComponentAction(
                        'wa:admin.booking',
                        'saveOrder',
                        {
                            mode: 'class',
                            dataType: 'json',
                            data: formData
                        }
                    ).then(
                        function (response) {
                            if (response) {
                                if (response.status === 'success') {
                                    let modal = $('.modal[data-name="modal-success-order-notification"]');
                                    modal.find('#order-blank').attr('href', response.data.blank_link);
                                    modal.addClass('active');
                                } else {
                                    console.log(response);
                                }
                            }
                            destroyPreloader('.preloader', 'form[name="iblock_add"]');
                        }
                    ).catch(
                        function (response) {
                            console.log(response);
                            destroyPreloader('.preloader', 'form[name="iblock_add"]');
                        }
                    )
                } else {
                    pushFormError(['Превышено допустимое число людей на маршруте!']);
                    destroyPreloader('.preloader', 'form[name="iblock_add"]');
                }
            });
        } else {
            BX.ajax.runComponentAction(
                'wa:admin.booking',
                'saveOrder',
                {
                    mode: 'class',
                    dataType: 'json',
                    data: formData
                }
            ).then(
                function (response) {
                    if (response) {
                        if (response.status === 'success') {
                            let modal = $('.modal[data-name="modal-success-order-notification"]');
                            modal.find('#order-blank').attr('href', response.data.blank_link);
                            modal.addClass('active');
                        } else {
                            console.log(response);
                        }
                    }
                    destroyPreloader('.preloader', 'form[name="iblock_add"]');
                }
            ).catch(
                function (response) {
                    console.log(response);
                    destroyPreloader('.preloader', 'form[name="iblock_add"]');
                }
            )
        }
    } else {
        destroyPreloader('.preloader', 'form[name="iblock_add"]');
    }
}

function onCarRadioChange() {
    $('#car-radio-group').find('input[name="car-radio"]').each(function () {
        $(this).change(function () {
            if ($(this).val() === '0') {
                $('#car-detail-hidden').hide();
            } else {
                $('#car-detail-hidden').show();
            }
        });
    });
}

function onCarCountChange() {
    $('#car-quantity').change(function () {
        let max = $(this).attr('max');
        if ($(this).val() >= max) {
            pushFormError(['Достигнуто максимальное количество автомобилей на объекте!']);
        }
        if ($(this).val() <= 1) {
            $(this).val(1);
        }
        BX.ajax.runComponentAction(
            'wa:admin.booking',
            'addCarIdField',
            {
                mode: 'class',
                dataType: 'json',
                data: {
                    COUNT: $(this).val(),
                }
            }
        ).then(
            function (response) {
                if (response) {
                    if (response.status === 'success') {
                        $('#cars-list').html(response.data.html);
                    } else {
                        console.log(response);
                    }
                }
            }
        ).catch(
            function (response) {
                console.log(response);
            }
        )
    });
}

function RecalculateSum() {
    let form = $('form[name="iblock_add"]');
    let sum = form.find('#order-sum-value');
    let time_limit_value = $('input[name="TIME_UNLIMIT_OBJECT"]').val();
    let isRoute = $('input[name="IS_ROUTE"]').val();
    if (isRoute !== 'true') {
        initPreloader(false, 'form[name="iblock_add"]');
        if (time_limit_value && time_limit_value === 'Да') {
            sum.html(calculateFixedObjectSum(
                form.find('#adult-quantity').val(),
                form.find('#beneficiaries-quantity').val(),
                form.find('input[name="CAPACITY_MAXIMUM"]').val(),
                form.find('input[name="CAPACITY_ESTIMATED"]').val(),
                form.find('input[name="PERMISSION"]:checked').val(),
                form.find('input[name="VISIT_PERMISSION_COST"]').val(),
                form.find('input[name="FIXED_COST"]').val(),
            ));
            destroyPreloader('.preloader', 'form[name="iblock_add"]');
        } else {
            sum.html(calculateOrderSum(
                form.find('input[name="BOOKING_OBJECT_PRICE"]').val(),
                form.find('input[name="OBJECT_DAILY_COST"]').val(),
                form.find('input[name="COST_PER_PERSON"]').val(),
                form.find('input[name="COST_PER_PERSON_ONE_DAY"]').val(),
                form.find('#adult-quantity').val(),
                form.find('input[name="CAPACITY_ESTIMATED"]').val(),
                form.find('input[name="CAPACITY_MAXIMUM"]').val(),
                form.find('#beneficiaries-quantity').val(),
                form.find('input[name="VISIT_PERMISSION_COST"]').val(),
                form.find('input[name="PERMISSION"]:checked').val(),
                $('#time-select-radio').find('input[name="radio"]:checked').data('period'),
                calculateOrderPeriod(form.find('input[name="ARRIVAL_DATE"]').val(), form.find('input[name="DEPARTURE_DATE"]').val())
            ));
            destroyPreloader('.preloader', 'form[name="iblock_add"]');
        }
    } else {
        sum.html(recalculateRoutePrice(
            form.find('input[name="BOOKING_OBJECT_PRICE"]').val(),
            form.find('#adult-quantity').val(),
            form.find('#beneficiaries-quantity').val(),
            form.find('input[name="PERMISSION"]:checked').val(),
            form.find('input[name="VISIT_PERMISSION_COST"]').val()
        ));
    }
}

function formLogic() {
    calendarSwitcher(
        '#time-select-radio',
        'input[name="ARRIVAL_DATE"]',
        $('input[name="BOOKING_OBJECT_ID"]').val(),
        'input[name="DEPARTURE_DATE"]'
    );
    let isRoute = $('input[name="IS_ROUTE"]').val();
    if (isRoute !== 'true') {
        reinitBookingFormCalendar(
            'input[name="ARRIVAL_DATE"]',
            $('input[name="BOOKING_OBJECT_ID"]').val(),
            'input[name="DEPARTURE_DATE"]',
            $('input[name="BOOKING_OBJECT_PERIOD"]').val()
        );
        let timLim = $('input[name="TIME_UNLIMIT_OBJECT"]').val();
        if (timLim) {
            if (timLim === 'Нет') {
                onChangeDateTimeValues(
                    'input[name="BOOKING_OBJECT_ID"]',
                    'input[name="ARRIVAL_DATE"]',
                    'input[name="DEPARTURE_DATE"]',
                    '#arrival-time-select',
                    '#departure-time-select',
                    '#time-select-radio input[name="radio"]:checked',
                    false);
            } else {
                onChangeDateTimeValues(
                    'input[name="BOOKING_OBJECT_ID"]',
                    'input[name="ARRIVAL_DATE"]',
                    'input[name="DEPARTURE_DATE"]',
                    '#arrival-time-select',
                    '#departure-time-select',
                    '#time-select-radio input[name="radio"]:checked',
                    false,
                    'Y',
                    $('#arrival-time-select').find('.custom-select_title').attr('data-selected-id'),
                    $('#departure-time-select').find('.custom-select_title').attr('data-selected-id'),
                );
            }
        } else {
            onChangeDateTimeValues(
                'input[name="BOOKING_OBJECT_ID"]',
                'input[name="ARRIVAL_DATE"]',
                'input[name="DEPARTURE_DATE"]',
                '#arrival-time-select',
                '#departure-time-select',
                '#time-select-radio input[name="radio"]:checked',
                false);
        }
        onGuestCountChangeAction();
        reinitCustomSelect('#arrival-time-select');
        reinitCustomSelect('#departure-time-select');
    } else {
        reinitBookingFormCalendar(
            'input[name="ARRIVAL_DATE"]',
            $('input[name="BOOKING_OBJECT_ID"]').val(),
            'input[name="DEPARTURE_DATE"]',
            $('input[name="BOOKING_OBJECT_PERIOD"]').val(),
            false,
            false,
            false,
            isRoute,
            $('input[name="DAILY_TRAFFIC"]').val()
        );
    }
    onInputchange();
    onCarRadioChange();
    onCarCountChange();
}

$(document).ready(function () {
    onObjectSelectAction();
});