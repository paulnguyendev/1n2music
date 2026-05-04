<div id="header">
    <div class="header-top">
        <div class="container">
            <div class="header-top-inner">
                <div class="header-top-left">
                    <div id="logo">
                        <a href="{{ route('public/home/index', ['locale' => rrt_get_locale()]) }}">
                            <img src="{{ asset('public/images/1N2Logo.png') }}" alt="">
                            <span>1N2 Music</span>
                        </a>
                    </div>
                </div>
                <div class="header-top-right">
                    <div id="menu">
                        <ul class="list-none menu-list">
                            <li><a href="{{ rrt_route('public/market/index') }}">Tracks</a></li>
                            <li><a href="{{ rrt_route('public/join/distribution/index') }}">Distribution</a></li>
                            <li><a href="{{ rrt_route('public/join/publishing/index') }}">Publishing</a></li>

                        </ul>
                    </div>
                    <div id="login">
                        <div class="authen-menu">
                            <ul class="list-none authen-menu-list">
                                @if (!rrt_check_login())
                                    <li><a href="{{ rrt_route('public/join/basic/index') }}">Sign up</a></li>
                                    <li><a href="{{ rrt_route('public/join/basic/index') }}">Sign in</a></li>
                                    <li class="btn-feature-menu"><a
                                            href="{{ rrt_route('public/join/sellBeats/index') }}">Start
                                            Selling</a></li>
                                @else
                                    <li class="btn-studio"><a href="{{ rrt_route('public/studio/home/index') }}">Studio
                                            <i class="fas fa-external-link-alt"></i></a></li>
                                    <li class="btn-account">
                                        <a href="" class="dropdown-item">
                                            <i class="fas fa-user-circle"></i> My Account
                                            <div class="dropdown-sub" style="z-index: 99">
                                                <div class="dropdown-sub-head">
                                                    <img src="{{ asset('public/images/default-avatar-circle.svg') }}"
                                                        alt="">
                                                    <a
                                                        href="{{ rrt_route('public/studio/home/index') }}">{{ rrt_get_user_login('username') }}</a>
                                                </div>
                                                <ul style="list-style: none ;">
                                                    <li>
                                                        <a
                                                            href="{{ rrt_route('public/producer/detail', ['username' => rrt_get_user_login('username'), 'user_id' => rrt_get_user_login('id')]) }}">My
                                                            List</a>
                                                    </li>
                                                    <li>
                                                        <a
                                                            href="{{ rrt_route('public/studio/favourite/index') }}">Favorites</a>
                                                    </li>
                                                    <li> <a
                                                            href="{{ rrt_route('public/studio/history/index') }}">History</a>
                                                    </li>
                                                    <li> <a
                                                            href="{{ rrt_route('public/studio/order/index') }}">Orders</a>
                                                    </li>
                                                    <li> <a href="{{ rrt_route('public/studio/giftcard/index') }}">Gift
                                                            Card</a></li>
                                                    <li> <a
                                                            href="{{ rrt_route('public/studio/message/index') }}">Messages</a>
                                                    </li>
                                                    <li> <a href="{{ rrt_route('public/studio/account/index') }}">Account
                                                            Setting</a></li>

                                                    <li> <a
                                                            href="{{ rrt_route('public/join/basic/logout') }}">Logout</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </a>
                                    </li>
                                @endif
                                <li class="btn-account btn-cart">
                                    <a href="{{ rrt_route('public/cart/index') }}" class="">
                                        <i class="fal fa-shopping-cart"></i>
                                        <span id="cart-total">{{ Cart::count() }}</span>
                                        @if (Cart::count() == 0)
                                            <div class="dropdown-sub cart-dropdown-sub">
                                                <i class="fal fa-shopping-cart icon-cart-empty"></i>
                                                <h3>Your cart is empty</h3>
                                                <p>When you add something to your cart, it will appear here</p>

                                            </div>
                                        @endif

                                    </a>
                                </li>

                            </ul>
                        </div>
                        <div class="authen-logged"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
