(function () {
    "use strict";

    var $rewardSettingModal = $('#rewardSettingModal');

    $('body').on('click', '.js-add-new-reward', function (e) {
        $rewardSettingModal.modal('show');
    });

    $('body').on('click', '.js-save-reward', function (e) {
        e.preventDefault();

        const $this = $(this);
        $this.addClass('loadingbar gray').prop('disabled', true);

        const $form = $rewardSettingModal.find('form');
        const data = $form.serializeObject();

        $.post(adminPanelPrefix + '/rewards/items', data, function (result) {
            Swal.fire({
                icon: 'success',
                html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveSuccessLang + '</h3>',
                showConfirmButton: false,
                width: '25rem',
            });

            setTimeout(() => {
                window.location.reload();
            }, 500);
        }).fail(err => {
            $this.removeClass('loadingbar gray').prop('disabled', false);

            var errors = err.responseJSON;
            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        });
    });

    $('body').on('click', '.js-edit-reward', function (e) {
        const $this = $(this);
        const id = $this.attr('data-id');
        const $form = $rewardSettingModal.find('form');

        removeAppendedInput();

        loadingSwl();

        $.get(`${adminPanelPrefix}/rewards/items/${id}`, function (result) {
            if (result && result.reward) {
                const {reward} = result;

                $rewardSettingModal.find('input[name="score"]').val(reward.score);
                $rewardSettingModal.find('select[name="type"]').val(reward.type);
                $rewardSettingModal.find('input[name="condition"]').val(reward.condition);
                $rewardSettingModal.find('input[name="status"]').prop('checked', (reward.status === 'active'));

                $form.prepend(`<input type="hidden" name="reward_id" value="${id}"/>`);

                handleVisibilityInputsByType(reward.type);

                $rewardSettingModal.modal('show');

                setTimeout(() => {
                    Swal.close();
                }, 500)
            }
        });
    });


    $('body').on('change', '#rewardSettingModal select[name="type"]', function () {
        const value = $(this).val();

        handleVisibilityInputsByType(value)
    });

    function handleVisibilityInputsByType(type) {
        const scoreInput = $('.js-score-input');
        const conditionInput = $('.js-condition-input');

        scoreInput.removeClass('d-none');
        if (type === 'badge') {
            scoreInput.addClass('d-none');
        }

        conditionInput.addClass('d-none');
        if ($.inArray(type, ['charge_wallet', 'account_charge', 'buy', 'buy_store_product']) !== -1) {
            conditionInput.removeClass('d-none');
        }
    }

    function removeAppendedInput() {
        const input = $rewardSettingModal.find('input[name="reward_id"]');

        if (input) {
            input.remove();
        }
    }
})(jQuery);
