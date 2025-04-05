(function () {
    "use strict";

    $('body').on('change', '#contentType', function () {
        const value = $(this).val();
        $('.js-content-fields').addClass('d-none');

        $(`.js-field-${value}`).removeClass('d-none');
    })

    function handleModal(path) {
        loadingSwl();

        $.get(path, function (result) {
            if (result && result.html) {

                Swal.fire({
                    html: result.html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '30rem',
                    onOpen: () => {
                        handleSearchableSelect2('modal-search-webinar-select2', adminPanelPrefix + '/webinars/search', 'title');
                        handleSearchableSelect2('modal-search-bundle-select2', adminPanelPrefix + '/bundles/search', 'title');
                        handleSearchableSelect2('modal-search-product-select2', adminPanelPrefix + '/store/products/search', 'title');
                        handleSearchableSelect2('modal-search-blog-select2', adminPanelPrefix + '/blog/search', 'title');
                        handleSearchableSelect2('modal-search-upcoming-course-select2', adminPanelPrefix + '/upcoming_courses/search', 'title');
                    }
                });
            }
        })
    }


    $('body').on('click', '.js-add-content', function (e) {
        e.preventDefault();

        const path = $(this).attr("data-path");
        handleModal(path)
    });

    $('body').on('click', '.js-edit-content', function (e) {
        e.preventDefault();

        const path = $(this).attr("href");
        handleModal(path)
    });

    $('body').on('click', '.js-save-content', function (e) {
        e.preventDefault()

        const $this = $(this);
        let form = $this.closest('.content-form');

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
                    title: result.title,
                    html: '<p class="font-16 text-center text-gray py-2">' + result.msg + '</p>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                setTimeout(() => {
                    window.location.reload();
                }, 1500);
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


})(jQuery)
