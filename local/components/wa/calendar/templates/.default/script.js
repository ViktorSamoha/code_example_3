function getMonth(month, year, action) {
    if (month && year && action) {
        let id = $('#element-id').html();
        BX.ajax.runComponentAction(
            'wa:calendar',
            'getMonth',
            {
                mode: 'class',
                dataType: 'json',
                data: {'ID': id, 'MONTH': month, 'YEAR': year, 'ACTION': action}
            }
        ).then(
            function (response) {
                if (response) {
                    $('#ajax-calendar').html(response.data.html);
                }
            }
        ).catch(
            function (response) {
                //popup.error(response.errors.pop().message);
            }
        )
    }
}

function getNextMonth(month, year) {
    getMonth(month, year, 'NEXT');
}

function getPrevMonth(month, year) {
    getMonth(month, year, 'PREV');
}