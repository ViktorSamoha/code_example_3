function onFormSubmit() {
    $('#user-edit-form-submit-btn').click(function (e) {
        e.preventDefault();
        let form = $('form[name="user_edit_form"]')[0];
        let formData = new FormData(form);
        /*let location_id = $('#user-location-select').find('.custom-select_title').attr('data-selected-id');
        let object_id = $('#user-object-select').find('.custom-select_title').attr('data-selected-id');
        formData.append('USER_LOCATION', location_id);
        formData.append('USER_OBJECT', object_id);*/
        /*for (let [name, value] of formData) {
            console.log(`${name} = ${value}`);
        }*/
        BX.ajax.runComponentAction(
            'wa:user.edit',
            'saveUserData',
            {
                mode: 'class',
                dataType: 'json',
                data: formData
            }
        ).then(
            function (response) {
                if (response) {
                    if (response.status === 'success') {
                        //alert(response.data.data);
                        $('.form-description').append(`<p>${response.data.data}</p>`)
                    }
                }
            }
        ).catch(
            function (response) {
                //popup.error(response.errors.pop().message);
            }
        )
    });
}

function fillObjectList(ar_locations, ar_objects) {
    BX.ajax.runComponentAction(
        'wa:user.edit',
        'getLocationObjects',
        {
            mode: 'class',
            dataType: 'json',
            data: {locations: ar_locations, objects: ar_objects}
        }
    ).then(
        function (response) {
            if (response) {
                if (response.status === 'success') {
                    html = '';
                    for (const [i, value] of Object.entries(response.data.data)) {
                        html += `<div class="custom-select_item">
                        <div class="checkbox checkbox-w-btn">
                        <input type="checkbox" id="checkbox_${value.ID}" value="${value.ID}" name="UF_USER_OBJECTS[]"`;
                        if (typeof value.CONDITION != null && typeof value.CONDITION != 'undefined') {
                            html += `${value.CONDITION}>`;
                        } else {
                            html += '>';
                        }
                        html += `<label for="checkbox_${value.ID}">
                        <div class="checkbox_text">${value.NAME}</div>
                        </label></div></div>`;
                    }
                    $('#user-object-select').find('.custom-select_body').html(html);
                }
            }
        }
    ).catch(
        function (response) {
            //popup.error(response.errors.pop().message);
        }
    )
}

$(window).on('load', function () {
    onFormSubmit();
    let ar_loc = [];
    let ar_obj = [];
    let location_select = $('#user-location-select');
    let object_select = $('#user-object-select');
    location_select.find('input[name="UF_USER_LOCATIONS[]"]').each(function () {
        if ($(this).is(':checked')) {
            ar_loc.push($(this).val());
        }
    });
    object_select.find('input[name="UF_USER_OBJECTS[]"]').each(function () {
        if ($(this).is(':checked')) {
            ar_obj.push($(this).val());
        }
    });
    location_select.find('input[name="UF_USER_LOCATIONS[]"]').change(function () {
        if ($(this).is(':checked')) {
            ar_loc.push($(this).val());
        } else {
            let index = ar_loc.indexOf($(this).val());
            if (index !== -1) {
                ar_loc.splice(index, 1);
            }
        }
        fillObjectList(ar_loc, ar_obj);
    });
    object_select.find('input[name="UF_USER_OBJECTS[]"]').change(function () {
        if ($(this).is(':checked')) {
            ar_obj.push($(this).val());
        } else {
            let index = ar_obj.indexOf($(this).val());
            if (index !== -1) {
                ar_obj.splice(index, 1);
            }
        }
    });
});