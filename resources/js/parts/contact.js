(function ($) {
    "use strict";

    const mapContainer = $('#contactMap');

    if (mapContainer && mapContainer.length) {
        const mapOption = {
            dragging: false,
            zoomControl: false,
            scrollWheelZoom: false,
        };
        const lat = mapContainer.attr('data-latitude');
        const lng = mapContainer.attr('data-longitude');
        const zoom = mapContainer.attr('data-zoom');

        const contactMap = L.map('contactMap', mapOption).setView([lat, lng], zoom);

        L.tileLayer(leafletApiPath, {
            maxZoom: 18,
            tileSize: 512,
            zoomOffset: -1,
            attribution: '© <a target="_blank" rel="nofollow" href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(contactMap);

        var myIcon = L.icon({
            iconUrl: '/assets/default/img/location.png',
            iconAnchor: [lat - 14, lng + 10],
        });
        L.marker([lat, lng], {color: '#43d477', icon: myIcon}).addTo(contactMap);
    }

})(jQuery);
