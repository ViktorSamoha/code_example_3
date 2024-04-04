$(document).ready(function () {
    let filterForm = $('form[class="booking-form"]');
    let filterFormBtn = filterForm.find('#apply-map-filter');
    filterFormBtn.click(function (e) {
        e.preventDefault();
        let locationId = filterForm.find('.custom-select_title').attr('data-selected-id')
        let guestCount = filterForm.find('#guest-quantity').val();
        let arrivalDate = filterForm.find('#map-filter-arrival-date').val();
        let departureDate = filterForm.find('#map-filter-departure-date').val();
        BX.ajax.runComponentAction(
            'wa:filter',
            'setFilter',
            {
                mode: 'class',
                dataType: 'json',
                data: {
                    locationId: locationId,
                    guestCount: guestCount,
                    arrivalDate: arrivalDate,
                    departureDate: departureDate
                }
            }
        ).then(
            function (response) {
                if (response) {
                    if (response.data.data) {
                        let arElementsId = response.data.data.ELEMENTS_ID;
                        let arSectionsId = response.data.data.SECTIONS_ID;
                        ajaxWrap('/ajax/locations/setMapFilter.php', {
                            arElementsId: arElementsId,
                            arSectionsId: arSectionsId,
                        }).then(function (html) {
                            if (html) {
                                let curPage = $('.location-group');
                                curPage.before(html);
                                curPage.remove();
                            }
                        });
                    }
                }
            }
        ).catch(
            function (response) {
                //popup.error(response.errors.pop().message);
            }
        )

    });
});
