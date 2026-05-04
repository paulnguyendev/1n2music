@php
    use App\Helpers\Template;
@endphp
@extends('public.main')
@section('title', 'Homepage')
@section('content')
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 0;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            height: 100%;

            background-color: rgba(0, 0, 0, 0.5);
            /* Màu nền với độ mờ */
            justify-content: center;
            align-items: center;
            z-index: 9999;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            /* Layer cao hơn để hiển thị trên cùng */
            width: 80%;
        }

        .popup-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
        }

        .close {
            float: right;
            cursor: pointer;
            color: #000;
            font-size: 38px
        }

        /* Hiển thị popup khi được mở */
        .popup.active {
            display: flex;
        }

        .contennt-bulletinboard p,
        .title-bulletinboard,
        .created_by-bulletinboard {
            color: #000
        }

        .title-bulletinboard,
        .created_by-bulletinboard {
            text-align: center;

        }
    </style>
    <div class="section section-hero-banner">
        <section class="section-hero">
            <div class="container">
                <div class="search-wrap">
                    <form action="{{ rrt_route('public/market/index') }}" class="form-search">
                        <button class="search-icon"><i class="far fa-search"></i></button>
                        <input type="hidden" name="genre" value="all">
                        <input type="text" name="search" placeholder="What are you looking for?">
                        <button type="button" class="search-type">
                            <span>All</span>
                            <i class="fal fa-angle-down"></i>
                        </button>
                        <div class="list-type">
                            @foreach ($genres as $genre)
                                <div data-id="{{ $genre->id }}" class="list-type-item">{{ $genre->name }} </div>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </section>
        @if ($slides)

            <section class="section-slide">
                <div class="list-slide-banner rrt-slider rrt-slick-slider" data-number-show="1" data-number-scroll="1"
                    data-fade = "true" data-autoplay="true" data-number-show-mobile = "1">
                    @foreach ($slides as $slide)
                        <a href="{{ $slide->link ?? '#' }}">
                            <div class="slide-banner-item">
                                <img src="{{ rrt_get_url_image_upload('banner', $slide->image ?? '') }}" alt="">
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

        @endif

    </div>


    <section class="section-tracks section-trending">
        <div class="container container-gap">
            <h2 class="title-main">{{ __('home.trending') }}</h2>
            <div class="list-track lazy-content list-skeleton"
                data-url="{{ rrt_route($controllerName . '/tracks', ['type' => 'trending']) }}" data-skeleton="4"
                id="trackTrending">
                <div class="card-skeleton">
                    <div class="image-skeleton"></div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-tracks section-recommend">
        <div class="container container-gap">
            <h2 class="title-main">Recommend</h2>
            <div class="list-track lazy-content list-skeleton " data-skeleton="4"
                data-url="{{ rrt_route($controllerName . '/tracks', ['type' => 'recommend']) }}" id="trackRecommend">
                <div class="card-skeleton">
                    <div class="image-skeleton"></div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-producers">
        <div class="container container-gap">
            <h2 class="title-main">Producers</h2>
            <div class="list-producer lazy-content list-skeleton" data-skeleton="4"
                data-url="{{ rrt_route($controllerName . '/users') }}" id="listProducer">
                <div class="card-skeleton">
                    <div class="image-skeleton"></div>
                    <div class="title-skeleton"></div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-genres">
        <div class="container container-gap">
            <h2 class="title-main">Genres</h2>
            <div class="list-generes lazy-content list-skeleton " data-skeleton="4"
                data-url="{{ rrt_route($controllerName . '/genres') }}" id="listGenres">
                <div class="card-skeleton">
                    <div class="image-skeleton"></div>
                    <div class="title-skeleton"></div>
                </div>
            </div>
        </div>
    </section>
    <section @isset($banner->link)
        data-url="{{ $banner->link }}"
    @endisset class="section-cta"
        id="mainpage-banner" style="background: url('/public/uploads/banner/{{ $banner->image }}') no-repeat">
        <div class="container container-gap text-center">
            <h2 class="title-main">Are you ready to start your online music career</h2>
            <div class="cta-desc">Join 1N2 Music right now and start selling your music to the world.</div>
            <div class="cta-list-btn">
                <a href="{{ rrt_route('public/market/index') }}" class="btn btn-primary">BROWSE BEATS</a>
                <a href="{{ rrt_route('public/join/sellBeats/index') }}" class="btn btn-default">START SELLING</a>
                <a href="{{ rrt_route('public/join/distribution/index') }}" class="btn btn-primary">DISTRIBUTE</a>
                <a href="{{ rrt_route('public/join/publishing/index') }}" class="btn btn-primary">PUBLISH</a>
            </div>
        </div>
    </section>
    <section class="section-related-content">
        <div class="container container-gap">
            <h2 class="title-main">Related Content</h2>
            <div class="list-youtube  rrt-slider rrt-slick-slider" data-number-show="4" data-number-scroll="1">

                @foreach ($relate_contents as $relate_content)
                    <a href="{{ $relate_content->url_youtube ?? '#' }}">
                        <div class="youtube-ittem">
                            <div class="youtube-inner">
                                <img src="{{ asset('public/images/youtube-thumb.jpg') }}" class="youtube-thumb">
                                <div class="youtube-time">9:45</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    <section class="section-blog">
        <div class="container container-gap">
            <h2 class="title-main">Bulletin Board</h2>
            <div class="blog-wrap">
                @foreach ($bulletins as $item)
                    <div class="blog-inner ">
                        <div class="list-blog">
                            @foreach ($item as $bulletin)
                                <a target="_blank"
                                    href="{{ rrt_route('public/bulletinboard/detailBulletionBoard', ['id' => $bulletin->id]) }}">
                                    <div class="blog-item open-popup" data-id="{{ $bulletin->id }}">
                                        <img src="/public/uploads/bulletins/{{ $bulletin->image }}" class="blog-thumb">
                                        <div class="blog-text">
                                            <h3 class="blog-item"><a target="_blank"
                                                    href="{{ rrt_route('public/bulletinboard/detailBulletionBoard', ['id' => $bulletin->id]) }}">{{ $bulletin->name }}</a>
                                            </h3>
                                            <div class="blog-desc">
                                                {{ $bulletin->description }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                {{-- <div class="pop-up">
                                    <div class="popup" id="popup-{{ $bulletin->id }}">
                                        <div class="popup-content">
                                            <span class="close" data-id="{{ $bulletin->id }}"
                                                id="closePopup-{{ $bulletin->id }}">&times;</span>
                                            <h2 class="title-bulletinboard">{{ $bulletin->name }}</h2>
                                            <p class="created_by-bulletinboard">Created by
                                                {{ $bulletin->users->fullname ?? '' }} <i
                                                    class="fa fa-clock">{{ \Carbon\Carbon::parse($bulletin->created_at)->format('M j, Y') }}</i>
                                            </p>
                                            <div class="contennt-bulletinboard">
                                                {!! $bulletin->content !!}
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @push('srcipt')
        <script>
            // document.getElementById('openPopup').addEventListener('click', function() {
            //     document.getElementById('popup').classList.add('active');
            // });

            // document.getElementById('closePopup').addEventListener('click', function() {
            //     document.getElementById('popup').classList.remove('active');
            // });

            // $('.open-popup').click(function(e) {
            //     e.preventDefault();
            //     let id = $(this).data('id');

            //     $('#popup-' + id).addClass('active')
            // });
            // $('.close').click(function(e) {
            //     e.preventDefault();
            //     let id = $(this).data('id');

            //     $('#popup-' + id).removeClass('active')

            // });
            $(document).ready(function() {
                $('#mainpage-banner').click(function() {
                    let url = $(this).data('url');
                    window.location.href = url;
                })
            });
        </script>
    @endpush
@endsection
