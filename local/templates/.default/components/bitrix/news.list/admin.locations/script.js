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
    ajaxWrap(
        '/ajax/locations/set_partner.php',
        {location_id: locationId, partner_id: partnerId, action_type: type}
    ).then(function (response) {
        //console.log(response);
        $(select).removeClass('loading');
        $(select).addClass('active');
        location.reload();
    });
}

function onActivationBtnClick() {
    $('.lk-loc_btn').click(function (e) {
        e.preventDefault();
        let loc_id = $(e.currentTarget).data('id');
        let action = $(e.currentTarget).data('action');
        if (loc_id && action) {
            ajaxWrap(
                '/ajax/objects/set_location_activity.php',
                {action: action, location: loc_id}
            ).then(function (response) {
                location.reload();
            });
        }
    });
}

$(document).ready(function () {
    onActivationBtnClick();
});