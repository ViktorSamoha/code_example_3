function deletePic(element) {
    let pict_box = $(element).parent();
    let input_id = $(element).data('fileId');
    let property_id = $(element).data('propertyId');
    let file_input = $('input[name="PROPERTY_FILE_' + property_id + '_' + input_id + '"]');
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
        '<div class="input-file-remove-btn" data-property-id="' + prop_id + '" data-file-id="' + id + '" onclick="deletePic(this)">' +
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
    let name = `${property_id}_K_${id}`;
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

function saveObject() {
    let form = $('.form-create-object');
    let formData = new FormData(form.get(0));
    let sectionValue = $('#section-selector').find('.custom-select_title').data('selectedId');
    let categoryValue = $('#category-selector').find('.custom-select_title').data('selectedId');
    let locationValue = $('#location-selector').find('.custom-select_title').data('selectedId');
    let startTimeValue = $('#start-time-select').find('.custom-select_title').data('selectedId');
    let endTimeValue = $('#end-time-select').find('.custom-select_title').data('selectedId');
    if (sectionValue) {
        formData.append('SECTION_ID', sectionValue);
    }
    if (categoryValue) {
        formData.append('CATEGORY_ID', categoryValue);
    }
    if (locationValue) {
        formData.append('LOCATION_ID', locationValue);
    }
    if (startTimeValue) {
        formData.append('START_TIME', startTimeValue);
    }
    if (endTimeValue) {
        formData.append('END_TIME', endTimeValue);
    }

    /*for (let [name, value] of formData) {
        console.log(`${name} = ${value}`);
    }*/

    BX.ajax.runComponentAction(
        'wa:admin.object',
        'saveObject',
        {
            mode: 'class',
            dataType: 'json',
            data: formData
        }
    ).then(
        function (response) {
            if (response) {
                //console.log(response);
                if (response.status === 'success') {
                    let modal = $('.modal[data-name="modal-success-object-add-notification"]');
                    modal.addClass('active');
                } else {
                    console.log(response);
                    pushFormError([response.data]);
                }
            }
        }
    ).catch(
        function (response) {
            console.log(response);
            pushFormError([response.data]);
        }
    )
}

function onTimeLimitSelect() {
    let price_block = $('#price-block');
    let work_time_block = $('#work-time-node');
    $('input[type="radio"][name="TIME_UNLIMIT_OBJECT"]').change(function () {
        if ($(this).val() == 38) {
            price_block.find('div[data-type="non-fix-price"]').show();
            price_block.find('div[data-type="fix-price"]').hide();
            price_block.show();
            work_time_block.hide();
        } else {
            price_block.find('div[data-type="fix-price"]').show();
            price_block.find('div[data-type="non-fix-price"]').hide();
            price_block.show();
            work_time_block.show();
        }
    });
}

function onBookingRadioChange() {
    $('input[name="CAN_BOOK"]').change(function () {
        if (Number($(this).val()) === 37) {
            $('#booking-alert-msg-block').show();
        } else {
            $('#booking-alert-msg-block').hide();
        }
    });
}

function onCategorySelectChange() {
    let select = $('#category-selector').find('.custom-select_title')[0];
    let categoryObserver = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutationRecord) {
            let category_id = $(mutationRecord.target).attr('data-selected-id');
            if (category_id == '98') {
                $('#route-prop-block').show();
                $('#object-prop-block').hide();
                $('.put-on-map-btn[data-name="modal-put-on-map"]').hide();
                $('.put-on-map-btn[data-name="modal-route-on-map"]').show();
            } else {
                $('#route-prop-block').hide();
                $('#object-prop-block').show();
                $('.put-on-map-btn[data-name="modal-put-on-map"]').show();
                $('.put-on-map-btn[data-name="modal-route-on-map"]').hide();
            }
        });
    });
    categoryObserver.observe(select, {attributes: true, attributeFilter: ['data-selected-id']});
}


$(document).ready(function () {
    onTimeLimitSelect();
    onCarPossibilityChange('CAR_POSSIBILITY_40');
    onDeleteLocation();
    onDeleteCharacteristic();
    onBookingRadioChange();
    let img_counter = $('#gallery-counter').val();
    let preview_counter = $('#video-preview-counter').val();
    let video_counter = $('#video-counter').val();
    onChangeFileInput('#file-input', img_counter);
    onChangeFileInput('#preview-file-input', preview_counter);
    onChangeFileInput('#video-file-input', video_counter);
    onCategorySelectChange();
});