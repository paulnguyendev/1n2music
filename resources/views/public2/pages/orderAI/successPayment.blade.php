@extends('public2.main')
@section('body_class', 'join-page page-distribution')
@section('content')
    <div class="section-cart section-padding">
        <div class="container">
            <div class="cart-title">
                <i class="fa fa-check-circle fa-lg"></i> {{ __('Payment Success') }}
            </div>
            <div class="cart-items">
                <table class="table-cart-item">
                    <thead>
                    <tr>
                        <th class="text-left">{{ __('Package') }}</th>
                        <th>{{ __('Usage Count') }}</th>
                        <th>{{ __('Days Available for Download') }}</th>
                        <th>{{ __('Price') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p>
                                    <strong>{{ $order->aiService->name ?? '' }}</strong>
                                </p>
                            </td>
                            <td class="text-center">
                                <p>
                                    <strong>{{ $order->usage_count ?? 0 }}</strong>
                                </p>
                            </td>
                            <td class="text-center">
                                <p>
                                    <strong>{{ $order->download_available ?? 0 }} {{ __('Days') }}</strong>
                                </p>
                            </td>
                            <td class="text-center">
                                {{ rrt_show_price($order->pay_amount ?? 0) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <a href="{{ rrt_route('public/studio/home/index') }}" class="btn btn-feature">{{ __('Back to Home') }}</a>
            </div>
        </div>
    </div>
@endsection
@push('srcipt')
    <script src="{{asset('public/style2/js/cart.js')}}?ver={{time()}}"></script>
@endpush
