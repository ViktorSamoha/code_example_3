var myMap = null;//карта

function showResultMsg(show, hide) {//показывает сообщение об успешном добавлении
    $(hide).addClass('hidden');
    $(show).removeClass('hidden');
}

function resetForm(form, msg_box, reset) {//скрывает сообщение об успешном добавлении и обновляет форму

    if (reset) {
        $(form).removeClass('hidden').trigger("reset");
    } else {
        $(form).removeClass('hidden');
    }

    let resultBlock = $(form).parent().find('.block-result');
    if (resultBlock.hasClass('hidden')) {
        resultBlock.removeClass('hidden');
    }

    let msg_container = $(msg_box);
    if (!msg_container.hasClass('hidden')) {
        msg_container.addClass('hidden');
    }
}

function pushErrorMsg(popupSelector, errorBlock, errorMsg) {
    let popup = $(popupSelector);
    let errBlock = popup.find(errorBlock);
    let errBlockBtn = errBlock.find('button');
    let form = popup.find('form');
    form.hide();
    errBlockParent = errBlock.parent();
    if (errBlockParent.hasClass('hidden')) {
        errBlockParent.removeClass('hidden');
    }
    errBlock.find('.block-success_text').html(`<p>${errorMsg}</p>`);
    errBlock.removeClass('hidden');
    errBlockBtn.click(function (e) {
        e.preventDefault();
        errBlock.addClass('hidden');
        form.show();
    });
}

/*
function addNewCategory(data) {//добавляет новую категорию
    let select_body = $('div[data-select-name="PROPERTY[IBLOCK_SECTION]"]');
    let html = '<div class="custom-select_item" data-id="' + data.ID + '">' + data.NAME + '' +
        '<button class="select-btn-delete" type="button" data-select-type="category" data-item-id="' + data.ID + '">' +
        '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
        '<path d="M0.351562 1.2627L10.5054 11.4165M10.6445 1.2627L0.49061 11.4165" stroke="#F71E1E"/></svg></button></div>';
    select_body.append(html);
    onDeleteLocation();
}

function addNewCharacteristic(data) {//добавляет новую характеристику
    let checkbox_list_body = $('.checkbox-list');
    let ar_checkbox = $(checkbox_list_body).find('input[type="checkbox"]');
    let last_element = ar_checkbox[ar_checkbox.length - 1];
    let last_element_number = $(last_element).data('number');
    let cb_number = parseInt(last_element_number) + 1;
    let cb_id = 'checkbox_' + cb_number;

    let html = '<div class="checkbox">' +
        '<input type="checkbox" id="' + cb_id + '" value="' + data.XML_ID + '" name="PROPERTY[8][' + cb_number + '][VALUE]" data-number="' + cb_number + '">' +
        '<label for="' + cb_id + '">' +
        '<div class="checkbox_text">' + data.NAME + '</div>' +
        '<button class="btn-delete" type="button" data-item-id="' + data.XML_ID + '">' +
        '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M0.351562 1.2627L10.5054 11.4165M10.6445 1.2627L0.49061 11.4165" stroke="#F71E1E"/>' +
        '</svg></button></label></div>';

    checkbox_list_body.append(html);
}

function addNewLocation(data) {
    let select_body = $('div[data-select-name="PROPERTY[1][0]"]');
    let html = '<div class="custom-select_item" data-id="' + data.ID + '">' + data.NAME + '' +
        '<button class="select-btn-delete" type="button" data-select-type="location" data-item-id="' + data.ID + '">' +
        '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
        '<path d="M0.351562 1.2627L10.5054 11.4165M10.6445 1.2627L0.49061 11.4165" stroke="#F71E1E"/></svg></button></div>';
    select_body.append(html);
    onDeleteLocation();
}

function addNewPartner(data) {
    let select_body = $('div[data-select-name="PROPERTY[70]"]');
    let html = '<div class="custom-select_item"><div class="checkbox checkbox-w-btn">' +
        '<input type="checkbox" id="partner_cb_' + data.ID + '" value="' + data.ID + '" name="PROPERTY[70][]" ><label for="partner_cb_' + data.ID + '">' +
        '<div class="checkbox_text">' + data.NAME + '</div></label></div></div>';
    select_body.append(html);
}*/

function sendAjax(componentName, actionName, data, actionThen) {//запрос на сервер
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
                switch (actionThen) {
                    case 'category':
                        if (response.status === 'success') {
                            //addNewCategory(response.data.data);
                            refreshCategorySelect();
                            showResultMsg('#category-done', '#add-category-form');
                        }
                        break;
                    case 'characteristic':
                        if (response.status === 'success') {
                            //addNewCharacteristic(response.data.data);
                            refreshCharacteristics();
                            showResultMsg('#characteristic-done', '#add-characteristic-form');
                        }
                        break;
                    case 'location':
                        if (response.status === 'success') {
                            refreshLocationSelect();
                            //addNewLocation(response.data.data);
                            showResultMsg('#location-done', '#add-location-form');
                        }
                        break;
                    case 'partner':
                        if (response.status === 'success') {
                            //addNewPartner(response.data.data);
                            refreshPartnerSelect();
                            showResultMsg('#partner-done', '#add-partner-form');
                        }
                        break;
                }
            }
        }
    ).catch(
        function (response) {
            //popup.error(response.errors.pop().message);
            //console.log(response);
            switch (actionThen) {
                case 'category':
                    if (response.data) {
                        pushErrorMsg('div[data-name="modal-add-category"]', '#category-error', response.data);
                    }
                    break;
                case 'characteristic':
                    if (response.data) {
                        pushErrorMsg('div[data-name="modal-add-characteristic"]', '#characteristic-error', response.data);
                    }
                    break;
                case 'location':
                    if (response.data) {
                        pushErrorMsg('div[data-name="modal-add-location"]', '#location-error', response.data);
                    }
                    break;
                case 'partner':
                    if (response.data) {
                        pushErrorMsg('div[data-name="modal-add-partner"]', '#partner-error', response.data);
                    }
                    break;
            }
        }
    )
};

function createCategoryFunction() {
    resetForm('#add-category-form', '#category-done', true);
    $('#create-category').click(function (e) {
        $(this).prop('disabled', true);
        setTimeout(function () {
            $(this).prop('disabled', false);
        }.bind(this), 1e3);
        e.preventDefault();
        let category_form = $('#add-category-form');//получаем форму
        let ajax_data = new FormData(category_form.get(0));//получаем данные формы
        let section_id = category_form.find('.custom-select_title').data('selectedId');
        if (section_id) {
            ajax_data.append('IBLOCK_SECTION_ID', section_id);
        }
        sendAjax("wa:admin", "createCategory", ajax_data, 'category');
    });
    $('.js-open-form').click(function () {
        let successBlock = $('#category-done');
        if (!successBlock.hasClass('hidden')) {
            successBlock.addClass('hidden');
        }
    });
}

function initYandexMap() {
    resetForm('#add-coords-form', '#coords-done', false);
    var myPlacemark;
    if (!myMap) {
        ymaps.ready(init);
    } else {
        if ($('#map').html().trim() == '') {
            ymaps.ready(init);
        }
    }
    $('#save-coords-btn').click(function (e) {
        $(this).prop('disabled', true);
        setTimeout(function () {
            $(this).prop('disabled', false);
        }.bind(this), 1e3);
        e.preventDefault();
        let _this = e.target;
        let object_edit_form = $('form[name="iblock_add"]');
        if ($(_this).data('nL') && $(_this).data('eL')) {
            object_edit_form.find('#object-n-l-coord').val($(_this).data('nL'));
            object_edit_form.find('#object-e-l-coord').val($(_this).data('eL'));
        } else {
            let [nL, eL] = $('#object-coords').val().split(', ');
            if (nL && eL) {
                object_edit_form.find('#object-n-l-coord').val(nL);
                object_edit_form.find('#object-e-l-coord').val(eL);
            }
        }
        showResultMsg('#coords-done', '#add-coords-form');
    });

    $('.js-open-form').click(function () {
        let successBlock = $('#coords-done');
        if (!successBlock.hasClass('hidden')) {
            successBlock.addClass('hidden');
        }
    });

    function returnCoords(coords) {
        let northern_latitude = coords[0];
        let eastern_longitude = coords[1];
        let save_btn = $('#save-coords-btn');
        $('#object-coords').val(northern_latitude + ', ' + eastern_longitude);
        save_btn.attr('data-n-l', northern_latitude);
        save_btn.attr('data-e-l', eastern_longitude);
    }

    function init() {
        let save_btn = $('#save-coords-btn');
        let nl = save_btn.data('nL');
        let el = save_btn.data('eL');
        if (!nl && !el) {
            myMap = new ymaps.Map('map', {
                center: [53.078924, 56.483197],
                zoom: 11
            }, {
                searchControlProvider: 'yandex#search'
            });
            let coord_nl = $('#object-n-l-coord').val();
            let coord_el = $('#object-e-l-coord').val();
            if (coord_nl && coord_el) {
                myPlacemark = createPlacemark([coord_nl, coord_el], $('input[name="PROPERTY[NAME][0]"]').val());
                $('#object-coords').val(`${coord_nl}, ${coord_el}`);
                myMap.geoObjects.add(myPlacemark);
                // Слушаем событие окончания перетаскивания на метке.
                myPlacemark.events.add('dragend', function () {
                    returnCoords(myPlacemark.geometry.getCoordinates());
                });
                // Слушаем клик на карте.
                mapEvent();
            } else {
                mapEvent();
            }
        }

        function mapEvent() {
            // Слушаем клик на карте.
            myMap.events.add('click', function (e) {
                var coords = e.get('coords');

                // Если метка уже создана – просто передвигаем ее.
                if (myPlacemark) {
                    myPlacemark.geometry.setCoordinates(coords);
                }
                // Если нет – создаем.
                else {
                    myPlacemark = createPlacemark(coords);
                    myMap.geoObjects.add(myPlacemark);
                    // Слушаем событие окончания перетаскивания на метке.
                    myPlacemark.events.add('dragend', function () {
                        returnCoords(myPlacemark.geometry.getCoordinates());
                    });
                }
                returnCoords(coords);
            });

        }

        // Создание метки.
        function createPlacemark(coords, object_name = 'Объект') {
            return new ymaps.Placemark(coords, {
                iconCaption: object_name
            }, {
                preset: 'islands#violetDotIconWithCaption',
                draggable: true
            });
        }

        function onSetObjectCoords() {
            $('#object-coords').on("keyup", function () {
                setTimeout(() => {
                    let coords = $('#object-coords').val().split(', ');
                    myPlacemark = createPlacemark(coords);
                    myMap.geoObjects.each(function (geoObject) {
                        myMap.geoObjects.remove(geoObject);
                    });
                    myPlacemark.events.add('dragend', function () {
                        returnCoords(myPlacemark.geometry.getCoordinates());
                    });
                    myMap.geoObjects.add(myPlacemark);
                    myMap.setCenter(myPlacemark.geometry.getCoordinates(), 11, {});
                }, 1000);
            });
        }

        onSetObjectCoords();
    }
}

function initYandexRouteMap() {
    resetForm('#add-route-form', '#route-done', false);
    if (!myMap) {
        ymaps.ready(init);
    } else {
        if ($('#route-map').html().trim() == '') {
            ymaps.ready(init);
        }
    }

    function init() {
        myMap = new ymaps.Map('route-map', {
            center: [53.078924, 56.483197],
            zoom: 11
        });
        let coordsValue = $('#route-coords').val();
        let routeCoords = [];
        if (coordsValue) {
            routeCoords = JSON.parse(coordsValue);
        }
        var myPolyline = new ymaps.Polyline(routeCoords, {
            balloonContent: "Маршрут"
        }, {
            balloonCloseButton: false,
            strokeColor: "#EBA311",
            strokeWidth: 4,
            strokeOpacity: 0.7,
            editorMenuManager: function (items) {
                items.push({
                    title: "Удалить линию",
                    onClick: function () {
                        myMap.geoObjects.remove(myPolyline);
                    }
                });
                return items;
            }
        });
        myMap.geoObjects.add(myPolyline);
        myPolyline.editor.startEditing();
        myPolyline.editor.startDrawing();
        $('#save-route-btn').click(function (e) {
            e.preventDefault();
            myPolyline.editor.stopEditing();
            let lineCoords = myPolyline.geometry.getCoordinates();
            let serializedCoords = JSON.stringify(lineCoords);
            $('form[name="iblock_add"]').find('input[name="ROUTE_COORDS"]').val(serializedCoords);
            showResultMsg('#route-done', '#add-route-form');
        });
    }
}

function createCharacteristicFunction() {
    resetForm('#add-characteristic-form', '#characteristic-done', true);
    $('#add-characteristic-btn').click(function (e) {
        $(this).prop('disabled', true);
        setTimeout(function () {
            $(this).prop('disabled', false);
        }.bind(this), 1e3);
        e.preventDefault();
        let category_form = $('#add-characteristic-form');//получаем форму
        let ajax_data = new FormData(category_form.get(0));//получаем данные формы
        sendAjax("wa:admin", "createCharacteristic", ajax_data, 'characteristic');
    });
    $('.js-open-form').click(function () {
        let successBlock = $('#characteristic-done');
        if (!successBlock.hasClass('hidden')) {
            successBlock.addClass('hidden');
        }
    });
}

function createLocationFunction() {
    resetForm('#add-location-form', '#location-done', true);
    $('#add-location-btn').click(function (e) {
        $(this).prop('disabled', true);
        setTimeout(function () {
            $(this).prop('disabled', false);
        }.bind(this), 1e3);
        e.preventDefault();
        let location_form = $('#add-location-form');//получаем форму
        let ajax_data = new FormData(location_form.get(0));//получаем данные формы
        let section_id = location_form.find('.custom-select_title').data('selectedId');
        if (section_id) {
            ajax_data.append('IBLOCK_SECTION_ID', section_id);
        }
        sendAjax("wa:admin", "createLocation", ajax_data, 'location');
    });
    $('.js-open-form').click(function () {
        let successBlock = $('#location-done');
        if (!successBlock.hasClass('hidden')) {
            successBlock.addClass('hidden');
        }
    });
}

function addPartnerModal() {
    resetForm('#add-partner-form', '#partner-done', true);
    $('#add-partner').click(function (e) {
        $(this).prop('disabled', true);
        setTimeout(function () {
            $(this).prop('disabled', false);
        }.bind(this), 1e3);
        e.preventDefault();
        let location_form = $('#add-partner-form');//получаем форму
        let ajax_data = new FormData(location_form.get(0));//получаем данные формы
        sendAjax("wa:admin", "addPartner", ajax_data, 'partner');
    });
    $('.js-open-form').click(function () {
        let successBlock = $('#partner-done');
        if (!successBlock.hasClass('hidden')) {
            successBlock.addClass('hidden');
        }
    });
}

$(window).on('load', function () {
    $('button[data-name="modal-add-category"]').click(function () {
        createCategoryFunction();
    });
    $('button[data-name="modal-put-on-map"]').click(function () {
        initYandexMap();
    });
    $('button[data-name="modal-route-on-map"]').click(function () {
        initYandexRouteMap();
    });
    $('button[data-name="modal-add-characteristic"]').click(function () {
        createCharacteristicFunction();
    });
    $('button[data-name="modal-add-location"]').click(function () {
        createLocationFunction();
    });
    $('button[data-name="modal-add-partner"]').click(function () {
        addPartnerModal();
    });
});