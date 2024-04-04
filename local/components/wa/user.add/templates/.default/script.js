function createUser(componentName, actionName, data) {
    BX.ajax.runComponentAction(
        componentName,
        actionName,
        {
            mode: 'class',
            dataType: 'json',
            data: data
        }
    ).then(
        function (response) {
            if (response) {
                if (response.status === 'success') {
                    $('form[name="new_user_form"]')[0].reset();
                    let modal = $('.modal[data-name="modal-success-new-user-add-notification"]');
                    modal.addClass('active');
                }
            }
        }
    ).catch(
        function (response) {
            //popup.error(response.errors.pop().message);
        }
    )
};

function fillObjectList(ar_locations) {
    BX.ajax.runComponentAction(
        'wa:user.add',
        'getLocationObjects',
        {
            mode: 'class',
            dataType: 'json',
            data: {locations: ar_locations}
        }
    ).then(
        function (response) {
            if (response) {
                if (response.status === 'success') {
                    $('#user-object-select').find('.custom-select_body').html(response.data.html);
                }
            }
        }
    ).catch(
        function (response) {
            //popup.error(response.errors.pop().message);
        }
    )
}

function onFormSubmit() {
    let form = $('form[name="new_user_form"]');
    let formData = new FormData(form[0]);
    createUser('wa:user.add', 'addNewUser', formData);
}

$(window).on('load', function () {
    $('#reg-form-submit-btn').click(function (e) {
        e.preventDefault();
        onFormSubmit();
    });
    let ar_loc = [];
    $('#user-location-select').find('input[name="UF_USER_LOCATIONS[]"]').change(function () {
        if ($(this).is(':checked')) {
            ar_loc.push($(this).val());
        } else {
            let index = ar_loc.indexOf($(this).val());
            if (index !== -1) {
                ar_loc.splice(index, 1);
            }
        }
        fillObjectList(ar_loc);
    });
});