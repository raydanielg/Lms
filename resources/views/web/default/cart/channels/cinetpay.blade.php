<html>
<head>
    <title>Cinetpay Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .message {
            text-align: center;
            margin-top: 16px;
            font-size: 15px;
            color: #222;
        }

        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite; /* Safari */
            animation: spin 2s linear infinite;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>

<div class="loader"></div>
<div class="message">{{ trans('update.you_are_being_redirected_to_the_payment_page_please_wait_a_moment') }}</div>

{!! $cinetpay !!}

<script>
    var payBtn = document.getElementById('payBtn');
    payBtn.click();
</script>
</body>
</html>

