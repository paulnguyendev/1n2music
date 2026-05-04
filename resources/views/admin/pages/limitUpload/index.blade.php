@extends('admin.main')
@section('page_title', 'Setting Limit Upload')
@section('title', 'Setting Limit Upload')
@section('buttons')
@endsection
@section('content')
    <div class="container">
        <h1>{{__('Limit Upload Tracks Settings')}}</h1>
        <form action="{{rrt_route($controllerName.'/saveSettings')}}" id="formSubmit" method="POST">
            @csrf
            <div class="form-group">
                <label for="limit_single">{{__('Limit Single Track')}}</label>
                <input type="number" class="form-control" id="limit_single" name="limit_single" value="{{ $limit_single ?? "" }}" min="-1">
                <span class="help-block"></span>
            </div>

            <div class="form-group">
                <label for="limit_album">{{__('Limit Album Track')}}</label>
                <input type="number" class="form-control" id="limit_album" name="limit_album" value="{{ $limit_album ?? "" }}" min="-1">
                <span class="help-block"></span>
            </div>
            <p class="ml-3" style="color: #666;">(*) {{__('Leave -1 if unlimited')}}</p>
            <button type="button" onclick="nav_submit_form(this)"  data-form="formSubmit" class="btn btn-info btn-ladda btn-ladda-spinner">{{__('Save Changes')}}</button>
        </form>
    </div>
@endsection
@push('script')
@endpush
