$(window).on('load', function () {
    let object_list_filter = $('#object-list-filter').find('.custom-select_title')[0];
    let object_list_filter_observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutationRecord) {
            let location_id = $(mutationRecord.target).attr('data-selected-id');
            if (location_id !== 'all') {
                let select = $(mutationRecord.target).closest('.custom-select');
                let select_body = select.find('.custom-select_body');
                if (location_id) {
                    initPreloader(false, '#object-list');
                    ajaxWrap('/ajax/objects/filter_objects_list.php', {location_id: location_id}).then(
                        function (data) {
                            $('#object-list').html(data);
                            $('#reset-btn').show();
                            destroyPreloader('.preloader', '#object-list');
                        }
                    );
                }
            } else {
                location.reload();
            }
        });
    });
    object_list_filter_observer.observe(object_list_filter, {attributes: true, attributeFilter: ['data-selected-id']});
});