function getInnerSectionElements(parentSectionCode, id, filterElements = false) {
    if (id && parentSectionCode) {
        let data = {};
        if (filterElements) {
            data = {id: id, parentSectionCode: parentSectionCode, filterElementsId: filterElements};
        } else {
            data = {id: id, parentSectionCode: parentSectionCode};
        }
        ajaxWrap(
            '/ajax/locations/getLocationElementsList.php',
            data
        ).then((response) => {
            if (response) {
                let modal = $('.r-modal[data-name="modal-location"]');
                let modalBody = modal.find('div[data-name="modal-ajax-body"]');
                modalBody.html(response);
                modal.addClass('active');
                modal.find('.r-modal-close-btn').click(function () {
                    modal.removeClass('active');
                });
                custom_openModal();
            }
        });
    }
}