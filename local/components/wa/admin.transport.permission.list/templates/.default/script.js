function setUserVehicleBlockStatus(id, action) {
    if (id && action) {
        let data = {};
        if (action === 'BLOCK') {
            data = {'VEHICLE_ID': id, 'ACTION': 'BLOCK'};
        } else if (action === 'UNBLOCK') {
            data = {'VEHICLE_ID': id, 'ACTION': 'UNBLOCK'};
        }
        if (data) {
            BX.ajax.runComponentAction(
                'wa:admin.transport.permission.list',
                'setUserVehicleBlockStatus',
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
}

function blockUserVehicle(vehicle_id) {
    if (vehicle_id) {
        $('.r-modal[data-name="modal-block-user-vehicle"]').addClass('active');
        $('#block-user-vehicle-confirm').click(function (e) {
            e.preventDefault();
            setUserVehicleBlockStatus(vehicle_id, 'BLOCK');
        });
    }

}

function unblockUserVehicle(vehicle_id) {
    if (vehicle_id) {
        $('.r-modal[data-name="modal-unblock-user-vehicle"]').addClass('active');
        $('#unblock-user-vehicle-confirm').click(function (e) {
            e.preventDefault();
            setUserVehicleBlockStatus(vehicle_id, 'UNBLOCK');
        });
    }
}