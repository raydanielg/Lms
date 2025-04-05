(function ($) {
    "use strict";

    // form serialize to Object
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

    function randomString() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    /**
     * close swl
     * */
    $('body').on('click', '.close-swl', function (e) {
        e.preventDefault();
        Swal.close();
    });

    if (jQuery().summernote) {
        makeSummernote($('#summernote'), 400)
    }


    $('body').on('click', '#saveAndPublish', function (e) {
        $(this).addClass('loadingbar primary').prop('disabled', true);
        e.preventDefault();
        $('#forDraft').val('publish');
        $('#upcomingCourseForm').trigger('submit');
    });

    $('body').on('click', '#saveAsDraft', function (e) {
        $(this).addClass('loadingbar primary').prop('disabled', true);
        e.preventDefault();
        $('#forDraft').val(1);
        $('#upcomingCourseForm').trigger('submit');
    });

    $('body').on('click', '#saveReject', function (e) {
        e.preventDefault();
        $(this).addClass('loadingbar primary').prop('disabled', true);
        $('#forDraft').val('reject');
        $('#upcomingCourseForm').trigger('submit');
    });

    $('body').on('change', '#categories', function (e) {
        e.preventDefault();
        let category_id = this.value;

        $.get(adminPanelPrefix + '/filters/get-by-category-id/' + category_id, function (result) {

            if (result && typeof result.filters !== "undefined" && result.filters.length) {
                let html = '';
                Object.keys(result.filters).forEach(key => {
                    let filter = result.filters[key];
                    let options = [];

                    if (filter.options.length) {
                        options = filter.options;
                    }

                    html += '<div class="col-12 col-md-3">\n' +
                        '<div class="webinar-category-filters">\n' +
                        '<strong class="category-filter-title d-block">' + filter.title + '</strong>\n' +
                        '<div class="py-10"></div>\n' +
                        '\n';

                    if (options.length) {
                        Object.keys(options).forEach(index => {
                            let option = options[index];

                            html += '<div class="form-group mt-20 d-flex align-items-center justify-content-between">\n' +
                                '<label class="" for="filterOption' + option.id + '">' + option.title + '</label>\n' +
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
    function editFaq($this, locale = null) {
        const faq_id = $this.attr('data-faq-id');
        const webinar_id = $this.attr('data-webinar-id');

        const edit_data = {
            item_id: webinar_id,
            locale: locale
        };

        $.post(adminPanelPrefix + '/faqs/' + faq_id + '/edit', edit_data, function (result) {
            if (result && result.faq) {
                const faq = result.faq;

                let edit_faq_modal = '<div id="addFAQsModal">';
                edit_faq_modal += $('#webinarFaqModal').html();
                edit_faq_modal += '</div>';
                edit_faq_modal = edit_faq_modal.replaceAll(adminPanelPrefix + '/faqs/store', adminPanelPrefix + '/faqs/' + faq_id + '/update');

                Swal.fire({
                    html: edit_faq_modal,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '48rem',
                    onOpen: () => {
                        var $modal = $('#addFAQsModal');

                        Object.keys(faq).forEach(key => {
                            $modal.find('[name="' + key + '"]').val(faq[key]);
                        });

                        var localeSelect = $modal.find('select[name="locale"]');

                        if (localeSelect) {
                            localeSelect.addClass('js-edit-faq-locale-ajax');
                            localeSelect.attr('data-faq-id', faq_id);
                            localeSelect.attr('data-webinar-id', webinar_id);
                        }
                    }
                });
            }
        });
    }

    $('body').on('click', '#upcomingCourseAddFAQ', function (e) {
        e.preventDefault();
        let add_faq_modal = '<div id="addFAQsModal">';
        add_faq_modal += $('#webinarFaqModal').html();
        add_faq_modal += '</div>';

        Swal.fire({
            html: add_faq_modal,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
        });
    });

    $('body').on('click', '#saveFAQ', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $('#addFAQsModal .js-faq-form');
        handleWebinarItemForm(form, $this);
    });

    $('body').on('click', '.edit-faq', function (e) {
        e.preventDefault();
        const $this = $(this);

        loadingSwl();

        editFaq($this);
    });

    $('body').on('change', '.js-edit-faq-locale-ajax', function (e) {
        e.preventDefault();
        const $this = $(this);
        const locale = $this.val();

        editFaq($this, locale);
    });

    $('body').on('click', '.js-get-faq-description', function (e) {
        e.preventDefault();
        const $this = $(this);
        const answer = $this.parent().find('input').val();

        var html = '<div class="my-2">' + answer + '</div>';

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '30rem',
        });
    });

    /*
    * add extra description
    * */
    $('body').on('click', '#add_new_learning_materials', function (e) {
        e.preventDefault();
        const key = randomString();

        let html = '<div id="extraDescriptionModal">';
        html += $('#extraDescriptionForm').html();
        html += '</div>';

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
            onOpen: function () {
                $('#extraDescriptionModal input[name="type"]').val('learning_materials')
            }
        });
    });

    function handleCompanyLogosInputHtml(key) {
        let html = '<div id="extraDescriptionModal">';
        html += $('#extraDescriptionForm').html();
        html += '</div>';

        var modalHtml = $(html);
        modalHtml.find('.js-form-groups').children().remove();
        modalHtml.find('.js-form-groups').append('<div class="form-group">\n' +
            '            <label class="input-label">image</label>\n' +
            '            <div class="input-group">\n' +
            '                <div class="input-group-prepend">\n' +
            '                    <button type="button" class="input-group-text admin-file-manager" data-input="image_' + key + '" data-preview="holder">\n' +
            '                        <i class="fa fa-upload"></i>\n' +
            '                    </button>\n' +
            '                </div>\n' +
            '                <input type="text" name="value" id="image_' + key + '" class="form-control"/>\n' +
            '            </div>\n' +
            '        </div>');

        var mainHtml = '<div id="extraDescriptionModal">';
        mainHtml += modalHtml.html();
        mainHtml += '</div>';

        return mainHtml;
    }

    $('body').on('click', '#add_new_company_logos', function (e) {
        e.preventDefault();
        const key = randomString();
        var html = handleCompanyLogosInputHtml(key)

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
            onOpen: function () {
                $('#extraDescriptionModal input[name="type"]').val('company_logos')
            }
        });
    });

    $('body').on('click', '#add_new_requirements', function (e) {
        e.preventDefault();
        const key = randomString();

        let html = '<div id="extraDescriptionModal">';
        html += $('#extraDescriptionForm').html();
        html += '</div>';

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
            onOpen: function () {
                $('#extraDescriptionModal input[name="type"]').val('requirements')
            }
        });
    });

    $('body').on('click', '#saveExtraDescription', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $('#extraDescriptionModal .js-form');
        handleWebinarItemForm(form, $this);
    });

    $('body').on('click', '.edit-extraDescription', function (e) {
        e.preventDefault();
        const $this = $(this);

        editExtraDescription($this);
    });

    $('body').on('change', '.js-edit-extraDescription-locale-ajax', function (e) {
        e.preventDefault();
        const $this = $(this);
        const locale = $this.val();

        editExtraDescription($this, locale);
    });

    function editExtraDescription($this, locale) {
        const item_id = $this.attr('data-item-id');
        const webinar_id = $this.attr('data-webinar-id');

        const rendomKey = randomString();

        const edit_data = {
            item_id: webinar_id,
            locale: locale
        };

        $.post(adminPanelPrefix + '/webinar-extra-description/' + item_id + '/edit', edit_data, function (result) {
            if (result && result.webinarExtraDescription) {
                const webinarExtraDescription = result.webinarExtraDescription;

                let html = '<div id="extraDescriptionModal">';
                html += $('#extraDescriptionForm').html();
                html += '</div>';

                if (webinarExtraDescription.type === 'company_logos') {
                    html = handleCompanyLogosInputHtml(rendomKey);
                }

                html = html.replaceAll(adminPanelPrefix + '/webinar-extra-description/store', adminPanelPrefix + '/webinar-extra-description/' + item_id + '/update');

                Swal.fire({
                    html: html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '48rem',
                    onOpen: () => {
                        var $modal = $('#extraDescriptionModal');

                        Object.keys(webinarExtraDescription).forEach(key => {
                            $modal.find('[name="' + key + '"]').val(webinarExtraDescription[key]);
                        });

                        var localeSelect = $modal.find('select[name="locale"]');

                        if (localeSelect) {
                            localeSelect.addClass('js-edit-extraDescription-locale-ajax');
                            localeSelect.attr('data-item-id', item_id);
                            localeSelect.attr('data-webinar-id', webinar_id);
                        }
                    }
                });
            }
        });
    }



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
