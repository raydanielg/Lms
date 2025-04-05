<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="w-100 h-100">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $pageTitle ?? '' }}{{ !empty($generalSettings['site_name']) ? (' | '.$generalSettings['site_name']) : '' }}</title>
    <link rel="stylesheet" href="/assets/default/css/app.css">
</head>

<body class="w-100 h-100">
    <div class="w-100 h-100" id="meet"></div>


</body>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
    "use strict"

    function jitsiMeeting() {
        const meeting_id = "{{ $session->id }}";

        const domain = '{{ getFeaturesSettings("jitsi_live_url") }}';
        const options = {
            roomName: meeting_id,
            role: '{{ $role }}',
            parentNode: document.querySelector('#meet'),

            userInfo: {
                email: "{{ $authUser->email }}",
                displayName: "{{ $authUser->full_name }}",
                role: '{{ $role }}',
            }
        }

        const api = new JitsiMeetExternalAPI(domain, options);
    }

    window.onload = jitsiMeeting();
</script>

