@php
    use App\Helpers\Template;

    $thumbnail = isset($user->thumbnail) ? asset('/public/upload/user/') . $user->thumbnail : asset('public/images/no-image.png');
    $social = [];
    foreach ($user->socialmedia as $key => $item) {
        $social[$item->name] = $item->link;
    }
@endphp
@extends('public.main')
@section('title', 'Producer')
@section('content')

    <style>
        .market-list-track {
            --f-columns: 4;
        }

        .content-detail-producer {
            display: flex;
            padding: 20px;
        }

        aside {
            width: 20%;
        }

        aside section {
            padding-top: 20px
        }

        .section-info {

            padding-top: 15px;
            position: relative;
        }

        .section-info .section-info_avater {

            display: flex;
            justify-content: center;
            align-content: center;
        }

        .section-info .section-info_avater img {
            width: 88px;
            height: 88px;
            border-radius: 50%;
        }

        .section-info_name {
            padding-top: 10px
        }

        .section-info_button {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding-top: 10px;
        }

        .section-info_button a {
            padding: 10px 15px;

            color: #fff;
            border-radius: 10px
        }

        .section-info_button .section-info_button_folow {
            background-color: #005ff8;
        }

        .section-info_button .section-info_button_message {
            background-color: #707070
        }

        .section-starts_item {
            display: flex;
            justify-content: space-between;
        }

        .section-starts_item span {
            padding-top: 20px
        }

        .section-achievements {
            display: block;
        }

        .section-achievements_icon {
            display: flex;


        }

        .section-products_item {
            padding-top: 20px
        }


        h4 {
            text-transform: uppercase
        }


        hr {
            margin-top: 20px
        }

        .bage {
            padding: 5px 10px;
            margin-left: 20px;
            border-radius: 10px
        }

        .badge-product {
            background-color: #005ff8
        }

        .section-about_content p,
        .section-findmeon_item {
            margin-top: 10px
        }

        .track-inner img {
            height: 180px;
        }

        main {
            width: 75%;
            padding-left: 80px
        }

        main .title {
            padding-bottom: 40px;
        }

        h2 {
            text-transform: uppercase;
            margin-top: 40px
        }

        .fa-tiktok::after {
            content: "\e07b";
        }

        .section-social-content {

            margin-top: 20px;

            /* Chia cột cho các phần tử con */
        }

        .social-content_item {
            margin: 20px 0px
        }

        .social-content_item i {
            font-size: 22px
        }

        .social-content_item a {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 16px;
        }

        .section-about {
            margin-top: 5px
        }
    </style>

    <div class="content-detail-producer container p-20">
        <aside>
            <section class="section-info">
                <div class="section-info_avater">
                    <img src="{{ $thumbnailUrl }}" alt="">
                </div>
                <div class="section-info_name">

                    <h2 class="text-center">{{ $user->fullname ?? $user->username }}</h2>
                    {{-- <p class="about text-center">-75%🔥OFF TODAY</p> --}}
                </div>
                <div class="section-info_button">
                    <a class="section-info_button_folow"
                        href="{{ rrt_route($controllerName . '/follow', ['username' => $user->username, 'user_id' => $user->id]) }}">
                        {{ $total }}
                        Follow</a>
                    {{-- <a class="section-info_button_message"
                        href="{{ rrt_route('public/producer/message', ['user_id' => $user->id, 'username' => $user->username]) }}">Message</a>
                </div> --}}
            </section>
            <section class="section-starts">
                <h4>START</h4>
                {{-- <div class="section-starts_item">
                    <span>Follower</span>
                    <span>180k</span>
                </div>
                <div class="section-starts_item">
                    <span>Plays</span>
                    <span>180k</span>
                </div> --}}
                <div class="section-starts_item">
                    <span>Tracks</span>
                    <span>{{ count($user->tracks->where('status', 'public')) }}</span>
                </div>
            </section>
            <hr>

            <section class="section-about">
                <h4>About Me</h4>
                <div class="section-about_content">
                    <p>{{ $user->bio }}</p>
                </div>
            </section>

            <section class="section-social">
                <h4>Find me on</h4>
                <div class="section-social-content">
                    @isset($social['instagram'])
                        <div class="social-content_item">
                            <a href="{{ $social['instagram'] ?? '#' }}"> <img
                                    src="{{ asset('studio/rrt/img/instagram-l-regular-solid.svg') }}"
                                    alt=""></i>Instagram</a>

                        </div>
                    @endisset
                    @isset($social['tiktok'])
                        <div class="social-content_item">
                            <a href="{{ $social['tiktok'] ?? '#' }}"> <img style="background-color:#fff"
                                    src="{{ asset('studio/rrt/img/tiktok-l-regular-solid.svg') }}" alt="">TikTok</a>


                        </div>
                    @endisset
                    @isset($social['soundcloud'])
                        <div class="social-content_item">
                            <a href="{{ $social['soundcloud'] ?? '#' }}"> <img
                                    src="{{ asset('studio/rrt/img/soundcloud-l-regular-solid.svg') }}"
                                    alt="">SoundCloud</a>
                        </div>
                    @endisset

                    {{-- @isset($social['id'])
                        <div class="social-content_item">
                            <a href="{{ $social['id'] ?? '#' }}"> <i class="fab fa-rocketchat"></i>ETC</a>

                        </div>
                    @endisset --}}
                </div>
            </section>
        </aside>
        <main>
            <section class="section-social">
                <div class="title">
                    <h2>Tracks</h2>
                </div>
                <section class="section section-tracks">
                    <div class="container">
                        <div class="market-list-track list-skeleton"
                            data-url={{ rrt_route($controllerName . '/getListTrack', ['user_id' => $user->id]) }}
                            data-skip="5">
                            <div class="card-skeleton">
                                <div class="image-skeleton"></div>
                                <div class="title-skeleton"></div>
                            </div>
                        </div>
                        @if (count($user->tracks->where('status', 'public')))
                            <div id="loadingMore">
                                <button class="btn btn-feature" id="btnLoadTrack" data-user = "{{ $user->id ?? '' }}">Load
                                    more</button>
                            </div>
                        @endif

                    </div>
                </section>
            </section>
            @if ($userIsComment && rrt_get_user_login('id') == $user->id)
                <div class="title">
                    <h2>Comments</h2>
                </div>
                <section class="section section-comments">
                    <div class="container">
                        <div class="list-comment"  data-url={{ rrt_route($controllerName . '/getListComment', ['user_id' => $user->id]) }}
                            data-skip="5">
                            @if (count($comments) > 0)
                                @include('public.pages.producer.comment_item',['comments'=> $comments])
                            
                            @else
                                <p>No data</p>
                            @endif


                        </div>
                        @if (count($comments) == 5)
                            <div id="loadingMore">
                                <button class="btn btn-feature" id="btnLoadCommentUser" data-user = "{{ $user->id ?? '' }}">Load
                                    more</button>
                            </div>
                        @endif
                    </div>
                </section>
            @endif


        </main>
    </div>
    @push('srcipt')
        {{-- <script>
            $('#btnLoadTrack').click(function(e) {
                e.preventDefault();
                let url = "{{ route('public/producer/getListTrack') }}"
                $.ajax({
                    type: "POST",
                    url: ,
                    data: "data",
                    dataType: "dataType",
                    success: function(response) {

                    }
                });
            });
        </script> --}}

        <script>
            $('.section-info_button_message').click(function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                $.ajax({
                    type: "get",
                    url: url,
                    dataType: "json",
                    success: function(res) {
                        if (res.status == 403) {
                            return showNotify("error", "Error", "Please Sign In")

                        }
                    }
                });
            })
            $('.section-info_button_folow').click(function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                $.ajax({
                    type: "get",
                    url: url,
                    dataType: "json",
                    success: function(res) {
                        if (res.status == 403) {
                            return showNotify("error", "Error", "Please Sign In")
                        } else {

                            $('.section-info_button_folow').text(res.total + ' Follow')
                        }
                    }
                });
            })
            const btnLoadCommentUser = $("#btnLoadCommentUser");
            const listComment = $(".list-comment");
            let skipComment = listComment.data('skip');
            btnLoadCommentUser.click(function() {
               let url = listComment.data('url');
              
               $.ajax({
                type: 'get',
                url: url,
                data: {
                    skip:skipComment
                },
                dataType: "json",
                beforeSend: function() {
                    showLoading();
                },
                success: function (response) {
                    skipComment = skipComment + 5;
                    let comments = response.comments ? response.comments : [];
                    let xhtml = response.xhtml ? response.xhtml : '';
                    if(comments.length > 0) {
                        listComment.append(xhtml);
                    }
                    else {
                        btnLoadCommentUser.hide();
                    }
                },
                complete: function() {
                    hideLoading();
                }
               });
            })
        </script>
    @endpush
@endsection
