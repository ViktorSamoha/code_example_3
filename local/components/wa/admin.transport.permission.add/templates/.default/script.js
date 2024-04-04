function userSelectChange(user_number = false) {
    let select = $('#ajax-select').find('.custom-select_title')[0];
    if (select) {
        let userSelectObserver = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutationRecord) {
                let user_record_id = $(mutationRecord.target).attr('data-selected-id');
                let url = new URL(window.location.href);
                if (user_number) {
                    url.searchParams.set('USER_NUMBER', user_number);
                }
                url.searchParams.set('USER_RECORD_ID', user_record_id);
                window.location.href = url;
            });
        });
        userSelectObserver.observe(select, {attributes: true, attributeFilter: ['data-selected-id']});
    }
}

function searchUser() {
    $('#search-user-action').click(function (e) {
        e.preventDefault();
        let user_number = $('#search-user-phone').val();
        if (user_number) {
            ajaxWrap(
                '/ajax/admin/transportPermissionSearchUser.php',
                {USER_NUMBER: user_number}
            ).then((response) => {
                if (response) {
                    $('#ajax-select-block').html(response);
                    reinitCustomSelect('#ajax-select');
                    userSelectChange(user_number);
                }
            });
        }
    });
}

function onFormSubmit() {
    $('#add-vehicle-permission').click(function (e) {
        e.preventDefault();
        let form_errors = $('#form-errors');
        let ar_errors = [];
        let form = $('.form-visiting-permit');
        let formData = new FormData(form.get(0));
        let user_vehicle_id = $('#user-vehicle').data('selectedId');
        let user_vehicle_type = $('#vehicle-type-select').data('selectedId');
        let route = $('#route').data('selectedId');
        if (user_vehicle_type) {
            formData.append('USER_VEHICLE_TYPE', user_vehicle_type);
        }
        if (user_vehicle_id) {
            formData.append('USER_VEHICLE_ID', user_vehicle_id);
        }
        if (route) {
            formData.append('ROUTE', route);
        } else {
            ar_errors.push("Необходимо выбрать маршрут!<br>");
        }

        /*for (let [name, value] of formData) {
            console.log(`${name} = ${value}`);
        }*/

        if (ar_errors.length > 0) {
            form_errors.html('');
            $.each(ar_errors, function (index, val) {
                form_errors.append(`<span class="warn-message">${val}</span>`);
            });
            ar_errors.length = 0
        } else {
            form_errors.html('');
            if (formData.entries().next().value.length > 0) {
                BX.ajax.runComponentAction(
                    'wa:admin.transport.permission.add',
                    'setUserVehiclePermission',
                    {
                        mode: 'class',
                        dataType: 'json',
                        data: formData
                    }
                ).then(
                    function (response) {
                        if (response) {
                            if (response.status === 'success') {
                                let success_modal = $('.modal[data-name="modal-success-transport-permission-notification"]');
                                let modal_blank_link = success_modal.find('#blank-link');
                                if (response.data.blank_link) {
                                    modal_blank_link.attr('href', response.data.blank_link);
                                }
                                success_modal.addClass('active');
                            }
                        }
                    }
                ).catch(
                    function (response) {
                        //popup.error(response.errors.pop().message);
                    }
                )
            }
        }
    });
}

var myMap = null;

function initYMap(coords, name = false) {
    if (coords) {
        let routeName = "Маршрут";
        if (name) {
            routeName = name;
        }
        if (!myMap) {
            ymaps.ready(init);
        } else {
            if ($('#route-map').html().trim() == '') {
                ymaps.ready(init);
            }else{
                reiniMap(coords, routeName);
            }
        }

        function init() {
            $('#route-map').css('height', '500px');
            myMap = new ymaps.Map('route-map', {
                center: [53.078924, 56.483197],
                zoom: 11
            });
            var myPolyline = new ymaps.Polyline(coords, {
                balloonContent: routeName
            }, {
                balloonCloseButton: true,
                strokeColor: "#EBA311",
                strokeWidth: 4,
                strokeOpacity: 0.7,
            });
            myMap.geoObjects.add(myPolyline);
        }

        function reiniMap(coords, name){
            if(coords){
                let routeName = "Маршрут";
                if (name) {
                    routeName = name;
                }
                var myPolyline = new ymaps.Polyline(coords, {
                    balloonContent: routeName
                }, {
                    balloonCloseButton: true,
                    strokeColor: "#EBA311",
                    strokeWidth: 4,
                    strokeOpacity: 0.7,
                });
                myMap.geoObjects.each(function (geoObject) {
                    myMap.geoObjects.remove(geoObject);
                });
                myMap.geoObjects.add(myPolyline);
            }
        }
    }
}

function getRouteMap(routeId) {
    if (routeId) {
        BX.ajax.runComponentAction(
            'wa:admin.transport.permission.add',
            'getRouteMap',
            {
                mode: 'class',
                dataType: 'json',
                data: {ID: routeId}
            }
        ).then(
            function (response) {
                if (response) {
                    if (response.status === 'success') {
                        if (response.data.coords) {
                            initYMap(JSON.parse(response.data.coords), response.data.name);
                        }else{
                            $('#route-map').html('').css('height', 'unset');
                        }
                    }
                }
            }
        ).catch(
            function (response) {
                //popup.error(response.errors.pop().message);
                $('#route-map').html('').css('height', 'unset');
            }
        )
    }
}

function onRouteSelect() {
    let routeSelect = $('#route')[0];
    let categoryObserver = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutationRecord) {
            let routeId = $(mutationRecord.target).attr('data-selected-id');
            if (routeId) {
                getRouteMap(routeId);
            }
        });
    });
    categoryObserver.observe(routeSelect, {attributes: true, attributeFilter: ['data-selected-id']});
}

$(document).ready(function () {
    searchUser();
    userSelectChange();
    onFormSubmit();
    onRouteSelect();
    $('input[name="visit-permission"]').each(function () {
        $(this).change(function () {
            if ($(this).is(':checked')) {
                if ($(this).val() === 'yes') {
                    $('#visit-permission-input').show();
                } else {
                    $('#visit-permission-input').hide();
                }
            }
        });
    });
});