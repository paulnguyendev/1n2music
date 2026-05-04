@extends('public.main')
@section('title', 'Publishing Join')
@section('body_class', 'page-publishing')
@section('content')
    <section class="section-subscription section-publishing">
        <div class="container">
            <div class="subscription-inner  container-gap">
                <h1 class="title-main">Today's the day you start collecting your royalties</h1>
                <div class="subscription-desc">
                    {!! $item['content'] ?? '' !!}
                </div>
                <div class="subscription-info text-center">
                    <h3 class="subscription-info-title">PUBLISHING SUBSCRIPTION</h3>
                    <div class="subscription-info-price">
                        <p class="subscription-price-number">
                            <strong>$100</strong><span>/year</span>
                        </p>
                        <p class="subscription-price-desc">
                            for unlimited tracks
                        </p>
                    </div>
                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="is_read">
                        <label for="is_read">I've read and accept the <br> <a href="#">Publishing Services
                                Agreement</a></label>
                    </div>
                    <button class="btn btn-primary w-100 btn-subscribe" disabled
                        data-url="{{ rrt_route($controllerName . '/register') }}">Subscribe</button>
                </div>
            </div>

        </div>
    </section>
@endsection
@push('srcipt')
    <script>
        const isRead = $("#is_read");
        const btnSubscribe = $(".btn-subscribe");
        isRead.change(function() {
            if ($(this).is(":checked")) {
                btnSubscribe.attr('disabled', false);
            } else {
                btnSubscribe.attr('disabled', true);
            }
        });
        btnSubscribe.click(function() {
            let url = $(this).data('url');
            window.location.href = url;
        })
    </script>
@endpush
