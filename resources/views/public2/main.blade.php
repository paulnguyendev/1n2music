<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="{{ asset('studio/css') }}/style.css?ver={{ time() }}">

    @include('public2.elements.head')
    @stack('css')
    <style>
        tbody td {
            border: 1px solid rgba(120, 130, 140, 0.25) !important;
        }

        tbody td:nth-child(-n+4) {
            border-left: none !important;
            border-right: none !important;
        }

        tbody td:first-child {
            border-left: 1px solid rgba(120, 130, 140, 0.25) !important;
        }

        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
            /* Dark overlay */
        }

        /* Modal content box */
        .modal-content {
            position: relative;
            margin: 15% auto;
            padding: 20px;
            border: none;
            width: 80%;
            max-width: 600px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        /* Close button */
        .modal-close {
            position: absolute;
            top: 0;
            right: 5px;
            color: #000000;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .modal-close:hover,
        .modal-close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        /* Image styling */
        .popup-image {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        /* Footer styling */
        .modal-footer {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-footer label {
            margin: 0;
            margin-right: 10px;
        }

        .modal-footer input[type="checkbox"] {
            position: inherit;
            opacity: 1;
            width: inherit;
            height: inherit;
        }

        .track-genres-item img.active {
            border: 3px solid #701919;
            padding: 2px;
        }

        .list-mood-item.active {
            background: rgba(0, 0, 0, 1);
        }

        .track-feature-wrap {
            height: auto;
            padding-bottom: 40px;
        }

        .language-select {
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
            font-size: 14px;
            color: #333;
        }

        .language-select:focus {
            outline: none;
            border-color: #007bff;
        }
    </style>
</head>

<body class= "@yield('body_class', 'body-wrap')">
    <header id="header">
        <div class="header-top">
            @if ($popup)
                <div id="popupModal" class="modal">
                    <div class="modal-content">
                        <span class="modal-close">&times;</span>
                        <div class="modal-body">
                            @if ($popup->embed_link)
                                <a href="{{ $popup->embed_link ?? '#' }}" target="_blank">
                                    <img src="{{ url('public/uploads/popup/' . $popup->pop_image) }}" alt="Popup Image"
                                        class="popup-image">
                                </a>
                            @else
                                <img src="{{ url('public/uploads/popup/' . $popup->pop_image) }}" alt="Popup Image"
                                    class="popup-image">
                            @endif
                        </div>
                        <div class="modal-footer">
                            <label for="dontShowAgain"> {{ __('Don’t show again for a week') }}</label>
                            <input type="checkbox" id="dontShowAgain">
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="header-main">
            <div class="container">
                <div id="header-main-left">
                    <div id="logo">
                        <a href="{{ rrt_route('public/home/index') }}">
                            <img src="{{ isset($footer) && isset($footer['setting_logo_header']) ? url('public/uploads/logo/' . $footer['setting_logo_header']) : asset('public/style2/img/1N2Logo 2.png') }}"
                                alt=""
                                onerror="this.onerror=null;this.src='{{ asset('public/style2/img/1N2Logo 2.png') }}';">
                        </a>
                    </div>
                </div>
                <div id="header-main-center">
                    <div id="menu-main">
                        <ul class="list-none">
                            <li class="{{ Route::currentRouteName() == 'public/home/index' ? 'active' : '' }}">
                                <a href="{{ rrt_route('public/home/index') }}">{{ __('Home') }}</a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'public/market/index' ? 'active' : '' }}">
                                <a href="{{ rrt_route('public/market/index') }}">{{ __('Track') }}</a>
                            </li>
                            {{--                            <li class="{{ Route::currentRouteName() == 'public/threads/index' ? 'active' : '' }}"> --}}
                            {{--                                <a href="{{ rrt_route('public/threads/index') }}">Threads</a> --}}
                            {{--                            </li> --}}
                            <li class="{{ Route::currentRouteName() == 'public/freeboards/index' ? 'active' : '' }}">
                                <a href="{{ rrt_route('public/freeboards/index') }}">{{ __('Free board') }}</a>
                            </li>
                            <li
                                class="{{ Route::currentRouteName() == 'public/join/distribution/index' ? 'active' : '' }}">
                                <a
                                    href="{{ rrt_route('public/join/distribution/index') }}">{{ __('Digital Distribution') }}</a>
                            </li>
                            <li
                                class="{{ Route::currentRouteName() == 'public/join/publishing/index' ? 'active' : '' }}">
                                <a href="{{ rrt_route('public/join/publishing/index') }}">{{ __('Publishing') }}</a>
                            </li>
                            @if (rrt_check_login() && rrt_role_buy_package())
                                <li
                                    class="{{ Route::currentRouteName() == 'public/join/seller/index' ? 'active' : '' }}">
                                    <a href="{{ rrt_route('public/join/seller/index') }}">{{ __('Seller') }}</a>
                                </li>
                            @endif
                            {{-- @if (rrt_check_login())
                                <li
                                    class="{{ Route::currentRouteName() == 'public/producer/detail' ? 'active' : '' }}">
                                    <a
                                        href="{{ rrt_route('public/producer/detail', ['username' => rrt_get_user_login('username'), 'user_id' => rrt_get_user_login('id')]) }}">My
                                        Account</a>
                                </li>
                            @endif --}}
                        </ul>
                    </div>
                </div>
                <div id="header-main-right">
                    <ul class="list-none">
                        @if (!rrt_check_login())
                            <li><a href="{{ rrt_route('public/auth/signIn') }}">{{ __('Sign in') }}</a></li>
                            <li><a href="{{ rrt_route('public/auth/signUp') }}">{{ __('Sign up') }}</a></li>
                            <li class="btn-gradient">
                                <a href="{{ rrt_route('public/auth/signUp', ['start_selling' => 'true']) }}">
                                    <img src="{{ asset('public/style2/img/icon_selling.svg') }}" alt="">
                                    <span>{{ __('Start selling') }}</span>
                                </a>
                            </li>
                        @else
                            <li><a href="{{ rrt_route('public/auth/logout') }}">{{ __('Sign out') }}</a></li>
                            <li class="btn-gradient">
                                <a href="{{ rrt_route('public/studio/home/index') }}">
                                    <img src="{{ asset('public/style2/img/icon_studio.svg') }}" alt="">
                                    <span>{{ __('Studio') }}</span>
                                </a>
                            </li>
                        @endif
                        <li class="menu-mobile">
                            <a href="">
                                <img src="{{ asset('public/style2/img/solar_menu-dots-linear.svg') }}" alt="">
                            </a>
                        </li>
                        <li>
                            <a href="{{ rrt_route('public/cart/index') }}">
                                <img src="{{ asset('public/style2/img/icon_cart.svg') }}" alt="">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="header-bottom">
            <div class="container">
                <div class="fixed-toggle-button">
                    <div class="mob-collapse">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="container">
                <div id="menu-header-bottom">
                    <ul class="list-none">
                        <!-- <li class="{{ Route::currentRouteName() == 'public/studio/myList/index' ? 'active' : '' }}">
                            <a href="{{ rrt_route('public/studio/myList/index') }}">
                                <img src="{{ asset('public/style2/img/icon_my_list.svg') }}" alt="">
                                <span>{{ __('My List') }}</span>
                            </a>
                        </li> -->
                        <li class="{{ Route::currentRouteName() == 'public/producer/my-producer' ? 'active' : '' }}">
                            <a href="{{ rrt_route('public/producer/my-producer') }}">
                                <img src="{{ asset('public/style2/img/icon_my_list.svg') }}" alt="">
                                <span>{{ __('My Producers') }}</span>
                            </a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'public/studio/favourite/index' ? 'active' : '' }}">
                            <a href="{{ rrt_route('public/studio/favourite/index') }}">
                                <img src="{{ asset('public/style2/img/icon_favourite.svg') }}" alt="">
                                <span>{{ __('Favorites') }}</span>
                            </a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'public/studio/history/index' ? 'active' : '' }}">
                            <a href="{{ rrt_route('public/studio/history/index') }}">
                                <img src="{{ asset('public/style2/img/icon_history.svg') }}" alt="">
                                <span>{{ __('History') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ rrt_route('public/studio/sale/index') }}">
                                <img src="{{ asset('public/style2/img/icon_order.svg') }}" alt="">
                                <span>{{ __('Orders') }}</span>
                            </a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'public/studio/giftcard/index' ? 'active' : '' }}">
                            <a href="{{ rrt_route('public/studio/giftcard/index') }}">
                                <img src="{{ asset('public/style2/img/icon_gift_card.svg') }}" alt="">
                                <span>{{ __('Gift Card') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ rrt_route('public/studio/account/index') }}">
                                <img src="{{ asset('public/style2/img/icon_account_setting.svg') }}" alt="">
                                <span>{{ __('Account Setting') }}</span>
                            </a>
                        </li>
                        <li>
                            <select id="languageSwitcher" class="language-select">
                                @foreach ($languages as $lang)
                                    <option value="{{ $lang->code }}"
                                        {{ app()->getLocale() == $lang->code ? 'selected' : '' }}>
                                        {{ $lang->name }}
                                    </option>
                                @endforeach
                            </select>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <main id="main" class="@yield('main_class', 'main')">
        @yield('content')
    </main>
    <div class="menu-mobile-wrap">
        <div class="menu-mobile-inner">
            <div class="container">
                <div class="menu-mobile-header">
                    <div class="menu-mobile-logo">
                        <img src="{{ asset('public/style2/img/logo-light.png') }}" alt="">
                    </div>
                    <div class="btn-close-menu-mobile">
                        &times;
                    </div>
                </div>
                <div class="menu-mobile-content">
                    <ul>
                        <li class="{{ Route::currentRouteName() == 'public/home/index' ? 'active' : '' }}">
                            <a href="{{ rrt_route('public/home/index') }}">{{ __('Home') }}</a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'public/market/index' ? 'active' : '' }}">
                            <a href="{{ rrt_route('public/market/index') }}">{{ __('Track') }}</a>
                        </li>
                        {{--                            <li class="{{ Route::currentRouteName() == 'public/threads/index' ? 'active' : '' }}"> --}}
                        {{--                                <a href="{{ rrt_route('public/threads/index') }}">Threads</a> --}}
                        {{--                            </li> --}}
                        <li class="{{ Route::currentRouteName() == 'public/freeboards/index' ? 'active' : '' }}">
                            <a href="{{ rrt_route('public/freeboards/index') }}">{{ __('Free board') }}</a>
                        </li>
                        <li
                            class="{{ Route::currentRouteName() == 'public/join/distribution/index' ? 'active' : '' }}">
                            <a
                                href="{{ rrt_route('public/join/distribution/index') }}">{{ __('Digital Distribution') }}</a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'public/join/publishing/index' ? 'active' : '' }}">
                            <a href="{{ rrt_route('public/join/publishing/index') }}">{{ __('Publishing') }}</a>
                        </li>
                        @if (rrt_check_login() && rrt_role_buy_package())
                            <li class="{{ Route::currentRouteName() == 'public/join/seller/index' ? 'active' : '' }}">
                                <a href="{{ rrt_route('public/join/seller/index') }}">{{ __('Seller') }}</a>
                            </li>
                        @endif
                        {{-- @if (rrt_check_login())
                            <li
                                class="{{ Route::currentRouteName() == 'public/producer/detail' ? 'active' : '' }}">
                                <a
                                    href="{{ rrt_route('public/producer/detail', ['username' => rrt_get_user_login('username'), 'user_id' => rrt_get_user_login('id')]) }}">My
                                    Account</a>
                            </li>
                        @endif --}}
                    </ul>
                    <div class="menu-mobile-bottom">
                        <ul>
                            @if (!rrt_check_login())
                                <li><a href="{{ rrt_route('public/auth/signIn') }}">{{ __('Sign In') }}</a></li>
                                <li><a href="{{ rrt_route('public/auth/signUp') }}">{{ __('Sign Up') }}</a></li>
                            @else
                                <li><a href="{{ rrt_route('public/auth/logout') }}">{{ __('Sign Out') }}</a></li>
                                <li><a href="{{ rrt_route('public/studio/home/index') }}">{{ __('Studio') }}</a>
                                </li>
                                <li><a
                                        href="{{ rrt_route('public/auth/signUp', ['start_selling' => 'true']) }}">{{ __('Start selling') }}</a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="music-player">
        <div class="close-player">&times;</div>
        <div class="container">
            <div class="music-player-inner trackbar-inner">
                <div class="music-info">
                    <img src="{{ asset('public/style2/img/author-thumb.png') }}" alt="Artist"
                        class="trackbar-author-thumb">
                    <div class="music-details">
                        <h3><a href="#" class="trackbar-title"
                                style="color:white;">{{ __('Smile In My Face') }}</a></h3>
                        <div class="music-detail-meta ">
                            <div class="music-detail-meta-text trackbar-meta-text">
                                {{-- <p>Thai VG</p>
                                <p>100.000 $ • 120 BPM</p> --}}
                            </div>
                            <div class="music-detail-buttons trackbar-buttons">
                                {{-- <button class="btn btn-download-track">
                                    <img src="{{ asset('public/style2/img/icon_download.svg') }}" alt="">
                                </button>
                                <button class="btn btn-add-cart-track">
                                    <img src="{{ asset('public/style2/img/icon_cart.svg') }}" alt="">
                                </button>
                                <button class="btn btn-add-favourite-track">
                                    <img src="{{ asset('public/style2/img/icon_favourite_dark.svg') }}" alt="">
                                </button> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="controls">
                    <div class="buttons">
                        <button id="prev"><svg class="icon-svg" viewBox="0 0 24 24"><path d="M11 19V5l-11 7 11 7zm11 0V5l-11 7 11 7z"/></svg></button>
                        <button class="play-pause"><svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></button>
                        <button id="next"><svg class="icon-svg" viewBox="0 0 24 24"><path d="M4 19l11-7L4 5v14zm11 0l11-7-11-7v14z"/></svg></button>
                    </div>
                </div>
                <div class="audio-wave">
                    <div class="visualizer" id="waveform"></div>
                    <div class="progress-bar">
                        <div class="progress" id="progress"></div>
                    </div>
                    <div class="time">
                        <span id="current-time">0:00</span> / <span id="duration">7:48</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loading">
        <div class="music-waves-2">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <footer id="footer">
        <div class="container">
            <div class="footer-info">
                <div class="footer-logo">
                    <img src="{{ isset($footer) && isset($footer['setting_logo_footer']) ? url('public/uploads/logo/' . $footer['setting_logo_footer']) : asset('public/style2/img/logo-vertical.png') }}"
                        alt=""
                        onerror="this.onerror=null;this.src='{{ asset('public/style2/img/logo-vertical.png') }}';">
                </div>
                <div class="footer-info-list">
                    <div class="footer-info-item">
                        <img src="{{ asset('public/style2/img/carbon_location-company.svg') }}" alt="">
                        <span>{{ __('Company Name') }}:
                            {{ isset($footer) && isset($footer['company_name']) ? $footer['company_name'] : '1N2 Music Co., Ltd.' }}</span>
                    </div>
                    <div class="footer-info-item">
                        <img src="{{ asset('public/style2/img/icon-park-outline_user-business.svg') }}"
                            alt="">
                        <span>{{ __('Founders') }}:
                            {{ isset($footer) && isset($footer['founder']) ? $footer['founder'] : 'Hoon Oh & Peter Lee' }}</span>
                    </div>
                    <div class="footer-info-item rd-mobile-tablet">
                        <img src="{{ asset('public/style2/img/mdi_address-marker-outline.svg') }}" alt="">
                        <span>{{ __('Address') }}:
                            {{ isset($footer) && isset($footer['address']) ? $footer['address'] : '29-13, Seongbok 2-ro 301beon-gil, Suji-gu, Yongin-si, Gyeonggi-do, South Korea' }}</span>
                    </div>
                    <div class="footer-info-item">
                        <img src="{{ asset('public/style2/img/ion_business-outline.svg') }}" alt="">
                        <span>{{ __('Business registration') }}:
                            {{ isset($footer) && isset($footer['business']) ? $footer['business'] : '220-88-21698' }}</span>
                    </div>
                </div>
            </div>
            @php
                $footerLinks = \App\Helpers\Link::getFooterLink();
                $footerSocial = \App\Helpers\Link::getFooterSocial();
            @endphp
            <div class="footer-links">
                @if (isset($footerLinks) && $footerLinks->isNotEmpty())
                    @foreach ($footerLinks as $column)
                        <div class="footer-link-item">
                            <p class="footer-title">{{ __($column['title'] ?? '') }}</p>
                            <ul class="list-none">
                                @foreach ($column['links'] as $link)
                                    @if ($link['url'])
                                        <li><a href="{{ $link['url'] }}">{{ __($link['title'] ?? '') }}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                @else
                @endif

                @if (isset($footerSocial['data']) &&
                        count($footerSocial['data']) &&
                        isset($footerSocial['title']) &&
                        $footerSocial['title']
                )
                    <div class="footer-link-item">
                        <p class="footer-title">{{ __($footerSocial['title']['title'] ?? '') }}</p>
                        <ul class="list-none">
                            @foreach ($footerSocial['data'] as $link)
                                @if ($link['url'])
                                    <li><a href="{{ $link['url'] }}">{{ __($link['title'] ?? '') }}</a></li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="footer-form">
                <p class="footer-title">
                    {{ __('Yes! Send me personalized tips for shopping and selling on 1N2 Music.') }}</p>
                <div class="form-subscribe">
                    <form id="form-newsletter" action="{{ rrt_route('public/newsletter/saveNewsletter') }}">
                        <input type="text" placeholder="{{ __('Subscribe to our Newsletter') }}"
                            class="form-control">
                        <button class="btn-gradient"><span>{{ __('Send') }}</span></button>
                    </form>
                </div>
            </div>
        </div>
    </footer>

    @include('public2.elements.srcipts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @stack('srcipt')
    <script>
        $(document).ready(function() {
            $('#copyButton').click(function() {
                var textToCopy = $(this).data('text');
                var tempInput = $("<input>");
                $("body").append(tempInput);
                tempInput.val(textToCopy).select();
                document.execCommand('copy');
                tempInput.remove();
                $('#tooltip').addClass('show');
                setTimeout(function() {
                    $('#tooltip').removeClass('show');
                }, 2000);
            });
            $("#form-newsletter").submit(function(e) {
                e.preventDefault();

                const value = $(this).find('input').val();
                const url = $(this).attr('action'); // Lấy URL từ thuộc tính action của form

                $.ajax({
                    type: "post",
                    url: url,
                    data: {
                        email: value
                    }, // Gửi dữ liệu email dưới dạng {email: value}
                    dataType: "json",
                    beforeSend: function() {
                        showLoading();
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success(response.message, "Success", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 3000,
                            });


                            $("#form-newsletter").find('input').val('');
                        }

                    },
                    error: function(error) {
                        if (error.responseJSON && error.responseJSON.message) {
                            toastr.error(error.responseJSON.message, "Error", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 3000,
                            });
                        } else {
                            toastr.error("An unknown error occurred. Please try again.",
                                "Error", {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 3000,
                                });
                        }
                     
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            });
        });



        document.addEventListener('DOMContentLoaded', function() {
            // Get the modal element
            var modal = document.getElementById('popupModal');

            // Get the close button element
            var closeButton = document.querySelector('.modal-close');
            if (modal) {
                // Check if the modal should be shown based on cookie
                if (!getCookie('hidePopup')) {
                    modal.style.display = 'block'; // Show the modal
                }
                // Close modal when the close button is clicked
                closeButton.onclick = function() {
                    modal.style.display = 'none'; // Hide the modal
                    // Only set cookie if checkbox is checked
                    if (document.getElementById('dontShowAgain').checked) {
                        setCookie('hidePopup', 'true', 7); // Set cookie to hide modal for 7 days
                    }
                };
                // Close modal when clicking outside of the modal content
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = 'none';
                    }
                };

            }





            // Function to get the value of a cookie by name
            function getCookie(name) {
                let matches = document.cookie.match(new RegExp(
                    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
                ));
                return matches ? decodeURIComponent(matches[1]) : undefined;
            }

            // Function to set a cookie with name, value, and expiration in days
            function setCookie(name, value, days) {
                let date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                document.cookie = name + "=" + encodeURIComponent(value) + "; path=/; expires=" + date
                    .toUTCString();
            }
        });
    </script>
</body>

</html>
