$(document).ready(function () {
    let filter_form = $('form.orders-filter');
    let filter_form_btn = $('#set-filter');
    filter_form_btn.click(function (e) {
        e.preventDefault();
        let formData = new FormData(filter_form[0]);
        let url = new URL(window.location.href);
        for (let [name, value] of formData) {
            url.searchParams.set(name, value);
        }
        let filter_status_select = $('#filter-status-select').data('selectedId');
        if (filter_status_select) {
            url.searchParams.set('STATUS', filter_status_select);
        }
        window.location.href = url;
    });
});