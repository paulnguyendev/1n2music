@extends('studio.pages.account.main')
@section('title', __('SNS'))
@section('account_title', __('SNS'))
@section('account_desc', __('Manage the SNS links you want to display on your profile.'))
@section('account_content')
    <form action="{{ rrt_route($controllerName . '/postSocial') }}" method="post">
        {{-- <div class="form-group">
            <label for="">Twitter</label>
            <div class="input-group">
                <img src="{{ asset('studio/rrt/img/twitter-l-regular-solid.svg') }}" alt="">
                <input @isset($result['twitter'])
            value="{{ $result['twitter'] }}"
        @endisset
                    name="twitter" type="text" class="form-control" placeholder="/nickname">
            </div>
        </div> --}}
        {{-- <div class="form-group">
            <label for="">{{__('Follow')}}</label>
            <div class="input-group">
                <i style="font-size: 20px" class="fa fa-user-plus"></i>
                <input @isset($result['follow'])
            value="{{ $result['follow'] }}"
        @endisset
                    name="follow" type="text" class="form-control" placeholder="/{{__('follow')}}">
            </div>
        </div> --}}
        <div class="form-group">
            <label for="">{{__('Instagram')}}</label>
            <div class="input-group">
                <img src="{{ asset('studio/rrt/img/instagram-l-regular-solid.svg') }}" alt="">
                <input @isset($result['instagram'])
            value="{{ $result['instagram'] }}"
        @endisset
                    name="instagram" type="text" class="form-control" placeholder="/{{__('nickname')}}">
            </div>
        </div>
        <div class="form-group">
            <label for="">{{__('SoundCloud')}}</label>
            <div class="input-group">
                <img src="{{ asset('studio/rrt/img/soundcloud-l-regular-solid.svg') }}" alt="">
                <input @isset($result['soundcloud'])
            value="{{ $result['soundcloud'] }}"
        @endisset
                    name="soundcloud" type="text" class="form-control" placeholder="/{{__('nickname')}}">
            </div>
        </div>
        <div class="form-group">
            <label for="">YouTube</label>
            <div class="input-group">
                <img src="{{ asset('studio/rrt/img/youtube-l-regular-solid.svg') }}" alt="">
                <input @isset($result['youtube'])
            value="{{ $result['youtube'] }}"
        @endisset
                    name="youtube" type="text" class="form-control" placeholder="/nickname">
            </div>
        </div>
        <div class="form-group">
            <label for="">{{__('TikTok')}}</label>
            <div class="input-group">
                <img src="{{ asset('studio/rrt/img/tiktok-l-regular-solid.svg') }}" alt="">
                <input @isset($result['tiktok'])
            value="{{ $result['tiktok'] }}"
        @endisset
                    name="tiktok" type="text" class="form-control" placeholder="/{{__('nickname')}}">
            </div>
        </div>
        <div class="form-group">

            <label for="">Facebook</label>
            <div class="input-group">
                <img src="{{ asset('studio/rrt/img/facebook-l-regular-solid.svg') }}" alt="">
                <input type="text" class="form-control"
                    @isset($result['facebook'])
                 value="{{ $result['facebook'] }}"
             @endisset
                    name="facebook" placeholder="/nickname">
            </div>
        </div>
        <div class="form-group">

            <label for="">Twitter(X)</label>
            <div class="input-group">
                <img src="{{ asset('studio/rrt/img/twitter-l-regular-solid.svg') }}" alt="">
                <input type="text" class="form-control"
                    @isset($result['twitter'])
                 value="{{ $result['twitter'] }}"
             @endisset
                    name="twitter" placeholder="/nickname">
            </div>
        </div>
        <div class="card-inner-footer">
            <div class="text-right">
                <button class="btn btn-primary">{{__('Save Changes')}}</button>
            </div>
        </div>
    </form>
@endsection
