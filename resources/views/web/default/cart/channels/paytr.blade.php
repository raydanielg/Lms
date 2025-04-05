<html>
<head>
    <title>Paytr Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            width: 100%;
            min-height: 100vh;
            margin: 0;
        }

        iframe {
            width: 100% !important;
            height: 100% !important;
            border: none !important;
        }
    </style>
</head>
<body>
<iframe src="{{ $iframUrl }}" frameborder="0"></iframe>
</body>
</html>

