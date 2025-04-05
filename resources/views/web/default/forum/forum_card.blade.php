<div class="row align-items-center my-15">
    <div class="col-12 col-md-6">
        <div class="d-flex align-items-center">
            <div class="forums-categories-card__icon p-5">
                <img src="{{ $forum->icon }}" alt="{{ $forum->title }}" class="img-cover">
            </div>
            <div class="ml-10">
                <a href="{{ $forum->getUrl() }}" class="d-block">
                    <div class="font-14 text-secondary font-weight-bold">{{ $forum->title }}</div>
                </a>
                <p class="font-12 text-gray mt-5">{{ $forum->description }}</p>
            </div>
        </div>
    </div>

    <div class="col-4 col-md-2 mt-10 mt-md-0 d-flex align-items-center justify-content-around">
        <div class="text-center">
            <span class="d-block font-14 text-gray font-weight-bold">{{ $forum->topics_count }}</span>
            <div class="d-block font-12 text-gray">{{ trans('update.topics') }}</div>
        </div>

        <div class="text-center">
            <span class="d-block font-14 text-gray font-weight-bold">{{ $forum->posts_count }}</span>
            <div class="d-block font-12 text-gray">{{ trans('site.posts') }}</div>
        </div>
    </div>

    <div class="col-8 col-md-4 mt-10 mt-md-0 forums-categories-card__last-post d-flex align-items-center">
        @if(!empty($forum->lastTopic))
            <div class="user-avatar rounded-circle">
                <img src="{{ $forum->lastTopic->creator->getAvatar(39) }}" class="img-cover rounded-circle" alt="{{ $forum->lastTopic->creator->full_name }}">
            </div>

            <div class="ml-5">
                <a href="{{ $forum->lastTopic->getPostsUrl() }}" class="d-block">
                    <span class="font-12 font-weight-500 text-gray text-ellipsis">{{ truncate($forum->lastTopic->title,30) }}</span>
                </a>
                <div class="text-gray font-12"><span class="font-weight-bold">{{ $forum->lastTopic->creator->full_name }}</span> {{ trans('public.in') }} {{ dateTimeFormat($forum->lastTopic->created_at,'j M Y | H:i') }}</div>
            </div>
        @endif
    </div>
</div>
