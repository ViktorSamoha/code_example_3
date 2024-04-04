function yandexMap(data) {
    this.map_json = data;
    this.map_object = null;
    this.map_object_manager = null;
}

yandexMap.prototype.init = function () {
    initPreloader('green', '.content-item[data-name="map"]');
    let component = this;
    ymaps.ready(function () {
        component.map_object = new ymaps.Map('map', {
            center: [53.078924, 56.483197],
            zoom: 11
        }, {
            searchControlProvider: 'yandex#search'
        }),
            component.map_object_manager = new ymaps.ObjectManager(
                {
                    clusterize: true,
                    gridSize: 40,
                }
            );
        component.map_object_manager.clusters.options.set('preset', 'islands#greenClusterIcons');//иконка кластера
        component.map_object.geoObjects.add(component.map_object_manager);//добавляем объекты на крату
        component.map_object_manager.add(component.map_json);

        function onObjectEvent(e) {
            var objectId = e.get('objectId');
            console.log(objectId);
        }

        component.map_object_manager.objects.events.add(['click'], onObjectEvent);
        destroyPreloader('.preloader--green', '.content-item[data-name="map"]');
    });
}

yandexMap.prototype.destroy = function () {
    const self = this;
    ymaps.ready(function () {
        self.map_object.destroy();
    });
}

yandexMap.prototype.reinit = function (data) {
    let component = this;
    component.map_object_manager.removeAll();
    component.map_object_manager.add(data);
}
yandexMap.prototype.empty = function () {
    let component = this;
    component.map_object_manager.removeAll();
}