function modalDeleteUser() {
    $('.r-modal[data-name="modal-delete-user"]').find('#user-delete-btn').click(function () {
        BX.ajax.runComponentAction(
            'wa:user.list',
            'deleteUser',
            {
                mode: 'class',
                dataType: 'json',
                data: {user_id: $(this).data('userId')}
            }
        ).then(
            function (response) {
                if (response) {
                    if (response.status === 'success') {
                        $('.r-modal[data-name="modal-delete-user"]').removeClass('active');
                        window.location.reload();
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

function onUserDelete() {
    $('.btn-remove').click(function (e) {
        e.preventDefault();
        $('.r-modal[data-name="modal-delete-user"]').find('#user-delete-btn').attr('data-user-id', $(e.currentTarget).data('id'));
    });
}

$(window).on('load', function () {
    onUserDelete();
    modalDeleteUser();
});