@extends('public.main')
@section('title', 'Market')
@section('content')
    @push('css')
        <style>
            .market-category-item.active {
                background: #1a1a1a;
                border: 1px solid #383838;
            }
        </style>
    @endpush
    <section class="section-category">
        <div class="container container-gap section-padding">
            <h2 class="title-main">Explore Tracks</h2>
            <div class="market-list-category  rrt-slick-slider rrt-slider" data-number-show="7">
                @foreach ($genres as $item)
                    @php

                        $thumbnail = $item['thumbnail'] ?? '';
                        $thumbnailUrl = $thumbnail ? url("public/uploads/genres/{$thumbnail}") : '';
                        $thumbnailUrl = rrt_show_thumbnail($thumbnailUrl);
                    @endphp
                    <div class="market-category-item {{ request()->genre == $item->id ? 'active' : '' }}">
                        <div class="market-category-thumb-wrap">
                            <a href="{{ rrt_route($controllerName . '/index', ['genre' => $item->id]) }}">
                                <img src="{{ $thumbnailUrl }}" alt="" class="market-category-thumb">
                            </a>
                            <div class="market-category-icon">
                                <i class="fa fa-analytics"></i>
                            </div>
                        </div>
                        <h3 class="market-category-title">
                            <a href="#"> {{ $item['name'] ?? '' }} </a>
                        </h3>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <section class="section-tags   section-padding ">
        <div class="container mb-0 container-gap">
            <h2 class="title-main">Mood Tracks</h2>
            @foreach ($moods as $mood)
                <a href="{{ rrt_route($controllerName . '/index', ['mood' => $mood->name]) }}">
                    <div class="tag-item">
                        <span>{{ $mood->name }}</span>
                    </div>
                </a>
            @endforeach

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
    <section class="section-tags section-padding">
        <div class="container">
            <div class="form-tag-search">
                <i class="far fa-search"></i>
                <input type="search" class="form-control" placeholder="Search for tags" id="search-tags">
            </div>
            @foreach ($tags as $tag)
                <a href="{{ rrt_route($controllerName . '/index', ['tag' => $tag->name]) }}">
                    <div class="tag-item">
                        <span>{{ $tag->name }}</span>
                    </div>
                </a>
            @endforeach

        </div>
    </section>

    <section class="section section-tracks">
        <div class="container">
            <div class="market-list-track list-skeleton"
                data-url={{ rrt_route($controllerName . '/list', ['username' => request()->name, 'genre' => request()->genre, 'search' => request()->search, 'tag' => request()->tag, 'mood' => request()->mood]) }}
                data-skip="5">
                <div class="card-skeleton">
                    <div class="image-skeleton"></div>
                    <div class="title-skeleton"></div>
                </div>

                {{-- @for ($i = 0; $i < 10; $i++)
                    <div class="track-item">
                        <a href=""><img src="{{ asset('public/images/track-thumb.png') }}" alt=""
                                class="track-thumb"></a>
                        <div class="trac-text">
                            <div class="track-meta">
                                <div class="track-meta-price">
                                    $4.95
                                </div>
                                <div class="track-meta-free">
                                    <span>Free</span>
                                    <i class="fas fa-download"></i>
                                </div>
                                <div class="track-meta-info">
                                    145 BM
                                </div>
                            </div>
                            <h3 class="track-title"><a href=""> Rich Flex - $30 UNLIMITED TODAY </a></h3>
                            <div class="track-author">
                                <a href=""> Anywaywell </a>
                            </div>
                        </div>
                    </div>
                @endfor --}}


            </div>

            <div id="loadingMore">
                <button class="btn btn-feature" id="btnLoadTrack">Load more</button>
            </div>
        </div>
    </section>
@endsection
