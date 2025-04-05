(function ($) {
    "use strict";

    const style = getComputedStyle(document.body);
    const primaryColor = style.getPropertyValue('--primary');

    function updateToDatabase(idString) {
        $.post(adminPanelPrefix + '/settings/personalization/statistics/sort', {items: idString}, function (result) {

            $.toast({
                heading: result.title,
                text: result.msg,
                bgColor: primaryColor,
                textColor: 'white',
                hideAfter: 10000,
                position: 'bottom-right',
                icon: 'success'
            });
        }).fail(err => {
            $.toast({
                heading: "Error",
                bgColor: '#f63c3c',
                textColor: 'white',
                hideAfter: 5000,
                position: 'bottom-right',
                icon: 'error'
            });
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

                    updateToDatabase(sortData.join(','));
                }
            });
        }
    }

    var target = $('.draggable-lists');
    setSortable(target);

    $('body').on('change', '#display_default_statisticsSwitch', function (e) {
        e.preventDefault();

        const $input = $('.js-custom-statistics');

        if (this.checked) {
            $input.addClass('d-none');
        } else {
            $input.removeClass('d-none');
        }
    });

    function getModalData(path) {
        loadingSwl();

        $.get(path, function (result) {
            if (result.code === 200) {
                Swal.fire({
                    html: result.html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '36rem',
                    onOpen: () => {
                        $(".colorpickerinput").colorpicker({
                            format: 'hex',
                            component: '.input-group-append',
                        });
                    }
                });
            }
        })
    }

    $('body').on('click', '.js-add-statistics, .js-edit-statistic', function () {
        const path = $(this).attr('data-path');

        getModalData(path)
    })

    $('body').on('change', '.js-statistic-locale', function () {
        const form = $(this).closest('#addStatisticItemForm');
        let path = form.attr('data-action')
        path = path.replaceAll('updateItem', 'editItem')
        path = path + "?locale=" + $(this).val()

        getModalData(path)
    })

    $('body').on('click', '.js-save-statistic', function () {
        const $this = $(this)
        const $form = $('#addStatisticItemForm')

        let data = serializeObjectByTag($form);
        let action = $form.attr('data-action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        $form.find('input').removeClass('is-invalid');
        $form.find('textarea').removeClass('is-invalid');

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

            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('.js-ajax-' + key);

                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    })

})(jQuery);
