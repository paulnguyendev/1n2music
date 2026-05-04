@extends('public2.main')
@section('body_class', 'join-page page-distribution')
@section('content')
    <div class="section-cart section-padding">
        <div class="container">
            <div class="cart-title">
                <i class="fa fa-check-circle fa-lg"></i> {{__('Payment Cancel')}}
            </div>
            <div class="cart-items">
                <p>{{__('Your Order has been canceled')}}</p>
            </div>
            <div class="text-center">
                <a href="{{ rrt_route('public/studio/home/index') }}" class="btn btn-feature">{{__('Back to Home')}}</a>
            </div>
        </div>
    </div>
@endsection
@push('srcipt')
    <script src="{{asset('public/style2/js/cart.js')}}?ver={{time()}}"></script>
@endpush
