function unsetObjectBooking(objectId, hlbId, arrivalDate, departureDate, orderId) {
    if (objectId && hlbId && arrivalDate && departureDate) {
        $('.r-modal[data-name="modal-cancel-reservation"]').addClass('active');
        $('#object-order-cancel-btn').click(function (e) {
            e.preventDefault();
            BX.ajax.runComponentAction(
                'wa:admin.booking.list',
                'unsetObjectRent',
                {
                    mode: 'class',
                    dataType: 'json',
                    data: {
                        ID: objectId,
                        HLB_ORDER_ID: hlbId,
                        ARRIVAL_DATE: arrivalDate,
                        DEPARTURE_DATE: departureDate,
                        ORDER_ID: orderId
                    }
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
            $('.r-modal[data-name="modal-delete-visitor"]').removeClass('active');
        });
    }
}

function deleteOrder(orderId, hlbId, isRoute = false) {
    if (orderId && hlbId) {
        $('.r-modal[data-name="modal-delete-order"]').addClass('active');
        $('#modal-delete-order-confirm').click(function (e) {
            e.preventDefault();
            BX.ajax.runComponentAction(
                'wa:admin.booking.list',
                'deleteOrder',
                {
                    mode: 'class',
                    dataType: 'json',
                    data: {ORDER_ID: orderId, HLB_ORDER_ID: hlbId, IS_ROUTE: isRoute}
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
            $('.r-modal[data-name="modal-delete-order"]').removeClass('active');
        });
    }
}