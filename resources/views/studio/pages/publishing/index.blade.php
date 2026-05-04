@extends('studio.main')
@section('body_class', 'body-full-width')
@section('content')
    @if ($subscription_order)
        {{__('You have subscribed to publishing')}}
    @else
    <section class="section-subscription section-publishing">
        <div class="container">
            <div class="subscription-inner  container-gap">
                <h1 class="title-main">{{ __("Today's the day you start collecting your royalties") }}</h1>
                <div class="subscription-desc">
                    <ul>
                        <li>{{ __('80/20 splits') }}</li>
                        <li>{{ __('Direct payment') }}</li>
                        <li>{{ __('One-year contracts') }}</li>
                        <li>{{ __('Worldwide royalty collection') }}</li>
                        <li>{{ __('Keep 100% ownership') }}</li>
                    </ul>
                </div>
                <div class="subscription-info text-center">
                    <h3 class="subscription-info-title">{{ __('PUBLISHING SUBSCRIPTION') }}</h3>
                    <div class="subscription-info-price">
                        <p class="subscription-price-number">
                            <strong>$100</strong><span>/{{ __('year') }}</span>
                        </p>
                        <p class="subscription-price-desc">
                            {{ __('for unlimited tracks') }}
                        </p>
                    </div>
                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="is_read">
                        <label for="is_read">{{ __('I read and accept the') }} <br> <a href="#">{{ __('Publishing Services Agreement') }}</a></label>
                    </div>
                    <a id="subscribe" href="{{ rrt_route('public/join/publishing/register') }}" class="btn btn-primary w-100 disabled">{{ __('Subscribe') }}</a>
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
            if (!confirm("{{ __('Do you want to register as a publishing?') }}")) {
                return false;
            } else {
                window.location.href = $(this).attr('href')
            }

        })
    </script>
@endpush
