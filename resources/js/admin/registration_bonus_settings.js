(function () {
    "use strict";

    $('body').on('change', '#unlock_registration_bonus_instantlySwitch', function () {
        const $withReferralSwitch = $('.js-unlock-registration-bonus-with-referral-field');
        const $enableReferredUsersPurchaseSwitch = $('.js-enable-referred-users-purchase-field');
        const $purchaseAmountUnlockingBonusField = $('.js-purchase-amount-for-unlocking-bonus-field');
        const $input = $('.js-number-of-referred-users-field');

        if (this.checked) {
            $withReferralSwitch.addClass('d-none');
            $enableReferredUsersPurchaseSwitch.addClass('d-none');
            $purchaseAmountUnlockingBonusField.addClass('d-none');
            $input.addClass('d-none');

            $withReferralSwitch.find('input').prop('checked', false);
            $enableReferredUsersPurchaseSwitch.find('input').prop('checked', false);
            $purchaseAmountUnlockingBonusField.find('input').val('');
            $input.find('input').val('');
        } else {
            $withReferralSwitch.removeClass('d-none');
            $enableReferredUsersPurchaseSwitch.removeClass('d-none');
        }
    });

    $('body').on('change', '#unlock_registration_bonus_with_referralSwitch', function () {
        const $input = $('.js-number-of-referred-users-field');
        const $enableReferredUsersPurchaseSwitch = $('.js-enable-referred-users-purchase-field');
        const $purchaseAmountUnlockingBonusField = $('.js-purchase-amount-for-unlocking-bonus-field');

        $input.find('input').val('');
        $enableReferredUsersPurchaseSwitch.find('input').prop('checked', false);
        $purchaseAmountUnlockingBonusField.find('input').val('');

        if (this.checked) {
            $input.removeClass('d-none');
            $enableReferredUsersPurchaseSwitch.removeClass('d-none');

        } else {
            $input.addClass('d-none');
            $enableReferredUsersPurchaseSwitch.addClass('d-none');
            $purchaseAmountUnlockingBonusField.addClass('d-none');
        }
    });

    $('body').on('change', '#enable_referred_users_purchaseSwitch', function () {
        const $purchaseAmountUnlockingBonusField = $('.js-purchase-amount-for-unlocking-bonus-field');

        $purchaseAmountUnlockingBonusField.find('input').val('');

        if (this.checked) {
            $purchaseAmountUnlockingBonusField.removeClass('d-none');
        } else {
            $purchaseAmountUnlockingBonusField.addClass('d-none');
        }
    });
})(jQuery);
