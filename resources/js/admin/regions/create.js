(function ($) {
    "use strict";

    $(document).ready(function () {

        const mapContainer = $('#mapBox');
        const lat = mapContainer.attr('data-latitude');
        const lng = mapContainer.attr('data-longitude');
        const zoom = mapContainer.attr('data-zoom');

        const mapOption = {
            dragging: true,
            zoomControl: true,
            scrollWheelZoom: true,
        };

        var map = L.map('mapBox', mapOption).setView([lat, lng], zoom);

        L.tileLayer(leafletApiPath, {
            maxZoom: 18,
            tileSize: 512,
            zoomOffset: -1,
            attribution: '© <a target="_blank" rel="nofollow" href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        map.on('moveend', function (e) {
            var centerLocation = map.getCenter();

            $('#LocationLatitude').val(centerLocation.lat);
            $('#LocationLongitude').val(centerLocation.lng);
        });

        map.on('dragstart', function () {
            $('.region-map .marker').addClass('dragging');
        });

        map.on('dragend', function () {
            $('.region-map .marker').removeClass('dragging')
        });

        function handleMapCenterAfterSelectChange($select, zoom) {
            const selectedOption = $select.find('option:checked');
            let mapCenter = selectedOption.attr('data-center');
            mapCenter = mapCenter.split(',');

            if (map) {
                map.setView([mapCenter[0], mapCenter[1]], zoom);
            }
        }

        $('body').on('change', '#citySelectBox select', function () {
            handleMapCenterAfterSelectChange($(this), 13);
        });

        $('body').on('change', '#provinceSelectBox select', function () {
            handleMapCenterAfterSelectChange($(this), 8);

            const type = $('input[name="type"]').val();
            const $this = $(this);

            if (type === 'district') {
                $this.addClass('loadingbar gray').prop('disabled', true);

                $.get(adminPanelPrefix + '/regions/citiesByProvince/' + $this.val(), function (result) {
                    if (result && result.code === 200) {
                        const selectBox = $('#citySelectBox');
                        let html = '<option value="">' + selectCityLang + '</option>';

                        if (result.cities && result.cities.length) {
                            for (let city of result.cities) {
                                html += '<option value="' + city.id + '" data-center="' + city.geo_center.join(',') + '">' + city.title + '</option>';
                            }
                        }

                        selectBox.find('select').prop('disabled', false);
                        selectBox.find('select').html(html);
                        selectBox.removeClass('d-none');
                        $this.removeClass('loadingbar gray').prop('disabled', false);
                    }
                })
            }
        });


        $('body').on('change', '#countrySelectBox select', function () {
            const type = $('input[name="type"]').val();
            const $this = $(this);

            handleMapCenterAfterSelectChange($(this), 5);

            if (type !== 'country' && type !== 'province') {

                $this.addClass('loadingbar gray').prop('disabled', true);

                $.get(adminPanelPrefix + '/regions/provincesByCountry/' + $this.val(), function (result) {
                    if (result && result.code === 200) {
                        const selectBox = $('#provinceSelectBox');
                        let html = '<option value="">' + selectProvinceLang + '</option>';

                        if (result.provinces && result.provinces.length) {
                            for (let province of result.provinces) {
                                html += '<option value="' + province.id + '" data-center="' + province.geo_center.join(',') + '">' + province.title + '</option>';
                            }
                        }

                        selectBox.find('select').prop('disabled', false);
                        selectBox.find('select').html(html);
                        selectBox.removeClass('d-none');
                        $this.removeClass('loadingbar gray').prop('disabled', false);
                    }
                })
            }
        });

    });
})(jQuery);
