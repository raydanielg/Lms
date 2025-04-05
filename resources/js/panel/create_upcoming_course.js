(function ($) {
    "use strict";

    if (jQuery().summernote) {
        makeSummernote($('#summernote'), 400)
    }


    $('body').on('click', '#sendForReview', function (e) {
        $(this).addClass('loadingbar primary').prop('disabled', true);
        e.preventDefault();
        $('#forDraft').val(0);
        $('#upcomingCourseForm').trigger('submit');
    });

    $('body').on('click', '#saveAsDraft', function (e) {
        $(this).addClass('loadingbar primary').prop('disabled', true);
        e.preventDefault();
        $('#forDraft').val(1);
        $('#upcomingCourseForm').trigger('submit');
    });

    $('body').on('click', '#getNextStep', function (e) {
        $(this).addClass('loadingbar primary').prop('disabled', true);
        e.preventDefault();
        $('#forDraft').val(1);
        $('#getNext').val(1);
        $('#upcomingCourseForm').trigger('submit');
    });

    $('body').on('click', '.js-get-next-step', function (e) {
        e.preventDefault();

        if (!$(this).hasClass('active')) {
            $(this).addClass('loadingbar primary').prop('disabled', true);
            const step = $(this).attr('data-step');

            $('#getStep').val(step);
            $('#forDraft').val(1);
            $('#getNext').val(1);
            $('#upcomingCourseForm').trigger('submit');
        }
    });


    function handleGetFiltersTitleFromTranslations(translations, defaultLocale) {
        let title = null;

        if (Object.keys(translations).length) {
            Object.keys(translations).forEach(key => {
                const translation = translations[key];

                if (translation.locale === defaultLocale) {
                    title = translation.title
                }
            })

            if (!title) {
                title = translations[0].title
            }
        }

        return title;
    }

    $('body').on('change', '#categories', function (e) {
        e.preventDefault();
        let category_id = this.value;
        $.get('/panel/filters/get-by-category-id/' + category_id, function (result) {

            if (result && typeof result.filters !== "undefined" && result.filters.length) {
                const defaultLocale = result.defaultLocale;
                let html = '';

                Object.keys(result.filters).forEach(key => {
                    let filter = result.filters[key];
                    let options = [];

                    if (filter.options.length) {
                        options = filter.options;
                    }

                    let filterTitle = filter.title;

                    if (!filterTitle && filter.translations) {
                        filterTitle = handleGetFiltersTitleFromTranslations(filter.translations, defaultLocale);
                    }

                    html += '<div class="col-12 col-md-3">\n' +
                        '<div class="webinar-category-filters">\n' +
                        '<strong class="category-filter-title d-block">' + filterTitle + '</strong>\n' +
                        '<div class="py-10"></div>\n' +
                        '\n';

                    if (options.length) {
                        Object.keys(options).forEach(index => {
                            let option = options[index];

                            let optionTitle = option.title;

                            if (!optionTitle && option.translations) {
                                optionTitle = handleGetFiltersTitleFromTranslations(option.translations, defaultLocale);
                            }

                            html += '<div class="form-group mt-20 d-flex align-items-center justify-content-between">\n' +
                                '<label class="cursor-pointer" for="filterOption' + option.id + '">' + optionTitle + '</label>\n' +
                                '<div class="custom-control custom-checkbox">\n' +
                                '<input type="checkbox" name="filters[]" value="' + option.id + '" class="custom-control-input" id="filterOption' + option.id + '">\n' +
                                '<label class="custom-control-label" for="filterOption' + option.id + '"></label>\n' +
                                '</div>\n' +
                                '</div>\n';
                        })
                    }

                    html += '</div></div>';
                });

                $('#categoriesFiltersContainer').removeClass('d-none');
                $('#categoriesFiltersCard').html(html);
            } else {
                $('#categoriesFiltersContainer').addClass('d-none');
                $('#categoriesFiltersCard').html('');
            }
        })
    });

    $('body').on('click', '.cancel-accordion', function (e) {
        e.preventDefault();

        $(this).closest('.accordion-row').remove();
    });

    function randomString() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    /*
    *
    * */

    window.handleWebinarItemForm = function (form, $this) {
        let data = serializeObjectByTag(form);
        let action = form.attr('data-action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        form.find('input').removeClass('is-invalid');
        form.find('textarea').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                //window.location.reload();
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveSuccessLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500)
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;

            if (errors && errors.status === 'zoom_token_invalid') {
                Swal.fire({
                    icon: 'error',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + errors.zoom_error_msg + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });
            }

            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = form.find('.js-ajax-' + key);

                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    }


    /**
     * add webinar FAQ
     * */
    $('body').on('click', '#upcomingCourseAddFAQ', function (e) {
        e.preventDefault();
        const key = randomString();

        let add_faq = $('#newFaqForm').html();
        add_faq = add_faq.replaceAll('record', key);

        $('#faqsAccordion').prepend(add_faq);

        feather.replace();
    });

    $('body').on('click', '.js-save-faq', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $this.closest('.faq-form');
        handleWebinarItemForm(form, $this);
    });

    $('body').on('click', '#add_new_learning_materials', function (e) {
        e.preventDefault();
        const key = randomString();

        let add_faq = $('#new_learning_materials_html').html();
        add_faq = add_faq.replaceAll('record', key);

        $('#learning_materials_accordion').prepend(add_faq);

        feather.replace();
    });

    $('body').on('click', '#add_new_company_logos', function (e) {
        e.preventDefault();
        const key = randomString();

        let add_faq = $('#new_company_logos_html').html();
        add_faq = add_faq.replaceAll('record', key);

        $('#company_logos_accordion').prepend(add_faq);

        feather.replace();
    });

    $('body').on('click', '#add_new_requirements', function (e) {
        e.preventDefault();
        const key = randomString();

        let add_faq = $('#new_requirements_html').html();
        add_faq = add_faq.replaceAll('record', key);

        $('#requirements_accordion').prepend(add_faq);

        feather.replace();
    });

    $('body').on('click', '.js-save-extra_description', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $this.closest('.extra_description-form');
        handleWebinarItemForm(form, $this);
    });



    $(document).ready(function () {
        const style = getComputedStyle(document.body);
        const primaryColor = style.getPropertyValue('--primary');

        function updateToDatabase(table, idString) {
            $.post('/panel/upcoming_courses/order-items', {table: table, items: idString}, function (result) {
                if (result && result.title && result.msg) {
                    $.toast({
                        heading: result.title,
                        text: result.msg,
                        bgColor: primaryColor,
                        textColor: 'white',
                        hideAfter: 10000,
                        position: 'bottom-right',
                        icon: 'success'
                    });
                }
            });
        }

        function setSortable(target) {
            if (target.length) {
                target.sortable({
                    group: 'no-drop',
                    handle: '.move-icon',
                    axis: "y",
                    update: function (e, ui) {
                        var sortData = target.sortable('toArray', {attribute: 'data-id'});
                        var table = e.target.getAttribute('data-order-table');

                        updateToDatabase(table, sortData.join(','))
                    }
                });
            }
        }

        const items = [];

        var draggableContentLists = $('.draggable-content-lists');
        if (draggableContentLists.length) {
            for (let item of draggableContentLists) {
                items.push($(item).attr('data-drag-class'))
            }
        }

        if (items.length) {
            for (let item of items) {
                const tag = $('.' + item);

                if (tag.length) {
                    setSortable(tag);
                }
            }
        }
    });


    $('body').on('change', '.js-video-demo-source', function (e) {
        e.preventDefault();

        const value = $(this).val();

        const $otherSources = $('.js-video-demo-other-inputs');
        const $secureHostSource = $('.js-video-demo-secure-host-input');

        if (value === "secure_host") {
            $otherSources.addClass('d-none');
            $secureHostSource.removeClass('d-none');

        } else {
            $otherSources.removeClass('d-none');
            $secureHostSource.addClass('d-none');

            const $filePathUploadButton = $('.js-video-demo-path-input .js-video-demo-path-upload');
            const $filePathLinkButton = $('.js-video-demo-path-input .js-video-demo-path-links');
            const $filePathInput = $('.js-video-demo-path-input input');

            $filePathUploadButton.addClass('d-none');
            $filePathLinkButton.addClass('d-none');

            if (value === 'upload') {
                $filePathUploadButton.removeClass('d-none');
            } else {
                $filePathLinkButton.removeClass('d-none');
            }

            if (videoDemoPathPlaceHolderBySource) {
                $filePathInput.attr('placeholder', videoDemoPathPlaceHolderBySource[value]);
            }
        }
    });

    $('body').on('change', '.js-upcoming-course-content-locale', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $(this).closest('.js-content-form');
        const locale = $this.val();
        const upcomingId = $this.attr('data-upcoming-course-id');
        const item_id = $this.attr('data-id');
        const relation = $this.attr('data-relation');
        let fields = $this.attr('data-fields');
        fields = fields.split(',');


        $this.addClass('loadingbar gray');

        const path = '/panel/upcoming_courses/' + upcomingId + '/getContentItemByLocale';
        const data = {
            item_id,
            locale,
            relation
        };

        $.post(path, data, function (result) {
            if (result && result.item) {
                const item = result.item;

                Object.keys(item).forEach(function (key) {
                    const value = item[key];

                    if ($.inArray(key, fields) !== -1) {
                        let element = $form.find('.js-ajax-' + key);
                        element.val(value);
                    }
                });

                $this.removeClass('loadingbar gray');
            }
        }).fail(err => {
            $this.removeClass('loadingbar gray');
        });
    });

})(jQuery);
