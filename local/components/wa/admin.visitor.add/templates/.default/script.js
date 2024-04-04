function deleteFile(element) {
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

function insertPict(selector, id, data, file_type) {
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
        '<div class="input-file-remove-btn" data-property-id="' + prop_id + '" data-file-id="' + id + '" onclick="deleteFile(this)">' +
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

function insertFileInput(parent, property_id, id, file) {
    let div = parent;
    let name = `${property_id}_FVAL_${id}`;
    let html = '<input type="file" name="' + name + '" value="">';
    div.append(html);
    let dt = new DataTransfer();
    dt.items.add(file);
    document.querySelector('input[name="' + name + '"]').files = dt.files;
}

function onChangeFileInput(inputSelector, counter) {
    let cur_file_id = counter;
    let inputPropertyId = $(inputSelector).data('id');
    $(inputSelector).change(function (event) {
        const files = event.target.files;
        let parentBlock = $(event.target).parent();
        for (const file of files) {
            let file_type = file.type;
            insertFileInput(parentBlock, inputPropertyId, counter, file);
            let reader = new FileReader();
            reader.addEventListener("load", () => {
                insertPict(inputSelector, cur_file_id, reader.result, file_type);
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
    let form = $('#user-edit-form');
    let form_inputs = form.find('input');
    let ar_errors = [];
    let form_errors = $('#form-errors');
    $.each(form_inputs, function (index, input) {
        if ($(input).is(':required')) {
            if (!$(input).val()) {
                $(input).parent().addClass('empty-field');
                ar_errors.push("Необходимо заполнить обязательные поля!<br>");
            } else if ($(input).attr('type') === 'tel') {
                if ($(input).val().length <= 3) {
                    $(input).parent().addClass('empty-field');
                    ar_errors.push("Необходимо заполнить обязательные поля!<br>");
                }
            }
        }
    });
    let pref_cb = $('#pref-category-checkbox');
    if (pref_cb.is(':checked')) {
        let pref_select = $('#user-edit-pref-select').find('.custom-select_title').data('selectedId');
        if (!pref_select) {
            ar_errors.push("Необходимо выбрать льготную категорию!<br>");
        } else if (pref_select === 21) {
            let location_select = $('#user-edit-location-select').find('.custom-select_title').data('selectedId');
            let pref_doc_number = form.find('input[name="PREF_DOC_NUMBER"]');
            let pref_doc_date = form.find('input[name="PREF_DOC_DATE"]');
            if (!location_select) {
                ar_errors.push("Необходимо выбрать населенный пункт!<br>");
            }
            if (!pref_doc_number.val()) {
                $(pref_doc_number).parent().addClass('empty-field');
                ar_errors.push("Необходимо заполнить номер документа подтверждающего льготу!<br>");
            }
            if (!pref_doc_date.val()) {
                $(pref_doc_date).parent().addClass('empty-field');
                ar_errors.push("Необходимо заполнить дату выдачи документа подтверждающего льготу!<br>");
            }
        }
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

//функция которая, сохраняет данные пользователя после редактирования
function saveUserData(e) {
    e.preventDefault();
    resetErrors();
    $('#form-errors').html('');
    let form = $('#user-edit-form');
    let data = new FormData(form.get(0));
    let pref_category_cb = form.find('#pref-category-checkbox');
    let pref_category, location, pref_doc_number, pref_doc_date = false;
    if (pref_category_cb.is(':checked')) {
        pref_category = form.find('#user-edit-pref-select').find('.custom-select_title').data('selectedId');
        location = form.find('#user-edit-location-select').find('.custom-select_title').data('selectedId');
        if (pref_category) {
            data.append('PREF_CATEGORY', pref_category);
        }
        if (location) {
            data.append('LOCATION', location);
        }
    }
    /*for (let [name, value] of data) {
        console.log(`${name} = ${value}`);
    }*/
    if (checkReqFields()) {
        BX.ajax.runComponentAction(
            'wa:admin.visitor.add',
            'addUser',
            {
                mode: 'class',
                dataType: 'json',
                data: data
            }
        ).then(
            function (response) {
                if (response) {
                    if (response.status === 'success') {
                        let success_modal = $('.modal[data-name="modal-success-user-registration-notification"]');
                        let modal_blank_link = success_modal.find('#user-page-link');
                        if (response.data.user_page_link) {
                            modal_blank_link.attr('href', response.data.user_page_link);
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

$(document).ready(function () {
    let pref_doc_counter = $('#pref-docs-count').val();
    onChangeFileInput('#pref-docs', pref_doc_counter);
    $('.c-doc-date').each(function () {
        flatpickr.localize(flatpickr.l10ns.ru);
        flatpickr(this, {
            dateFormat: "d.m.Y",
            allowInput: "true",
            allowInvalidPreload: true,
            disableMobile: "true",
            minDate: "01-01-1900"
        });
    });
});