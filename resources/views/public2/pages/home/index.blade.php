@php
    use App\Helpers\Template;
    use App\Helpers\Link;
@endphp
@extends('public2.main')
@push('css')
  
@endpush
@section('content')
    <section class="section-slide">
        @if ($slides)
            <div class="container desktop-slides">
                <div class="slide-hero rrt-slick-slider" data-number-show="1" data-dots='true' data-arrows="false"
                    data-number-show-mobile="1">
                    @forelse($slides as $slide)
                        <div class="slide-hero-item">
                            <img src="{{ url('') }}/public/uploads/banner/{{ $slide['image'] }}" alt="">
                        </div>
                    @empty
                        <div class="slide-hero-item">
                            <img src="{{ asset('/public/images/default-desktop.png') }}" alt="">
                        </div>
                    @endforelse
                </div>
                <div class="slide-search">
                    <div class="slide-search-input">
                        <form action="{{ rrt_route('public/market/index') }}">
                            <img src="{{ asset('public/style2/img/icon_search.svg') }}" alt="">
                            <input type="text"
                                placeholder="{{ __('Explore new sound - search for beats and producer') }}" name="search">
                        </form>
                    </div>
                    <div class="slide-search-category">
                        <ul class="list-none">
                            <li><a href="">{{ __('All') }}</a></li>
                            @foreach ($genres as $genre)
                                <li><a href="{{ rrt_route('public/market/index', ['genres' => $genre->id ?? '']) }}">
                                        {{ __($genre->name ?? '') }} </a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tablet Slides -->
        @if ($tabletSlides)
            <div class="container tablet-slides">
                <div class="slide-hero rrt-slick-slider" data-number-show="1" data-dots='true' data-arrows="false"
                    data-number-show-mobile="1">
                    @forelse($tabletSlides as $slide)
                        <div class="slide-hero-item">
                            <img src="{{ url('') }}/public/uploads/banner/{{ $slide['image'] }}" alt="">
                        </div>
                    @empty
                        <div class="slide-hero-item">
                            <img src="{{ asset('/public/images/default-tablet.png') }}" alt="">
                        </div>
                    @endforelse
                </div>
                <div class="slide-search">
                    <div class="slide-search-input">
                        <form action="{{ rrt_route('public/market/index') }}">
                            <img src="{{ asset('public/style2/img/icon_search.svg') }}" alt="">
                            <input type="text"
                                placeholder="{{ __('Explore new sound - search for beats and producer') }}" name="search">
                        </form>
                    </div>
                    <div class="slide-search-category">
                        <ul class="list-none">
                            <li><a href="">{{ __('All') }}</a></li>
                            @foreach ($genres as $genre)
                                <li><a href="{{ rrt_route('public/market/index', ['genres' => $genre->id ?? '']) }}">
                                        {{ __($genre->name ?? '') }} </a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Mobile Slides -->
        @if ($mobileSlides)
            <div class="container mobile-slides">
                <div class="slide-hero rrt-slick-slider" data-number-show="1" data-dots='true' data-arrows="false"
                    data-number-show-mobile="1">
                    @forelse($mobileSlides as $slide)
                        <div class="slide-hero-item">
                            <img src="{{ url('') }}/public/uploads/banner/{{ $slide['image'] }}" alt="">
                        </div>
                    @empty
                        <div class="slide-hero-item">
                            <img src="{{ asset('/public/images/default-mobile.png') }}" alt="">
                        </div>
                    @endforelse
                </div>
                <div class="slide-search">
                    <div class="slide-search-input">
                        <form action="{{ rrt_route('public/market/index') }}">
                            <img src="{{ asset('public/style2/img/icon_search.svg') }}" alt="">
                            <input type="text"
                                placeholder="{{ __('Explore new sound - search for beats and producer') }}" name="search">
                        </form>
                    </div>
                    <div class="slide-search-category">
                        <ul class="list-none">
                            <li><a href="">{{ __('All') }}</a></li>
                            @foreach ($genres as $genre)
                                <li><a href="{{ rrt_route('public/market/index', ['genres' => $genre->id ?? '']) }}">
                                        {{ __($genre->name ?? '') }} </a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </section>
    <section class="section-track-all">
        <div class="container">
            <div class="track-all-inner">
                <div class="track-all-left">
                    <div class="track-list-wrap">
                        <div class="section-title-wrap">
                            <h2 class="section-title">
                                <img src="{{ asset('public/style2/img/solar_cup-music-linear.svg') }}" alt="">
                                <span>{{ __('Trending') }}</span>
                            </h2>
                            <a href="">{{ __('See more') }}</a>
                        </div>
                        <div class="track-list-items">
                            @if ($trendings)
                                @foreach ($trendings as $trending)
                                    @include('public2.globals.track-item', ['item' => $trending])
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="track-list-wrap">
                        <div class="section-title-wrap">
                            <h2 class="section-title">
                                <img src="{{ asset('public/style2/img/icon_recommend.svg') }}" alt="">
                                <span>{{ __('Recommend') }}</span>
                            </h2>
                            <a href="">{{ __('See more') }}</a>
                        </div>
                        <div class="track-list-items">
                            @if ($recommendeds)
                                @foreach ($recommendeds as $recommended)
                                    @include('public2.globals.track-item', ['item' => $recommended])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="track-all-right">
                    <div class="track-filter-wrap">
                        <div class="track-producer-wrap">
                            <div class="section-title-wrap">
                                <h2 class="section-title">
                                    <img src="{{ asset('public/style2/img/fluent_mic-sparkle-16-regular.svg') }}"
                                        alt="">
                                    <span>{{ __('Producers') }}</span>
                                </h2>
                                <a href="{{ rrt_route('public/producers/index') }}">{{ __('See more') }}</a>
                            </div>
                            @if ($users)
                                <div class="track-list-producer rrt-slick-slider rrt-slider " data-number-show="5">
                                    @foreach ($users as $user)
                                        @php
                                            $thumbnail = $user['thumbnail'] ?? '';
                                            $thumbnailUrl = $thumbnail ? url("public/uploads/users/{$thumbnail}") : '';
                                            $thumbnailUrl = rrt_show_thumbnail($thumbnailUrl);
                                            $userId = $user->id ?? '';
                                            $username = $user->username ?? '';
                                            $username = preg_replace('/[^A-Za-z0-9]/', '', $username);
                                            $route_detail = rrt_route('public/producers/detail', [
                                                'user_id' => $userId,
                                                'username' => $username,
                                            ]);
                                            $fullname = rrt_get_fullname_by_user($user);
                                        @endphp
                                        <div class="list-producer-item">
                                            <div class="producer-thumb">
                                                <a href="{{ $route_detail }}"> <img src="{{ $thumbnailUrl }}"
                                                        alt=""></a>
                                            </div>
                                            <div class="producer-text">
                                                <h3 class="limit-text limit-1"><a href="{{ $route_detail }}">
                                                        {{ $fullname }} </a></h3>
                                                <p class="limit-text limit-1"> {{ $username }} </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="track-genres-wrap">
                            <div class="section-title-wrap">
                                <h2 class="section-title">
                                    <img src="{{ asset('public/style2/img/icon_recommend.svg') }}" alt="">
                                    <span>{{ __('Related Videos') }}</span>
                                </h2>
                                {{--                                <a href="">{{ __('See more') }}</a> --}}
                            </div>
                            @if ($relate_contents)
                                <div class="list-track-genres row-3 list-related-contents">
                                    @php
                                        function getYoutubeVideoId($url)
                                        {
                                            preg_match('/[?&]v=([^&]+)/', $url, $matches);
                                            return $matches[1] ?? '';
                                        }
                                    @endphp
                                    @foreach ($relate_contents as $relate_content)
                                        @php
                                            $youtubeUrl = $relate_content['url_youtube'] ?? '';
                                            $videoId = getYoutubeVideoId($youtubeUrl);
                                            $thumbnailUrl = $videoId
                                                ? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg"
                                                : asset('public/images/no-image.png');
                                        @endphp
                                        <div class="track-genres-item" style="text-align: center"
                                            data-video="{{ $relate_content['url_youtube' ?? ''] }}">
                                            <img class="content_thumbnail" src="{{ $thumbnailUrl }}" alt="">
                                            <button class="video-btn"><i class="fab fa-youtube"></i></button>
                                            <span class="content-relate"> {{ $relate_content->name ?? '-' }} </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="videoModal" class="modal" style="justify-content: center; align-items: center">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="embed-container">
                <iframe id="videoPlayer" src="" title="YouTube video player" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
        </div>
    </div>
    @php
        $bannerUrl = asset('public/uploads/banner/' . $banner['image'] ?? null);
        $bannerTabletUrl = asset('public/uploads/banner/' . $tabletBanner['image'] ?? null);
        $bannerMobileUrl = asset('public/uploads/banner/' . $mobileBanner['image'] ?? null) ;
    @endphp
    @if ($banner && !$tabletBanner && !$mobileBanner)
        <style>
            .section-cta {
                background-image: {{ $bannerUrl }};
            }
        </style>
    @elseif ($tabletBanner && !$mobileBanner)
        <style>
            @media (min-width: 1024px) {
                .section-cta {
                    background-image: {{ $bannerUrl }};
                }
            }

            @media (max-width: 1023px) {
                .section-cta {
                    background-image: {{$bannerTabletUrl}}
                }
            }
        </style>
    @else
        <style>
            @media (min-width: 1024px) {
                .section-cta {
                    background-image: {{ $bannerUrl }};
                }
            }

            @media (min-width: 768px) and (max-width: 1023px) {
                .section-cta {
                    background-image: {{$bannerTabletUrl}}
                }
            }

            @media (max-width: 767px) {
                .section-cta {
                    background-image:  {{$bannerMobileUrl}};
                }
            }
        </style>
    @endif
    <section class="section-cta">
        <div class="container">
            <div class="cta-inner">
                <h2 class="title-main">{{ __('Are you ready to start your online music career') }}</h2>
                <div class="cta-desc">
                    <p>{{ __('Join 1N2 Music right now and start selling your music to the world.') }}</p>
                </div>
                <div class="cta-buttons">
                    <a href="{{ rrt_route('public/market/index') }}">{{ __('Browse Beats') }}</a>
                    @if (!rrt_check_login())
                        <a
                            href="{{ rrt_route('public/auth/signUp', ['start_selling' => 'true']) }}">{{ __('Start Selling') }}</a>
                    @endif
                    <a href="{{ rrt_route('public/join/distribution/index') }}">{{ __('Digital Distribution') }}</a>
                    <a href="{{ rrt_route('public/join/publishing/index') }}">{{ __('Publishing') }}</a>
                </div>
            </div>
        </div>
    </section>
    <section class="section-bulletin-board">
        <div class="container">
            <div class="section-title-wrap">
                <h2 class="section-title">
                    <img src="{{ asset('public/style2/img/hugeicons_news.svg') }}" alt="">
                    <span>{{ __('Bulletin Board') }}</span>
                </h2>
                <a href="{{ rrt_route('public/threads/index') }}">{{ __('See more') }}</a>
            </div>
            @if ($bulletins)
                <div class="list-bulletin-board rrt-slick-slider rrt-slider" data-number-show="4"
                    data-number-show-mobile="1" data-number-show-tablet="2">
                    @foreach ($bulletins as $bulletin)
                        @php
                            $bulletinUserId = $bulletin->admin_id ?? '';
                           
                         
                            
                            // Lấy thông tin admin từ admin_id
                            $adminName = "";
                            $bulletinAuthorUrl  = "";
                            if ($bulletinUserId) {
                                $admin = App\Models\AdminModel::find($bulletinUserId);
                                $adminName = $admin ? ($admin->fullname ?? $admin->username) : __('Admin');
                                $bulletinAuthorUrl = rrt_show_upload_url($admin->thumbnail ?? null,'admins');
                                $detailUrl = rrt_route('public/threads/detail', ['code' => $bulletin->code ?? '']);
                            } else {
                                $adminName = __('Admin');
                            }
                        @endphp
                        <div class="bulletin-board-item">
                            <div class="bulletin-board-inner">
                                <div class="bulletin-board-thumb">
                                    <a href="{{ $detailUrl }}"> <img
                                            src="{{ rrt_show_upload_url($bulletin->thumbnail ?? '', 'threads') }}"
                                            alt=""></a>
                                </div>
                                <div class="bulletin-board-text">
                                    <h3><a href="{{ $detailUrl }}" class="limit-text limit-2 ">
                                            {{ $bulletin->name ?? '-' }} </a></h3>
                                    <div class="bulletin-board-meta">
                                        <div class="bulletin-board-author">
                                            <img src="{{ $bulletinAuthorUrl }}" alt="{{ $adminName }}">
                                            <span>{{ $adminName }}</span>
                                        </div>
                                        <div class="bulletin-board-category">
                                            {{ getTimeDiffHuman($bulletin->created_at ?? '') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    <section class="section-about">
        <div class="container">
            <div class="section-about-inner">
                <div class="section-about-text">
                    <p class="sub-title">{{ __('FOR PRODUCERS') }}</p>
                    <h2 class="title-main">
                        {{ isset($footer) && isset($footer['producer_setting_title']) ? __($footer['producer_setting_title']) : __('Your music business in one place') }}
                    </h2>
                    <p>{{ isset($footer) && isset($footer['producer_setting_content']) ? __($footer['producer_setting_content']) : __("Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.") }}
                    </p>
                </div>
                <div class="section-about-image">
                    <img src="{{ isset($footer) && isset($footer['producer_setting_image']) ? url('public/uploads/banner/' . $footer['producer_setting_image']) : asset('public/style2/img/about-thumb.png') }}"
                        alt=""
                        onerror="this.onerror=null;this.src='{{ asset('public/style2/img/about-thumb.png') }}';">
                </div>
            </div>
        </div>
    </section>

@endsection
