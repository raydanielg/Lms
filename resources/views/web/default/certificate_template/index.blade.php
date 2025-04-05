<html lang="fa">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $pageTitle ?? '' }}</title>

    <style>

        @font-face {
            font-family: 'fontFamily';
            src: url({{ public_path('/store/pdf_fonts/DejaVuSans.ttf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }

        html, body {
            background-image: url("{{ $image }}");
            background-repeat: no-repeat;
            background-size: cover;
            margin: 0;
            border: initial;
            border-radius: initial;
            page-break-after: always;
            font-size: 14px;
            font-weight: 400;
            font-family: 'fontFamily'
        }

        @page {
            size: A4;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            font-family: 'fontFamily' !important;
        }
    </style>
</head>

<body id="app">
{!! $body !!}
</body>
