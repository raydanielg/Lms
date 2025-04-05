<div class="no-result default-no-result mt-5 d-flex align-items-center justify-content-center flex-column">
    <div class="no-result-logo {{ !empty($noResultSmLogo) ? 'sm-logo' : '' }}">
        <img src="/assets/default/img/no-results/{{ $file_name }}" alt="">
    </div>
    <div class="d-flex align-items-center flex-column mt-3 text-center">
        <h2>{{ $title }}</h2>
        <p class="mt-1 text-center">{!! $hint !!}</p>
        @if(!empty($btn))
            <a href="{{ $btn['url'] }}" class="btn btn-primary mt-2">{{ $btn['text'] }}</a>
        @endif
    </div>
</div>
