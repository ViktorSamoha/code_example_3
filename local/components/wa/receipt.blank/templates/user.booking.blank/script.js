function PageMap(data) {
    this.map_point = data;
}

PageMap.prototype.init = function () {
    let data = JSON.parse(this.map_point);
    ymaps.ready(function () {
        var myMap = null;//карта
        var objManager = null;
        myMap = new ymaps.Map('map', {
            center: data.features[0].geometry.coordinates,
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
        myMap.geoObjects.add(objManager);
        objManager.add(data);
    });
}

$(window).on('load', function () {
    printBlank();
});
