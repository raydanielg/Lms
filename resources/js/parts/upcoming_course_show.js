(function ($) {
    "use strict";


    /**
     * webinar demo modal
     * */
    var courseDemoVideoPlayer;

    $('body').on('click', '#webinarDemoVideoBtn', function (e) {
        e.preventDefault();

        if (courseDemoVideoPlayer !== undefined) {
            courseDemoVideoPlayer.dispose();
        }

        let path = $(this).attr('data-video-path');
        let source = $(this).attr('data-video-source');
        const height = $(window).width() > 991 ? 480 : 264;

        const videoTagId = 'demoVideoPlayer';
        const {html, options} = makeVideoPlayerHtml(path, source, height, videoTagId);

        let modalHtml = '<div id="webinarDemoVideoModal" class="demo-video-modal">\n' +
            '<h3 class="section-title after-line font-20 text-dark-blue">' + webinarDemoLang + '</h3>\n' +
            '<div class="demo-video-card mt-25">\n';

        modalHtml += html;

        modalHtml += '</div></div>';

        Swal.fire({
            html: modalHtml,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
            onOpen: () => {
                courseDemoVideoPlayer = videojs(videoTagId, options);
            }
        });
    });

    /**
     * webinar report modal
     * */
    $('body').on('click', '#webinarReportBtn', function (e) {
        e.preventDefault();

        let modal_html = $('#webinarReportModal').html();

        Swal.fire({
            html: modal_html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
        });
    });

    $('body').on('click', '.js-course-report-submit', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $this.closest('form');
        const action = $form.attr('action');
        const data = $form.serializeObject();

        $this.addClass('loadingbar primary').prop('disabled', true);

        $form.find('.invalid-feedback').text('');
        $form.find('.is-invalid').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + reportSuccessLang + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else if (result && result.code === 401) {
                Swal.fire({
                    icon: 'error',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + reportFailLang + '</h3>',
                    showConfirmButton: false,
                });
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;
            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        });
    });


    $('body').on('click', '#favoriteToggle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const href = $(this).attr('href');
        const icon = $(this).find('svg');

        if (icon.hasClass('favorite-active')) {
            icon.removeClass('favorite-active');
        } else {
            icon.addClass('favorite-active');
        }

        $.get(href, function (result) {

        });
    });

    $('body').on('click', '.js-share-course', function (e) {
        e.preventDefault();

        Swal.fire({
            html: $('#courseShareModal').html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            onOpen: () => {
                $('[data-toggle="tooltip"]').tooltip();
            },
            width: '32rem',
        });
    });

    $('body').on('click', '.js-follow-upcoming-course', function (e) {
        e.preventDefault();
        const $this = $(this);
        const path = $this.attr('data-path');

        $this.addClass('loadingbar primary').prop('disabled', true);

        $.get(path, function (result) {
            if (result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + result.msg + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        });
    });

    $('body').on('click', '.js-course-share-link-copy', function (e) {
        e.preventDefault();

        $(this).attr('data-original-title', copiedLang)
            .tooltip('show');

        $(this).attr('data-original-title', copyLang);

        copyToClipboard();
    });

    function copyToClipboard() {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.css('position', 'absolute');

        $temp.val($('.js-course-share-link').html()).select();
        document.execCommand("copy");
        $temp.remove();
    }


    function errorToast(heading, text) {
        $.toast({
            heading: heading,
            text: text,
            bgColor: '#f63c3c',
            textColor: 'white',
            hideAfter: 10000,
            position: 'bottom-right',
            icon: 'error'
        });
    }
})(jQuery);
