function checkReqFields() {
    let form = $('form.form-visiting-permit');
    let form_inputs = form.find('input');
    let ar_errors = [];
    let form_errors = $('#form-errors');
    let personal_data_confirm = $('#personal-data-confirm').is(':checked');
    let visiting_rules_confirm = $('#visiting-rules-confirm').is(':checked');
    $.each(form_inputs, function (index, input) {
        if ($(input).is(':required')) {
            if (!$(input).val()) {
                $(input).parent().addClass('empty-field');
                ar_errors.push("Необходимо заполнить обязательные поля!<br>");
            }
        }
    });
    if (!personal_data_confirm) {
        ar_errors.push("Необходимо дать согласие на обработку персональных данных!<br>");
    }
    if (!visiting_rules_confirm) {
        ar_errors.push("Необходимо согласиться с правилами посещения парка!<br>");
    }
    if (ar_errors.length > 0) {
        ar_errors = arrayUnique(ar_errors);
        $.each(ar_errors, function (index, error) {
            form_errors.append(`<span class="warn-message">${error}</span>`);
        });
        ar_errors.length = 0
    } else {
        return true;
    }
}

function recalculatePermissionPrice() {
    let count = $('input[name="VISITORS_COUNT"]').val();
    if (count) {
        let data = {count: count}
        BX.ajax.runComponentAction(
            'wa:user.visiting.permit',
            'recalculatePermissionPrice',
            {
                mode: 'class',
                dataType: 'json',
                data: data
            }
        ).then(
            function (response) {
                if (response) {
                    //console.log(response);
                    if (response.status === 'success') {
                        $('#permission-price').html(response.data.price);
                    } else {
                        console.log(response);
                        //TODO:СДЕЛАТЬ ВСПЛЫВАШКУ С ВЫБОРОМ ДАЛЬНЕЙШИХ ДЕЙСТВИЙ
                    }
                }
            }
        ).catch(
            function (response) {
                //popup.error(response.errors.pop().message);
            }
        )
    } else {
        console.log('Ошибка: отсутствует значение количества посетителей!');
    }
}

function gatherFormData() {
    let form = $('form.form-visiting-permit');
    let formData = new FormData(form.get(0));
    let form_selects = form.find('.custom-select_title');
    $.each(form_selects, function (index, select) {
        let select_name = $(select).data('name');
        let select_value = $(select).data('selectedId');
        if (select_value) {
            formData.append(select_name, select_value);
        }
    });
    let price = $('#permission-price').html();
    if (price) {
        formData.append('PRICE', price);
    }
    /*for (let [name, value] of formData) {
        console.log(`${name} = ${value}`);
    }*/
    return formData;
}

function addVisitor() {
    $('#add-visitor').click(function (e) {
        e.preventDefault();
        $('section[data-name="modal-add-visitor"]').addClass('active');
    });
}

function getPermission() {
    $('#get-permission').click(function (e) {
        e.preventDefault();
        $('#form-errors').html('');
        if (checkReqFields()) {
            let data = gatherFormData();
            if (data) {
                BX.ajax.runComponentAction(
                    'wa:user.visiting.permit',
                    'registerUserPermission',
                    {
                        mode: 'class',
                        dataType: 'json',
                        data: data
                    }
                ).then(
                    function (response) {
                        if (response) {
                            if (response.status === 'success') {
                                let success_modal = $('.modal[data-name="modal-success-permission-notification"]');
                                let modal_blank_link = success_modal.find('#blank-link');
                                if (response.data.blank_link) {
                                    modal_blank_link.attr('href', response.data.blank_link);
                                }
                                success_modal.addClass('active');
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
    });
}

function addVisitors() {
    /*$('#add-visitors').click(function () {
        let visitors_list = [];
        $('#visitors-list').find('input[type="checkbox"]:checked').each(function () {
            visitors_list.push($(this).val());
        });
        if (visitors_list.length > 0) {
            $('input[name="VISITORS_COUNT"]').val(Number(visitors_list.length));
            ajaxWrap(
                '/ajax/user/addVisitors.php', {id: visitors_list}
            ).then(
                function (html) {
                    if (html) {
                        $('#ajax').html(html);
                        recalculatePermissionPrice();
                    }
                }
            );
        }
    });*/
    let visitors_list = [];
    $('#visitors-list').find('input[type="checkbox"]').each(function () {
        $(this).change(function () {
            if ($(this).val() && $(this).is(':checked')) {
                visitors_list.push($(this).val());
            } else {
                for (var key in visitors_list) {
                    if (visitors_list[key] == $(this).val()) {
                        visitors_list.splice(key, 1);
                    }
                }
            }
            $('input[name="VISITORS_COUNT"]').val(Number(visitors_list.length));
            ajaxWrap(
                '/ajax/user/addVisitors.php', {id: visitors_list}
            ).then(
                function (html) {
                    if (html) {
                        $('#ajax').html(html);
                        recalculatePermissionPrice();
                    } else {
                        $('#ajax').html('');
                        recalculatePermissionPrice();
                    }
                }
            );
        });
    });
}

function unsetVisitor(visitor_id) {
    if (visitor_id) {
        $('#visitor-block-' + visitor_id).remove();
        let visitor_checkbox = $('#visitors-list').find(`input[value="${visitor_id}"]`);
        if (visitor_checkbox.is(':checked')) {
            visitor_checkbox.prop('checked', false);
        }
        let visitors_list = [];
        $('#visitors-list').find('input[type="checkbox"]:checked').each(function () {
            visitors_list.push($(this).val());
        });
        if (visitors_list.length > 0) {
            $('input[name="VISITORS_COUNT"]').val(Number(visitors_list.length));
            recalculatePermissionPrice();
        } else {
            $('input[name="VISITORS_COUNT"]').val('0');
            recalculatePermissionPrice();
        }
    }
}

//скрипт, который дает выбрать только дату заезда и автоматом проставляет дату выезда
function limitCalendarDates() {
    let date_input_group = $('.js-input-date-group');
    let arrival_date_input = date_input_group.find('input[name="USER_ARRIVAL_DATE"]');
    let departure_date_input = date_input_group.find('input[name="USER_DEPARTURE_DATE"]');
    flatpickr.localize(flatpickr.l10ns.ru);
    flatpickr(arrival_date_input, {
        dateFormat: "d.m.Y",
        allowInput: "true",
        allowInvalidPreload: true,
        disableMobile: "true",
        minDate: "today",
        onChange: function onChange(selectedDates, dateStr, instance) {
            let date = new Date(selectedDates);
            date.setDate(date.getDate() + 2);
            departure_date_input.val(date.toLocaleDateString());
        },
    });
}

$(document).ready(function () {
    getPermission();
    addVisitor();
    resetErrors();
    addVisitors();
    limitCalendarDates();
});