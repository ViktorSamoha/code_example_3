function updatePermission(action, id, deny_text = '') {
    if (action && id) {
        let data = {};
        if (deny_text) {
            data = {'ACTION': action, 'PERMISSION_ID': id, 'DENY_TEXT': deny_text};
        } else {
            data = {'ACTION': action, 'PERMISSION_ID': id};
        }
        BX.ajax.runComponentAction(
            'wa:receipt.blank',
            'updateVehiclePermission',
            {
                mode: 'class',
                dataType: 'json',
                data: data
            }
        ).then(
            function (response) {
                if (response) {
                    if (response.status === 'success') {
                        window.location.reload();
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
    } else {
        console.log('Ошибка - отсутствует значение id');
    }
}

function onFormSubmit() {
    $('#save-blank').click(function (e) {
        e.preventDefault();
        let permit_status = $('input[name="permit-status"]:checked').val();
        let permission_id = $('input[name="PERMISSION_ID"]').val();
        if (permit_status === 'no') {
            let deny_text = $('textarea[name="DENY_TEXT"]').val();
            let form_errors = $('#form-errors');
            if (deny_text) {
                form_errors.html('');
                updatePermission('DENY', permission_id, deny_text);
            } else {
                form_errors.html('<span class="warn-message">Необходимо заполнить причину отказа!</span>');
            }
        } else {
            updatePermission('APPROVE', permission_id);
        }
    });
}

$(window).on('load', function () {
    printBlank();
    onFormSubmit();
});