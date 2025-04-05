<script src="//pay.voguepay.com/js/voguepay.js"></script>

<script>
    closedFunction = function () {
        location.href = '{{ $closedUrl }}'
    }

    successFunction = function (transaction_id) {
        location.href = '{{ $successUrl }}' + '&transaction_id=' + transaction_id
    }

    failedFunction = function (transaction_id) {
        location.href = '{{ $failedUrl }}' + '&transaction_id=' + transaction_id
    }
</script>
@if ($test_mode)
    <input type="hidden" id="merchant_id" name="v_merchant_id" value="demo">
@else
    <input type="hidden" id="merchant_id" name="v_merchant_id" value="{{ $voguepay_merchant_id }}">
@endif

<script>

    window.onload = function () {
        pay3();
    }

    function pay3() {
        Voguepay.init({
            v_merchant_id: document.getElementById("merchant_id").value,
            total: '{{ $total_amount }}',
            cur: '{{ $currency }}',
            merchant_ref: 'ref123',
            memo: 'Payment',
            developer_code: '5a61be72ab323',
            store_id: 1,
            loadText: 'Custom load text',

            customer: {
                name: '{{ $userData['name'] }}',
                address: '{{ $userData['address'] }}',
                city: '{{ $userData['city'] }}',
                state: 'Customer state',
                zipcode: '{{ $userData['postcode'] }}',
                email: '{{ $userData['email'] }}',
                phone: '{{ $userData['phone'] }}'
            },
            closed: closedFunction,
            success: successFunction,
            failed: failedFunction
        });
    }
</script>
