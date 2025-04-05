<html>
<head>
    <title>Mercado Checkout Pro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite; /* Safari */
            animation: spin 2s linear infinite;
            margin: auto;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>
<body>
<div class="cho-container"style="display: none;"></div>
<br>
<br>

<script>
    // Add the SDK credentials
    const mp = new MercadoPago('{{ $public_key }}', {
        locale: 'en-US',
        advancedFraudPrevention:true,
    });

    // Initialize the checkout
    mp.checkout({
        preference: {
            id: '{{ $preference_id }}'
        },
        autoOpen: true,
        render: {
            container: ".cho-container", // Indica el nombre de la clase donde se mostrará el botón de pago
            label: "Pagar", // Cambia el texto del botón de pago (opcional)
        },
    });
</script>
</body>
</html>
