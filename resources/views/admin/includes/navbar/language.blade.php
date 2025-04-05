@php
    $userLanguages = !empty($generalSettings['site_language']) ? [$generalSettings['site_language'] => getLanguages($generalSettings['site_language'])] : [];

    if (!empty($generalSettings['user_languages']) and is_array($generalSettings['user_languages'])) {
        $userLanguages = getLanguages($generalSettings['user_languages']);
    }

    $localLanguage = [];

    foreach($userLanguages as $key => $userLanguage) {
        $localLanguage[localeToCountryCode($key)] = $userLanguage;
    }

@endphp

@if(!empty($localLanguage) and count($localLanguage) > 1)
    <form action="/locale" method="post" class="mr-2 mr-md-3 mb-0 admin-navbar-locale">
        {{ csrf_field() }}

        <input type="hidden" name="locale">

        @if(!empty($previousUrl))
            <input type="hidden" name="previous_url" value="{{ $previousUrl }}">
        @endif

        <div class="language-select">
            <div id="localItems"
                 data-selected-country="{{ localeToCountryCode(mb_strtoupper(app()->getLocale())) }}"
                 data-countries='{{ json_encode($localLanguage) }}'
            ></div>
        </div>
    </form>
@else
    <div class="mr-2 mx-md-3"></div>
@endif

@push('scripts_bottom')
    <link href="/assets/default/vendors/flagstrap/css/flags.css" rel="stylesheet">
    <script src="/assets/default/vendors/flagstrap/js/jquery.flagstrap.min.js"></script>
    <script src="/assets/default/js/parts/top_nav_flags.min.js"></script>
@endpush
