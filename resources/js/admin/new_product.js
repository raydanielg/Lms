(function ($) {
    "use strict";

    if (jQuery().summernote) {
        makeSummernote($('#summernote'), 400)
    }

    $('body').on('change', '#unlimitedInventorySwitch', function () {
        const inventoryInputs = $('.js-inventory-inputs');

        if (this.checked) {
            inventoryInputs.addClass('d-none');
        } else {
            inventoryInputs.removeClass('d-none');
        }
    });

    $('body').on('click', '.add-btn', function (e) {
        var mainRow = $('.main-row');
        var imagesCount = $('.product-images-input-group').length;

        if (imagesCount < 4) {
            var copy = mainRow.clone();
            copy.removeClass('main-row');
            copy.removeClass('d-none');

            const addBtn = copy.find('.add-btn');

            if (addBtn) {
                addBtn.removeClass('add-btn btn-primary')
                    .addClass('btn-danger remove-btn');

                const icon = addBtn.find('i');
                icon.removeClass();

                icon.addClass('fa fa-times');
            }

            var copyHtml = copy.prop('innerHTML');
            copyHtml = copyHtml.replaceAll('record', randomString());
            copyHtml = copyHtml.replaceAll('btn-primary', 'btn-danger');
            copyHtml = copyHtml.replaceAll('add-btn', 'remove-btn');

            copy.html(copyHtml);
            $('#productImagesInputs').append(copy);
        } else {
            $.toast({
                heading: requestFailedLang,
                text: maxFourImageCanSelect,
                bgColor: '#f63c3c',
                textColor: 'white',
                hideAfter: 10000,
                position: 'bottom-right',
                icon: 'error'
            });
        }
    });

    $('body').on('click', '.remove-btn', function (e) {
        e.preventDefault();
        $(this).closest('.input-group').remove();
    });

    function randomString() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < 4; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    $('body').on('click', '#productAddFile', function (e) {
        e.preventDefault();
        const nameId = randomString();

        let add_file_modal = '<div id="addFilesModal">';
        add_file_modal += $('#productFileModal').html();
        add_file_modal += '</div>';
        add_file_modal = add_file_modal.replaceAll('str_', '');
        add_file_modal = add_file_modal.replaceAll('record', nameId);

        Swal.fire({
            html: add_file_modal,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
            onOpen: function () {
                const $form = $('#addFilesModal form');

                handleFileItemsByType($form);
            }
        });
    });


    $('body').on('click', '.js-show-description', function (e) {
        e.preventDefault();

        const message = $(this).parent().find('input[type="hidden"]').val();

        const $modal = $('#fileDescriptionModal');
        $modal.find('.modal-body').html(message);

        $modal.modal('show');
    });

    $('body').on('click', '.edit-file', function (e) {
        e.preventDefault();
        const $this = $(this);

        loadingSwl();

        editFile($this);
    });

    $('body').on('change', '.js-edit-file-locale-ajax', function (e) {
        e.preventDefault();
        const $this = $(this);
        const locale = $this.val();

        editFile($this, locale);
    });

    function editFile($this, locale = null) {
        const file_id = $this.attr('data-file-id');
        const product_id = $this.attr('data-product-id');


        const edit_data = {
            product_id: product_id,
            locale: locale
        };

        $.post(adminPanelPrefix + '/store/products/files/' + file_id + '/edit', edit_data, function (result) {
            if (result && result.file) {
                const file = result.file;

                createEditFileModal(file);
            }
        });
    }

    function createEditFileModal(file) {
        let edit_file_modal = '<div id="addFilesModal">';
        edit_file_modal += $('#productFileModal').html();
        edit_file_modal += '</div>';
        edit_file_modal = edit_file_modal.replaceAll('/files/store', '/files/' + file.id + '/update');
        edit_file_modal = edit_file_modal.replaceAll('str_', '');
        const random = randomString();
        edit_file_modal = edit_file_modal.replaceAll('record', random);

        Swal.fire({
            html: edit_file_modal,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
            onOpen: () => {
                var $modal = $('#addFilesModal');

                Object.keys(file).forEach(key => {
                    if (key === 'status') {
                        const checked = (file.status === 'active');
                        $modal.find('input[name="' + key + '"]').prop('checked', checked);
                    } else if (key === 'online_viewer') {
                        const checked = (file.online_viewer && file.online_viewer === 1);
                        $modal.find('input[name="' + key + '"]').prop('checked', checked);
                    } else {
                        $modal.find('[name="' + key + '"]').val(file[key]);
                    }
                });

                var localeSelect = $modal.find('select[name="locale"]');

                if (localeSelect) {
                    localeSelect.addClass('js-edit-file-locale-ajax');
                    localeSelect.attr('data-file-id', file.id);
                    localeSelect.attr('data-product-id', file.product_id);
                }

                const $form = $modal.find('form');

                handleFileItemsByType($form);
            }
        });
    }

    $('body').on('click', '#saveFile', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $('#addFilesModal form');

        let action = form.attr('action');

        handleProductItemForm(form, $this, action);
    });

    function handleProductItemForm(form, $this, action) {
        $this.addClass('loadingbar gray').prop('disabled', true);
        form.find('input').removeClass('is-invalid');
        form.find('textarea').removeClass('is-invalid');

        var formData = new FormData();

        const items = form.find('input, textarea, select').serializeArray();

        $.each(items, function () {
            formData.append(this.name, this.value);
        });

        $.ajax({
            url: action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function (result) {
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
                    }, 500);
                }
            },
            error: function (err) {
                $this.removeClass('loadingbar gray').prop('disabled', false);
                var errors = err.responseJSON;

                if (errors && errors.errors) {
                    Object.keys(errors.errors).forEach((key) => {
                        const error = errors.errors[key];
                        let element = form.find('[name="' + key + '"]');

                        element.addClass('is-invalid');
                        element.parent().find('.invalid-feedback').text(error[0]);
                    });
                }
            }
        });
    }

    function handleSpecificationTags(elClassName) {
        if (jQuery().tagsinput) {
            var input_tags = $('.' + elClassName);
            input_tags.tagsinput({
                tagClass: 'badge badge-primary',
                maxTags: (input_tags.data('max-tag') ? input_tags.data('max-tag') : 10),
            });
        }
    }

    function handleSpecificationSelect2(elClassName) {
        const el = $('.' + elClassName);

        if (el && el.length) {
            el.select2({
                placeholder: $(this).data('placeholder'),
                //minimumInputLength: 3,
                allowClear: true,
                /*ajax: {
                    url: adminPanelPrefix + '/store/products/specifications/search',
                    dataType: 'json',
                    type: "POST",
                    quietMillis: 50,
                    data: function (params) {
                        return {
                            term: params.term,
                            category_id: $('#categories').val(),
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.title,
                                    id: item.id,
                                    input_type: item.input_type,
                                }
                            })
                        };
                    }
                }*/
            });

            el.on('change', function (e) {
                const specificationId = e.target.value;
                const $form = $(e.target).closest('.specification-form');

                handleSpecificationInputType($form, specificationId)
            });
        }
    }

    function handleSpecificationMultiValueSelect2(elClassName) {
        const el = $('.' + elClassName);

        if (el && el.length) {
            el.select2();
        }
    }

    $(document).ready(function () {
        handleSpecificationMultiValueSelect2('select-multi-values-select2');

        handleSpecificationSelect2('search-specification-select2');
    })

    function handleSpecificationInputType($form, specificationId) {
        $.get(adminPanelPrefix + '/store/products/specifications/' + specificationId + '/get', function (result) {
            if (result) {
                const {specification, multiValues} = result;

                const multiValuesInput = $form.find('.js-multi-values-input');
                const summaryInput = $form.find('.js-summery-input');
                const allowSelectionInput = $form.find('.js-allow-selection-input');

                $form.find('.js-input-type').val(specification.input_type);

                allowSelectionInput.find('input').prop('checked', false);

                if (specification.input_type === 'multi_value') {
                    multiValuesInput.removeClass('d-none');
                    allowSelectionInput.removeClass('d-none');
                    summaryInput.addClass('d-none');

                    const select = $('.multi_values-select2');
                    let html = '';
                    if (multiValues) {
                        for (const multiValue of multiValues) {
                            html += `<option value="${multiValue.id}">${multiValue.title}</option>`;
                        }
                    }
                    select.append(html);

                    handleSpecificationMultiValueSelect2('multi_values-select2');
                } else {
                    multiValuesInput.addClass('d-none');
                    allowSelectionInput.addClass('d-none');
                    summaryInput.removeClass('d-none');
                }

                allowSelectionInput.find('input').prop('checked', false);
            }
        });
    }

    $('body').on('click', '#productAddSpecification', function (e) {
        e.preventDefault();
        const key = randomString();

        $(this).closest('.col-12').find('.no-result').addClass('d-none').removeClass('d-flex');

        let html = $('#newSpecificationForm').html();
        html = html.replaceAll('record', key);

        html = html.replaceAll('specification-select2', 'search-specification-select2-' + key);
        html = html.replaceAll('multi_values-select', 'multi_values-select2');

        html = html.replaceAll('input_tags', 'input_tags-' + key);

        $('#specificationsAccordion').prepend(html);

        handleSpecificationTags('input_tags-' + key);

        handleSpecificationSelect2('search-specification-select2-' + key);
    });

    $('body').on('click', '.js-save-specification', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $this.closest('.specification-form');

        let action = form.attr('data-action');

        handleProductItemForm(form, $this, action);
    });

    $('body').on('click', '.cancel-accordion', function (e) {
        e.preventDefault();

        $(this).closest('.col-12').find('.no-result').removeClass('d-none').addClass('d-flex');

        $(this).closest('.accordion-row').remove();
    });

    $('body').on('click', '#productAddFAQ', function (e) {
        e.preventDefault();

        $(this).closest('.col-12').find('.no-result').addClass('d-none').removeClass('d-flex');

        const key = randomString();

        let html = $('#newFaqForm').html();
        html = html.replaceAll('record', key);

        $('#faqsAccordion').prepend(html);
    });

    $('body').on('click', '.js-save-faq', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $this.closest('.faq-form');

        let action = form.attr('data-action');

        handleProductItemForm(form, $this, action);
    });

    $('body').on('click', '#saveAndPublish', function (e) {
        e.preventDefault();
        $('#productStatusInput').val('active');
        $('#productForm').trigger('submit');
    });

    $('body').on('click', '#saveReject', function (e) {
        e.preventDefault();
        $('#productStatusInput').val('inactive');
        $('#productForm').trigger('submit');
    });

    $('body').on('change', '#categories', function (e) {
        e.preventDefault();
        let category_id = this.value;
        $.get(adminPanelPrefix + '/store/products/filters/get-by-category-id/' + category_id, function (result) {

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
                        });
                    }

                    html += '</div></div>';
                });

                $('#categoriesFiltersContainer').removeClass('d-none');
                $('#categoriesFiltersCard').html(html);
            } else {
                $('#categoriesFiltersContainer').addClass('d-none');
                $('#categoriesFiltersCard').html('');
            }
        });
    });

    function handleGetFiltersTitleFromTranslations(translations, defaultLocale) {
        let title = null;

        if (Object.keys(translations).length) {
            Object.keys(translations).forEach(key => {
                const translation = translations[key];

                if (translation.locale === defaultLocale) {
                    title = translation.title;
                }
            });

            if (!title) {
                title = translations[0].title;
            }
        }

        return title;
    }

    $('body').on('change', '.js-product-content-locale', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $(this).closest('.js-content-form');
        const locale = $this.val();
        const productId = $this.attr('data-product-id');
        const item_id = $this.attr('data-id');
        const relation = $this.attr('data-relation');
        let fields = $this.attr('data-fields');
        fields = fields.split(',');


        $this.addClass('loadingbar gray');

        const path = adminPanelPrefix + '/store/products/' + productId + '/getContentItemByLocale';
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
                        let elKey = key;

                        if (relation === 'selectedSpecifications') {
                            elKey = 'tags';

                            if (item.type === 'textarea') {
                                elKey = 'summary';
                            }
                        }

                        let element = $form.find('.js-ajax-' + elKey);

                        if (elKey === 'tags') {
                            element.tagsinput('destroy');
                        }

                        element.val(value);

                        if (elKey === 'tags') {
                            const randomClass = 'tags-' + randomString();

                            element.addClass(randomClass)
                            handleSpecificationTags(randomClass);
                        }
                    }
                });

                $this.removeClass('loadingbar gray');
            }
        }).fail(err => {
            $this.removeClass('loadingbar gray');
        });
    });

    $('body').on('change', '.js-ajax-file_type', function (e) {
        e.preventDefault();

        const $form = $(this).closest('form');
        //const source = formGroup.find('.js-file-storage').val();

        handleFileItemsByType($form);
    });

    function handleFileItemsByType($form) {
        const fileType = $form.find('.js-ajax-file_type').val();
        const $onlineViewerInput = $form.find('.js-online_viewer-input');

        if (fileType && (fileType === 'pdf')) {
            $onlineViewerInput.removeClass('d-none');
        } else {
            $onlineViewerInput.find('input').prop('checked', false);
            $onlineViewerInput.addClass('d-none');
        }
    }

})(jQuery);
