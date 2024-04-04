$(window).on('load', function () {
    initEditButtonsActions();
    initRemoveButtonsActions();
});

function initEditButtonsActions() {
    let ar_edit_buttons = $('.btn-edit');
    $(ar_edit_buttons).each(function () {
        $(this).click(function () {
            /*console.log($(this).data('orderId'));*/
            let order_id = $(this).data('orderId');
        });
    });
}

function initRemoveButtonsActions() {
    let ar_remove_buttons = $('.btn-remove');
    $(ar_remove_buttons).each(function () {
        $(this).click(function () {
            //console.log($(this).data('orderId'));
            let order_id = $(this).data('orderId');
            sendAjax("wa:admin", "deleteOrder", {order_id: order_id});
        });
    });
}

function sendAjax(componentName, actionName, data) {
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
                console.log(response.data.data);
            }
        }
    ).catch(
        function (response) {
            //popup.error(response.errors.pop().message);
        }
    )
};