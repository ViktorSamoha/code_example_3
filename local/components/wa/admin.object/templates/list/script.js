function deleteObjectAction(objectId){
    if(objectId){
        $('.r-modal[data-name="modal-delete-object"]').addClass('active');
        $('#modal-delete-object-confirm').click(function(e){
            e.preventDefault();
            BX.ajax.runComponentAction(
                'wa:admin.object',
                'deleteObject',
                {
                    mode: 'class',
                    dataType: 'json',
                    data: {OBJECT_ID: objectId}
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
            $('.r-modal[data-name="modal-delete-object"]').removeClass('active');
        });
    }
}