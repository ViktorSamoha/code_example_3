//логика file инпута
function addVisitor_deleteFile(element) {
    let pict_box = $(element).parent();
    let input_id = $(element).data('fileId');
    let property_id = $(element).data('propertyId');
    let file_input = $('input[name="' + property_id + '_FVAL_' + input_id + '"]');
    let delete_file_id = $(element).data('deleteId');
    if (delete_file_id) {
        ajaxWrap(
            '/ajax/user/deleteDocument.php', {file_id: delete_file_id}
        ).then(function () {
            file_input.remove();
            pict_box.remove();
        });
    } else {
        file_input.remove();
        pict_box.remove();
    }
}

function addVisitor_insertPict(selector, id, data, file_type) {
    let div = $(selector);
    let img_data = '';
    if (file_type !== 'application/pdf') {
        img_data = data;
    } else {
        img_data = '/local/templates/.default/assets/img/default_doc_icon.svg';
    }
    let prop_id = div.data('id');
    let html = '<div class="input-file_preview active">' +
        '<img src="' + img_data + '" alt="">' +
        '<div class="input-file-remove-btn" data-property-id="' + prop_id + '" data-file-id="' + id + '" onclick="addVisitor_deleteFile(this)">' +
        '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path ' +
        'd="M2.28795 2.28746C2.4286 2.14686 2.61933 2.06787 2.8182 2.06787C3.01707 2.06787 3.2078 2.14686 3.34845 ' +
        '2.28746L6.00045 4.93946L8.65245 2.28746C8.79391 2.15084 8.98336 2.07525 9.18001 2.07696C9.37665 2.07866 ' +
        '9.56476 2.15754 9.70382 2.2966C9.84288 2.43565 9.92175 2.62376 9.92346 2.82041C9.92517 3.01706 9.84957 3.20651' +
        ' 9.71296 3.34796L7.06095 5.99996L9.71296 8.65197C9.84957 8.79342 9.92517 8.98287 9.92346 9.17952C9.92175 ' +
        '9.37616 9.84288 9.56427 9.70382 9.70333C9.56476 9.84239 9.37665 9.92126 9.18001 9.92297C8.98336 9.92468 ' +
        '8.79391 9.84909 8.65245 9.71247L6.00045 7.06046L3.34845 9.71247C3.207 9.84909 3.01755 9.92468 2.8209 ' +
        '9.92297C2.62425 9.92126 2.43614 9.84239 2.29709 9.70333C2.15803 9.56427 2.07915 9.37616 2.07744 ' +
        '9.17952C2.07573 8.98287 2.15133 8.79342 2.28795 8.65197L4.93995 5.99996L2.28795 3.34796C2.14735 ' +
        '3.20732 2.06836 3.01658 2.06836 2.81771C2.06836 2.61884 2.14735 2.42811 2.28795 2.28746V2.28746Z" fill="#F2F2F2"/>' +
        '</svg></div></div>';
    div.parent().find('label').after(html);
}

function addVisitor_insertFileInput(parent, property_id, id, file) {
    let div = parent;
    let name = `${property_id}_FVAL_${id}`;
    let html = '<input type="file" name="' + name + '" value="">';
    div.append(html);
    let dt = new DataTransfer();
    dt.items.add(file);
    document.querySelector('input[name="' + name + '"]').files = dt.files;
}

function addVisitor_onChangeFileInput(inputSelector, counter) {
    let cur_file_id = counter;
    let inputPropertyId = $(inputSelector).data('id');
    $(inputSelector).change(function (event) {
        const files = event.target.files;
        let parentBlock = $(event.target).parent();
        for (const file of files) {
            let file_type = file.type;
            addVisitor_insertFileInput(parentBlock, inputPropertyId, counter, file);
            let reader = new FileReader();
            reader.addEventListener("load", () => {
                addVisitor_insertPict(inputSelector, cur_file_id, reader.result, file_type);
                cur_file_id++;
            }, false);
            if (file) {
                reader.readAsDataURL(file);
            }
            counter++;
        }
    });
}


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

function getAjaxBlockData() {
    let ajaxBlockHtml = $('#ajax').html();
    if (ajaxBlockHtml !== '') {
        let data = new FormData();
        $('#ajax').find('.custom-select').each(function () {
            let select = $(this);
            if (select) {
                let selectId = select.data('id');
                let selectValue = select.find('.custom-select_title').data('selectedId');
                let selectProp = select.data('prop');
                if (selectProp && selectValue) {
                    data.append(selectProp + '_USER_' + selectId, selectValue);
                }
            }
        });
        return data;
    } else {
        return false;
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
    let prefCategory = $('#main-user-pref-category-select').attr('data-selected-id');
    let prefCategoryLocation = $('#user-location').attr('data-selected-id');
    if (prefCategory) {
        formData.append('PREF_CATEGORY', prefCategory);
    }
    if (prefCategoryLocation) {
        formData.append('LOCATION', prefCategoryLocation);
    }
    let ajaxData = getAjaxBlockData();
    if (ajaxData !== false) {
        for (let [name, value] of ajaxData) {
            formData.append(name, value);
        }
    }
    let price = $('#permission-price').html();
    if (price) {
        formData.append('PRICE', price);
    }

    /*for (let [name, value] of formData) {
        console.log(`${name} = ${value}`);
    }*/

    return formData;
}

function reinitCalendar(input) {
    if (input) {
        flatpickr.localize(flatpickr.l10ns.ru);
        flatpickr(input, {
            dateFormat: "d.m.Y",
            allowInput: "true",
            allowInvalidPreload: true,
            disableMobile: "true",
            minDate: "01-01-1900",
        });
    }
}

function reinitDopInputs(checkBoxId) {
    if (checkBoxId) {
        $(checkBoxId).change(function () {
            if ($(this).is(':checked')) {
                let parentBlock = $(this).closest('.js-parent-hidden-block');
                if (parentBlock) {
                    let hiddenBlock = parentBlock.find('.js-hidden-block');
                    if (hiddenBlock) {
                        hiddenBlock.removeClass('hidden');
                    }
                }
            }
        });
    }
}

function reinitBenifitLocationSelectLogic(selectId, id) {
    if (selectId) {
        let parentBlock = $(selectId).closest('.js-hidden-block');
        let locationProp = false;
        if (parentBlock) {
            locationProp = parentBlock.find('.dop-inputs_item');
        }
        let observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutationRecord) {
                let selectedProperty = $(mutationRecord.target).attr('data-selected-id');
                if (locationProp) {
                    if (selectedProperty == locationProp.data('id')) {
                        reinitCustomSelect('#pref-category-location-select-' + id);
                        locationProp.removeClass('hidden');
                    }
                }
            });
        });
        observer.observe($(selectId).find('.custom-select_title')[0], {
            attributes: true,
            attributeFilter: ['data-selected-id']
        });
    }
}

function initFormLogic(formId) {
    if (formId) {
        reinitDopInputs('#user-' + formId + '-pref-category');
        reinitCustomSelect('#pref-category-select-' + formId);
        reinitBenifitLocationSelectLogic('#pref-category-select-' + formId, formId);
        addVisitor_onChangeFileInput('#u-' + formId + '-pref-docs', 0);
        reinitCalendar($('input[name="PREF_DOC_DATE_USER_' + formId + '"]'));
    }
}

function deleteNewVisitor(id) {
    if (!isNaN(id)) {
        let visitorsCount = $('input[name="VISITORS_COUNT"]');
        let visitorBlock = $('#add-user-form-' + id);
        if (visitorBlock) {
            visitorBlock.remove();
            visitorsCount.val(Number(visitorsCount.val()) - 1);
            recalculatePermissionPrice();
        }
    }
}

function addVisitor() {
    $('#add-visitor').click(function (e) {
        e.preventDefault();
        let visitorsCount = $('input[name="VISITORS_COUNT"]');
        ajaxWrap('/ajax/user/newVisitorBlock.php', {count: visitorsCount.val()}).then(function (html) {
            if (html) {
                $('#ajax').append(html);
            }
            initFormLogic(visitorsCount.val());
            visitorsCount.val(Number(visitorsCount.val()) + 1);
            recalculatePermissionPrice();
        });

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
                            }else{
                                console.log(response);
                            }
                        }
                    }
                ).catch(
                    function (response) {
                        console.log(response);
                    }
                )

            }
        }
    });
}

function addVisitors() {
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
    let pref_doc_counter = 0;
    addVisitor_onChangeFileInput('#u-pref-docs', pref_doc_counter);
    getPermission();
    addVisitor();
    resetErrors();
    addVisitors();
    limitCalendarDates();
    reinitCalendar($('input.c-doc-date'));
});