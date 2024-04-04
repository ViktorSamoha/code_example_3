function ajax(params, url) {
    $.ajax({
        url: url,
        method: 'post',
        data: params,
        success: function (data) {
            //console.log(data);
            $('body').removeClass('no-scroll');
            //$('.table-wrap').html(data);
            location.reload();
        }
    });
}

function filterOrderList() {
    let id_input = $('#order-id-filter');
    let date_input = $('#order-date-filter');
    //let my_orders_btn = $('#my-orders-filter-btn');
    let setBtn = $('#set-filter-btn');

    setBtn.click(function () {
        let orderId = id_input.val();
        let dateVal = date_input.val();
        let url = new URL(location.origin + location.pathname);
        let params = new URL(location.href).searchParams;
        for (const [key, value] of params) {
            url.searchParams.set(key, value);
        }
        url.searchParams.set('code', orderId);
        url.searchParams.set('date', dateVal);
        location.href = url;
    });
    /*my_orders_btn.click(function (e) {
        let btn = e.target;
        if ($(btn).attr('data-action') === 'my') {
            let data_obj = $(btn).data('objId');
            let data_loc = $(btn).data('locId');
            let url = new URL(location.origin + location.pathname);
            if (data_obj) {
                let params_string = data_obj.split("'");
                let params = [];
                for (let i = 1; i < params_string.length; i++) {
                    if (i % 2 !== 0) {
                        params.push(params_string[i]);
                    }
                }
                url.searchParams.set('orders', JSON.stringify(params));
            }
            if (data_loc) {
                let params_string = data_loc.split("'");
                let params = [];
                for (let i = 1; i < params_string.length; i++) {
                    if (i % 2 !== 0) {
                        params.push(params_string[i]);
                    }
                }
                url.searchParams.set('locations', JSON.stringify(params));
            }


            location.href = url;
        } else {
            let url = new URL(location.origin + location.pathname);
            location.href = url;
        }
    });*/

    let location_filter_block = $('#user-location-filter');
    let location_filter = $(location_filter_block).find('.custom-select_title')[0];
    if (location_filter) {
        let locationObserver = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutationRecord) {
                let locationId = $(mutationRecord.target).attr('data-selected-id');
                let url = new URL(location.origin + location.pathname);
                if (locationId) {
                    let params = [];
                    params.push(locationId);
                    url.searchParams.set('locations', params);
                }
                location.href = url;
            });
        });
        locationObserver.observe(location_filter, {attributes: true, attributeFilter: ['data-selected-id']});
    }
}

function initCalendar() {
    let input = $('#order-date-filter');
    flatpickr.localize(flatpickr.l10ns.ru);
    flatpickr(input, {
        dateFormat: "d.m.Y",
        allowInput: "true",
        allowInvalidPreload: true,
        disableMobile: "true",
        minDate: "01-01-2022",
    });
}

function free(object) {
    openModal();
    let params = {
        object_id: $(object).data('objectId'),
        record_id: $(object).data('hlbOrderId'),
        object_arrival_date: $(object).data('objectArrivalDate'),
        object_departure_date: $(object).data('objectDepartureDate'),
    };
    $('#object-order-cancel-btn').click(function () {
        ajax(params, '/ajax/orders/cancel_object_rent.php');
        $('div[data-name="modal-cancel-reservation"]').removeClass('active');
    });
}

function openModal() {
    $('.js-open-r-modal').click(function (e) {
        let modal_name = $(e.currentTarget).data('name');
        $('.r-modal[data-name="' + modal_name + '"]').addClass('active');
    });
}

function getArchiveOrders() {
    $('#orders-archive-btn').click(function (e) {
        e.preventDefault();
        let url = new URL(location.origin + location.pathname);
        let params = new URL(location.href).searchParams;
        for (const [key, value] of params) {
            url.searchParams.set(key, value);
        }
        url.searchParams.set('arch', 'Y');
        location.href = url;
    });
}

function onSearch() {
    let searchForm = $('#search-form-block');
    let FioInput = searchForm.find('#search-field-fio');
    let carIdInput = searchForm.find('#search-field-car-id');
    let objectNameInput = searchForm.find('#search-field-object-name');
    let searchBtn = searchForm.find('#search-btn');
    searchBtn.click(function (e) {
        e.preventDefault();
        let url = new URL(location.origin + location.pathname);
        let params = new URL(location.href).searchParams;
        for (const [key, value] of params) {
            url.searchParams.set(key, value);
        }
        url.searchParams.set('search_fio', FioInput.val());
        url.searchParams.set('search_car_id', carIdInput.val());
        url.searchParams.set('search_object_name', objectNameInput.val());
        location.href = url;
    });
}

$(window).on('load', function () {
    filterOrderList();
    initCalendar();
    getArchiveOrders();
    onSearch();
});