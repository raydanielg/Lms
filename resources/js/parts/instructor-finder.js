(function ($) {
    var instructorFinderMap;

    function handleMap() {
        const mapContainer = $('#instructorFinderMap');
        const mapOption = {
            dragging: true,
            zoomControl: true,
            scrollWheelZoom: false,
        };
        const lat = mapContainer.attr('data-latitude');
        const lng = mapContainer.attr('data-longitude');
        const zoom = mapContainer.attr('data-zoom');

        instructorFinderMap = L.map('instructorFinderMap', mapOption).setView([lat, lng], zoom);

        L.tileLayer(leafletApiPath, {
            maxZoom: 18,
            minZoom: 3,
            tileSize: 512,
            zoomOffset: -1,
            attribution: '© <a target="_blank" rel="nofollow" href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(instructorFinderMap);

        let countryMarkers = [];
        let provinceMarkers = [];
        let cityMarkers = [];


        if (mapUsers && Array.isArray(mapUsers)) {
            var myMarkersGroup = L.markerClusterGroup({
                showCoverageOnHover: false,
            });

            for (const mapUser of mapUsers) {
                const marker = makeUserMarker(mapUser);

                myMarkersGroup.addLayer(marker);
            }

            instructorFinderMap.addLayer(myMarkersGroup);
        }

        feather.replace();
    }


    handleMap();

    function makeUserMarker(user) {
        const userMarker = L.divIcon({
            html: "<div class='marker-pin rounded-circle'><img src='" + user.avatar + "' class='img-cover rounded-circle' alt='" + user.full_name + "'/></div>",
            iconAnchor: [25, 50],
            iconSize: [50, 50],
            className: 'rounded-circle bg-white border-0'
        });

        const marker = L.marker([user.location[0], user.location[1]], {icon: userMarker});

        marker.bindPopup(handleUserMapCardHtml(user), {
            className: 'map-instructor-card-popup'
        });

        return marker;
    }

    function getStarIconHtml() {
        return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>`;
    }

    function handleUserRateHtml(rate) {
        let i = 5;

        let html = `<div class="stars-card d-flex align-items-center mt-10">`;

        while (--i >= 5 - rate) {
            html += `<i class="active">${getStarIconHtml()}</i>`;
        }

        while (i-- >= 0) {
            html += `<i class="">${getStarIconHtml()}</i>`;
        }

        html += `</div>`;

        return html;
    }

    function handleUserMapCardHtml(user) {
        return `<div class="map-instructor-card p-10">
            <div class="d-flex align-items-center flex-column px-24 px-lg-32">
                <div class="map-instructor-card-avatar rounded-circle mt-10">
                    <img src="${user.avatar ?? ''}" class="img-cover rounded-circle" alt="${user.full_name ?? ''}">
                </div>

                <h4 class="font-16 font-weight-bold mt-5">${user.full_name ?? ''}</h4>
                <span class="d-block font-12 text-gray">${user.bio ?? ''}</span>

                ${handleUserRateHtml(user.rate)}

                ${
                    user.price ?
                        `<div class="mt-20">
                            <span class="text-primary font-20 font-weight-bold">${currency}${user.price ?? ''}</span>
                            <span class="font-14 font-weight-500 text-gray">/${hourLang}</span>
                        </div>`
                        :
                        `<div class="mt-20">
                            <span class="text-primary font-20 font-weight-bold">${freeLang}</span>
                        </div>`
                }

            </div>

            <a href="${user.profileUrl}" class="btn btn-primary btn-sm btn-block mt-20 text-white" target="_blank">${profileLang}</a>
        </div>`;
    }

    $(document).ready(function () {
        var rangeTimeOut = undefined;

        function handleDoubleRange($el, item) {
            if ($el && $el.length) {
                const minLimit = $el.attr('data-minLimit');
                const maxLimit = $el.attr('data-maxLimit');

                const minTimeEl = $el.find('input[name="min_' + item + '"]');
                const maxTimeEl = $el.find('input[name="max_' + item + '"]');

                const minValue = minTimeEl.val();
                const maxValue = maxTimeEl.val();

                const range = $el.wRunner({
                    type: 'range',
                    limits: {
                        minLimit,
                        maxLimit,
                    },
                    rangeValue: {
                        minValue,
                        maxValue,
                    },
                    step: 1,
                });

                range.onValueUpdate(function (values) {
                    minTimeEl.val(values.minValue);
                    maxTimeEl.val(values.maxValue);

                    if (rangeTimeOut !== undefined) {
                        clearTimeout(rangeTimeOut);
                    }

                    rangeTimeOut = setTimeout(() => {
                        $('#filtersForm').trigger('submit');
                    }, 1500);
                });
            }
        }


        handleDoubleRange($('#priceRange'), 'price');

        handleDoubleRange($('#instructorAgeRange'), 'age');

        handleDoubleRange($('#timeRangeInstructorPage'), 'time');
    });

    $('body').on('change', '#topFilters input,#topFilters select', function (e) {
        e.preventDefault();

        $('#filtersForm').trigger('submit');
    });
})(jQuery);
