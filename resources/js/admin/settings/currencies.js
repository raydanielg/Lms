(function () {
    "use strict"

    $('body').on('click', '#add_multi_currency', function (e) {
        e.preventDefault();
        var multiCurrencyModal = $('#multiCurrencyModal');
        var clone = multiCurrencyModal.clone();

        let copyHtml = clone.prop('innerHTML');
        copyHtml = copyHtml.replaceAll('js-select2', "js-select22");
        clone.html(copyHtml);

        Swal.fire({
            html: clone.html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '36rem',
            onOpen: function () {
                $('.js-select22').select2()
            }
        });
    })

    $('body').on('click', '.save-currency', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $this.closest('.currency-form');
        let data = serializeObjectByTag(form);
        let action = form.attr('data-action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        form.find('input').removeClass('is-invalid');
        form.find('textarea').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
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
                    let element = form.find('.js-ajax-' + key);
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    });

    $('body').on('click', '.js-edit-currency', function (e) {
        e.preventDefault();
        const $this = $(this);
        const path = $this.attr('data-path');

        loadingSwl();

        $.get(path, function (result) {
            if (result && result.html) {
                let $html = '<div id="editCurrency">' + result.html + '</div>';

                Swal.fire({
                    html: $html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '36rem',
                    onOpen: () => {
                        const editModal = $('#editCurrency');
                        editModal.find('.js-select2').select2()
                    }
                });
            }
        })
    });

    $('body').on('change', '#multiCurrencySwitch', function () {
        const $section = $('.js-multi-currency-section');

        if (this.checked) {
            $section.removeClass('d-none')
        } else {
            $section.addClass('d-none')
        }
    })

    $(document).ready(function () {
        const style = getComputedStyle(document.body);
        const primaryColor = style.getPropertyValue('--primary');

        function updateToDatabase(table, idString) {
            $.post(adminPanelPrefix + '/settings/financial/currency/order-items', {table: table, items: idString}, function (result) {
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

        setSortable($('.draggable-currency-lists'));
    })

})(jQuery)
