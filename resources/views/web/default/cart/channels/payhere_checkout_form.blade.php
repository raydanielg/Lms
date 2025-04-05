

<form style="display: none" method="POST" action="{{ $action_url }}" id="payhere-checkout-form">
    <input type="hidden" name="merchant_id" value="{{ $merchant_id }}">
    <!-- Replace your Merchant ID -->
    <input type="hidden" name="return_url" value="{{ $return_url }}">
    <input type="hidden" name="cancel_url" value="{{ $cancel_url }}">
    <input type="hidden" name="notify_url" value="{{ $notify_url }}">
    <br><br>Custom Params<br>
    <input type="text" name="custom_1" value="{{ $order_id }}">
    <input type="text" name="custom_2" value="">
    <br><br>Item Details<br>
    <input type="text" name="order_id" value="{{ $order_id }}">
    <input type="text" name="items" value="{{ trans("checkout_payment")  }}"><br>
    <input type="text" name="currency" value="{{ $currency }}">
    <input type="text" name="amount" value="{{ $amount }}">
    <br><br>Customer Details<br>
    <input type="text" name="first_name" value="{{ $first_name }}">
    <input type="text" name="last_name" value="{{ $last_name }}"><br>
    <input type="text" name="email" value="{{ $email }}">
    <input type="text" name="phone" value="{{ $phone }}"><br>
    <input type="text" name="address" value="{{ $address }}">
    <input type="text" name="city" value="{{ $city }}">
    <input type="hidden" name="country" value="Sri Lanka"><br><br>
    <input type="submit" value="Buy Now">

</form>


<script type="text/javascript">
    var payhere_checkout_form =  document.getElementById('payhere-checkout-form');
    payhere_checkout_form.submit();
</script>
