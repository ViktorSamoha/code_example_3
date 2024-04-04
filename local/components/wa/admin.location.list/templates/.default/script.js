function setLocationPartner(cb) {
    let locationId = $(cb).data('locationId');
    let partnerId = $(cb).val();
    let type = null;
    if (cb.checked) {
        type = 'set';
    } else {
        type = 'delete';
    }
    let select = cb.closest('.custom-select.custom-select--sm');
    $(select).removeClass('active');
    $(select).addClass('loading');
    if (locationId && partnerId && type) {
        BX.ajax.runComponentAction(
            'wa:admin.location.list',
            'setSectionPartner',
            {
                mode: 'class',
                dataType: 'json',
                data: {SECTION_ID: locationId, ACTION: type, PARTNER_ID: partnerId}
            }
        ).then(
            function (response) {
                if (response) {
                    if (response.status === 'success') {
                        $(select).removeClass('loading');
                        $(select).addClass('active');
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
    }
}

function onActivationBtnClick() {
    $('.lk-loc_btn').click(function (e) {
        e.preventDefault();
        let loc_id = $(e.currentTarget).data('id');
        let action = $(e.currentTarget).data('action');
        if (loc_id && action) {
            BX.ajax.runComponentAction(
                'wa:admin.location.list',
                'setSectionActivity',
                {
                    mode: 'class',
                    dataType: 'json',
                    data: {SECTION_ID: loc_id, ACTION: action}
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
        }
    });
}

$(document).ready(function () {
    onActivationBtnClick();
});