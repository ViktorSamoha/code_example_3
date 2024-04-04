function Bron(data) {
    this.data = data;

    //данная конструкция проходит по объекту с данными и чистит его от пустот
    for (section in this.data) {
        for (section_data in this.data[section]) {
            if (this.data[section][section_data] === ''
                || typeof this.data[section][section_data] == null
                || typeof this.data[section][section_data] == "undefined") {
                delete this.data[section][section_data];
            }
            if (section_data === 'ITEMS' && typeof this.data[section][section_data] === "object") {
                for (items in this.data[section][section_data]) {
                    for (item in this.data[section][section_data][items]) {
                        for (item_data in this.data[section][section_data][items][item]) {
                            if (this.data[section][section_data][items][item][item_data] === ''
                                || typeof this.data[section][section_data][items][item][item_data] == null
                                || typeof this.data[section][section_data][items][item][item_data] == "undefined") {
                                delete this.data[section][section_data][items][item][item_data];
                            }
                            if (typeof this.data[section][section_data][items][item][item_data] == "object") {
                                for (obj_data in this.data[section][section_data][items][item][item_data]) {
                                    if (this.data[section][section_data][items][item][item_data][obj_data] === ''
                                        || typeof this.data[section][section_data][items][item][item_data][obj_data] == null
                                        || typeof this.data[section][section_data][items][item][item_data][obj_data] == "undefined") {
                                        delete this.data[section][section_data][items][item][item_data][obj_data];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}

var myMap = null;//карта

var objManager = null;

function reinitCalendarSlider() {
    var sliderCalendar = new Swiper('.calendar', {
        slidesPerView: 1,
        spaceBetween: 10,
        navigation: {
            nextEl: '.calendar_next',
            prevEl: '.calendar_prev'
        },
        breakpoints: {
            320: {
                slidesPerView: 1,
                spaceBetween: 10
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 20
            }
        }
    });
}

function clearCalendar() {
    let calendar = $('.calendar-block');
    let months = calendar.find('.c-month');
    let days = months.find('span');
    for (let day of days) {
        if (day.classList.length > 0) {
            $(day).removeClass('second-half-day');
            $(day).removeClass('first-half-day');
            $(day).addClass('day-free');
        } else {
            let attr = $(day).attr('data-date');
            if (typeof attr !== 'undefined' && attr !== false) {
                $(day).addClass('day-free');
            }
        }
    }
}

function setBookedDates(arDates) {
    if (Object.entries(arDates).length > 0) {
        let calendar = $('.calendar-block');
        let months = calendar.find('.c-month');
        for (const [date, date_props] of Object.entries(arDates)) {
            let day = months.find('span[data-date="' + date + '"]');
            day.removeClass('day-free');
            if (date_props.status === "available") {
                day.addClass(date_props.class);
            }
        }
    } else {
        clearCalendar();
    }
}

function reinitPhotoSlider() {
    var sliderPhoto = new Swiper('.slider-photo', {
        slidesPerView: 1,
        spaceBetween: 10,
        speed: 500,
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        navigation: {
            nextEl: '.slider-photo_next',
            prevEl: '.slider-photo_prev'
        }
    });
}

function reinitTextWrap() {
    const textBlock = document.querySelector('.d-text');
    if (textBlock) {
        const textBlockWrap = textBlock.querySelector('.d-text_wrap');
        if (textBlockWrap.offsetHeight > 120) {
            textBlock.classList.add('hidden-text');
        } else {
            textBlock.classList.remove('hidden-text');
        }
    }
}

function setBookingFormData(data, service_cost, object_cost) {
    let modal = $('section[data-name="modal-booking"]');
    let radio_group = modal.find('#service-cost');
    let html = null;
    if (data.SERVICE_COST.length === 2) {
        html = '<div class="radio">' +
            '<input type="radio" id="radio_07" data-period="couple" name="radio" checked>' +
            '<label for="radio_07"><div class="radio_text">На несколько суток</div>' +
            '</label></div><div class="radio">' +
            '<input type="radio" id="radio_08" data-period="day" name="radio">' +
            '<label for="radio_08"><div class="radio_text">Дневное пребывание</div></label></div>';
    } else {
        if (data.SERVICE_COST[0] === "Дневное пребывание до определенного времени") {
            html = '<input type="hidden" id="radio_08" data-period="day" name="radio">';
        } else if (data.SERVICE_COST[0] === "Сутки") {
            html = '<input type="hidden" id="radio_07" data-period="couple" name="radio">';
        }
    }
    if (html) {
        $(radio_group).html(html);
        onInputchange();
    }

    if (modal.find('form[name="iblock_add"]').find('input[name="OBJECT_COST"]').length > 0) {
        modal.find('form[name="iblock_add"]').find('input[name="OBJECT_COST"]').attr('value', data.OBJECT_COST);
    } else {
        modal.find('form[name="iblock_add"]').append('<input type="hidden" name="OBJECT_COST" value="' + data.OBJECT_COST + '">');
    }

    if (modal.find('form[name="iblock_add"]').find('input[name="OBJECT_DAILY_COST"]').length > 0) {
        modal.find('form[name="iblock_add"]').find('input[name="OBJECT_DAILY_COST"]').attr('value', data.OBJECT_DAILY_COST);
    } else {
        modal.find('form[name="iblock_add"]').append('<input type="hidden" name="OBJECT_DAILY_COST" value="' + data.OBJECT_DAILY_COST + '">');
    }

    if (modal.find('form[name="iblock_add"]').find('input[name="COST_PER_PERSON"]').length > 0) {
        modal.find('form[name="iblock_add"]').find('input[name="COST_PER_PERSON"]').attr('value', data.COST_PER_PERSON);
    } else {
        modal.find('form[name="iblock_add"]').append('<input type="hidden" name="COST_PER_PERSON" value="' + data.COST_PER_PERSON + '">');
    }

    if (modal.find('form[name="iblock_add"]').find('input[name="COST_PER_PERSON_ONE_DAY"]').length > 0) {
        modal.find('form[name="iblock_add"]').find('input[name="COST_PER_PERSON_ONE_DAY"]').attr('value', data.COST_PER_PERSON_ONE_DAY);
    } else {
        modal.find('form[name="iblock_add"]').append('<input type="hidden" name="COST_PER_PERSON_ONE_DAY" value="' + data.COST_PER_PERSON_ONE_DAY + '">');
    }

    if (modal.find('form[name="iblock_add"]').find('input[name="CAPACITY_ESTIMATED"]').length > 0) {
        modal.find('form[name="iblock_add"]').find('input[name="CAPACITY_ESTIMATED"]').attr('value', data.CAPACITY_ESTIMATED);
    } else {
        modal.find('form[name="iblock_add"]').append('<input type="hidden" name="CAPACITY_ESTIMATED" value="' + data.CAPACITY_ESTIMATED + '">');
    }

    if (modal.find('form[name="iblock_add"]').find('input[name="CAPACITY_MAXIMUM"]').length > 0) {
        modal.find('form[name="iblock_add"]').find('input[name="CAPACITY_MAXIMUM"]').attr('value', data.CAPACITY_MAXIMUM);
    } else {
        modal.find('form[name="iblock_add"]').append('<input type="hidden" name="CAPACITY_MAXIMUM" value="' + data.CAPACITY_MAXIMUM + '">');
    }

    if (data.OBJECT_COST) {
        modal.find('form[name="iblock_add"]').find('#booking-sum-value').html(data.OBJECT_COST);
    }

    /*    if (data.ID) {
            modal.find('form[name="iblock_add"]').find('#input-object-id').val(data.ID);
        }

        if (data.NAME) {
            modal.find('.modal_subtitle').html(data.NAME);
        }*/
}

function getCalendarPeriod(id) {
    let period = 'couple';
    let radio_inputs = $('#service-cost').find('input[name="radio"]');
    radio_inputs.each(function () {
        if ($(this).attr('type') === 'hidden') {
            period = $(this).data('period');
        } else {
            if ($(this).attr('checked') === 'checked') {
                period = $(this).data('period');
            }
        }
    });
    reinitBookingFormCalendar('input[name="PROPERTY[11][0][VALUE]"]', id, '.second-range-input', period, true);//убираем из календарика на форме бронирования занятые даты
}

function setDetailData(data) {
    let ar_data = data;
    let card_body = $('.detail-card');//находим детальную карточку
    let card_title = card_body.find('.detail-card_title');//название
    let card_gps = card_body.find('.detail-card_subtitle');//координаты
    let card_gallery = card_body.find('.slider-photo_wrap');//галерея
    let card_features = card_body.find('.description .icons-group');//особенности
    let card_description = card_body.find('.description .d-text_wrap');//описание
    let booking_button = card_body.find('#booking-button');

    $(booking_button).attr("data-object-id", ar_data.ID);
    $(booking_button).attr("data-object-name", ar_data.NAME);
    if (ar_data.TIME_LIMIT) {
        $(booking_button).attr("data-time-limit-value", ar_data.TIME_LIMIT);
        if (ar_data.TIME_LIMIT === 'Y') {
            $(booking_button).attr("data-fixed-price-value", ar_data.FIXED_COST);
        } else {
            $(booking_button).removeAttr("data-fixed-price-value");
        }
    }
    if (ar_data.CAR_POSSIBILITY) {
        $(booking_button).attr("data-car-possibility", ar_data.CAR_POSSIBILITY);
    } else {
        $(booking_button).removeAttr("data-car-possibility");
    }
    if (ar_data.CAR_CAPACITY) {
        $(booking_button).attr("data-car-capacity", ar_data.CAR_CAPACITY);
    } else {
        $(booking_button).removeAttr("data-car-capacity");
    }
    $(card_title).html(ar_data.NAME);//вставляем название из данных которые получили
    $(card_gps).html('GPS координаты: ' + ar_data.GPS_N_L + ' ' + ar_data.GPS_E_L);//вставляем координаты
    $(card_gallery).html(function () {
        let slides = '';
        if (ar_data.PICTURES) {
            $(ar_data.PICTURES).each(function (i, src) {
                if (typeof src == 'string') {
                    let slide_body =
                        '<div class="slider-photo_item swiper-slide">' +
                        '<a href="' + src + '" class="photo" data-fancybox>' +
                        '<img src="' + src + '" alt=""></a></div>';
                    slides += slide_body;
                }
            });
        }
        if (ar_data.VIDEOS) {
            $(ar_data.VIDEOS).each(function (i, obj) {
                if (typeof obj.PREVIEW == 'string' && typeof obj.VIDEO == 'string') {
                    let slide_body =
                        '<div class="slider-photo_item swiper-slide">' +
                        '<a href="' + obj.VIDEO + '" class="photo" data-fancybox>' +
                        '<img src="' + obj.PREVIEW + '" alt="">' +
                        '<svg class="play-icon" width="69" height="69" viewBox="0 0 69 69" ' +
                        'fill="none" xmlns="http://www.w3.org/2000/svg">' +
                        '<path d="M26.9733 47.7693L47.7693 34.5L26.9733 21.2307V47.7693ZM34.5128 69C29.7568 69 25.2912 ' +
                        '68.0947 21.1159 66.2842C16.9406 64.4736 13.2836 62.0004 10.1448 58.8646C7.00615 55.7288 4.53068 ' +
                        '52.0752 2.71841 47.9038C0.906136 43.7325 0 39.2688 0 34.5128C0 29.7421 0.905274 25.2567 2.71582 ' +
                        '21.0569C4.52643 16.857 6.99963 13.2037 10.1354 10.0969C13.2712 6.99018 16.9248 4.53068 21.0962 ' +
                        '2.71841C25.2675 0.906139 29.7312 0 34.4872 0C39.2579 0 43.7433 0.905273 47.9431 2.71582C52.143 ' +
                        '4.52643 55.7963 6.98366 58.9031 10.0875C62.0098 13.1914 64.4693 16.8412 66.2816 21.0371C68.0939 ' +
                        '25.2331 69 29.7164 69 34.4872C69 39.2432 68.0947 43.7088 66.2842 47.8841C64.4736 52.0594 62.0163 ' +
                        '55.7164 58.9125 58.8552C55.8086 61.9938 52.1588 64.4693 47.9629 66.2816C43.7669 68.0939 39.2836 ' +
                        '69 34.5128 69Z" fill="#F0B32F"/></svg></a></div>';
                    slides += slide_body;
                }
            });
        }
        return slides;
    });//формируем слайды и добавляем их в слайдер
    reinitPhotoSlider();//реинитим слайдер с картинками на детальной карточке
    $(card_features).html(function () {
        let features = '';
        $(ar_data.FEATURES).each(function (i, value) {
            let feature = Object.keys(value).map((i) => value[i]);
            let feature_body =
                '<div class="icon-item">' +
                '<div class="icon-item_img">' +
                '<img src="' + feature[0] + '" alt=""/>' +
                '</div><span class="icon-item_title">' + feature[1] + '</span></div>'
            features += feature_body;
        });
        return features;
    });//фирмируем особенности и добавляем их
    $(card_description).html(ar_data.DETAIL_TEXT);//вставляем описание
    reinitTextWrap();
    if (typeof ar_data.CAN_BOOK != 'undefined' && ar_data.CAN_BOOK != null) {
        if (ar_data.CAN_BOOK === 'Нет') {
            banBookAction(ar_data.ID);
        } else {
            _defaultAction();
        }
    } else {
        _defaultAction();
    }
    destroyPreloader('.preloader', '.detail-card');

    function _defaultAction() {
        clearCalendar();
        setBookedDates(ar_data.BOOKED_DATES);
        reinitCalendarSlider();
        setBookingFormData(ar_data);
        getCalendarPeriod(ar_data.ID);
        //onCalendarDateClick();
    }

    function banBookAction(object_id) {
        card_body.find('.calendar-block').hide();
        let modal_window = $('section[data-name="modal-booking"]').find('.modal_block');
        let modal_msg_box = modal_window.find('#ban-booking-msg');
        modal_window.find('form[name="iblock_add"]').hide();
        $.ajax({
            url: '/ajax/objects/get_object_alert_msg.php',
            method: 'post',
            data: {object_id: object_id},
            success: function (data) {
                //console.log(data);
                modal_msg_box.html(data);
                modal_msg_box.show();
            }
        });

    }
}

/*function reinitDetailCard() {
    let modal_window = $('section[data-name="modal-booking"]').find('.modal_block');
    modal_window.find('form[name="iblock_add"]').show();
    modal_window.find('#ban-booking-msg').hide();
    $('.detail-card').find('.calendar-block').show();
}*/

/*function getDetailData(btn, id) {

    let item_id = '';

    if (btn === null) {
        item_id = id;
    } else {
        item_id = $(btn).data('id');
    }

    BX.ajax.runComponentAction(
        'wa:bron',
        'getDetailCardData',
        {
            mode: 'class',
            dataType: 'json',
            data: {id: item_id}
        }
    ).then(
        function (response) {
            if (response) {
                //console.log(response.data.data);
                setDetailData(response.data.data);
            }
        }
    ).catch(
        function (response) {
            //popup.error(response.errors.pop().message);
        }
    )
}*/

/*function openDetailCard(_this, point_id) {
    $('.detail-card').addClass('active');
    reinitDetailCard();
    initPreloader(false, '.detail-card');
    if (_this === null) {
        getDetailData(null, point_id);
    } else {
        getDetailData(_this);
    }
}*/

//функция перезапускает переключение табов
/*function tabSwitcher() {
    let arTabs = $('.tabs').find('.tab');
    let arTabsContent = $('.tabs-content').find('.tabs-content_item');
    arTabs.each(function (i) {
        $(this).click(function () {
            arTabs.each(function (j) {
                if (j !== i) {
                    if (this.classList.contains('active')) {
                        $(arTabs[i]).addClass('active');
                        $(this).removeClass('active');
                        $(arTabsContent[i]).addClass('active');
                        $(arTabsContent[j]).removeClass('active');
                    }
                }
            });
        });
    });
}*/

//функция обращается к ajax файлу, который возвращает html страницы
function getSectionElements(id) {
    if (id) {
        initPreloader('green', '.content-item[data-name="list"]');
        ajaxWrap(
            '/ajax/locations/getLocationList.php',
            {id: id}
        ).then((response) => {
            if (response) {
                $('#main-page-step-1').hide();
                $('#main-page-step-2').show();
                $('#ajax-data').html(response);
                destroyPreloader('.preloader--green', '.content-item[data-name="list"]');
                //tabSwitcher();
                custom_openModal();
            }
        });
    }
}



/*function createHTML(sections) {

    if (typeof sections !== 'Object') {
        sections = Object.values(sections);
    }

    let page_content = document.querySelector('.catalog-list');

    let html = '';

    for (let i = 0; i < Object.keys(sections).length; i++) {

        if (sections[i] != null) {
            let items;

            if (sections[i].hasOwnProperty('ITEMS')) {

                if (typeof sections[i].ITEMS !== 'undefined' || typeof sections[i].ITEMS !== 'null') {
                    items = Object.values(sections[i].ITEMS);
                }

                if (items.length > 0) {
                    html += '<div class="catalog-block">';
                    html += '<h2 class="title">' + sections[i].NAME + '</h2>';
                    html += '<div class="catalog">';
                    if (typeof items !== 'undefined') {
                        if (items.length > 0) {
                            items.forEach((item) => {
                                html += '<div class="card">';
                                if (item.FIELDS.PREVIEW_PICTURE.SRC) {
                                    html += '<img src="' + item.FIELDS.PREVIEW_PICTURE.SRC + '" alt="' + item.FIELDS.NAME + '" class="card_img">';
                                } else {
                                    html += '<img src="/assets/development/images/card_01.jpeg" alt="" class="card_img">';
                                }
                                html += '<div class="card_text">\n' +
                                    '<h3 class="card_title">' + item.FIELDS.NAME + '</h3>\n' +
                                    '<a class="secondary-btn js-open-detail-card" data-id="' + item.FIELDS.ID + '" onclick="openDetailCard(this)">Подробнее</a>' +
                                    '</div>\n' +
                                    '</div>';
                            });
                        }
                    }
                    html += '</div></div>';
                }
            }

        }

    }

    //html += '</div>';

    page_content.innerHTML = html;

    destroyPreloader('.preloader--green', '.content-item[data-name="list"]');

}

function updateYandexMap(data) {

    if (!myMap) initYandexMap(data);

    objManager.removeAll();//чистим объекты на карте

    objManager.add(data);

    destroyPreloader('.preloader--green', '.content-item[data-name="map"]');
}

function initYandexMap(data) {

    myMap = new ymaps.Map('map', {
        center: [53.078924, 56.483197],
        zoom: 11
    }, {
        searchControlProvider: 'yandex#search'
    }),
        objManager = new ymaps.ObjectManager(
            {
                clusterize: true,
                gridSize: 40,
            }
        );

    objManager.clusters.options.set('preset', 'islands#greenClusterIcons');//иконка кластера

    myMap.geoObjects.add(objManager);//добавляем объекты на крату

    objManager.add(data);

    function onObjectEvent(e) {
        var objectId = e.get('objectId');

        //console.log(objectId);

        openDetailCard(null, objectId);

    }

    objManager.objects.events.add(['click'], onObjectEvent);

    destroyPreloader('.preloader--green', '.content-item[data-name="map"]');
}*/

Bron.prototype.componentAjaxAction = function (componentName, actionName, data, then) {
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

                switch (then) {
                    case 'createHTML':
                        createHTML(response.data.data.LOCATIONS);
                        updateYandexMap(response.data.data.MAP_JSON);
                        break;

                    case 'setDetailCard':

                        break;
                    /*case 'initMap':
                        initYandexMap(response.data.data.MAP_JSON);
                        break;*/

                }

                //console.log(response.data.data);
            }
        }
    ).catch(
        function (response) {
            //popup.error(response.errors.pop().message);
        }
    )
};

Bron.prototype.resetFilter = function (action) {
    let select = document.querySelector('.custom-select');
    select.querySelector('.custom-select_title').setAttribute('data-selected-id', 'all');
    select.querySelector('.custom-select_title').innerHTML = 'Все локации';
    let CheckBoxContainer = document.querySelector('.checkbox-block');
    let arrCheckBox = CheckBoxContainer.querySelectorAll('input');
    arrCheckBox.forEach((checkbox) => {
        if (checkbox.checked) {
            $(checkbox).removeAttr('checked');
        }
    });
    document.querySelector('#guest-quantity').setAttribute('value', '1');
    let dateBlock = document.querySelector('.input-date-block');
    dateBlock.querySelector('#arrival-date').value = '';
    dateBlock.querySelector('#departure-date').value = '';
    this.componentAjaxAction('wa:bron', 'sortContent', {}, action);
}

Bron.prototype.getData = function (action) {

    let select = document.querySelector('.custom-select');
    let location_id = select.querySelector('.custom-select_title').dataset.selectedId;
    let CheckBoxContainer = document.querySelector('.checkbox-block');
    let arrCheckBox = CheckBoxContainer.querySelectorAll('input');
    let arrCheckedObjectTypes = [];
    let guestQuantity = document.querySelector('#guest-quantity').value;
    let dateBlock = document.querySelector('.input-date-block');
    let arrivalDate = dateBlock.querySelector('#arrival-date').value;
    let departureDate = dateBlock.querySelector('#departure-date').value;

    arrCheckBox.forEach((checkbox) => {
        if (checkbox.checked) {
            let sectionId = checkbox.dataset.sectionId;
            arrCheckedObjectTypes.push(sectionId);
        }
    });

    //ajax на класс POST
    this.componentAjaxAction('wa:bron', 'sortContent', {
        location_id: location_id,
        section_id: arrCheckedObjectTypes,
        guest_quantity: guestQuantity,
        arrival_date: arrivalDate,
        departure_date: departureDate,
    }, action);

}

Bron.prototype.init = function () {
    let filter_form_btn = document.getElementsByName("apply-filter")[0];
    let map_btn = document.getElementById("vue-map");
    let reset_btn = document.getElementById("reset-filter-button");

    filter_form_btn.onclick = (e) => {
        e.preventDefault();
        initPreloader('green', '.content-item[data-name="list"]');
        this.getData('createHTML');
        if (isMobile()) {
            var filter = document.querySelector('.filter');
            filter.classList.remove('active');
        }
    };

    map_btn.onclick = (e) => {
        window.location.href = "/map/";
        /*initPreloader('green', '.content-item[data-name="map"]');
        this.getData('createHTML');*/
    };

    reset_btn.onclick = (e) => {
        this.resetFilter('createHTML');
    };
};
