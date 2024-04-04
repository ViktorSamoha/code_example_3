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
            'wa:user.transport.permit',
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

    $('#get-permission').click(function (e) {
        e.preventDefault();
        let personal_data_confirm = $('#personal-data-confirm').is(':checked');
        let visiting_rules_confirm = $('#visiting-rules-confirm').is(':checked');
        let form_errors = $('#form-errors');
        let ar_errors = [];
        let data = {};
        if (personal_data_confirm) {
            form_errors.html('');
            if (visiting_rules_confirm) {
                form_errors.html('');
                let user_vehicle = $('#user-vehicle').data('selectedId');
                let route = $('#route').data('selectedId');
                let ar_date = $('input[name="ARRIVAL_DATE"]').val();
                let dep_date = $('input[name="DEPARTURE_DATE"]').val();
                let permission_code = $('input[name="PERMISSION_CODE"]').val();
                if (user_vehicle) {
                    data.USER_VEHICLE = user_vehicle;
                } else {
                    ar_errors.push("Необходимо выбрать транспортное средство!<br>");
                }
                if (route) {
                    data.ROUTE = route;
                } else {
                    ar_errors.push("Необходимо выбрать маршрут!<br>");
                }
                if (ar_date) {
                    data.ARRIVAL_DATE = ar_date;
                } else {
                    ar_errors.push("Необходимо выбрать дату заезда!<br>");
                }
                if (dep_date) {
                    data.DEPARTURE_DATE = dep_date;
                } else {
                    ar_errors.push("Необходимо выбрать дату выезда!<br>");
                }
                if (permission_code) {
                    data.PERMISSION_CODE = permission_code;
                } else {
                    ar_errors.push("Для оформления разрешения на транспортное средство, необходимо иметь разрешение на посещение!<br>");
                }

                //console.log(data);

            } else {
                ar_errors.push("Необходимо согласиться с правилами посещения парка!<br>");
            }
        } else {
            ar_errors.push("Необходимо дать согласие на обработку персональных данных!<br>");
        }
        if (ar_errors.length > 0) {
            $.each(ar_errors, function (index, val) {
                form_errors.append(`<span class="warn-message">${val}</span>`);
            });
            ar_errors.length = 0
        } else {
            if (!isEmpty(data)) {
                BX.ajax.runComponentAction(
                    'wa:user.transport.permit',
                    'setUserVehiclePermission',
                    {
                        mode: 'class',
                        dataType: 'json',
                        data: data
                    }
                ).then(
                    function (response) {
                        if (response) {
                            //console.log(response);
                            if (response.status === 'success') {
                                ajaxWrap(
                                    '/ajax/user/userTransportPermissionSuccess.php', {}
                                ).then(
                                    function (html) {
                                        if (html) {
                                            let formNode = $('.form-transport-permit');
                                            formNode.before(html);
                                            formNode.remove();
                                        }
                                    }
                                );
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
});