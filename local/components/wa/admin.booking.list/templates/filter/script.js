$(document).ready(function () {
    let filter_form = $('form.orders-filter');
    let filter_form_btn = $('#set-filter');
    filter_form_btn.click(function (e) {
        e.preventDefault();
        let formData = new FormData(filter_form[0]);
        let sectionFilterSelect = $('#user-location-filter').find('.custom-select_title').attr('data-selected-id');
        formData.append('LOCATION_ID', sectionFilterSelect);
        let url = new URL(window.location.href);
        for (let [name, value] of formData) {
            url.searchParams.set(name, value);
        }
        window.location.href = url;
    });
    $('.c-input-date').each(function () {
        flatpickr.localize(flatpickr.l10ns.ru);
        flatpickr(this, {
            dateFormat: "d.m.Y",
            allowInput: "true",
            allowInvalidPreload: true,
            disableMobile: "true",
            minDate: "01-01-2020"
        });
    });
});