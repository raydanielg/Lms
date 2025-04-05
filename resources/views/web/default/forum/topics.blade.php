@extends('web.default.layouts.app')

@section('content')
    <div class="container">
        <section class="topics-title-section mt-30 mt-md-50 px-20 px-md-30 py-25 py-md-35 rounded-lg">
            <h1 class="font-30 font-weight-bold text-white">{{ $forum->title }}</h1>
            <p class="font-14 text-white">{{ $forum->description }}</p>

            <div class="mt-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb p-0 m-0">
                        <li class="breadcrumb-item font-12 text-white"><a href="/" class="text-white">{{ getGeneralSettings('site_name') }}</a></li>
                        <li class="breadcrumb-item font-12 text-white"><a href="/forums" class="text-white">{{ trans('update.forum') }}</a></li>
                        <li class="breadcrumb-item font-12 text-white font-weight-bold" aria-current="page">{{ $forum->title }}</li>
                    </ol>
                </nav>
            </div>
        </section>

        <div class="topics-filters-section bg-white rounded-lg px-20 py-25 mt-40">
            <div class="row">
                <div class="col-12 col-md-5">
                    <h3 class="font-16 font-weight-bold text-secondary">{{ trans('update.topics_in_this_forum',['count' => $topics->count()]) }}</h3>

                    <div class="d-flex align-items-center mt-5">
                        <a href="{{ request()->url() }}?sort=newest" class="font-14 font-weight-500 mr-20 {{ (empty(request()->get('sort')) or request()->get('sort') == 'newest') ? 'text-primary' : 'text-gray' }}">{{ trans('public.newest') }}</a>
                        <a href="{{ request()->url() }}?sort=popular_topics" class="font-14 font-weight-500 mr-20 {{ (request()->get('sort') == 'popular_topics') ? 'text-primary' : 'text-gray' }}">{{ trans('update.popular_topics') }}</a>
                        <a href="{{ request()->url() }}?sort=not_answered" class="font-14 font-weight-500 {{ (request()->get('sort') == 'not_answered') ? 'text-primary' : 'text-gray' }}">{{ trans('update.not_answered') }}</a>
                    </div>
                </div>

                <div class="col-12 col-md-7  mt-15 mt-lg-0">
                    <div class="row">
                        <div class="col-12 col-lg-7">
                            <form action="" method="get">
                                <div class="d-flex align-items-center">
                                    <input type="text" name="search" value="{{ request()->get('search') }}" class="form-control flex-grow-1 input-search-topic" placeholder="{{ trans('update.search_in_this_forum') }}">
                                    <button type="submit" class="btn btn-sm btn-primary btn-search-topic ml-10">
                                        <i data-feather="search" class="text-white" width="20" height="20"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-12 col-lg-5 mt-15 mt-lg-0 text-right">
                            @if($forum->close)
                                <button type="button" class="btn btn-primary btn-create-topic disabled btn-block" disabled>
                                    <i data-feather="file" class="text-white" width="16" height="16"></i>
                                    <span class="ml-1">{{ trans('update.forum_closed') }}</span>
                                </button>
                            @else
                                <a href="/forums/create-topic{{ $forum->checkUserCanCreateTopic() ? '?forum_id='.$forum->id : '' }}" class="btn btn-primary btn-create-topic btn-block">
                                    <i data-feather="file" class="text-white" width="16" height="16"></i>
                                    <span class="ml-1">{{ trans('update.create_a_new_topic') }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-20">
            <div class="row">
                <div class="col-12 col-md-9">
                    @if($forum->checkUserCanCreateTopic())
                        @if(!empty($topics) and count($topics))
                            <div class="rounded-lg px-15 py-20 border bg-white">

                                @foreach($topics as $topic)
                                    <div class="topics-lists-card row align-items-center py-10">
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="topic-user-avatar rounded-circle">
                                                    <img src="{{ $topic->creator->getAvatar() }}" class="img-cover rounded-circle" alt="{{ $topic->creator->full_name }}">
                                                </div>
                                                <div class="ml-10 mw-100">
                                                    <a href="{{ $topic->getPostsUrl() }}" class="">
                                                        <h4 class="font-14 font-weight-bold text-secondary text-ellipsis">{{ $topic->title }}</h4>
                                                    </a>
                                                    <span class="d-block font-12 mt-5 text-gray">{{ trans('public.by') }} {{ $topic->creator->full_name }} {{ trans('public.in') }} {{ dateTimeFormat($topic->created_at,'j M Y | H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 d-none d-md-block">
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
                                                                <h4 class="font-12 font-weight-500 text-gray">{{ $topic->lastPost->user->full_name }}</h4>
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

                            <div class="mt-20">
                                {{ $topics->appends(request()->input())->links('vendor.pagination.panel') }}
                            </div>
                        @else
                            <div class="topics-not-result d-flex align-items-center justify-content-center flex-column">
                                <div class="topics-not-result-icon d-flex align-items-center justify-content-center">
                                    <img src="/assets/default/img/learning/forum-empty.svg" class="img-fluid" alt="">
                                </div>

                                <div class="d-flex align-items-center flex-column mt-10 text-center">
                                    <h3 class="font-20 font-weight-bold text-dark-blue text-center">{{ trans('update.result_not_found') }}</h3>
                                    <p class="font-14 font-weight-500 text-gray mt-5 text-center">{{ trans('update.try_another_word_to_reach_results') }}</p>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="topics-not-result d-flex align-items-center justify-content-center flex-column">
                            <div class="topics-not-result-icon d-flex align-items-center justify-content-center">
                                <img src="/assets/default/img/learning/forum-empty.svg" class="img-fluid" alt="">
                            </div>

                            <div class="d-flex align-items-center flex-column mt-10 text-center">
                                <h3 class="font-20 font-weight-bold text-dark-blue text-center">{{ trans('update.result_not_found') }}</h3>
                                <p class="font-14 font-weight-500 text-gray mt-5 text-center">{{ trans('update.you_not_access_to_this_forum') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-12 col-md-3">
                    @if(!empty($topUsers) and count($topUsers))
                        <div class="rounded-lg p-15 border bg-white">
                            <h3 class="topics-right-side-title position-relative font-16 text-dark font-weight-bold mb-25">{{ trans('update.top_users') }}</h3>

                            @foreach($topUsers as $topUser)
                                @if(!empty($topUser->all_posts))
                                    <div class="d-flex align-items-center mt-15">
                                        <div class="topics-right-side-user-avatar rounded-circle">
                                            <img src="{{ $topUser->getAvatar(48) }}" class="img-cover rounded-circle" alt="{{ $topUser->full_name }}">
                                        </div>
                                        <div class="ml-10">
                                            <a href="{{ $topUser->getProfileUrl() }}" class="d-block">
                                                <span class="font-14 font-weight-500 text-secondary">{{ $topUser->full_name }}</span>
                                            </a>
                                            <span class="d-block font-12 font-weight-500 text-gray">{{ trans('update.n_posts',['count' => $topUser->posts]) }} | {{ trans('update.n_topics',['count' => $topUser->topics]) }}</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($popularTopics) and count($popularTopics))
                        <div class="rounded-lg p-15 border bg-white mt-20">
                            <h3 class="topics-right-side-title position-relative font-16 text-dark font-weight-bold mb-25">{{ trans('update.popular_topics') }}</h3>

                            @foreach($popularTopics as $popularTopic)
                                <div class="d-flex align-items-center mt-15">
                                    <div class="topics-right-side-user-avatar rounded-circle">
                                        <img src="{{ !empty($popularTopic->creator) ? $popularTopic->creator->getAvatar(48) : '' }}" class="img-cover rounded-circle" alt="{{ !empty($popularTopic->creator) ? $popularTopic->creator->full_name : '' }}">
                                    </div>
                                    <div class="ml-10">
                                        <a href="{{ $popularTopic->getPostsUrl() }}" class="d-block pb-5">
                                            <span class="font-14 font-weight-500 text-secondary">{{ $popularTopic->title }}</span>
                                        </a>
                                        <span class="d-block font-12 font-weight-500 text-gray">{{ trans('public.by') }} {{ $popularTopic->creator->full_name }} | {{ trans('update.n_posts',['count' => $popularTopic->posts_count]) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
