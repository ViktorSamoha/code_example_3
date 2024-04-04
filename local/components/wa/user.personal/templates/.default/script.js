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

function popupLogic() {
    var checkboxGroup = document.querySelectorAll('.js-switch-hidden-block');
    let checkbox = checkboxGroup[0];
    let prefSelect = $('#user-edit-pref-select').find('.custom-select_title')[0];
    let hidden_block = $('#user-edit-form').find('.dop-inputs_item');
    let hidden_block_id = hidden_block.data('id');
    let prefSelectObserver = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutationRecord) {
            let pref_id = $(mutationRecord.target).attr('data-selected-id');
            if (pref_id) {
                if (pref_id == hidden_block_id) {
                    hidden_block.removeClass('hidden');
                } else {
                    hidden_block.addClass('hidden');
                }
            }
        });
    });
    let pref_doc_counter = $('#pref-docs-count').val();
    checkbox.addEventListener('change', function () {
        var block = this.closest('.js-parent-hidden-block');
        var hiddenBlock = block.querySelector('.js-hidden-block');

        if (this.checked) {
            hiddenBlock.classList.remove('hidden');
        } else {
            hiddenBlock.classList.add('hidden');
        }
    });
    reinitCustomSelect('#user-edit-pref-select');
    reinitCustomSelect('#user-edit-location-select');
    prefSelectObserver.observe(prefSelect, {attributes: true, attributeFilter: ['data-selected-id']});
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
    onChangeFileInput('#pref-docs', pref_doc_counter);
}

$(document).ready(function () {
    //скрипт который, запрашивает форму редактирования личных данных пользователя
    $('#edit-user-data').click(function (e) {
        e.preventDefault();
        ajaxWrap(
            '/ajax/user/editPersonalData.php', {template: 'edit_user_data_form'}
        ).then(
            function (html) {
                if (html) {
                    $('main').after(html);
                    popupLogic();
                }
            }
        );
    });
});

//функция которая, сохраняет данные пользователя после редактирования
function saveUserData(e) {
    e.preventDefault();
    let form = $('#user-edit-form');
    let data = new FormData(form.get(0));
    let pref_category_cb = form.find('#pref-category-checkbox');
    let pref_category, location = false;

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
    BX.ajax.runComponentAction(
        'wa:user.personal',
        'saveUserData',
        {
            mode: 'class',
            dataType: 'json',
            data: data
        }
    ).then(
        function (response) {
            if (response) {
                if (response.status === 'success') {
                    let success_msg = response.data.data;
                    ajaxWrap(
                        '/ajax/user/editPersonalData.php', {template: 'user_form'}
                    ).then(
                        function (html) {
                            if (html) {
                                $('.modal[data-name="modal-edit"]').removeClass('active');
                                let success_html = '';
                                if (success_msg) {
                                    success_html = `<div class="success-msg">${success_msg}</div>`;
                                }
                                $('#ajax-user-data').html(html).after(success_html);
                            }
                        }
                    );
                }
            }
        }
    ).catch(
        function (response) {
            //popup.error(response.errors.pop().message);
        }
    )
}

//функция, которая удаляет посетителя
function deleteVisitor(visitor_id) {
    if (visitor_id) {
        $('.r-modal[data-name="modal-delete-visitor"]').addClass('active');
        BX.ajax.runComponentAction(
            'wa:user.personal',
            'deleteVisitor',
            {
                mode: 'class',
                dataType: 'json',
                data: {VISITOR_ID: visitor_id}
            }
        ).then(
            function (response) {
                if (response) {
                    if (response.status === 'success') {
                        let success_msg = response.data.msg;
                        ajaxWrap(
                            '/ajax/user/refreshVisitorsList.php', {template: 'visitor_list'}
                        ).then(
                            function (html) {
                                if (html) {
                                    let success_html = '';
                                    if (success_msg) {
                                        success_html = `<div class="success-msg">${success_msg}</div>`;
                                    }
                                    $('#ajax-group-list').html(html).after(success_html);
                                } else {
                                    $('#ajax-group-list').html('');
                                }
                            }
                        );
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
        $('.r-modal[data-name="modal-delete-visitor"]').removeClass('active');
    }
}

function deleteVehicle(vehicle_id) {
    if (vehicle_id) {
        $('#delete-vehicle-confirm').click(function (e) {
            e.preventDefault();
            BX.ajax.runComponentAction(
                'wa:user.personal',
                'deleteVehicle',
                {
                    mode: 'class',
                    dataType: 'json',
                    data: {VEHICLE_ID: vehicle_id}
                }
            ).then(
                function (response) {
                    if (response) {
                        if (response.status === 'success') {
                            let success_msg = response.data.msg;
                            ajaxWrap(
                                '/ajax/user/refreshVehicleList.php', {template: 'vehicle_list'}
                            ).then(
                                function (html) {
                                    if (html) {
                                        let success_html = '';
                                        if (success_msg) {
                                            success_html = `<div class="success-msg">${success_msg}</div>`;
                                        }
                                        $('#ajax-vehicle-list').html(html).after(success_html);
                                    }
                                }
                            );
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
            $('.r-modal[data-name="modal-delete-vehicle"]').removeClass('active');
        });
    }
}

function editVehicle(vehicle_id) {
    if (vehicle_id) {
        window.location.href = '/user/add_transport/?ID=' + vehicle_id;
    }
}