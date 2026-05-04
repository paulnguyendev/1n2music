@extends('studio.main')
@section('body_class', 'body-full-width')
@section('content')
@if ($subscription_order)
<section class="section-subscription section-distribution">
    <div class="container">
        <div class="subscription-inner container-gap">
            <h1 class="title-main">{{ __('You have subscribed to distribution') }}</h1>
        </div>
    </div>
</section>
@else
<section class="section-subscription section-distribution">
    <div class="container">
        <div class="subscription-inner container-gap">
            <h1 class="title-main">{{ __('Let the world hear your voice') }}</h1>
            <div class="subscription-desc">
                <ul>
                    <li>{{ __('Put your music on Melon, Genie Music, Spotify, Apple Music, TikTok and 30+ stores') }}</li>
                    <li>{{ __('Add collaborators and split revenue') }}</li>
                    <li>{{ __('Keep 100% of your earnings. Upload an unlimited number of tracks') }}</li>
                </ul>
            </div>
            <div class="subscription-info text-center">
                <h3 class="subscription-info-title">{{ __('DISTRIBUTION SUBSCRIPTION') }}</h3>
                <div class="subscription-info-price">
                    <p class="subscription-price-number">
                        <strong>{{ __('$50') }}</strong><span>{{ __('/year') }}</span>
                    </p>
                    <p class="subscription-price-desc">
                        {{ __('for unlimited tracks') }}
                    </p>
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="is_read">
                    <label for="is_read">{{ __('I read and accept the') }}<br> 
                        <a href="#">{{ __('Distribution Services Agreement') }}</a></label>
                </div>
                <a id="subscribe" href="{{ rrt_route('public/join/distribution/register') }}"
                    class="btn btn-primary w-100 disabled">{{ __('Subscribe') }}</a>
            </div>
        </div>
    </div>
</section>
@endif



@endsection
@push('script')
    <script>
        $('#is_read').click(function(e) {
            if ($(this).is(":checked")) {
                $('#subscribe').removeClass('disabled')
            } else {
                $('#subscribe').addClass('disabled')
            }

        });
        $('#subscribe').click(function(e) {
            e.preventDefault();
            if (!confirm('Do you want to register as a distributor?')) {
                return false;
            } else {
                window.location.href = $(this).attr('href')
            }

        })
    </script>
@endpush
