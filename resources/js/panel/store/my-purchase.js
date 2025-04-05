(function () {
    "use strict";


    $('body').on('click', '.js-view-tracking-code', function () {
        const $this = $(this);
        const saleId = $this.attr('data-sale-id');
        const orderId = $this.attr('data-product-order-id');

        const path = `/panel/store/purchases/${saleId}/getProductOrder/${orderId}`;

        $.get(path, function (result) {
            if (result && result.order) {

                const html = `
                    <div>
                        <h3 class="section-title after-line font-20 text-dark-blue mb-25">${viewTrackingCodeModalTitleLang}</h3>

                        <div class="mt-15 w-100">
                            <div class="">
                                <span class="font-weight-500 mr-5">${trackingCodeLang} :</span>
                                <span class="font-14 text-gray">${result.order.tracking_code}</span>
                            </div>

                            <div class="mt-15">
                                <span class="font-weight-500 mr-5">${addressLang} :</span>
                                <span class="font-14 text-gray">${result.order.address}</span>
                            </div>
                        </div>

                        <div class="mt-30 d-flex align-items-center justify-content-end">
                            ${
                    (result.shipping_tracking_url && result.shipping_tracking_url !== '' && result.shipping_tracking_url !== 'null') ?
                        `
                                            <div class="mr-20">
                                                <a href="${result.shipping_tracking_url}" class="btn btn-primary btn-sm">${shippingTrackingUrlLang}</a>
                                            </div>
                                           `
                        : ''
                }

                            <button type="button" class="btn btn-danger btn-sm close-swl">${closeLang}</button>
                        </div>
                    </div>
                `;

                Swal.fire({
                    html: html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '40rem',
                });
            }
        });
    });

    $('body').on('click', '.js-got-the-parcel', function () {
        const $this = $(this);
        const saleId = $this.attr('data-sale-id');
        const orderId = $this.attr('data-product-order-id');

        const path = `/panel/store/purchases/${saleId}/productOrder/${orderId}/setGotTheParcel`;

        const html = `
                    <div>
                        <h3 class="section-title after-line font-20 text-dark-blue mb-25">${gotTheParcelLang}</h3>

                        <div class="mt-15 w-100">
                            <p class="">${gotTheParcelConfirmTextLang}</p>
                        </div>

                        <div class="mt-30 d-flex align-items-center justify-content-end">
                            <button type="button" id="saveGotTheParcel" data-action="${path}" class="btn btn-sm btn-primary">${confirmLang}</button>
                            <button type="button" class="btn btn-danger btn-sm close-swl ml-10">${closeLang}</button>
                        </div>
                    </div>
                `;

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '32rem',
        });
    });

    $('body').on('click', '#saveGotTheParcel', function (e) {
        e.preventDefault();
        const $this = $(this);
        const path = $this.attr('data-action');

        $this.addClass('loadingbar primary').prop('disabled', true);

        $.get(path, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + gotTheParcelSaveSuccessLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                Swal.fire({
                    icon: 'error',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + gotTheParcelSaveErrorLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });
            }
        });
    });

    $('body').on('click', '.js-show-product-files', function (e) {
        e.preventDefault();
        const path = $(this).attr('href');

        loadingSwl();

        $.get(path, function (result) {
            if (result && result.code === 200) {

                const html = `
                    <div>
                        <h3 class="section-title after-line font-20 text-dark-blue mb-25">${filesLang}</h3>

                        <div class="mt-15 w-100">${result.html}</div>

                        <div class="mt-30 d-flex align-items-center justify-content-end">
                            <button type="button" class="btn btn-danger btn-sm close-swl ml-10">${closeLang}</button>
                        </div>
                    </div>
                `;


                Swal.fire({
                    html: html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '32rem',
                    onOpen: () => {
                        feather.replace();
                    }
                });
            }
        })
    })

})(jQuery);
