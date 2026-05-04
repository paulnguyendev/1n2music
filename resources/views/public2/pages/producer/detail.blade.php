@php
    use App\Helpers\Template;

    $thumbnail = isset($user->thumbnail)
        ? asset('/public/upload/user/') . $user->thumbnail
        : asset('public/images/no-image.png');
    $social = [];
    foreach ($user->socialmedia as $key => $item) {
        $social[$item->name] = $item->link;
    }
@endphp
@extends('public2.main')
@section('title', 'Producer')
@push('css')
    <style>
        .discography-grid {
            display: grid;

            gap: 20px;
            margin-top: 20px;
        }

        .discography-item {
            position: relative;
            cursor: pointer;
        }

        .discography-item img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            aspect-ratio: 16/9;
            object-fit: cover;
        }

        .discography-item .title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px;
            font-size: 14px;
            line-height: 1.5;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .rrt-slider.discography-grid .slick-prev {
            left: -15px;
            font-size: 16px;
            z-index: 9999;
        }

        .rrt-slider.discography-grid .slick-next {
            font-size: 16px;
            right: -15px;
        }

        @media (max-width: 768px) {
            .discography-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .discography-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: auto;
            padding: 0;
            width: 90%;
            max-width: 800px;
            border-radius: 8px;
        }

        .close {
            position: absolute;
            right: -30px;
            top: -30px;
            color: white;
            font-size: 28px;
            font-weight: bold;
            z-index: 1001;
            cursor: pointer;
        }

        .embed-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            max-width: 100%;
            border-radius: 8px;
        }

        .embed-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 8px;
        }

        .discography-item:hover img {
            opacity: 0.8;
        }
    </style>
@endpush
@section('content')
    <section class="section-track-all">
        <div class="container">
            <div class="track-all-inner producer-container">
                <div class="producer-info-container">
                    <div class="track-feature-wrap">
                        <div class="section-title-wrap">
                            <h2 class="section-title">
                                <img src="{{ asset('public/style2/img/fluent_mic-sparkle-16-regular.svg') }}" alt="">
                                <span>{{ __('Producer') }}</span>
                            </h2>
                            <div class="section-title-md">
                                <span>{{ __('Tracks') }}: {{ $tracks->count() ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="track-feature-item justify-content-between"
                            data-track="{{ asset('public/style2/img/touch-clouds-trippie-redd-x-lil-uzi-vert-type-beat_TK16814844.mp3-WhYAuI867N.mp3') }}">
                            <div class="track-feature-thumb">
                                <img src="{{ $thumbnailUrl }}" alt="">
                            </div>
                            <div class="track-feature-info">
                                <h3>{{ $fullname ?? '' }}</h3>
                                @if (isset($socials) && !empty($socials))
                                    <div class="socials-container">
                                        @foreach ($socials as $key => $link)
                                            <div class="social-link">
                                                <a target="_blank" href="{{ $link ?? '#' }}"><img
                                                        src="{{ asset('studio/rrt/img') }}/{{ $key }}-l-regular-solid.svg"
                                                        alt=""></a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @php
                                    $username = preg_replace('/[^A-Za-z0-9]/', '', $user->username ?? '');
                                @endphp
                                <div>
                                    <button class="btn btn-primary info-button-follow"
                                        data-url="{{ rrt_route($controllerName . '/follow', ['username' => $username, 'user_id' => $user->id]) }}"><i
                                            class="fa fa-heart"></i> <b id="follower">{{ __('Follow') }}:
                                            {{ $total }}</b></button>
                                </div>
                            </div>
                            @if (isset($user->bio) && !empty($user->bio))
                                <div class="producer-description-container">
                                    <p class="producer-description">
                                        {{ $user->bio ?? '' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="producer-track">
                    <div class="discography-wrap">
                        <div class="section-title-wrap">
                            <h2 class="section-title">
                                <img src="{{ asset('public/style2/img/music-player-song-svgrepo-com.svg') }}"
                                    alt="">
                                <span>{{ __('Discography') }}</span>
                            </h2>
                        </div>
                        <div class="discography-content">
                            @php
                                $discographyLinks = [];
                                if (!empty($user->discography)) {
                                    try {
                                        $jsonLinks = json_decode($user->discography, true);
                                        if (is_array($jsonLinks)) {
                                            $discographyLinks = $jsonLinks;
                                        } else {
                                            $textLinks = explode(', ', $user->discography);
                                            foreach ($textLinks as $link) {
                                                $parts = explode(' - ', $link, 2);
                                                if (count($parts) == 2) {
                                                    $discographyLinks[] = [
                                                        'title' => $parts[0],
                                                        'url' => $parts[1],
                                                    ];
                                                }
                                            }
                                        }
                                    } catch (Exception $e) {
                                        // Handle error if needed
                                    }
                                }
                            @endphp
                            @if (!empty($discographyLinks))
                                <div class="discography-grid rrt-slick-slider rrt-slider " data-number-show="3"
                                    data-number-show-mobile="1" data-number-show-tablet="2">
                                    @foreach ($discographyLinks as $link)
                                        @php
                                            $embedUrl = rrt_get_youtube_url($link['url']);
                                            $videoId = str_replace('https://www.youtube.com/embed/', '', $embedUrl);
                                            $thumbnailUrl = $videoId
                                                ? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg"
                                                : asset('public/images/no-image.png');
                                        @endphp
                                        <div class="discography-item" data-video="{{ $link['url'] }}">
                                            <img src="{{ $thumbnailUrl }}" alt="{{ $link['title'] }}">
                                            <div class="title">{{ $link['title'] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center">{{ __('No discography available') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="track-list-wrap">
                        <div class="section-title-wrap">
                            <h2 class="section-title">
                                <img src="{{ asset('public/style2/img/music-player-song-svgrepo-com.svg') }}"
                                    alt="">
                                <span>{{ __('Tracks') }}</span>
                            </h2>
                        </div>
                        <div class="track-list-items">
                            @if (!$tracks->isEmpty())
                                @foreach ($tracks as $track)
                                    @include('public2.globals.track-item', ['item' => $track])
                                @endforeach
                            @else
                                <p class="text-center">{{ __('No data') }}</p>
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
    @push('srcipt')
        <script>
            $(document).on('click', '.info-button-follow', function(e) {
                e.preventDefault();
                console.log('Button clicked');
                let url = $(this).data('url');
                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "json",
                    success: function(res) {
                        if (res.status == 403) {
                            showNotify("error", "Error", "Please Sign In");
                        } else {
                            $('#follower').text("{{ __('Follower') }}" + ": " + res.total);
                        }
                        if (res.redirect) {
                            window.location = res.redirect
                        }
                    },
                    error: function() {
                        showNotify("error", "Error", "An error occurred while processing your request.");
                    }
                });
            });

            // Handle discography video clicking
            $(document).on('click', '.discography-item', function() {
                var videoUrl = $(this).data('video');

                // Convert to embed URL if needed
                if (videoUrl.includes('youtube.com/watch?v=')) {
                    videoUrl = videoUrl.replace('watch?v=', 'embed/');
                } else if (videoUrl.includes('youtu.be/')) {
                    videoUrl = 'https://www.youtube.com/embed/' + videoUrl.split('youtu.be/')[1];
                }

                // Set the iframe source
                $('#videoPlayer').attr('src', videoUrl);

                // Show the modal
                $('#videoModal').css('display', 'flex');
            });

            // Close the modal
            $(document).on('click', '.close', function() {
                $('#videoModal').css('display', 'none');
                $('#videoPlayer').attr('src', '');
            });

            // Close the modal when clicking outside of it
            $(window).on('click', function(event) {
                if ($(event.target).is('#videoModal')) {
                    $('#videoModal').css('display', 'none');
                    $('#videoPlayer').attr('src', '');
                }
            });
        </script>
    @endpush
@endsection
