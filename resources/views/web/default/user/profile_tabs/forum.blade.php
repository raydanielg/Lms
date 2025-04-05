@if(!empty($forumTopics) and !$forumTopics->isEmpty())
    <div class="px-15 py-20">

        @foreach($forumTopics as $topic)
            <div class="topics-lists-card row align-items-center py-10">
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="topic-user-avatar rounded-circle">
                            <img src="{{ $user->getAvatar() }}" class="img-cover rounded-circle" alt="{{ $user->full_name }}">
                        </div>
                        <div class="ml-10 mw-100">
                            <a href="{{ $topic->getPostsUrl() }}" class="">
                                <h4 class="font-16 font-weight-bold text-secondary text-ellipsis">{{ $topic->title }}</h4>
                            </a>
                            <span class="d-block font-14 text-gray">{{ trans('public.by') }} {{ $user->full_name }} {{ trans('public.in') }} {{ dateTimeFormat($topic->created_at,'j M Y | H:i') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="row">
                        <div class="col-3 text-center">
                            <span class="d-block font-14 text-gray font-weight-bold">{{ $topic->posts_count }}</span>
                            <span class="d-block font-12 text-gray">{{ trans('site.posts') }}</span>
                        </div>
                        <div class="col-3 d-flex align-items-center">
                            @if($topic->pin)
                                <div class="topics-lists-card__icons rounded-circle mr-10">
                                    <img src="/assets/default/img/learning/un_pin.svg" alt="" class="img-cover rounded-circle">
                                </div>
                            @endif

                            @if($topic->close)
                                <div class="topics-lists-card__icons rounded-circle">
                                    <img src="/assets/default/img/learning/lock.svg" alt="" class="img-cover rounded-circle">
                                </div>
                            @endif
                        </div>
                        <div class="col-12 col-md-6">
                            @if(!empty($topic->lastPost))
                                <div class="d-flex align-items-center">
                                    <div class="topic-last-post-user-avatar rounded-circle">
                                        <img src="{{ $topic->lastPost->user->getAvatar(30) }}" class="img-cover rounded-circle" alt="{{ $topic->lastPost->user->full_name }}">
                                    </div>
                                    <div class="ml-10">
                                        <h4 class="font-14 font-weight-500 text-gray">{{ $topic->lastPost->user->full_name }}</h4>
                                        <span class="d-block font-12 font-weight-500 text-gray">{{ trans('public.in') }} {{ dateTimeFormat($topic->lastPost->created_at,'j M Y | H:i') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    @include(getTemplate() . '.includes.no-result',[
        'file_name' => 'webinar.png',
        'title' => trans('update.instructor_not_have_topics'),
        'hint' => '',
    ])
@endif

