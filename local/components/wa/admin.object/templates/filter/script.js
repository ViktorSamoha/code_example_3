function setObjectListFilter() {
    let filterValue = $('#object-list-filter').find('.custom-select_title').data('selectedId');
    let url = new URL(window.location.href);
    if (filterValue !== 'all') {
        url.searchParams.set('SECTION_ID', filterValue);
    }else{
        url.searchParams.delete("SECTION_ID");
    }
    window.location.href = url;
}