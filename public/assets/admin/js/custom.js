/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */
(function ($) {
    "use strict";

    var datefilter = $('.datefilter');
    datefilter.daterangepicker({
        singleDatePicker: true,
        timePicker: false,
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    datefilter.on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });

    datefilter.on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });


    $('body').on('click', '.admin-file-manager', function (e) {
        e.preventDefault();
        $(this).filemanager('file', {prefix: '/laravel-filemanager'})
    });

    $('body').on('click', '.admin-file-view', function (e) {
        e.preventDefault();
        var input = $(this).attr('data-input');

        var img_src = $('#' + input).val();

        $('#fileViewModal').find('img').attr('src', img_src);
        $('#fileViewModal').modal('show');
    });


// ********************************************
// ********************************************
// date & time piker
    window.resetDatePickers = () => {
        if (jQuery().daterangepicker) {
            const $dateRangePicker = $('.date-range-picker');
            const format1 = $dateRangePicker.attr('data-format') ?? 'YYYY-MM-DD';
            const timepicker1 = !!$dateRangePicker.attr('data-timepicker');
            const drops1 = $dateRangePicker.attr('data-drops') ?? 'down';

            $dateRangePicker.daterangepicker({
                locale: {
                    format: format1,
                    cancelLabel: 'Clear'
                },
                drops: drops1,
                autoUpdateInput: false,
                timePicker: timepicker1,
                timePicker24Hour: true,
                opens: 'right'
            });
            $dateRangePicker.on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format(format1) + ' - ' + picker.endDate.format(format1));
            });

            $dateRangePicker.on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });


            const $datetimepicker = $('.datetimepicker');
            const format2 = $datetimepicker.attr('data-format') ?? 'YYYY-MM-DD HH:mm';
            const drops2 = $datetimepicker.attr('data-drops') ?? 'down';

            $datetimepicker.daterangepicker({
                locale: {
                    format: format2,
                    cancelLabel: 'Clear'
                },
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                autoUpdateInput: false,
                drops: drops2,
            });
            $datetimepicker.on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm'));
            });

            $datetimepicker.on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

            const $datepicker = $('.datepicker');
            const drops3 = $datepicker.attr('data-drops') ?? 'down';

            $datepicker.daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear'
                },
                singleDatePicker: true,
                timePicker: false,
                autoUpdateInput: false,
                drops: drops3,
            });
            $datepicker.on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });

            $datepicker.on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });
        }
    };
    resetDatePickers();

// Timepicker
    if (jQuery().timepicker) {
        $(".setTimepicker").each(function (key, item) {
            $(item).timepicker({
                icons: {
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down'
                },
                showMeridian: false,
            });
        })
    }

// ********************************************
// ********************************************
// select 2
    window.resetSelect2 = () => {
        if (jQuery().select2) {
            $(".select2").select2({
                placeholder: $(this).data('placeholder'),
                allowClear: true,
                width: '100%',
            });
        }
    };
    resetSelect2();
// ********************************************
// ********************************************
// inputtags
    if (jQuery().tagsinput) {
        var input_tags = $('.inputtags');
        input_tags.tagsinput({
            tagClass: 'badge badge-primary',
            maxTags: (input_tags.data('max-tag') ? input_tags.data('max-tag') : 10),
        });
    }

    window.handleSearchableSelect2 = function (className, path, itemColumn) {
        const $els = $('.' + className);

        if ($els.length) {
            for (const el of $els) {
                const $el = $(el);

                $el.select2({
                    placeholder: $el.attr('data-placeholder'),
                    minimumInputLength: 3,
                    allowClear: true,
                    ajax: {
                        url: path,
                        dataType: 'json',
                        type: "POST",
                        quietMillis: 50,
                        data: function (params) {
                            return {
                                term: params.term,
                                option: $el.attr('data-search-option'),
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: item[itemColumn],
                                        id: item.id
                                    };
                                })
                            };
                        }
                    }
                });
            }
        }
    };

    $(document).ready(function () {

        handleSearchableSelect2('search-user-select2', adminPanelPrefix + '/users/search', 'name');

        handleSearchableSelect2('search-user-select22', adminPanelPrefix + '/users/search', 'name');

        handleSearchableSelect2('search-webinar-select2', adminPanelPrefix + '/webinars/search', 'title');

        handleSearchableSelect2('search-bundle-select2', adminPanelPrefix + '/bundles/search', 'title');

        handleSearchableSelect2('search-forum-topic-select2', adminPanelPrefix + '/forums/topics/search', 'title');

        handleSearchableSelect2('search-product-select2', adminPanelPrefix + '/store/products/search', 'title');

        handleSearchableSelect2('search-category-select2', adminPanelPrefix + '/categories/search', 'title');

        handleSearchableSelect2('search-blog-select2', adminPanelPrefix + '/blog/search', 'title');

        handleSearchableSelect2('search-upcoming-course-select2', adminPanelPrefix + '/upcoming_courses/search', 'title');


        var datefilter = $('.datefilter');
        datefilter.daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        datefilter.on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        datefilter.on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

        const sidebar_nicescroll = $(".main-sidebar").getNiceScroll();
        if (typeof sidebar_nicescroll !== "undefined" && sidebar_nicescroll.length) {
            const $active = $('.nav-item.active');

            if ($active && $active.length) {
                sidebar_nicescroll.doScrollPos(0, ($active.position().top - 100));
            }
        }
    });


    // Define LFM summernote button

    if (jQuery().summernote) {
        makeSummernote($(".summernote"))
    }


    $('body').on('change', '.js-edit-content-locale', function (e) {
        const val = $(this).val();

        if (val) {
            var url = window.location.origin + window.location.pathname;

            url += (url.indexOf('?') > -1) ? '&' : '?';

            url += 'locale=' + val;

            window.location.href = url;
        }
    });

    var $colorpickerinput = $(".colorpickerinput");
    if ($colorpickerinput.length) {
        var colorpickerinputFormat = $colorpickerinput.attr('data-format') ?? 'hex';

        $colorpickerinput.colorpicker({
            format: colorpickerinputFormat,
            component: '.input-group-append',
        });
    }

    $.fn.serializeObject = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    window.serializeObjectByTag = (tagId) => {
        var o = {};
        var a = tagId.find('input, textarea, select').serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    //
    // delete sweet alert
    $('body').on('click', '.delete-action', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const href = $(this).attr('href');

        const title = $(this).attr('data-title') ?? deleteAlertHint;
        const confirm = $(this).attr('data-confirm') ?? deleteAlertConfirm;

        var html = '<div class="">\n' +
            '    <p class="">' + title + '</p>\n' +
            '    <div class="mt-30 d-flex align-items-center justify-content-center">\n' +
            '        <button type="button" id="swlDelete" data-href="' + href + '" class="btn btn-sm btn-primary">' + confirm + '</button>\n' +
            '        <button type="button" class="btn btn-sm btn-danger ml-10 close-swl">' + deleteAlertCancel + '</button>\n' +
            '    </div>\n' +
            '</div>';

        Swal.fire({
            title: deleteAlertTitle,
            html: html,
            icon: 'warning',
            showConfirmButton: false,
            showCancelButton: false,
            allowOutsideClick: () => !Swal.isLoading(),
        })
    });

    $('body').on('click', '#swlDelete', function (e) {
        e.preventDefault();
        var $this = $(this);
        const href = $this.attr('data-href');

        $this.addClass('loadingbar primary').prop('disabled', true);

        $.get(href, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    title: (typeof result.title !== "undefined") ? result.title : deleteAlertSuccess,
                    text: (typeof result.text !== "undefined") ? result.text : deleteAlertSuccessHint,
                    showConfirmButton: false,
                    icon: 'success',
                });

                if (typeof result.dont_reload === "undefined") {
                    setTimeout(() => {
                        if (typeof result.redirect_to !== "undefined" && result.redirect_to !== undefined && result.redirect_to !== null && result.redirect_to !== '') {
                            window.location.href = result.redirect_to;
                        } else {
                            window.location.reload();
                        }
                    }, 1000);
                }
            } else {
                Swal.fire({
                    title: deleteAlertFail,
                    text: deleteAlertFailHint,
                    icon: 'error',
                })
            }
        }).error(err => {
            Swal.fire({
                title: deleteAlertFail,
                text: deleteAlertFailHint,
                icon: 'error',
            })
        }).always(() => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
        });
    })

    $('body').on('change', 'input[type="file"].custom-file-input', function () {
        const value = this.value;

        if (value) {
            const splited = value.split('\\');

            if (splited.length) {
                $(this).closest('.custom-file').find('.custom-file-label').text(splited[splited.length - 1])
            }
        }
    })

    /**********
     * Captcha
     * *********/
    $(document).ready(function () {
        function captcha_src(callback) {

            $.ajax({
                url: adminPanelPrefix + '/captcha/create',
                type: 'post',
                cache: false,
                timeout: 30000,
                success: function (data) {
                    if (data.status == 'success') {
                        if (callback) {
                            callback(data.captcha_src);
                        }
                    } else {
                        callback(false);
                    }
                }
            });
        }

        function refreshCaptcha() {
            captcha_src(function (captcha_src) {
                if (captcha_src) {
                    $('.captcha-image').attr('src', captcha_src);
                } else {
                    $('.captcha-image').closest('.form-group').find('.help-block').html('Oops!');
                }
            });
        }


        $('body').on('click', '#refreshCaptcha', function (e) {
            e.preventDefault();
            refreshCaptcha();
        });

        const $refreshCaptcha = $('#refreshCaptcha');

        setTimeout(function () {
            if ($refreshCaptcha.length) {
                $refreshCaptcha.trigger('click')
            }
        }, 100)
    })

    /**********
     * Captcha
     * *********/

    window.lockBodyScroll = function (lock) {
        const root = document.getElementsByTagName('html')[0];

        if (lock) {
            root.classList.add('close-body-scroll');
        } else {
            root.classList.remove('close-body-scroll');
        }
    }

    $('body').on('click', '.js-currency-dropdown-item', function () {
        const $this = $(this);
        const value = $this.attr('data-value');
        const title = $this.attr('data-title');
        const parent = $this.closest('.js-currency-select');

        parent.find('input[name="currency"]').val(value);
        parent.find('.js-lang-title').text(title);

        if (!parent.hasClass('js-dont-submit')) {
            parent.find('form').trigger('submit')
        }
    });

    $('body').on('change', '.js-commission-type-input', function () {
        const $this = $(this);
        const $parent = $this.closest('.row');
        const type = $this.val();
        const $span = $parent.find('.js-commission-value-span')
        const $input = $parent.find('.js-commission-value-input');

        const currency = $this.attr('data-currency');

        if (type === "percent") {
            $span.html("(%)");
            $input.attr({
                'maxlength': '3',
                'min': '0',
                'max': '100'
            });
        } else {
            $span.html("(" + currency + ")");
            $input.removeAttr('maxlength min max');
        }
    })

})(jQuery);
