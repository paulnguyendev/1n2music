@extends('public.main')
@section('title', 'Distribution Join')
@section('body_class', 'page-distribution')
@section('content')
    <style>
        .popup-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .popup {
            top: 30%;
            width: 600px;
            height: 150px;
            position: absolute;
            z-index: 9999;
            background-color: white;
            color: #000;
            padding: 20px;
            border-radius: 5px;
        }

        #closeButton {
            margin-top: 10px;
        }

        #popupContainer p {
            margin: 3px 0px
        }

        .desc>a:hover {
            color: red
        }
    </style>
    @isset($status)
        <div class="popup-container" id="popupContainer">
            <div class="popup">
                <h2 class="text-center mb-2">DISTRIBUTION SUBSCRIPTION</h2>
                <p class="text-center">{{ $status ?? '' }}!</p>
                <p class="text-center desc">{!! $desc ?? '' !!}</a>
                </p>
                <button style="display: none" id="closeButton">Close</button>
            </div>
        </div>
    @endisset

    <section class="section-subscription section-distribution"
        style="
        background: url('{{ asset($item['background']) ?? '' }}');
        background-size: cover;
        background-repeat: no-repeat;
    ">
        <div class="container">
            <div class="subscription-inner  container-gap">
                <h1 class="title-main">{{ $item['heading'] ?? '' }}</h1>
                <div class="subscription-desc">
                    {!! $item['content'] ?? '' !!}
                </div>
                <div class="subscription-info text-center">
                    <h3 class="subscription-info-title">DISTRIBUTION SUBSCRIPTION</h3>
                    <div class="subscription-info-price">
                        <p class="subscription-price-number">
                            <strong>${{ $item['price'] ?? 0 }}</strong><span>/year</span>
                        </p>
                        <p class="subscription-price-desc">
                            for unlimited tracks
                        </p>
                    </div>
                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="is_read">
                        <label for="is_read">I've read and accept the <br> <a href="#">Distribution Services
                                Agreement</a></label>
                    </div>
                    <button class="btn btn-primary w-100 btn-subscribe"
                        data-url="{{ rrt_route($controllerName . '/register') }}" disabled>Subscribe</button>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const popupContainer = document.getElementById("popupContainer");
            const closeButton = document.getElementById("closeButton");

            // Show popup
            function showPopup() {
                popupContainer.style.display = "flex";
            }

            // Close popup
            function closePopup() {
                popupContainer.style.display = "none";
            }

            // Close popup when close button is clicked
            closeButton.addEventListener("click", closePopup);

            // Show popup initially (you can call this function when needed)
            showPopup();
        });
    </script>
@endpush
