@php
    use App\Helpers\Template;
    use App\Helpers\Link;
@endphp
@extends('public2.main')
@section('main_class', 'page-market')
@push('css')
    <style>
        /* .mr-1{
            margin-right: 1em;
        } */
        .search-button {
            height: 40px !important;
        }
        select {
            height: 40px !important;
        }
    </style>
@endpush
@section('content')
    <section class="section-track-all">
        <div class="container">
            <div class="track-all-inner">
                <div class="w-100">
                    <div class="track-producer-wrap">
                        <div class="section-title-wrap">
                            <h2 class="section-title">
                                <img src="{{ asset('public/style2/img/fluent_mic-sparkle-16-regular.svg') }}" alt="">
                                <span>{{__('Producers')}}</span>
                            </h2>
                            <form action="" method="get" class="form-inline">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="form-group mr-1">
                                        <input type="search" name="search" class="form-control" id="search" value="{{ request()->search }}" placeholder="Enter username or email">
                                    </div>
                                    <div class="form-group mr-1">
                                        <select name="sort" class="form-control" id="sort">
                                            <option value="">-- Sort --</option>
                                            <option value="asc" {{ request()->sort == 'asc' ? 'selected' : '' }}>A -> Z</option>
                                            <option value="desc" {{ request()->sort == 'desc' ? 'selected' : '' }}>Z -> A</option>
                                        </select>
                                    </div>
                                    <div class="form-group mr-1">
                                        <button type="submit" class="btn btn-light search-button">
                                            <img src="{{ asset('public/style2/img/icon_search.svg') }}" alt="Search Icon">
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if ($users->isNotEmpty())
                            <div class="list-producer-container">
                                @foreach ($users as $user)
                                    @php
                                        $thumbnail = $user->thumbnail ?? '';
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
                                            <a href="{{ $route_detail }}">
                                                <img src="{{ $thumbnailUrl }}" class="card-img-top" alt="">
                                            </a>
                                        </div>
                                        <div class="producer-text">
                                            <h5 class="limit-text limit-1">
                                                <a href="{{ $route_detail }}">{{ $fullname }}</a>
                                            </h5>
                                            <p class="limit-text limit-1">{{ $username }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="pagination-wrapper">
                                {{ $users->appends(request()->query())->links() }}
                            </div>
                        @else
                            <p>{{__('No producers found')}}.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('srcipt')
    <script>
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
            console.log(offsetTop);
            if (element) {
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        }
        const wavesurferFeatureItem = WaveSurfer.create({
            container: '.track-feature-item #waveform',
            waveColor: '#A8DBA8',
            progressColor: '#3B8686',
            barWidth: 3,
            barHeight: 1,
            height: 100,
            responsive: true,
        });
        wavesurferFeatureItem.load($('.track-feature-item').data('track'));
        const btnPlay = $('.track-feature-item .play-pause');
        btnPlay.on('click', function() {
            if (isPlaying) {
                wavesurferFeatureItem.pause();
                $(this).html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>');
            } else {
                wavesurferFeatureItem.play();
                $(this).html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>');
            }
            isPlaying = !isPlaying;
        });
        wavesurferFeatureItem.on('audioprocess', function() {
            const currentTime = wavesurfer.getCurrentTime();
            $('.track-feature-item .current-time').text(formatTime(currentTime));
        });
        wavesurferFeatureItem.on('ready', function() {
            $('.track-feature-item .duration').text(formatTime(wavesurfer.getDuration()));
        });
    </script>
@endpush
