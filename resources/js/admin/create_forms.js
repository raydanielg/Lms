(function () {
    "use strict";


    $(document).ready(function () {
        const style = getComputedStyle(document.body);
        const primaryColor = style.getPropertyValue('--primary');

        function updateToDatabase(path, idString) {
            console.log(idString)
            $.post(path, {items: idString}, function (result) {
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
            })
        }

        function setSortable(target, moveClass) {
            if (target.length) {
                target.sortable({
                    group: 'no-drop',
                    handle: '.' + moveClass,
                    axis: "y",
                    update: function (e, ui) {
                        var sortData = target.sortable('toArray', {attribute: 'data-id'});
                        var path = e.target.getAttribute('data-path');

                        updateToDatabase(path, sortData.join(','))
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
                const moveClass = tag.attr('data-move-class')

                if (tag.length) {
                    setSortable(tag, moveClass);
                }
            }
        }
    });


    $('body').on('change', '#enableLoginSwitch', function () {
        const $card = $('.js-enable-login-fields');

        if (this.checked) {
            $card.removeClass('d-none')
        } else {
            $card.addClass('d-none')
        }
    });

    $('body').on('change', '#enable_welcome_messageSwitch', function () {
        const $card = $('.js-enable-welcome-message-fields');

        if (this.checked) {
            $card.removeClass('d-none')
        } else {
            $card.addClass('d-none')
        }
    });

    $('body').on('change', '#enable_tank_you_messageSwitch', function () {
        const $card = $('.js-enable-tank-you-message-fields');

        if (this.checked) {
            $card.removeClass('d-none')
        } else {
            $card.addClass('d-none')
        }
    });

    $('body').on('change', '.js-form-field-type', function () {
        const value = $(this).val();

        const $titleCard = $('.js-form-field-title-card');
        const $optionsCard = $('.js-field-options');

        $titleCard.addClass('d-none');
        $optionsCard.addClass('d-none');

        if (value) {
            $titleCard.removeClass('d-none')
        }

        if ($.inArray(value, ['dropdown', 'checkbox', 'radio']) !== -1) {
            $optionsCard.removeClass('d-none');
        }
    });

    $('body').on('click', '.js-add-form-field', function (e) {
        e.preventDefault();
        const newFormField = $('#newFormField');
        const key = randomString();

        let html = newFormField.html();

        html = html.replace(/record/g, key);

        $("#formFieldsCard").prepend(html);
    })

    $('body').on('click', '.js-save-form-field', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $this.closest('.js-field-form');

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
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + fieldSaveSuccessLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }
        }).fail(function (err) {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;

            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = form.find('.js-ajax-' + key);

                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    })

    $('body').on('click', '.add-field-option-btn', function (e) {
        e.preventDefault();
        const key = randomString();
        const $parent = $(this).closest('.js-field-options');

        let html = `<li class="form-group list-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text cursor-pointer move-icon2">
                                                        <i class="fa fa-arrows-alt"></i>
                                                    </div>
                                                </div>

                                                <input type="text" name="ajax[options][${key}][title]"
                                                       class="form-control w-auto flex-grow-1"
                                                       placeholder="${chooseTitleLang}"/>

                                                <div class="input-group-append">
                                                    <button type="button" class="btn remove-field-option-btn btn-danger"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                        </li>`;


        $parent.find('.js-field-options-lists').append(html);
    });

    $('body').on('click', '.remove-field-option-btn', function (e) {
        e.preventDefault();
        $(this).closest('.form-group').remove();
    });


    function handleGetTitleFromTranslations(translations, defaultLocale) {
        let title = null;

        if (Object.keys(translations).length) {
            Object.keys(translations).forEach(key => {
                const translation = translations[key];

                if (translation.locale === defaultLocale) {
                    title = translation.title
                }
            })
        }

        return title;
    }


    $('body').on('change', '.js-form-field-locale', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $(this).closest('.js-field-form');
        const locale = $this.val();
        const path = $this.attr('data-path') + '?locale=' + locale;

        $this.addClass('loadingbar gray');

        $.get(path, function (result) {
            $this.removeClass('loadingbar gray');

            if (result.code === 200) {
                let field = result.field;
                const locale = result.locale;

                let fieldTitle = '';
                if (field.translations) {
                    fieldTitle = handleGetTitleFromTranslations(field.translations, locale);
                }

                $form.find(`.js-title-field-${field.id}`).val(fieldTitle)

                if (field.options && field.options.length) {
                    Object.keys(field.options).forEach(index => {
                        let option = field.options[index];

                        let optionTitle = '';
                        if (option.translations) {
                            optionTitle = handleGetTitleFromTranslations(option.translations, locale);
                        }

                        $form.find(`.js-title-option-${option.id}`).val(optionTitle)
                    });
                }

            }
        }).fail(err => {
            $this.removeClass('loadingbar gray');
        });

    });


    /* feather icons */
    // **
    // **
    feather.replace();
})(jQuery);
