$(document).ready(function () {
    let filterForm = $('form[class="filter"]');
    let filterFormBtn = filterForm.find('button[name="apply-filter"]');
    filterFormBtn.click(function (e) {
        e.preventDefault();
        let locationId = filterForm.find('.custom-select_title').data('selectedId');
        let objectTypeId = filterForm.find('input[type="checkbox"]:checked').data('objectTypeId');
        let guestCount = filterForm.find('#guest-quantity').val();
        let arrivalDate = filterForm.find('#arrival-date').val();
        let departureDate = filterForm.find('#departure-date').val();
        BX.ajax.runComponentAction(
            'wa:filter',
            'setFilter',
            {
                mode: 'class',
                dataType: 'json',
                data: {
                    locationId: locationId,
                    objectTypeId: objectTypeId,
                    guestCount: guestCount,
                    arrivalDate: arrivalDate,
                    departureDate: departureDate
                }
            }
        ).then(
            function (response) {
                if (response) {
                    if (response.data.data) {
                        let parentSectionId = $('#parent-section-id').val();
                        let arElementsId = response.data.data.ELEMENTS_ID;
                        let arSectionsId = response.data.data.SECTIONS_ID;
                        ajaxWrap('/ajax/locations/setCatalogFilter.php',{
                            arElementsId:arElementsId,
                            arSectionsId:arSectionsId,
                            parentSectionId:parentSectionId,
                        }).then(function(html){
                            if(html){
                                let curPage = $('.page-w-aside_content');
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
