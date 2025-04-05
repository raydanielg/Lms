
<div class="top-navbar d-flex border-bottom">
    <div class="container d-flex justify-content-between flex-column flex-lg-row">
        <a class="navbar-brand navbar-order mr-0 d-flex align-items-center justify-content-center" href="/">
            @if(!empty($generalSettings['logo']))
                <img src="{{ $generalSettings['logo'] }}" class="img-cover" alt="site logo">
            @endif
        </a>

        <div class="top-contact-box border-bottom d-flex flex-column flex-md-row align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center">
                @if(!empty($generalSettings['site_phone']))
                    <span class="d-flex align-items-center py-10 py-lg-0 text-dark-blue font-14">
                        <i data-feather="phone" width="20" height="20" class="mr-10"></i>
                        {{ $generalSettings['site_phone'] }}
                    </span>
                @endif

                @if(!empty($generalSettings['site_email']))
                    <div class="border-left mx-5 mx-lg-15 h-100"></div>

                    <span class="d-flex align-items-center py-10 py-lg-0 text-dark-blue font-14">
                        <i data-feather="mail" width="20" height="20" class="mr-10"></i>
                        {{ $generalSettings['site_email'] }}
                    </span>
                @endif
            </div>

            {{-- User Menu --}}
            @include('web.default.includes.top_nav.user_menu')

        </div>
    </div>
</div>
