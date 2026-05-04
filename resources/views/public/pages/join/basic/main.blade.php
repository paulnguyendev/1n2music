@extends('public.main')


@section('content')
    <div class="basic-area">
       
        <form class="form-basic-join" data-url="@yield('form_url',rrt_route($controllerName . "/checkEmail"))">
            @yield('content_basic')
        </form>
    </div>
@endsection

