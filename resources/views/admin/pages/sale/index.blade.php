@extends('admin.main')@section('page_title', 'Account')
@section('title', 'Order details')
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/form') }}" class="btn btn-primary">{{__('Create a New Account')}}</a>
@endsection