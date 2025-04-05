<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    {{-- <link rel="stylesheet" type="text/css" href="{{ url('/css/email.css') }}"> --}}
    <style type="text/css">
        /* /\/\/\/\/\/\/\/\/ CLIENT-SPECIFIC STYLES /\/\/\/\/\/\/\/\/ */
        #outlook a {
            padding: 0;
        }

        /* Force Outlook to provide a "view in browser" message */
        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        /* Force Hotmail to display emails at full width */
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
            line-height: 100%;
        }

        /* Force Hotmail to display normal line spacing */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        /* Prevent WebKit and Windows mobile changing default text sizes */
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        /* Remove spacing between tables in Outlook 2007 and up */
        img {
            -ms-interpolation-mode: bicubic;
        }

        /* Allow smoother rendering of resized image in Internet Explorer */
        /* /\/\/\/\/\/\/\/\/ RESET STYLES /\/\/\/\/\/\/\/\/ */
        body {
            margin: 0;
            padding: 0;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body, #bodyTable, #bodyCell {
            height: 100% !important;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        /* /\/\/\/\/\/\/\/\/ TEMPLATE STYLES /\/\/\/\/\/\/\/\/ */
        /* ========== Page Styles ========== */
        * {
            font-family: Tahoma, Geneva, sans-serif !important;
        }

        #bodyCell {
            padding: 0;
        }

        #templateContainer {
            width: 600px;
        }

        body, #bodyTable {
            background-color: #F5F6F7;
        }

        #bodyCell {
            border-top: 5px solid #43d477;
        }

        #templateContainer {
            border: 1px solid #BBBBBB;
        }

        h1 {
            display: block;
            font-size: 18px;
            font-style: normal;
            font-weight: bold;
            line-height: 100%;
            letter-spacing: normal;
            margin-top: 0;
            margin-right: 0;
            margin-bottom: 30px;
            margin-left: 0;
            text-align: center;
            color: #484848;
        }

        h2 {
            color: #404040 !important;
            display: block;
            font-size: 20px;
            font-style: normal;
            font-weight: bold;
            line-height: 100%;
            letter-spacing: normal;
            margin-top: 0;
            margin-right: 0;
            margin-bottom: 10px;
            margin-left: 0;
            text-align: left;
        }

        h3 {
            display: block;
            font-size: 16px;
            font-weight: bold;
            line-height: 160%;
            letter-spacing: normal;
            margin-top: 0;
            margin-right: 0;
            margin-bottom: 10px;
            margin-left: 0;
            color: #3b4144;
        }

        h4 {
            color: #808080 !important;
            display: block;
            font-size: 14px;
            font-style: italic;
            font-weight: normal;
            line-height: 100%;
            letter-spacing: normal;
            margin-top: 0;
            margin-right: 0;
            margin-bottom: 10px;
            margin-left: 0;
            text-align: left;
        }

        /* ========== Header Styles ========== */
        #templatePreheader {
            /* background-color:#F4F4F4;*/
            /* border-bottom:1px solid #CCCCCC;*/
        }

        .preheaderContent {
            color: #808080;
            font-size: 10px;
            line-height: 125%;
            text-align: right;
        }

        .preheaderContent a:link, .preheaderContent a:visited,
        .preheaderContent a .yshortcuts {
            color: #606060;
            font-weight: normal;
        }

        #templateHeader {
            border-top: 1px solid #FFFFFF;
            border-bottom: 1px solid #CCCCCC;
        }

        .headerContent {
            color: #505050;
            font-size: 20px;
            font-weight: bold;
            line-height: 100%;
            padding-top: 0;
            padding-right: 0;
            padding-bottom: 0;
            padding-left: 0;
            text-align: center;
            vertical-align: middle;
        }

        .headerContent a:link, .headerContent a:visited,
        .headerContent a .yshortcuts {
            color: #43d477;
            font-weight: normal;
        }

        #headerImage {
            height: auto;
            max-width: 600px;
        }

        /* ========== Body Styles ========== */
        #templateBody {
            /*

        @editable   border-top:1px solid #FFFFFF;*/
            /*

        @editable   border-bottom:1px solid #CCCCCC;*/
        }

        .bodyContent {
            color: #505050;
            font-size: 14px;
            line-height: 150%;
            padding-top: 30px;
            padding-right: 30px;
            padding-bottom: 30px;
            padding-left: 30px;
            text-align: left;
        }

        .bodyContent a:link, .bodyContent a:visited,
        .bodyContent a .yshortcuts {
            color: #43d477;
            font-weight: normal;
            text-decoration: none;
        }

        .bodyContent img {
            display: inline;
            height: auto;
            max-width: 560px;
        }

        /* ========== Footer Styles ========== */
        #templateFooter {
            /*

        @editable   border-top:1px solid #FFFFFF;*/
        }

        .footerContent {
            color: #999999;
            font-size: 14px;
            line-height: 150%;
            padding-top: 20px;
            padding-right: 20px;
            padding-bottom: 20px;
            padding-left: 20px;
            text-align: center;
        }

        .footerContent a:link, .footerContent a:visited,
        .footerContent a .yshortcuts, .footerContent a span {
            color: #999999;
            font-weight: normal;
            text-decoration: none;
        }

        /* /\/\/\/\/\/\/\/\/ MOBILE STYLES /\/\/\/\/\/\/\/\/ */
        @media only screen and (max-width: 480px) {
            /* /\/\/\/\/\/\/ CLIENT-SPECIFIC MOBILE STYLES /\/\/\/\/\/\/ */
            body, table, td, p, a, li, blockquote {
                -webkit-text-size-adjust: none !important;
            }

            /* Prevent Webkit platforms from changing default text sizes */
            body {
                width: 100% !important;
                min-width: 100% !important;
            }

            /* Prevent iOS Mail from adding padding to the body */
            /* /\/\/\/\/\/\/ MOBILE RESET STYLES /\/\/\/\/\/\/ */
            #bodyCell {
                padding: 0px !important;
            }

            /* /\/\/\/\/\/\/ MOBILE TEMPLATE STYLES /\/\/\/\/\/\/ */
            /* ======== Page Styles ======== */
            #templateContainer {
                max-width: 600px !important;
                width: 100% !important;
            }

            h1 {
                font-size: 20px !important;
                line-height: 120% !important;
            }

            h2 {
                font-size: 20px !important;
                line-height: 100% !important;
            }

            h3 {
                font-size: 18px !important;
            }

            h4 {
                font-size: 16px !important;
                line-height: 100% !important;
            }

            /* ======== Header Styles ======== */
            /*#templatePreheader{display:none !important;}*/
            /* Hide the template preheader to save space */
            #headerImage {
                height: auto !important;
                max-width: 600px !important;
                width: 100% !important;
            }

            .headerContent {
                font-size: 20px !important;
                line-height: 125% !important;
            }

            /* ======== Body Styles ======== */
            .bodyContent {
                font-size: 16px !important;
                line-height: 125% !important;
            }

            /* ======== Footer Styles ======== */
            .footerContent {
                font-size: 14px !important;
                line-height: 115% !important;
            }

            /* ======== Column Styles ======== */
            .templateColumnContainer {
                display: table !important;
                width: 100% !important;
            }

            .columnImage {
                height: auto !important;
                max-width: 480px !important;
                width: 100% !important;
            }

            .leftColumnContent {
                font-size: 16px !important;
                line-height: 125% !important;
            }

            .rightColumnContent {
                font-size: 16px !important;
                line-height: 125% !important;
            }

            .more {
                margin-bottom: 20px;
            }
        }

        .templateContainer {
            border: none !important;
        }

        .headerContent {
            padding-top: 20px;
            padding-right: 30px;
            padding-left: 30px;
        }

        .bodyContent {
            background-color: #fff;
            display: block;
            margin: 20px;
        }

        #templateContainer {
            border: none;
        }

        p {
            font-weight: 100;
            text-align: left;
            line-height: 1.7rem;
        }

        .btn {
            border: medium none;
            cursor: pointer;
            text-align: center;
            -webkit-transition: background 200ms ease 0s;
            transition: background 200ms ease 0s;
            background: #43d477;
            color: #ffffff !important;
            font-size: 1rem;
            margin-top: 40px;
            padding: 10px 0 12px 0;
            text-decoration: none !important;
            display: block;
            border-radius: 4px;
        }

        .code {
            text-align: center;
            color: #43d477;
            font-size: 32px;
            font-weight: bold;
        }

        .properties-wrapper {
            padding-top: 25px;
            padding-right: 15px;
            padding-left: 15px;
            padding-bottom: 0;
        }

        .post-wrapper {
            padding: 20px 10px 10px 10px !important;
        }

        /* ========== Column Styles ========== */
        .templateColumnContainer {
            width: 260px;
        }

        /**
        *
        @tab
        Columns
                *
        @
        section left column text
                *
        @tip
        Set the styling for your email's left column content text. Choose a size and color that is easy to read.
                */
        .leftColumnContent {
            color: #505050;
            font-size: 14px;
            line-height: 150%;
            padding-top: 0;
            padding-right: 20px;
            padding-bottom: 20px;
            padding-left: 20px;
            text-align: right;
        }

        .leftColumnContent a:link, .leftColumnContent a:visited,
        .leftColumnContent a .yshortcuts {
            font-weight: bold;
            text-decoration: none;
        }

        /**
        *
        @tab
        Columns
                *
        @
        section right column text
                *
        @tip
        Set the styling for your email's right column content text. Choose a size and color that is easy to read.
                */
        .rightColumnContent {
            color: #505050;
            font-size: 14px;
            line-height: 150%;
            padding-top: 0;
            padding-right: 20px;
            padding-bottom: 20px;
            padding-left: 20px;
        }

        /**
        *
        @tab
        Columns
                *
        @
        section right column link
                *
        @tip
        Set the styling for your email's right column content links. Choose a color that helps them stand out from your text.
                */
        .rightColumnContent a:link, .rightColumnContent a:visited,
        .rightColumnContent a .yshortcuts {
            font-weight: bold;
            text-decoration: none;
        }

        .leftColumnContent img, .rightColumnContent img {
            display: inline;
            height: auto;
            max-width: 260px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 22px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-info {
            color: #31708f;
            background-color: #d9edf7;
            border-color: #bce8f1;
        }
        .emailConfigCode {
            text-align: center;
            font-size: 25px;
        }

    </style>
      @if(!empty($generalSettings['site_name']))
    <title>{{ $generalSettings['site_name'] }}</title>
   @else
   <title>Platform Title</title>
   @endif
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
<center>
    <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
        <tr>
            <td align="center" valign="top" id="bodyCell">
                <!-- BEGIN TEMPLATE // -->
                <table border="0" cellpadding="0" cellspacing="0" id="templateContainer">
                    <tr>
                        <td align="center" valign="top">
                            <!-- BEGIN PREHEADER // -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templatePreheader">
                                <tr>
                                    <td class="headerContent" width="100%">
                                        <a href=""><img src="{{ url($generalSettings['logo']) }}" style="max-width:128px;margin-bottom: 8px;margin-top: 24px" id="headerImage campaign-icon" mc:label="header_image" mc:edit="header_image" mc:allowtext/></a>
                                    </td>
                                </tr>
                            </table>
                            <!-- // END PREHEADER -->
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <!-- BEGIN BODY // -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
                                <tr>
                                    @yield('body')

                                </tr>
                            </table>
                            <!-- // END BODY -->
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <!-- BEGIN FOOTER // -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateFooter">
                                <tr>
                                    <td valign="top" class="footerContent" style="padding-top:0; padding-bottom:40px;" mc:edit="footer_content02">
                                        @yield('cancel')
                                    </td>
                                </tr>
                                {{--   <tr>
                                       <td valign="top" class="footerContent" mc:edit="footer_content00">
                                           <a href="*|TWITTER:PROFILEURL|*">Follow on Twitter</a>&nbsp;&nbsp;&nbsp;<a href="*|FACEBOOK:PROFILEURL|*">Friend on Facebook</a>&nbsp;&nbsp;&nbsp;<a href="*|FORWARD|*">Forward to Friend</a>&nbsp;
                                       </td>
                                   </tr>--}}
                            </table>
                            <!-- // END FOOTER -->
                        </td>
                    </tr>
                </table>
                <!-- // END TEMPLATE -->
            </td>
        </tr>
    </table>
</center>
</body>
</html>
