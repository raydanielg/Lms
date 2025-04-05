(function ($) {
    "use strict";

    new Swiper('#bestRateInstructorsSwiper', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.best-rate-swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            991: {
                slidesPerView: 3,
            },

            660: {
                slidesPerView: 2,
            },
        }
    });

    new Swiper('#topSaleInstructorsSwiper', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.best-sale-swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            991: {
                slidesPerView: 3,
            },

            660: {
                slidesPerView: 2,
            },
        }
    });

    var loadMoreInstructors = {
        page: 1,
        has_more: true,
    };

    $('body').on('click', '#loadMoreInstructors', function (e) {
        e.preventDefault();
        const $this = $(this);
        const role = $this.attr('data-page');
        const url = $this.attr('data-url');

        $this.addClass('loadingbar gray').prop('disabled', true);

        if (loadMoreInstructors.has_more) {
            getInstructors(loadMoreInstructors.page + 1, role, url);
        }
    });

    function getInstructors(page = 1, role, url = null) {
        const $form = $('#filtersForm');
        let data = $form.serializeObject();
        data['page'] = page;

        $('#loadMoreInstructors').removeClass('d-none');
//
        const link = url ?? '/load_more/' + role;
        $.get(link, data, function (result) {
            if (result && result.html) {
                $('#instructorsList').append(result.html);


                if (page < result.last_page) {
                    loadMoreInstructors = {
                        page: page,
                        has_more: true,
                    };
                } else {
                    loadMoreInstructors = {
                        page: page,
                        has_more: false,
                    };

                    $('#loadMoreInstructors').addClass('d-none');
                }
            }
        }).always(() => {
            $('#loadMoreInstructors').removeClass('loadingbar gray').prop('disabled', false);
            feather.replace();
        });
    }

    var timeOut = undefined;
    $('body').on('change', '#filtersForm input,#filtersForm select', function (e) {
        e.preventDefault();

        const $form = $('#filtersForm');

        loadMoreInstructors = {
            page: 1,
            has_more: true,
        };

        if (timeOut !== undefined) {
            clearTimeout(timeOut);
        }


        timeOut = setTimeout(() => {
            $form.trigger('submit');
        }, 1000)
    });
})(jQuery);
