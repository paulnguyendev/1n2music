@extends('public2.main')
@section('body_class', 'authen-page')
@section('content')
    <div class="authen-wrap">
        <div class="authen-inner">
            <div class="authen-left">
                <div class="authen-logo">
                    <img src="{{ asset('public/style2/img/logo-vertical.svg') }}" alt="">
                </div>
                <div class="authen-title">
                    <span>{{__('Welcome')}} {{$user->email??''}}</span>
                </div>
                <form id="authen-form" action="{{rrt_route('public/auth/loginToken')}}" method="post">
                    @csrf
                        <input type="hidden" name="token" value="{{$token}}">
                    <div class="form-group list-plan-group">
                        <div class="checkbox-container">
                            <input type="checkbox" name="start_selling" value="1" id="basic">
                            <span class="custom-checkbox"></span>
                            <label for="basic">{{__('Start Selling')}}</label>
                        </div>
                    </div>
                        <div class="form-group">
                            <button id="btn-continue" class="btn-authen">{{__('Continue')}}</button>
                        </div>
                </form>
            </div>
            <div class="authen-right">
                <div class="authen-bg">
                    <img src="{{ asset('public/style2/img/bg-authen.png') }}" alt="">
                </div>
            </div>
        </div>
    </div>
@endsection
@push('srcipt')
@endpush
