(function ($) {
    "use strict";

    $(document).ready(function () {
        var mapBox = $('#mapBox');
        var mapContainer = $('#mapContainer');
        var LocationLatitude = $('#LocationLatitude');
        var LocationLongitude = $('#LocationLongitude');

        var map;

        function handleMap(lat, lng, zoom) {
            mapContainer.removeClass('d-none');

            const mapOption = {
                dragging: true,
                zoomControl: true,
                scrollWheelZoom: true,
            };

            map = L.map('mapBox', mapOption).setView([lat, lng], zoom);

            L.tileLayer(leafletApiPath, {
                maxZoom: 18,
                tileSize: 512,
                zoomOffset: -1,
                attribution: '© <a target="_blank" rel="nofollow" href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            map.on('moveend', function (e) {
                var centerLocation = map.getCenter();

                LocationLatitude.val(centerLocation.lat);
                LocationLongitude.val(centerLocation.lng);
            });

            map.on('dragstart', function () {
                $('.region-map .marker').addClass('dragging');
            });

            map.on('dragend', function () {
                $('.region-map .marker').removeClass('dragging')
            });
        }


        const lat = LocationLatitude.val();
        const lng = LocationLongitude.val();

        if (lat && lng) {
            const zoom = mapBox.attr('data-zoom');

            handleMap(lat, lng, zoom);
        } else {
            mapContainer.addClass('d-none');
        }

        function handleMapCenterAfterSelectChange($select, zoom) {
            const selectedOption = $select.find('option:checked');
            let mapCenter = selectedOption.attr('data-center');
            mapCenter = mapCenter.split(',');

            LocationLatitude.val(mapCenter[0]);
            LocationLongitude.val(mapCenter[1]);

            if (map) {
                map.setView([mapCenter[0], mapCenter[1]], zoom);
            } else {
                handleMap(mapCenter[0], mapCenter[1], zoom);
            }
        }

        $('body').on('change', 'select[name="country_id"]', function () {
            handleMapCenterAfterSelectChange($(this), 5);

            const $this = $(this);

            $this.addClass('loadingbar gray').prop('disabled', true);

            $('select[name="province_id"]').val(null).prop('disabled', true);
            $('select[name="city_id"]').val(null).prop('disabled', true);
            $('select[name="district_id"]').val(null).prop('disabled', true);

            $.get('/regions/provincesByCountry/' + $this.val(), function (result) {
                if (result && result.code === 200) {
                    const selectBox = $('select[name="province_id"]');
                    let html = '<option value="">' + selectProvinceLang + '</option>';

                    if (result.provinces && result.provinces.length) {
                        for (let province of result.provinces) {
                            html += '<option value="' + province.id + '" data-center="' + province.geo_center.join(',') + '">' + province.title + '</option>';
                        }
                    }

                    selectBox.prop('disabled', false);
                    selectBox.html(html);

                    $this.removeClass('loadingbar gray').prop('disabled', false);
                }
            });
        });

        $('body').on('change', 'select[name="province_id"]', function () {
            handleMapCenterAfterSelectChange($(this), 8);

            const $this = $(this);

            $this.addClass('loadingbar gray').prop('disabled', true);

            $('select[name="city_id"]').val(null).prop('disabled', true);
            $('select[name="district_id"]').val(null).prop('disabled', true);

            $.get('/regions/citiesByProvince/' + $this.val(), function (result) {
                if (result && result.code === 200) {
                    const selectBox = $('select[name="city_id"]');

                    let html = '<option value="">' + selectCityLang + '</option>';

                    if (result.cities && result.cities.length) {
                        for (let city of result.cities) {
                            html += '<option value="' + city.id + '" data-center="' + city.geo_center.join(',') + '">' + city.title + '</option>';
                        }
                    }

                    selectBox.prop('disabled', false);
                    selectBox.html(html);

                    $this.removeClass('loadingbar gray').prop('disabled', false);
                }
            });
        });

        $('body').on('change', 'select[name="city_id"]', function () {
            handleMapCenterAfterSelectChange($(this), 10);

            const $this = $(this);

            $this.addClass('loadingbar gray').prop('disabled', true);

            $('select[name="district_id"]').val(null).prop('disabled', true);

            $.get('/regions/districtsByCity/' + $this.val(), function (result) {
                if (result && result.code === 200) {
                    const selectBox = $('select[name="district_id"]');

                    let html = '<option value="">' + selectDistrictLang + '</option>';

                    if (result.districts && result.districts.length) {
                        for (let district of result.districts) {
                            html += '<option value="' + district.id + '" data-center="' + district.geo_center.join(',') + '">' + district.title + '</option>';
                        }
                    }

                    selectBox.prop('disabled', false);
                    selectBox.html(html);

                    $this.removeClass('loadingbar gray').prop('disabled', false);
                }
            });
        });

        $('body').on('change', 'select[name="district_id"]', function () {
            handleMapCenterAfterSelectChange($(this), 13);
        });

        $('body').on('change', 'input[name="group_meeting"]', function () {
            if (this.checked) {
                $('#groupMeetingAddress').removeClass('d-none');
            } else {
                $('#groupMeetingAddress').addClass('d-none');
            }
        });
    });
})(jQuery);
