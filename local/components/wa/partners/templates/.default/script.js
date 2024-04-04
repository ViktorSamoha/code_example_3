function reinitPartnersTable(data) {
    let partners_table = $('#partners-table');
    partners_table.html('');
    let html = '';
    if (data.length) {
        html = '<tr><th>Идентификатор</th><th>Наименование</th><th>E-mail</th><th>Телеграм API</th><th></th></tr>';
        for (const [key, value] of Object.entries(data)) {
            html += `<tr>
            <td>${value.ID}</td>
            <td>${value.UF_NAME}</td>
            <td>${value.UF_PARTNER_EMAIL}</td>
            <td>${value.UF_TELEGRAM_API}</td>
            <td>
                <button class="btn-remove js-open-r-modal" data-name="modal-delete-partner"
                        data-id="${value.ID}">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M5.79167 17.083C5.47222 17.083 5.19444 16.965 4.95833 16.7288C4.72222 16.4927 4.60417 16.208 4.60417 15.8747V4.54134H3.75V3.60384H7.3125V3.02051H12.6875V3.60384H16.25V4.54134H15.3958V15.8747C15.3958 16.208 15.2778 16.4927 15.0417 16.7288C14.8056 16.965 14.5278 17.083 14.2083 17.083H5.79167ZM7.9375 14.3747H8.89583V6.29134H7.9375V14.3747ZM11.1042 14.3747H12.0625V6.29134H11.1042V14.3747Z"
                            fill="#ED8C00"/>
                    </svg>
                </button>
            </td>
        </tr>`;
        }
    } else {
        html = '<p>Список пуст</p>';
    }
    partners_table.html(html);
}

function modalDeletePartner() {
    $('.r-modal[data-name="modal-delete-partner"]').find('#partner-delete-btn').click(function () {
        BX.ajax.runComponentAction(
            'wa:partners',
            'deletePartner',
            {
                mode: 'class',
                dataType: 'json',
                data: {id: $(this).data('partnerId')}
            }
        ).then(
            function (response) {
                if (response) {
                    if (response.status === 'success') {
                        $('.r-modal[data-name="modal-delete-partner"]').removeClass('active');
                        reinitPartnersTable(response.data.data);
                        init();
                        reopenModal();
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

function reopenModal() {
    $('.js-open-r-modal').click(function (e) {
        let modal_name = $(e.currentTarget).data('name');
        $('.r-modal[data-name="' + modal_name + '"]').addClass('active');
    });
}

function onBtnRomoveClick() {
    $('.btn-remove').click(function (e) {
        e.preventDefault();
        $('.r-modal[data-name="modal-delete-partner"]').find('#partner-delete-btn').attr('data-partner-id', $(e.currentTarget).data('id'));
    });
}

function init() {
    onBtnRomoveClick();
    modalDeletePartner();
}

$(window).on('load', function () {
    init();
});