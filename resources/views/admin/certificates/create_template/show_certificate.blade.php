@php
    $rtlLanguages = !empty($generalSettings['rtl_languages']) ? $generalSettings['rtl_languages'] : [];
    $isRtl = ((in_array(mb_strtoupper(app()->getLocale()), $rtlLanguages)) or (!empty($generalSettings['rtl_layout']) and $generalSettings['rtl_layout'] == 1));

    $certificateLtrFont = getCertificateMainSettings('ltr_font');
    $certificateRtlFont = getCertificateMainSettings('rtl_font');
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>

        @if(!empty($certificateLtrFont))
            @font-face {
                font-family: 'ltr-font-name';
                font-style: normal;
                font-weight: 400;
                font-display: swap;
                src: url({{ url($certificateLtrFont) }});
            }
        @endif

        @if(!empty($certificateRtlFont))
            @font-face {
                font-family: 'rtl-font-name';
                font-style: normal;
                font-weight: 400;
                font-display: swap;
                src: url({{ url($certificateRtlFont) }});
            }
        @endif

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'ltr-font-name';
        }

        body.rtl {
            direction: rtl;
            font-family: 'rtl-font-name';
        }

        .certificate-template-container {
            width: {{ \App\Models\CertificateTemplate::$templateWidth }}px;
            height: {{ \App\Models\CertificateTemplate::$templateHeight }}px;
            position: relative;
            border: 2px solid #000;
            background-repeat: no-repeat;
            background-size: contain;
        }

        .certificate-template-container .draggable-element {
            position: absolute !important;
            display: inline-block;
            white-space: pre-wrap;

        }



    </style>
</head>
<body class="{{ $isRtl ? 'rtl' : '' }}">

        {!! $body !!}

</body>
</html>
