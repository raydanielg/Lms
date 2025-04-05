@extends(getTemplate().'.layouts.app')

@section('content')
    @php
        $get404ErrorPageSettings = get404ErrorPageSettings();
    @endphp

    <section class="my-50 container text-center">
        <div class="row justify-content-md-center">
            <div class="col col-md-6">
                <img src="{{ $get404ErrorPageSettings['error_image'] ?? '' }}" class="img-cover " alt="">
            </div>
        </div>

        <h2 class="mt-25 font-36">{{ $get404ErrorPageSettings['error_title'] ?? '' }}</h2>
        <p class="mt-25 font-16">{{ $get404ErrorPageSettings['error_description'] ?? '' }}</p>
    </section>
@endsection
