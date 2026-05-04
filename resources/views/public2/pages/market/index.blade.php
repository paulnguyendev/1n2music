@php
    use App\Helpers\Template;
    use App\Helpers\Link;
@endphp
@extends('public2.main')

@section('main_class', 'page-market')
@section('content')


    <section class="section-genres">
        <div class="container">
            <div class="section-title-wrap">
                <h2 class="section-title">
                    <img src="{{ asset('public/style2/img/icon_recommend.svg') }}" alt="">
                    <span>{{ __('Explore Tracks') }}</span>
                </h2>
                <div class="slide-search-input">
                    <img src="{{ asset('public/style2/img/icon_search.svg') }}" alt="">
                    <input type="text" placeholder="{{ __('What are you looking for') }}" id="search"
                        data-url="{{ rrt_route($controllerName . '/index') }}">
                </div>
            </div>
            <div class="list-track-genres rrt-slider rrt-slick-slider" data-number-show="5">
                @foreach ($genres as $genre)
                    @php
                        $thumbnail = $genre['thumbnail'] ?? '';
                        $thumbnailUrl = $thumbnail ? url("public/uploads/genres/{$thumbnail}") : '';
                        $thumbnailUrl = rrt_show_thumbnail($thumbnailUrl);

                        $currentParams = request()->query();
                        $currentParams['genres'] = $genre['id'] ?? '';

                        $isActive = request()->query('genres') == $genre['id'];
                    @endphp
                    <div class="track-genres-item">
                        <a href="{{ rrt_route('public/market/index', $currentParams) }}">
                            <img src="{{ $thumbnailUrl }}" alt="" class="{{ $isActive ? 'active' : '' }}">
                            <span>{{ __($genre->name ?? '-') }}</span>
                        </a>
                    </div>
                @endforeach
            </div>

        </div>
    </section>
    <section class="section-moods">
        <div class="container">
            <h3 class="section-title">{{ __('Moods') }}</h3>
            @if ($moods)
                <div class="list-mood">
                    @foreach ($moods as $mood)
                        @php
                            $currentParams = request()->query();
                            $currentParams['mood'] = $mood->id ?? '';

                            $isActive = request()->query('mood') == $mood->id;
                        @endphp
                        <div class="list-mood-item {{ $isActive ? 'active' : '' }}">
                            <a href="{{ rrt_route($controllerName . '/index', $currentParams) }}">
                                {{ __($mood->name ?? '') }}
                            </a>
                        </div>
                    @endforeach

                </div>
            @endif

        </div>
    </section>
    <section class="section-track-all">
        <div class="container">
            <div class="track-all-inner">
                <div class="track-all-left">
                    <div class="track-producer-wrap">
                        <div class="section-title-wrap">
                            <h2 class="section-title">
                                <img src="{{ asset('public/style2/img/fluent_mic-sparkle-16-regular.svg') }}"
                                    alt="">
                                <span>{{ __('Producers') }}</span>
                            </h2>
                            <a href="{{ rrt_route('public/producers/index') }}">{{ __('See more') }}</a>
                        </div>
                        @if ($users && count($users) > 0)
                            <div class="track-list-producer rrt-slick-slider rrt-slider" data-number-show="5">
                                @foreach ($users as $user)
                                    @php
                                        $thumbnail = $user['thumbnail'] ?? '';
                                        $thumbnailUrl = $thumbnail ? url("public/uploads/users/{$thumbnail}") : '';
                                        $thumbnailUrl = rrt_show_thumbnail($thumbnailUrl);
                                        $userId = $user->id ?? '';
                                        $username = $user->username ?? '';
                                        $username = preg_replace('/[^A-Za-z0-9]/', '', $username);
                                        $route_detail = rrt_route('public/producer/detail', [
                                            'user_id' => $userId,
                                            'username' => $username,
                                        ]);
                                        $fullname = rrt_get_fullname_by_user($user);
                                    @endphp
                                    <div class="list-producer-item">
                                        <div class="producer-thumb">
                                            <a href="{{ $route_detail }}">
                                                <img src="{{ $thumbnailUrl }}" alt="">
                                            </a>
                                        </div>
                                        <div class="producer-text">
                                            <h3 class="limit-text limit-1"><a href="{{ $route_detail }}">
                                                    {{ $fullname }}</a></h3>
                                            <p class="limit-text limit-1">{{ $username }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center" style=" background: rgba(230, 230, 230, 1);">{{__('No data')}}</p>
                        @endif
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
                        <div class="pagination-wrap">
                            <ul class="pagination">
                                @for ($i = 1; $i <= $tracks->lastPage(); $i++)
                                    <li class="{{ $i == $tracks->currentPage() ? 'active' : '' }}">
                                        <a href="{{ $tracks->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="track-all-right">
                    <div class="track-feature-wrap">
                        <div class="section-title-wrap">
                            <h2 class="section-title">
                                <span>{{ __('Featured Tracks') }}</span>
                            </h2>
                            <div id="favourite" 
                            data-url="#" 
                            data-id="#" 
                            data-favourite="#">
                                <div class="track-feature-buttons">
                                    <img style="background:none;" src="{{ asset('public/style2/img/icon_favourite_dark.svg') }}" alt="">
                                </div>
                            </div>
                            <div id="cart" 
                            class = 'btn-add-cart'
                            data-id = '#'
                            data-contract-ids = '#'
                            data-login="{{ rrt_check_login() ? '1' : '0' }}" 
                            data-url-cart = "{{ rrt_route('public/cart/postAddCart') }}"
                            data-url = "{{ rrt_route('public/track/listContracts') }}"
                            >
                                <div class="track-feature-buttons">
                                    <img style="background:none;" src="{{ asset('public/style2/img/icon_cart.svg') }}" alt="">
                                </div>
                            </div>
                            <div id="coin" 
                            class = 'btn-checkout'
                            data-id = '#'
                            data-type = 'checkout'
                            data-contract-ids = '#'
                            data-login="{{ rrt_check_login() ? '1' : '0' }}" 
                            data-url-cart = "{{ rrt_route('public/cart/postAddCart') }}"
                            data-url = "{{ rrt_route('public/track/listContracts') }}"
                            >
                                <div class="track-feature-buttons">
                                    {!! rrt_icon_coin_checkout('background:none;'); !!}
                                </div>
                            </div>
                            <a id="download" href="#" download style="display:none;">
                                <div class="track-feature-buttons">
                                    <img style="background:none;" src="{{ asset('public/style2/img/icon_download.svg') }}" alt="">
                                </div>
                            </a>
                        </div>
                        @if (isset($featuredTracks) && count($featuredTracks) > 0)
                            @php
                                $featuredTrack = $featuredTracks[0];
                            @endphp
                            <div class="track-feature-item"
                                data-track="{{ url('public/uploads/tracks/' . $featuredTrack->file[0]->name) }}">
                                <div class="track-feature-thumb">
                                    <img src="{{ url('public/uploads/tracks/' . $featuredTrack->thumbnail) }}"
                                        alt=""
                                        onerror="this.onerror=null;this.src='{{ asset('public/images/no-image.png') }}';">

                                </div>
                                <div class="track-feature-info">
                                    <h3>{{ $featuredTrack->name }}</h3>
                                    <p>{{ $featuredTrack->user->fullname }}</p>
                                    <div class="track-feature-meta">
                                        <span>{{ Template::showTrackPrice($featuredTrack->listContracts()->get()->toArray()) ?? 0 }}</span>
                                        <span>{{ ($featuredTrack->bpm_number ? $featuredTrack->bpm_number : 0) . ' BPM' }}</span>
                                    </div>
                                </div>
                                <div class="controls">
                                    <div class="buttons">
                                        <button id="prev_featured"><svg class="icon-svg" viewBox="0 0 24 24"><path d="M11 19V5l-11 7 11 7zm11 0V5l-11 7 11 7z"/></svg></button>
                                        <button class="play-pause-featured"><svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></button>
                                        <button id="next_featured"><svg class="icon-svg" viewBox="0 0 24 24"><path d="M4 19l11-7L4 5v14zm11 0l11-7-11-7v14z"/></svg></button>
                                    </div>
                                </div>
                                <div class="audio-wave">
                                    <div class="visualizer" id="featured_waveform"></div>
                                    <div class="progress-bar">
                                        <div class="progress" id="progress"></div>
                                    </div>
                                    <div class="time">
                                        <span class="current-time">0:00</span> / <span class="duration">7:48</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="track-list-wrap" style="margin-top: 30px">
                        <div class="section-title-wrap">
                            <h2 class="section-title">
                                <img src="{{ asset('public/style2/img/music-player-song-svgrepo-com.svg') }}"
                                     alt="">
                                <span>{{ __('Featured Tracks') }}</span>
                            </h2>
                        </div>
                        <div class="track-list-container">
                        @foreach ($featuredTracks as $track)
                            <div class="track-list-items">
                                <div class="featured-track">
                                    <div class="track-item-left">
                                        <div class="track-item-play">
                                            <svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                        <div class="track-item-info" style="width: 150px">
                                            <h3 class="limit-text limit-1"> {{ $track->name ?? '' }}</h3>
                                        </div>
                                    </div>
                                    <div class="track-item-right">
                                        <a href="#" class="limit-text limit-1">
                                            {{$track->user->fullname ?? ''}} </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('srcipt')
    <script>
        var isPlaying = false;
        wavesurfer.on('play', function() {
            isPlaying = true;
        });

        wavesurfer.on('pause', function() {
            isPlaying = false;
        });
        const btnPlayer = $('.play-pause:not(.track-feature-item .play-pause)');



        var isFeaturedPlaying = false;
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const searchValue = this.value;
                if (searchValue) {
                    const searchUrl = this.getAttribute('data-url') + "?search=" + encodeURIComponent(searchValue);
                    window.location.href = searchUrl;
                }
            }
        });
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.size > 0) {
            const searchValue = urlParams.get('search');
            if (searchValue) {
                document.getElementById('search').value = searchValue;
            }
            const element = document.querySelector('.track-list-wrap');
            const offsetTop = element.getBoundingClientRect().top + window.scrollY - 150; // Adjust -100 if needed
            if (element) {
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        }


        // Update load featured Tracks
        const tracks = [
            @foreach ($featuredTracks as $track)
                {
                    id: '{{ $track->id }}',
                    url: '{{ url('public/uploads/tracks/' . $track->file[0]->name) }}',
                    favourite_url : '{{ rrt_route('public/track/postFavourite') }}',
                    favourite : '{{ $track->favourites ? count($track->favourites) : 0 }}',
                    download : '{{ $track->download ? $track->download : 0 }}',
                    contract_ids : '{{ $track->contract_ids ?? "" }}',
                    title: '{{ $track->name }}',
                    artist: '{{ $track->user->fullname }}',
                    price: '{{ Template::showTrackPrice($track->listContracts()->get()->toArray()) ?? 0 }}',
                    bpm: '{{ ($track->bpm_number ? $track->bpm_number : 0) . ' BPM' }}',
                    image: '{{ url('public/uploads/tracks/' . $track->thumbnail) }}'
                },
            @endforeach
        ];

        var currentIndex = 0;
        var startSaveHistory = false;

        var wavesurferFeatureItem = WaveSurfer.create({
            container: '.track-feature-item #featured_waveform',
            waveColor: '#A8DBA8',
            progressColor: '#3B8686',
            barWidth: 3,
            barHeight: 1,
            height: 100,
            responsive: true,
        });
        wavesurferFeatureItem.on('play', function() {
            isFeaturedPlaying = true;
        });
        wavesurferFeatureItem.on('pause', function() {
            isFeaturedPlaying = false;
        });
        // Load track function
        function loadTrack(index) {
            const track = tracks[index];
            wavesurferFeatureItem.load(track.url);

            $('.track-feature-item h3').text(track.title);
            $('.track-feature-item p').text(track.artist);
            $('.track-feature-item .track-feature-meta span:nth-child(1)').text(track.price);
            $('.track-feature-item .track-feature-meta span:nth-child(2)').text(track.bpm);

            const imgElement = $('.track-feature-item .track-feature-thumb img');
            imgElement.attr('src', track.image);
            imgElement.on('error', function() {
                $(this).attr('src', '{{ asset('public/images/no-image.png') }}');
            });

            // Add data to button 
            if (download) {
                $('#download').attr('href', track.url);
                $('#download').attr('download', track.title + '.mp3');
                $('#download').show();
            }

            $('#favourite')
            .addClass('btn-add-favourite')
            .attr('data-url', track.favourite_url)
            .attr('data-id', track.id)
            .attr('data-favourite', track.favourite);
            if (track.favourite > 0) {
                $('#favourite').addClass('active').find('img').attr('src', '{{ asset('public/style2/img/carbon_favorite.svg') }}');
            }else{
                $('#favourite').removeClass('active').find('img').attr('src', '{{ asset('public/style2/img/icon_favourite_dark.svg') }}');
            }

            $('#cart')
            .attr('data-id', track.id)
            .attr('data-contract-ids', track.contract_ids || '');

            $('#coin')
            .attr('data-id', track.id)
            .attr('data-contract-ids', track.contract_ids || '');
            wavesurferFeatureItem.on('ready', function() {
                $('.track-feature-item .current-time').text('0:00');
                $('.track-feature-item .duration').text(formatTime(wavesurferFeatureItem.getDuration()));
                if (isFeaturedPlaying) {
                    wavesurferFeatureItem.play();
                    $('.track-feature-item .play-pause').html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>');
                } else {
                    $('.track-feature-item .play-pause').html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>');
                }
                currentIndex = index;
                updateListIcon(index)
            });
            if(startSaveHistory){
                saveHistory(track.id);
            }
        }
        function updateListIcon(index){
            $('.featured-track').removeClass('active');
            $('.featured-track').eq(index).addClass('active');
            $('.featured-track').find('.track-item-play').html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>')
            let html = isFeaturedPlaying ? `<svg class="icon-svg" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>` : `<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>`
            $('.featured-track').eq(index).find('.track-item-play').html(html);
        }
        // Next
        $('#next_featured').on('click', function() {
            currentIndex = (currentIndex + 1) % tracks.length;
            startSaveHistory = false;
            if (isFeaturedPlaying) {
                startSaveHistory = true;
            }
            loadTrack(currentIndex);
            if (isFeaturedPlaying) {
                wavesurferFeatureItem.play();
                updateListIcon(currentIndex)
                if (isPlaying) {
                    btnPlayer.html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>');
                    wavesurfer.pause();
                    updateListIcon(currentIndex)
                }
            } else {
                resetPlayback();
                updateListIcon(currentIndex)
            }
        });

        // Pre
        $('#prev_featured').on('click', function() {
            currentIndex = (currentIndex - 1 + tracks.length) % tracks.length;
            startSaveHistory = false;
            if (isFeaturedPlaying) {
                startSaveHistory = true;
            }
            loadTrack(currentIndex);
            if (isFeaturedPlaying) {
                wavesurferFeatureItem.play();
                updateListIcon(currentIndex)
                if (isPlaying) {
                    btnPlayer.html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>');g;
                    wavesurfer.pause();
                    updateListIcon(currentIndex)
                }
            } else {
                resetPlayback();
                updateListIcon(currentIndex)
            }
        });

        // play/pause
        $('.play-pause-featured').on('click', function() {
            if (isFeaturedPlaying) {
                wavesurferFeatureItem.pause();
                isFeaturedPlaying = !isFeaturedPlaying;
                $(this).html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>');
                updateListIcon(currentIndex)
            } else {
                if (isPlaying) {
                    btnPlayer.html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>');
                    wavesurfer.pause();
                    updateListIcon(currentIndex)
                }
                if(!startSaveHistory){
                    const favouriteDiv = $(this)
                        .closest('.track-feature-wrap')
                        .find('.section-title-wrap')
                        .find('#favourite');
                    let trackId = favouriteDiv.data('id');
                    if (trackId) {
                        saveHistory(trackId);
                    }                    
                }
                wavesurferFeatureItem.play();
                isFeaturedPlaying = !isFeaturedPlaying;
                $(this).html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>');
                updateListIcon(currentIndex)
            }
        });

        // Function reset play
        function resetPlayback() {
            wavesurferFeatureItem.stop();
            $('.track-feature-item .play-pause').html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>');
            isFeaturedPlaying = false;
        }

        // Updat time playing
        wavesurferFeatureItem.on('audioprocess', function() {
            const currentTime = wavesurferFeatureItem.getCurrentTime();
            $('.track-feature-item .current-time').text(formatTime(currentTime));
        });

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const sec = Math.floor(seconds % 60).toString().padStart(2, '0');
            return `${minutes}:${sec}`;
        }
        $('.featured-track').each(function(index) {
            $(this).on('click', function() {
                startSaveHistory = true;
                loadTrack(index);
                if (isFeaturedPlaying) {
                    wavesurferFeatureItem.play();
                    updateListIcon(currentIndex)
                    if (isPlaying) {
                        btnPlayer.html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>');
                        wavesurfer.pause();
                    }
                } else {
                    resetPlayback();
                    updateListIcon(currentIndex)
                }
            });
        });
        wavesurferFeatureItem.on('finish', function() {
            isFeaturedPlaying = false;
            updateListIcon(currentIndex);
            $('.play-pause-featured').html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>');
        });
        loadTrack(currentIndex);

        $(document).ready(function() { 
            // Favourite featured track
            const handleFavouriteTrack = function(trackItem, ele) {
                const id = ele.data('id');
                let favourite = ele.data('favourite');
                const url = ele.data('url');
                const data = {
                    track_id: id,
                    check_active: favourite
                };
                $.ajax({
                    type: "post",
                    url: url,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        favourite = response?.favourite;
                        ele.data('favourite', favourite);
                        if (favourite == 1) {
                            $('#favourite').attr('data-favourite', '1');
                            $('.btn-add-favourite').find('img').attr('src',
                                '{{ asset('public/style2/img/carbon_favorite.svg') }}')
                            $('.btn-add-favourite').removeClass('active');
                            return showNotify("success", "{{__('Success')}}", "{{__('Favorite track successfully')}}")
                        } else {
                            $('#favourite').attr('data-favourite', '0');
                            $('.btn-add-favourite').find('img').attr('src',
                            '{{ asset('public/style2/img/icon_favourite_dark.svg') }}')
                            $('.btn-add-favourite').addClass('active');
                            return showNotify("success", "{{__('Success')}}", "{{__('Deleted from Favorites')}}")
                        }
                    }
                });
            };
            $(document).on('click', '.btn-add-favourite', function() {
                handleFavouriteTrack(this, $('.btn-add-favourite'));
            });

            // Cart
            $(document).on('click', '.btn-add-cart', function() {
                const contract_ids = $(this).attr('data-contract-ids');
                const url = $(this).data('url');
                let isLogin = $(this).data('login');
                if (isLogin == 0) {
                    return showNotify("error", "{{__('Error')}}", "{{__('Please Sign In')}}")
                }
                fetchContracts(url, contract_ids);
            });
        });
    </script>
@endpush
