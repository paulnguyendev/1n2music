@extends('public2.main')
@section('body_class', 'join-page page-distribution')
@section('content')
    <div class="section-cart section-padding">
        <div class="container">
            <div class="cart-title">
                <i class="fa fa-check-circle fa-lg"></i> {{__('Payment Success')}}
            </div>
            <div class="cart-items">
                <table class="table-cart-item">
                    <thead>
                    <tr>
                        <th style="width:50%">{{__('Order Details')}}</th>
                        <th>{{__('Information')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><strong>{{__('Email')}}</strong></td>
                        <td>{{ $order->user->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{__('Subscription')}}</strong></td>
                        <td>{{ $order->subscription->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{__('Price')}}</strong></td>
                        <td>{{ rrt_show_price($order->subscription->price ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{__('Subscription Status')}}</strong></td>
                        <td>{{ ucfirst($order->status) ?? __('Pending') }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <a href="{{ rrt_route('public/home/index') }}" class="btn btn-feature">{{__('Back to Home')}}</a>
            </div>
        </div>
    </div>
@endsection
@push('srcipt')
    <script src="{{asset('public/style2/js/cart.js')}}?ver={{time()}}"></script>
@endpush
