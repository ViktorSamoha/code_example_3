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

function addVisitorModalCheckReqFields() {
    let form = $('#add-new-visitor');
    let form_inputs = form.find('input');
    let ar_errors = [];
    let form_errors = $('#modal-form-errors');
    if ($('#pref-category').is(':checked')) {
        let pref_category_select = $('#user-pref-category').attr('data-selected-id');
        if (!pref_category_select) {
            ar_errors.push("Необходимо выбрать льготу<br>");
        } else {
            if (pref_category_select === '21') {
                let pref_doc_number = form.find('input[name="U_PREF_DOC_NUMBER"]');
                let pref_doc_date = form.find('input[name="U_PREF_DOC_DATE"]');
                if (!pref_doc_number.val()) {
                    pref_doc_number.parent().addClass('empty-field');
                    ar_errors.push("Необходимо заполнить обязательные поля!<br>");
                }
                if (!pref_doc_date.val()) {
                    pref_doc_date.parent().addClass('empty-field');
                    ar_errors.push("Необходимо заполнить обязательные поля!<br>");
                }
            }
        }
    }
    $.each(form_inputs, function (index, input) {
        if ($(input).is(':required')) {
            if (!$(input).val()) {
                $(input).parent().addClass('empty-field');
                ar_errors.push("Необходимо заполнить обязательные поля!<br>");
            }
        }
    });
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

$(document).ready(function () {
    //close modal
    let modal = $('section[data-name="modal-add-visitor"]');
    let close_btn = modal.find('.modal-close-btn');
    close_btn.click(function (e) {
        e.preventDefault();
        modal.removeClass('active');
    });

    //add file logic
    let pref_doc_counter = 0;
    onChangeFileInput('#pref-docs', pref_doc_counter);

    flatpickr.localize(flatpickr.l10ns.ru);
    flatpickr(modal.find('input[name="U_PREF_DOC_DATE"]'), {
        dateFormat: "d.m.Y",
        allowInput: "true",
        allowInvalidPreload: true,
        disableMobile: "true",
        minDate: "01-01-1900",
    });

    //form submit logic
    $('#add-new-visitor-action').click(function (e) {
        e.preventDefault();
        $('#add-new-visitor-action').prop("disabled", true);
        let form = $('#add-new-visitor');
        let data = new FormData(form.get(0));
        if ($('#pref-category').is(':checked')) {
            let u_pref_category = form.find('#user-pref-category').attr('data-selected-id');
            if (u_pref_category) {
                data.append('U_PREFERENTIAL_CATEGORY', u_pref_category);
            }
            if (u_pref_category === '21') {
                let u_location = form.find('#user-location').data('selectedId');
                if (u_location) {
                    data.append('U_LOCATION', u_location);
                }
            }
        }
        /*for (let [name, value] of data) {
            console.log(`${name} = ${value}`);
        }*/
        if (addVisitorModalCheckReqFields()) {
            resetErrors();
            $('#modal-form-errors').html('');
            BX.ajax.runComponentAction(
                'wa:user.add.visitor',
                'addUserGroupElement',
                {
                    mode: 'class',
                    dataType: 'json',
                    data: data
                }
            ).then(
                function (response) {
                    if (response) {
                        if (response.status === 'success') {
                            let user_record_id = modal.find('input[name="USER_RECORD_ID"]').val();
                            if (user_record_id) {
                                ajaxWrap(
                                    '/ajax/user/refreshVisitorsSelect.php', {user_record_id: user_record_id}
                                ).then(
                                    function (html) {
                                        if (html) {
                                            $('#visitors-list').html(html);
                                            modal.removeClass('active');
                                        }
                                    }
                                );
                            }
                            $('.modal[data-name="modal-add-visitor"]').removeClass('active');
                            $('#add-new-visitor-action').prop("disabled", false);
                        }
                    }
                }
            ).catch(
                function (response) {
                    //popup.error(response.errors.pop().message);
                }
            )

        }
    });
});