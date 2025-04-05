(function () {
    "use strict";

    function makeEnterTrackingCodeModalHtml(order, saleId) {
        return `
            <div>
                <h3 class="section-title after-line font-20 text-dark-blue mb-25">${enterTrackingCodeModalTitleLang}</h3>

                <form action="/panel/store/sales/${saleId}/productOrder/${order.id}/setTrackingCode" method="post">

                    <div class="mt-15 w-100">
                        <div class="">
                            <span class="font-weight-500 mr-5">${addressLang} :</span>
                            <span class="font-14 text-gray">${order.address}</span>
                        </div>
                    </div>

                    <div class="form-group mt-20">
                        <label class="input-label">${trackingCodeLang}</label>
                        <input type="text" name="tracking_code" class="form-control" placeholder=""/>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mt-30 d-flex align-items-center justify-content-end">
                        <button type="button" id="saveTrackingCode" class="btn btn-sm btn-primary">${saveLang}</button>
                        <button type="button" class="btn btn-sm btn-danger ml-2 close-swl">${closeLang}</button>
                    </div>
                </form>
            </div>
        `;
    }

    $('body').on('click', '.js-enter-tracking-code', function () {
        const $this = $(this);
        const saleId = $this.attr('data-sale-id');
        const orderId = $this.attr('data-product-order-id');

        const path = `/panel/store/sales/${saleId}/getProductOrder/${orderId}`;

        $.get(path, function (result) {
            if (result && result.order) {

                Swal.fire({
                    html: makeEnterTrackingCodeModalHtml(result.order, saleId),
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

    $('body').on('click', '#saveTrackingCode', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $this.closest('form');

        let action = form.attr('action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        form.find('input').removeClass('is-invalid');

        let data = form.serializeObject();

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + trackingCodeSaveSuccessLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;

            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = form.find('[name="' + key + '"]');

                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        });
    });
})(jQuery);
