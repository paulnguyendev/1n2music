@php
    use App\Helpers\Template;
    use App\Helpers\Link;
    $flag_thumbnail = 0;
    foreach ($track->file as $key => $file) {
        if ($file->type == 'thumbnail') {
            $flag_thumbnail = 1;
            $thumbnail = '/public/uploads/tracks/' . $track->thumbnail;
        }
    }
    if ($flag_thumbnail == 0) {
        $thumbnail = asset('public/images/no-image.png');
    }
    $file = $track['file'] ?? [];

    // Sử dụng helper function để có URL hình ảnh an toàn cho meta tags
    $thumbnailUrl = $flag_thumbnail
        ? rrt_get_safe_image_url($track->thumbnail, 'tracks')
        : asset('public/images/no-image.png');
    $pageTitle = $track->name ?? '1N2 MUSIC';
    $pageDescription = $track->description ?? '1N2 MUSIC - Digital music distribution and publishing platform';
@endphp
@extends('public2.main')
@section('title', 'Track Detail')

@section('meta_title', $pageTitle)
@section('meta_description', $pageDescription)
@section('meta_image', $thumbnailUrl)

@push('css')
    <link rel="stylesheet" href="{{ asset('public/css') }}/responsive.css?ver={{ time() }}">
    <style>
        #main {
            width: 100%;
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

        div#iframe-audio>* {
            min-height: 200px;
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
            width: 200px;
            height: 200px;
            object-fit: cover;
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

        .section-products_item,
        .section-aboutme_item {
            padding-top: 20px;
            flex-wrap: wrap;
            display: flex;
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
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .badge-product {
            color: white;
            background-color: #005ff8
        }

        .section-aboutme_item,
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

        .button-play {
            border-radius: 999px;
            border: 0.5px solid hsla(0, 0%, 100%, .16);
            width: 100%;
            display: flex;
            position: relative;
            align-items: center;
            height: 80px;
        }

        .icon-play {
            display: block;
            padding: 18px;
            background-color: #005ff8;
            border-radius: 50%;
            font-size: 24px;
            margin-left: 20px
        }

        .line-music {
            margin-left: 20px;
            width: 100% !important;
        }

        .licensing-header {
            display: flex;
            justify-content: space-between;
            margin-top: 40px
        }

        .licensing-header_title h4 {
            font-size: 18px;
            text-transform: capitalize
        }

        .licensing-header_button {
            display: flex;
            align-items: center;
            justify-content: space-around;
        }

        .licensing-header_button span {
            font-size: 12px
        }

        .licensing-header_button span p {
            font-size: 24px
        }

        .licensing-header_button button {
            color: #fff;
            background-color: #005ff8;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
        }

        .licensing-card {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .licensing-card_item {
            margin-top: 40px;
            flex-basis: calc(100%/3);
            border-radius: 10px;
            border: 0.5px solid hsla(0, 0%, 100%, .16);
            padding: 20px;
            box-sizing: border-box;
            cursor: pointer;
            background-color: #262626;
            text-align: center;
            color: white;
        }

        .licensing-card_item h4 {
            color: white;
        }

        .licensing-card_item:hover {
            border: 3px solid #005ff8;
        }

        .licensing-card_item.active {
            background-color: #262626;
            ;
            border: 3px solid #005ff8;
        }

        .license-dropdown {
            margin-top: 10px;
            width: 100%;
            position: relative;
            text-align: center;
        }

        .license-info-toggle {
            display: inline-block;
            width: 32px;
            height: 32px;
            line-height: 32px;
            text-align: center;
            color: #fff;
            background-color: #005ff8;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .license-info-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .license-info-toggle.active i {
            transform: rotate(180deg);
        }

        .license-dropdown-content {
            display: none;
            position: absolute;
            background-color: #262626;
            width: calc(100% + 40px);
            left: -20px;
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
            z-index: 10;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: left;
        }

        .license-dropdown-content:before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid #262626;
        }

        .license-dropdown-content p {
            margin-bottom: 10px;
            font-size: 14px;
            color: white;
            text-align: left;
            line-height: 1.4;
        }

        .license-dropdown-content p:last-child {
            margin-bottom: 0;
        }

        .license-dropdown-show {
            display: block;
        }

        .section-comment {
            margin-top: 40px;
        }

        .section-comment h4 {
            text-transform: capitalize;
            font-size: 18px
        }

        .comment-writing {
            height: 40px;
            margin-top: 50px;
            display: flex
        }

        .comment-writing_text {
            width: 100%;
        }

        .comment-writing_text input {
            margin-left: 20px;
            height: 100%;
            background-color: transparent;
            border: 0;
            max-height: inherit;
            min-height: 24px;
            overflow-x: hidden;
            overflow-y: scroll;
            padding-right: 12px;
            text-wrap: normal;
            transition: height .2s ease-out;
            width: 100%;
            word-break: break-word;
            font-size: 14px;
            letter-spacing: .1px;
            line-height: 20px;
            font-weight: 400;
            text-transform: none;
            color: black;
            border-bottom: 1px solid black;
        }

        .comment-item {
            margin-top: 20px;
            display: flex;
        }

        .comment-list {
            margin-top: 40px
        }

        .comment-item_right {
            margin-left: 20px
        }

        .section-button {
            display: flex;
            /* grid-template-columns: repeat(4 1fr);
                                                                                                                                                                                                                                                                            gap: 50px; */
            justify-content: center;
            align-content: center
        }

        .section-button_item {
            width: 25%;
        }

        .section-button_item p {
            text-align: center
        }

        .section-button_item i {
            font-size: 24px;
            cursor: pointer;
        }

        .btn-comment {
            padding: 5px;
            border-radius: 5px;
        }

        .comment-child {
            margin-top: 20px
        }

        .comment-item_right {
            width: 100%;
        }

        .comment-item_right .time {
            margin-left: 5px;
            font-weight: 400;
        }

        .reply-btn {
            cursor: pointer;
        }

        .reply-form {
            display: none;

        }

        .reply-form .comment-writing_text {
            margin-top: 10px
        }

        .tagname {
            color: red
        }

        .btn-see-more {
            margin-top: 10px;
            border: 1px solid;
            padding: 10px;
            display: inline-block;
            line-height: 1;
            border-radius: 5px;

            background-color: #000;
            color: #fff
        }

        .btn-see-more:hover {
            background: #fff;
            color: #333;

        }

        /* Return Policy Styles */
        .return-policy-section {
            margin: 40px 0 20px;
        }

        .return-policy-accordion {
            border: 1px solid hsla(0, 0%, 100%, .16);
            border-radius: 10px;
            overflow: hidden;
            background-color: #262626;
        }

        .return-policy-header {
            background-color: #262626;
            cursor: pointer;
            padding: 15px;
            position: relative;
        }

        .return-policy-header h5 {
            margin: 0;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .return-policy-toggle-icon {
            transition: transform 0.3s;
        }

        .return-policy-toggle-icon.collapsed {
            transform: rotate(-90deg);
        }

        .return-policy-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background-color: #262626;
        }

        .return-policy-content.active {
            max-height: 300px;
            overflow-y: auto;
        }

        .return-policy-body {
            padding: 15px;
            color: white;
        }

        .return-policy-body h2,
        .return-policy-body h3,
        .return-policy-body h4 {
            color: white;
            margin-bottom: 10px;
        }

        .return-policy-body p {
            margin-bottom: 10px;
        }
    </style>
@endpush
@section('content')
    <div class="content-detail-producer container p-20">
        <aside>
            <section class="section-info">
                <a href="{{ Link::producerDetail($track->id ?? '') }}">
                    <div class="section-info_avater">
                        {!! Template::showTrackThumbnail($file) !!}
                    </div>
                    <div class="section-info_name">
                        <h2 class="text-center">{{ $track->name ?? 'Name of the song' }}</h2>
                        <p class="text-center">{{ $track->user->fullname ?? '' }}</p>
                    </div>
                </a>
            </section>
            <section class="section-button">
                <div class="section-button_item btn-favourite-track {{ $checkFavourite ? 'active' : '' }}"
                    data-login="{{ rrt_check_login() ? '1' : '0' }}" data-track-id="{{ $track['id'] }}"
                    data-url="{{ rrt_route($controllerName . '/postFavourite', ['track_id' => $track['id'] ?? '']) }}">
                    <p><a href="#"></a><i class="fa fa-heart" aria-hidden="true"></i></p>
                    <p>{{ $totalFavourite ?? 0 }}</p>
                </div>
                {{-- <div class="section-button_item">
                    <p><a href="#"><i class="fa fa-retweet" aria-hidden="true"></i></a></p>
                    <p>{{ $track->like->count ?? 0 }}</p>
                </div>
                <div class="section-button_item">
                    <p><a href="#"><i class="fa fa-plus" aria-hidden="true"></i></a></p>
                </div>
                <div class="section-button_item">
                    <p><a href="#"><i class="fa fa-upload" aria-hidden="true"></i></a></p>
                </div> --}}
            </section>
            <section class="section-starts">
                <h4> {{__('Infomation')}} </h4>
                <div class="section-starts_item">
                    <span> {{__('Published')}} </span>
                    <span>{{ \Carbon\Carbon::parse($track->created_at)->format('M j, Y') }}</span>
                </div>
                <div class="section-starts_item">
                    <span> {{__('Plays')}} </span>
                    <span>{{ $track->download->count() }}</span>
                </div>
                <div class="section-starts_item">
                    <span> {{__('Key')}} </span>
                    <span>{{ $track->track_key_id }}</span>
                </div>
                <div class="section-starts_item">
                    <span> {{__('BPM')}} </span>
                    <span>{{ $track->bpm_number }}</span>
                </div>
            </section>
            <hr>
            {{-- <section class="section-products">
                <h4>Tag </h4>
                <div class="section-products_item">
                    @foreach ($track->listTags as $item)
                        <span class="bage badge-product">{{ $item->name }}</span>
                    @endforeach
                </div>
            </section> --}}
            <section class="section-aboutme">
                <h4> {{__('Genres')}} </h4>
                <div class="section-aboutme_item">
                    @foreach ($track->genres as $item)
                        <span class="bage badge-product">{{ $item->name }}</span>
                    @endforeach
                </div>
            </section>
        </aside>
        <main>
            @php
                $type = $track->type ?? 'track';

            @endphp
            @if ($type == 'track')
                <div id="iframe-audio">
                    <iframe scrolling="no" width="100%" style="background-color: #fff;"
                        src="{{ rrt_route('public/track/getAudio', ['code' => $track->code]) }}" frameborder="0"></iframe>
                </div>
                <section class="licensing">
                    <div class="licensing-header">
                        <div class="licensing-header_title">
                            <h4>Licensing</h4>
                        </div>
                        <div class="licensing-header_button">
                            <span style="padding:15px">Total: <p>
                                    @if (count($track->list_contracts))
                                        {{ rrt_show_price($track->list_contracts[0]->price) }}
                                    @else
                                        Updating
                                    @endif
                                </p></span>
                            @foreach ($track->list_contracts as $item)
                                @if ($item->contractSetting->category == 'free')
                                    <button data-code="{{ $track->code }}" id="download_track" style="margin-right: 20px">
                                        <i class="fa fa-download"></i>
                                        <span>Download</span>
                                    </button>
                                @endif
                            @endforeach
                            @if (count($track->list_contracts))
                                <button class="btn-add-cart" data-id="{{ $track->id ?? '' }}"
                                    data-url="{{ rrt_route('public/cart/postAddCart') }}"
                                    data-login="{{ rrt_check_login() ? '1' : '0' }}">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span>Add To Cart</span>
                                </button>
                            @endif

                        </div>
                    </div>
                    <hr>
                    <div class="licensing-card">
                        @foreach ($track->list_contracts as $key => $contact)
                            <div class="licensing-card_item {{ $key == 0 ? 'active' : '' }}"
                                data-id="{{ $contact->id ?? '' }}"
                                data-name="{{ $contact->contractSetting->contract_info->name ?? '' }}"
                                data-deliverables="{{ $contact->contractSetting->deliverables }}">
                                <h4>{{ $contact->contractSetting->contract_info->name }}</h4>
                                <p>{{ rrt_show_price($contact->price) }}</p>
                                <span>{{ rrt_get_deliverables_name($contact->contractSetting->deliverables) }}</span>
                                <div class="license-dropdown">
                                    <span class="license-info-toggle"><i class="fa fa-chevron-down"></i></span>
                                    <div class="license-dropdown-content">
                                        <p><strong>Deliverables:</strong>
                                            {{ rrt_get_deliverables_name($contact->contractSetting->deliverables) }}</p>
                                        @if ($contact->contractSetting->description)
                                            <p><strong>Description:</strong> {{ $contact->contractSetting->description }}
                                            </p>
                                        @endif
                                        @if ($contact->contractSetting->featureList)
                                            <p><strong>Features:</strong> {{ $contact->contractSetting->featureList }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- License Offers Section -->
            @php
                $licenseOffersPage = \App\Models\PageModel::where([
                    'type' => 'shop-policy',
                    'slug' => 'license-offers',
                ])->first();

                // Get translation if available
                $licenseTranslation = null;
                if ($licenseOffersPage) {
                    $currentLocale = app()->getLocale();

                    $licenseTranslation = \App\Models\PageTranslationModel::where('page_id', $licenseOffersPage->id)
                        ->where('language', $currentLocale)
                        ->first();
                }
            @endphp
            <div class="return-policy-section">
                <div class="return-policy-accordion">
                    <div class="return-policy-header" id="licenseOffersToggle">
                        <h5>
                            <span>
                                @if ($licenseOffersPage)
                                    @if ($licenseTranslation && !empty($licenseTranslation->content))
                                        {{ $licenseTranslation->name }}
                                    @else
                                        <h5>{{ $licenseOffersPage->name }}</h5>
                                    @endif

                                @endif
                            </span>
                            <i class="fas fa-chevron-down return-policy-toggle-icon"></i>
                        </h5>




                    </div>
                    <div class="return-policy-content">
                        <div class="return-policy-body">


                            @if ($licenseOffersPage)
                                @if ($licenseTranslation && !empty($licenseTranslation->content))
                                    {!! $licenseTranslation->content !!}
                                @else
                                    {!! $licenseOffersPage->content !!}
                                @endif
                            @else
                                <p>License offers information is not available at this time.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- End License Offers Section -->

            <!-- Return Policy Section -->
            <div class="return-policy-section">
                @php
                    $policyPage = \App\Models\PageModel::where([
                        'type' => 'shop-policy',
                        'slug' => 'refundreturn-policy',
                    ])->first();

                    // Get translation if available
                    $policyTranslation = null;
                    if ($policyPage) {
                        $currentLocale = app()->getLocale();
                        $policyTranslation = \App\Models\PageTranslationModel::where('page_id', $policyPage->id)
                            ->where('language', $currentLocale)
                            ->first();
                    }
                @endphp
                <div class="return-policy-accordion">
                    <div class="return-policy-header" id="returnPolicyToggle">
                        <h5>
                            <span>
                                @if ($policyPage)
                                    @if ($policyTranslation && !empty($policyTranslation->content))
                                        {{ $policyTranslation->name }}
                                    @else
                                        <h5>{{ $policyPage->name }}</h5>
                                    @endif
                                @endif
                            </span>
                            <i class="fas fa-chevron-down return-policy-toggle-icon"></i>
                        </h5>
                    </div>
                    <div class="return-policy-content">
                        <div class="return-policy-body">


                            @if ($policyPage)
                                @if ($policyTranslation && !empty($policyTranslation->content))
                                    {!! $policyTranslation->content !!}
                                @else
                                    {!! $policyPage->content !!}
                                @endif
                            @else
                                <p>Return policy information is not available at this time.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Return Policy Section -->

            <div class="section-comment">
                <div class="comment-header">
                    <h4> {{__('Comments')}} </h4>
                </div>

                <div class="comment-writing">
                    <div class="comment-writing_user">
                        <img style="height:40px;" src="{{ asset('public/images/default-avatar-circle.svg') }}"
                            alt="">
                    </div>
                    <div class="comment-writing_text">
                        <input type="text" id="content_comment" placeholder="{{__('Share your thought')}}">
                    </div>
                    <div class="comment-writing_send">
                        <span style="cursor: pointer" data-track="{{ $track->id }}" data-parent="0"
                            data-url="{{ rrt_route($controllerName . '/postComment') }}"
                            data-login="{{ rrt_check_login() ? '1' : '0' }}" id="btn-send-comment" class="btn-comment">
                            <i class="fa fa-send"></i></span>
                    </div>
                </div>
                <div class="comment-list" id="comment-list"
                    data-urlpostcommnet="{{ rrt_route($controllerName . '/postComment') }}"
                    data-url="{{ rrt_route($controllerName . '/getCommentToTrack') }}" data-id="{{ $track->id }}">
                    {{-- <div class="comment-item">
                        <div class="comment-item_left">
                            <img height="30px" src="{{ asset('public/images/default-avatar-circle.svg') }}"
                                alt="">
                        </div>
                        <div class="comment-item_right">
                            <h6>DUY HANDSOME <span>1h ago</span></h6>
                            <p>He's very very handsome.</p>
                            <i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;
                            <a href="#">Repply</a>
                            <div class="comment-item_right comment-child">
                                <h6>DUY HANDSOME <span>1h ago</span></h6>
                                <p>He's very very handsome.</p>
                                <i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;
                                <a href="#">Repply</a>
                            </div>
                        </div>
                    </div>
                    <div class="comment-item">
                        <div class="comment-item_left">
                            <img height="30px" src="{{ asset('public/images/default-avatar-circle.svg') }}"
                                alt="">
                        </div>
                        <div class="comment-item_right">
                            <h6>GIANG CA ALONE <span>1h ago</span></h6>
                            <p>He's very very alone.</p>
                            <i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;
                            <a href="#">Repply</a>
                        </div>
                    </div> --}}
                </div>
            </div>
        </main>
    </div>
@endsection
@push('srcipt')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        // Return policy accordion animation
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('returnPolicyToggle').addEventListener('click', function() {
                this.querySelector('.return-policy-toggle-icon').classList.toggle('collapsed');
                var content = this.nextElementSibling;
                if (content.classList.contains('active')) {
                    content.classList.remove('active');
                } else {
                    content.classList.add('active');
                }
            });

            document.getElementById('licenseOffersToggle').addEventListener('click', function() {
                this.querySelector('.return-policy-toggle-icon').classList.toggle('collapsed');
                var content = this.nextElementSibling;
                if (content.classList.contains('active')) {
                    content.classList.remove('active');
                } else {
                    content.classList.add('active');
                }
            });
        });

        $('.licensing-card_item').click(function(e) {
            let text_price = $(this).children('p').text();
            $('.licensing-card_item').removeClass('active')
            $(this).addClass('active')
            $('.licensing-header_button span p').text(text_price)
        })
        $('#download_track').click(function() {
            let code = $(this).data('code');
            let url = "{{ rrt_route('public/track/download') }}";
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                    code: code
                },
                success: function(res) {
                    if (res.status == 403) {
                        return showNotify("error", "Error", "Please Sign In");
                    }
                    if (res.error) {
                        return showNotify("error", "Error", res.error);
                    }
                    downloadMP3(res.url, res.fileName);
                }
            });
        })

        // Handle license dropdown buttons
        $(document).on('click', '.license-info-toggle', function(e) {
            e.stopPropagation();
            $(this).toggleClass('active');
            const dropdownContent = $(this).next('.license-dropdown-content');
            $('.license-dropdown-content').not(dropdownContent).removeClass('license-dropdown-show');
            $('.license-info-toggle').not($(this)).removeClass('active');
            dropdownContent.toggleClass('license-dropdown-show');
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.license-dropdown').length) {
                $('.license-dropdown-content').removeClass('license-dropdown-show');
                $('.license-info-toggle').removeClass('active');
            }
        });

        const btnFavouriteTrack = $(".btn-favourite-track");
        btnFavouriteTrack.click(function() {
            let id = $(this).data('id');
            let isLogin = $(this).data('login');
            let trackId = $(this).data('track-id');


            let countEle = $(this).find('p:last-child');
            let count = countEle.text();
            let url = $(this).data('url');
            if (!isLogin) {
                return showNotify("error", "Error", "Please Sign In")
            }
            let data = {};
            let checkActive = 0;
            if ($(this).hasClass('active')) {
                checkActive = 1;
            }
            data.check_active = checkActive;
            data.track_id = trackId;
            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: "json",
                success: function(response) {
                    const total = response.total ? response.total : 0;
                    countEle.text(total);
                    if (checkActive == 1) {
                        btnFavouriteTrack.removeClass('active');
                        return showNotify("success", "Success", "Unfavourite Success")
                    } else {
                        btnFavouriteTrack.addClass('active');
                        return showNotify("success", "Success", "Favourite Success")
                    }

                }
            });
        })
        const btnAddCart = $(".btn-add-cart");
        btnAddCart.click(function() {
            let id = $(this).data('id');
            let isLogin = $(this).data('login');
            console.log("isLogin: ", isLogin);
            let contractID = $(".licensing-card_item.active").data('id');
            let contractName = $(".licensing-card_item.active").data('name');
            let contractDeliverables = $(".licensing-card_item.active").data('deliverables');
            let url = $(this).data('url');
            if (isLogin == 0) {
                return showNotify("error", "{{ __('Error') }}", "{{ __('Please Sign In') }}")
            }
            let data = {
                track_id: id,
                contract_id: contractID,
                contract_name: contractName,
                contract_deliverables: contractDeliverables,
            };
            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: "json",
                success: function(response) {
                    const count = response.count ? response.count : 0;
                    const cartTotal = $("#cart-total");
                    $(".btn-account").addClass('no-dropdown');


                    cartTotal.html(count);
                    return showNotify("success", "Success", "Add Cart Success")
                }
            });


        })

        $(document).on('click', '.btn-comment', function(e) {
            let is_login = $('#btn-send-comment').data('login');
            let id = $(this).data('comment');
            if (is_login == 1) {
                let val_comment = $('#comment-' + id).val();

                $(document).on('input', '#comment-' + id, function() {
                    // Get the value of the dynamically added input field
                    val_comment = $(this).val();

                });

                if (val_comment == 'undifiend' || val_comment == '') {
                    return showNotify("error", "Error", "Content comment empty")
                } else {
                    let url = $('#comment-list').data('urlpostcommnet');
                    let track_id = $(this).data('track');
                    let parent = $(this).data('parent');
                    if (parent == 0) {
                        val_comment = $('#content_comment').val();
                    }
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            comment: val_comment,
                            track_id: track_id,
                            parent: parent
                        },
                        dataType: "json",
                        success: function(response) {

                            if (response.status == 403) {
                                return showNotify("error", "Error", "No permission")
                            }
                            $(this).val('');
                            if (parent == 0) {
                                $('#content_comment').val();
                            }
                            getListConmentToTrack();

                        }
                    });
                }
            } else {
                return showNotify("error", "Error", "Please Sign In")
            }
        })

        function getListConmentToTrack() {
            const list_conment = $('#comment-list');
            let url = list_conment.data('url');
            let track_id = list_conment.data('id');
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    track_id: track_id,

                },
                dataType: "json",
                success: function(response) {
                    let html = render_comment(response.data)
                    list_conment.html(html);
                }
            });
        }

        function render_comment(data, child = '', name = '', level = 0, id = 0) {

            let html = '';
            level += 1;
            let count = data.length;
            $.each(data, function(i, v) {

                let locale = '{{ request()->locale }}';
                let url = `${locale}/producer/${v.user.username}-${v.user.id}`;
                html += `<div class="comment-item ${child}">
                    <div class="comment-item_left">
                        <img style="height:30px" src="{{ asset('public/images/default-avatar-circle.svg') }}"
                            alt="">
                    </div>
                    <div class="comment-item_right">
                        <h6><a target="_blank" href="/${url}">${ v.user.first_name + ' ' + v.user.last_name }</a><span class="time">${formatTimeAgo(v.created_at)}</span></h6>
                        <p><span class="tagname">${name}</span> ${v.content}.</p>
                        <span   class="reply-btn">Repply</span>
                        <div class="reply-form">
                            <div style="display:flex">
                                <div class="comment-writing_text">
                                    <input type="text" id="comment-${v.id}"  placeholder="Share your thought">
                                </div>
                                <div class="comment-writing_send">
                                    <span style="cursor: pointer" data-track="${v.track_id}" data-parent="${level > 3 ? id : v.id}" data-comment="${v.id}" data-login="0" class="btn-comment">
                                    <i class="fa fa-send"></i>
                                    </span>
                                </div>
                            </div>
                    </div>`;
                if (v.comment.length) {

                    let name = v.user.first_name + ' ' + v.user.last_name;

                    html += render_comment(v.comment, 'comment-child', name, level, v.id)

                } else {


                    html += `</div>

                </div>`;

                    if (count == i + 1) {
                        html +=
                            `<button data-level="${level}" data-count="1" data-comment="${v.id}" class="btn-see-more">See more comment</button>`
                    }

                }

            });

            html += `</div>
                </div>`;
            return html;

        }
        $(document).on('click', '.reply-btn', function() {

            $(this).siblings('.reply-form').slideToggle();


        });


        function formatTimeAgo(dateTimeString) {

            const currentTime = moment();
            const timeAgo = moment(dateTimeString);
            const duration = moment.duration(currentTime.diff(timeAgo));

            if (duration.asSeconds() < 60) {
                return Math.round(duration.asSeconds()) + ' seconds ago';
            } else if (duration.asMinutes() < 60) {
                return Math.round(duration.asMinutes()) + ' minutes ago';
            } else if (duration.asHours() < 24) {
                return Math.round(duration.asHours()) + ' hours ago';
            } else if (duration.asDays() < 365) {
                return Math.round(duration.asDays()) + ' days ago';
            } else {
                return Math.round(duration.asYears()) + ' years ago';
            }
        }

        function render_comment_see_more() {

            $(document).on('click', '.btn-see-more', function() {
                let id = $(this).data('comment');
                let url = "{{ rrt_route($controllerName . '/seeMoreComment') }}";
                let count = $(this).data('count');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        id: id,
                        count: count,

                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 200) {
                            let data = response.data;
                            let html = '';
                            $.each(data, function(i, v) {
                                html += `<div class="comment-item comment-child">
                    <div class="comment-item_left">
                        <img height="30px" src="{{ asset('public/images/default-avatar-circle.svg') }}"
                            alt="">
                    </div>
                    <div class="comment-item_right">
                        <h6><a target="_blank" href="/${url}">${ v.user.first_name + ' ' + v.user.last_name }</a><span class="time">${formatTimeAgo(v.created_at)}</span></h6>
                        <p><span class="tagname">${name}</span> ${v.content}.</p>
                        <span   class="reply-btn">Repply</span>
                        <div class="reply-form">
                            <div style="display:flex">
                                <div class="comment-writing_text">
                                <input type="text" id="comment-${v.id}"  placeholder="Share your thought">
                            </div>
                            <div class="comment-writing_send">
                                <span style="cursor: pointer" data-track="${v.track_id}" data-parent="${ v.id}" data-comment="${v.id}" data-login="0" class="btn-comment">
                                <i class="fa fa-send"></i>
                            </span>
                            </div>
                </div>
                        </div>`;
                                $('.btn-see-more').data('comment', v.id)
                            });
                            $('.btn-see-more').before(html);

                        } else {

                            $('.btn-see-more').css('display', 'none');
                        }
                    }
                });
            })
        }
        $(document).ready(function() {
            getListConmentToTrack();
            render_comment_see_more();
        });
    </script>
@endpush
