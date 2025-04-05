(function ($) {
    "use strict";

    /* dropdown */
    // **
    // **
    $('.dropdown-toggle').dropdown();

    /**
     * close swl
     * */
    $('body').on('click', '.close-swl', function (e) {
        e.preventDefault();
        Swal.close();
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    // ********************************************
    // ********************************************
    // select 2
    window.resetSelect2 = () => {
        if (jQuery().select2) {
            $(".select2").select2({
                width: '100%',
            });
        }
    };
    resetSelect2();

    /*
    * loading Swl
    * */
    window.loadingSwl = () => {
        const loadingHtml = '<div class="d-flex align-items-center justify-content-center my-50 "><img src="/assets/default/img/loading.gif" width="80" height="80"></div>';
        Swal.fire({
            html: loadingHtml,
            showCancelButton: false,
            showConfirmButton: false,
            width: '30rem',
        });
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
                    const timeout = result.timeout ?? 1000;

                    setTimeout(() => {
                        if (typeof result.redirect_to !== "undefined" && result.redirect_to !== undefined && result.redirect_to !== null && result.redirect_to !== '') {
                            window.location.href = result.redirect_to;
                        } else {
                            window.location.reload();
                        }
                    }, timeout);
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

    // ********************************************
    // ********************************************
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

    $('.accordion-row').on('shown.bs.collapse', function () {
        var icon = $(this).find('.collapse-chevron-icon:first');
        icon.removeClass('feather-chevron-down');
        icon.addClass('feather-chevron-up');
    });
    $('.accordion-row').on('hidden.bs.collapse', function () {
        var icon = $(this).find('.collapse-chevron-icon:first');
        icon.removeClass('feather-chevron-up');
        icon.addClass('feather-chevron-down');
    });

    $('body').on('change', '#userLanguages', function (e) {
        $(this).closest('form').trigger('submit');
    });

    /*
    * Handle ajax FORBIDDEN requests
    * */
    $(document).on('ajaxError', function (event, xhr) {
        if (xhr.status === 401 || xhr.status === 403) {
            $.toast({
                heading: forbiddenRequestToastTitleLang,
                text: forbiddenRequestToastMsgLang,
                bgColor: '#f63c3c',
                textColor: 'white',
                hideAfter: 10000,
                position: 'bottom-right',
                icon: 'error'
            });
        }
    });


    /*
    * // handle limited account modal
    * */
    window.handleLimitedAccountModal = function (html, size = 30) {
        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            width: size + 'rem',
        });
    };

    window.randomString = function (count = 5) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < count; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    };


    if (jQuery().summernote) {
        makeSummernote($(".main-summernote"))
    }

    var $advertisingModalSettings = $('#advertisingModalSettings');

    if ($advertisingModalSettings && $advertisingModalSettings.length) {
        Swal.fire({
            html: $advertisingModalSettings.html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '36rem',

        });
    }


    $('body').on('click', '.btn-add-product-to-cart', function (e) {
        e.preventDefault();

        const item_id = $(this).attr('data-id');

        const html = `
            <form action="/cart/store" method="post" class="" id="productAddToCartForm">
                <input type="hidden" name="_token" value="${window.csrfToken}">
                <input type="hidden" name="item_id" value="${item_id}">
                <input type="hidden" name="item_name" value="product_id">
            </form>
        `;

        $('body').append(html);

        $(this).addClass('loadingbar primary').prop('disabled', true);

        const $form = $('#productAddToCartForm');

        $form.trigger('submit');
    });

    $('body').on('change', 'input[type="file"].custom-file-input', function () {
        const value = this.value;

        if (value) {
            const splited = value.split('\\');

            if (splited.length) {
                $(this).closest('.custom-file').find('.custom-file-label').text(splited[splited.length - 1])
            }
        }
    })

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

    window.lockBodyScroll = function (lock) {
        const root = document.getElementsByTagName('html')[0];

        if (lock) {
            root.classList.add('close-body-scroll');
        } else {
            root.classList.remove('close-body-scroll');
        }
    }


    $('body').on('click', '.js-copy', function (e) {
        e.preventDefault();

        const $this = $(this);
        const inputName = $this.attr('data-input');
        const copyText = $this.attr('data-copy-text');
        const doneText = $this.attr('data-done-text');

        const input = $this.closest('.form-group').find('input[name="' + inputName + '"]');

        input.removeAttr('disabled');
        input.focus();
        input.select();
        document.execCommand("copy");
        navigator.clipboard.writeText(input.val());

        $this.attr('data-original-title', doneText)
            .tooltip('show');
        $this.attr('data-original-title', copyText);
    });

    $('body').on('click', '.js-has-subcategory', function (e) {

        if(window.innerWidth < 992) {
            e.preventDefault();
        }
    })


    /* feather icons */
    // **
    // **
    feather.replace();

})(jQuery);

