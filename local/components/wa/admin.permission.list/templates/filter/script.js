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
        window.location.href = url;
    });
});