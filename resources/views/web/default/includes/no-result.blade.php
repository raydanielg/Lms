<div class="no-result default-no-result mt-50 d-flex align-items-center justify-content-center flex-column">
    <div class="no-result-logo">
        <img src="/assets/default/img/no-results/{{ $file_name }}" alt="">
    </div>
    <div class="d-flex align-items-center flex-column mt-30 text-center">
        <h2 class="text-dark-blue">{{ $title }}</h2>
        <p class="mt-1 text-center text-gray font-weight-500">{!! $hint !!}</p>
        @if(!empty($btn))
            <a href="{{ $btn['url'] }}" class="btn btn-sm btn-primary mt-25">{{ $btn['text'] }}</a>
        @endif
    </div>
</div>
