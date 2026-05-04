@php
    use App\Helpers\Template;
@endphp
<div id="footer">
    <div class="container">
        <div class="footer-inner">

            {{-- <div class="footer-item" style="">
                <img class="footer-title" src="/public/assets/public/images/1N2Logo.png" alt=""
                    style="background-size: cover;
                background-repeat: no-repeat;">
            </div> --}}
            <div class="footer-item">
                <p class="footer-title">{{ $footer['footer_col_1_title' ?? ''] }} </p>

                @if (Template::showFooterItemLink(1, $footer))
                    <ul class="list-none footer-menu">
                        @foreach (Template::showFooterItemLink(1, $footer) as $item)
                            <li><a target="_blank" href="{{ $item['link'] }}">{{ $item['title'] }}</a></li>
                        @endforeach
                    </ul>

                @endif
            </div>
            <div class="footer-item">
                <p class="footer-title">{{ $footer['footer_col_2_title' ?? ''] }} </p>

                @if (Template::showFooterItemLink(2, $footer))

                    <ul class="list-none footer-menu">
                        @foreach (Template::showFooterItemLink(2, $footer) as $item)
                            <li><a href="{{ $item['link'] }}">{{ $item['title'] }}</a></li>
                        @endforeach
                    </ul>

                @endif

            </div>
            <div class="footer-item">
                <p class="footer-title">{{ $footer['footer_col_3_title' ?? ''] }}</p>
                <ul class="list-none footer-menu">
                    <li>
                        <a href="{{ $footer['youtube'] ?? '#' }}">
                            <i class="fab fa-youtube"></i>
                            <span>Youtube</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $footer['instagram'] ?? '#' }}">
                            <i class="fab fa-instagram"></i>
                            <span>Instagram</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $footer['soundcloud'] ?? '#' }}">
                            <i class="fab fa-soundcloud"></i>
                            <span>SoundCloud</span>
                        </a>
                    </li>

                </ul>
            </div>
            <div class="foot-item">
                <p class="footer-title">{{ $footer['footer_col_4_title' ?? ''] }}</p>
                <ul class="list-none footer-menu">
                    <li><a href="#">Company Name: {{ $footer['company_name'] }} </a></li>
                    <li><a href="#">Founders: {{ $footer['founder'] }}</a></li>
                    <li><a href="#">Address: {{ $footer['address'] }} </a></li>
                    <li><a href="#">Business registration: {{ $footer['business'] }} </a></li>
                    <li><a href="#">Digital Sales registration: {{ $footer['digital'] }} </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
