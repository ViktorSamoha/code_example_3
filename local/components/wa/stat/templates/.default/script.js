//функция собирает даты и возвращает объект с ними
function getDates() {
    let a_result = [];
    let a_date = $('#arrival-date').val();
    let d_date = $('#departure-date').val();
    if (a_date || d_date) {
        if (a_date && d_date) {
            a_result.arrival_date = a_date;
            a_result.departure_date = d_date;
            return a_result;
            //return {arrival_date: a_date, departure_date: d_date};
        } else if (a_date) {
            a_result.arrival_date = a_date;
            return a_result;
            //return {arrival_date: a_date};
        } else if (d_date) {
            a_result.departure_date = d_date;
            return a_result;
            //return {departure_date: d_date};
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function getLocations() {
    let ar_loc = [];
    $('#location-select').find('input[name="LOCATIONS[]"]').each(function () {
        if ($(this).is(':checked')) {
            ar_loc.push($(this).val());
        } else {
            let index = ar_loc.indexOf($(this).val());
            if (index !== -1) {
                ar_loc.splice(index, 1);
            }
        }
    });
    if (ar_loc.length) {
        return ar_loc;
    } else {
        return false;
    }
}

function getObjects() {
    let ar_obj = [];
    $('#object-select').find('input[name="OBJECTS[]"]').each(function () {
        if ($(this).is(':checked')) {
            ar_obj.push($(this).val());
        } else {
            let index = ar_obj.indexOf($(this).val());
            if (index !== -1) {
                ar_obj.splice(index, 1);
            }
        }
    });
    if (ar_obj.length) {
        return ar_obj;
    } else {
        return false;
    }
}

function getUsers() {
    let ar_usr = [];
    $('#user-select').find('input[name="USERS[]"]').each(function () {
        if ($(this).is(':checked')) {
            ar_usr.push($(this).val());
        } else {
            let index = ar_usr.indexOf($(this).val());
            if (index !== -1) {
                ar_usr.splice(index, 1);
            }
        }
    });
    if (ar_usr.length) {
        return ar_usr;
    } else {
        return false;
    }
}

function getSettings() {
    let ar_settings = [];
    $('#report-settings').find('input[name="SETTINGS[]"]').each(function () {
        if ($(this).is(':checked')) {
            ar_settings.push($(this).val());
        } else {
            let index = ar_settings.indexOf($(this).val());
            if (index !== -1) {
                ar_settings.splice(index, 1);
            }
        }
    });
    if (ar_settings.length) {
        return ar_settings;
    } else {
        return false;
    }
}

function collectData() {
    let dates = getDates();
    let locations = getLocations();
    let objects = getObjects();
    let users = getUsers();
    let settings = getSettings();
    let data = {};
    if (dates) {
        data.dates = dates;
    }
    if (locations) {
        data.locations = locations;
    }
    if (objects) {
        data.objects = objects;
    }
    if (users) {
        data.users = users;
    }
    if (settings) {
        data.settings = settings;
    }
    if (Object.keys(data).length) {
        return data;
    } else {
        return false;
    }
}

function getTable(data) {
    BX.ajax.runComponentAction(
        'wa:stat',
        'getTable',
        {
            mode: 'class',
            dataType: 'json',
            data: data
        }
    ).then(
        function (response) {
            destroyPreloader('.preloader', '.lk_content');
            if (response) {
                $('#report-config-form').hide();
                $('#ajax').show();
                $('#ajax-content').html(response.data.data);
            }
        },
        function(response) {
            destroyPreloader('.preloader', '.lk_content');
            $('form').find('.form-warn-message').html('Нет данных за выбранный период');
            setTimeout(function(){
                $('form').find('.form-warn-message').html('');
            }, 5000);
        }
    );
}

function getStatistics() {
    let form_data = collectData();
    if (Object.keys(form_data).length) {
        initPreloader(false, '.lk_content');
        getTable(form_data);
    }
}

function getExcel() {
    let data = [];
    $('#ajax-content').find('table').each(function () {
        let ar_table = [];
        let fb = $(this).closest('.form-block');
        if (fb.length) {
            let title = fb.find('h3').html();
            //эта темка нужна чтоб заголовок в таблице выводился ))))
            let title_html = `<tbody><tr><td colspan="4"></td></tr></tbody><tbody><tr><th colspan="4"><b>${title}</b></th></tr></tbody>`;
            let tableHtml = this.innerHTML;
            let injectPosition = tableHtml.indexOf("<tbody>");
            let html = [tableHtml.slice(0, injectPosition), title_html, tableHtml.slice(injectPosition)].join('');
            //конец костылика
            ar_table.push(html);
        } else {
            ar_table.push(this.innerHTML);
        }
        data.push(ar_table);
    });
    BX.ajax.runComponentAction(
        'wa:stat',
        'getExcel',
        {
            mode: 'class',
            dataType: 'json',
            data: data
        }
    ).then(
        function (response) {
            if (response) {
                window.location.href = response.data.data;
            }
        }
    );
}

//вызываем плагин календарика на нужные инпуты
function reinitFlatpickr() {
    $('.c-input').each(function () {
        flatpickr.localize(flatpickr.l10ns.ru);
        flatpickr(this, {
            dateFormat: "d.m.Y",
            allowInput: "true",
            allowInvalidPreload: true,
            disableMobile: "true",
            minDate: "2020-01"
        });
    });
}

$(window).on('load', function () {
    $('#generate-report').click(function (e) {
        e.preventDefault();
        getStatistics();
    });
    $('#get-excel').click(function (e) {
        e.preventDefault();
        getExcel();
    });
    reinitFlatpickr();
});