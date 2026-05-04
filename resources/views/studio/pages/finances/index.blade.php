@extends('studio.main')
@section('page_title', __('Payment Account'))
@section('title', __('Payment Account'))
@section('content')
    <style>
        .payment-method-item {
            cursor: pointer;
        }

        .unactive {
            background: #000;
            opacity: 0.4;
            border: 2px solid #000;
            color: #fff;
        }

        .unactive .icon-check {
            display: none;
        }

        .unactive i {
            color: #fff
        }

        .error {
            color: red
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card_title">{{ __('Payment Account') }}</h4>
                        @if ($user->paymentAccount)
                            @if (count($user->paymentAccount->paymentMethod) < 2 && count($user->paymentAccount->paymentMethod) > 0)
                                <a href="{{ rrt_route($controllerName . '/form', ['step' => 'general']) }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i>
                                    <span>{{ __('Add Payout Method') }}</span>
                                </a>
                            @endif
                        @endif
                    </div>
                    @if ($user->paymentAccount)
                        <div class="row">
                            @isset($user->paymentAccount->paymentMethod)
                                @foreach ($user->paymentAccount->paymentMethod as $item)
                                    @php
                                        $isActive = $item['is_active'] ?? 0;
                                    @endphp
                                    <div class="col-sm-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title">
                                                    <a href="{{ rrt_route($controllerName . '/account', ['method' => $item->method ?? 'paypal']) }}"
                                                        class="payment-method-item {{ $isActive == 1 ? 'active' : '' }}">
                                                        <i class="fa fa-{{ $item->method }}"></i>
                                                        <p class="text-uppercase">{{ __($item->method) }}</p>
                                                        <div class="icon-check"></div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endisset
                        </div>
                    @endif
                    @if (!$user->paymentAccount)
                        <div class="text-center payment-inner">
                            <i class="fa fa-money icon-payment"></i>
                            <p><strong>{{ __('You do not currently have a payout method setup.') }}</strong></p>
                            <p>{{ __('Add a payout method to receive payouts.') }}</p>

                            <a href="{{ rrt_route($controllerName . '/form', ['step' => 'general']) }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i>
                                <span>{{ __('Add Payout Method') }}</span>
                            </a>
                        </div>
                    @else
                        @if (!count($user->paymentAccount->paymentMethod))
                            <div class="text-center payment-inner">
                                <i class="fa fa-money icon-payment"></i>
                                <p><strong>{{ __('You do not currently have a payout method setup.') }}</strong></p>
                                <p>{{ __('Add a payout method to receive payouts.') }}</p>

                                <a href="{{ rrt_route($controllerName . '/form', ['step' => 'general']) }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i>
                                    <span>{{ __('Add Payout Method') }}</span>
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            $(document).ready(function() {
                $('.payment-method-item').click(function() {
                    if ($(this).hasClass('active')) {
                        let url = $(this).data('formbank');

                        window.location = url;
                    }
                    let id = $(this).data('id');
                    let url = $(this).data('url')
                    let selected = $(this).data('selected');
                    let method = $(this).data('method')

                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            id: id,
                            selected: selected,
                            method: method
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.status_code == 200) {
                                window.location = response.redirect
                            }

                        }
                    });
                })
            });
        </script>
    @endpush
@endsection
