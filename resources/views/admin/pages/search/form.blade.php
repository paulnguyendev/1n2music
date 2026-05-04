@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <a href="{{rrt_route($controllerName . "/index")}}" class="btn btn-default">{{__('Back')}}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in" data-form="formSubmit">{{__('Save Changes')}}</button>
@endsection
@section('content')
    <form id="formSubmit" action = "{{rrt_route($controllerName . '/save',['id' => $id])}}" method = "post">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Information') }}</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('First Name') }} (*)</label>
                                    <input type="text" class="form-control" name="first_name" value="{{$item['first_name'] ?? ''}}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Last Name') }} (*)</label>
                                    <input type="text" class="form-control" name="last_name" value="{{$item['last_name'] ?? ''}}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Email') }} (*)</label>
                                    <input type="email" class="form-control" name="email" value="{{$item['email'] ?? ''}}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Phone') }} (*)</label>
                                    <input type="tel" class="form-control" name="phone" value="{{$item['phone'] ?? ''}}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Login info') }}</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('Username') }} (*)</label>
                                    <input type="text" class="form-control" name="username" value="{{$item['username'] ?? ''}}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('Password') }} (*)</label>
                                    <input type="password" class="form-control" name="password" value="{{$item['password'] ?? ''}}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('Status') }}</label>
                                    @php
                                        $status = $item['status'] ?? ''; 
                                    @endphp
                                    <select name="status" class="form-control" id="">
                                        <option value="active" {{$status == 'active' ? 'selected' : ''}}>{{ __('Active') }}</option>
                                        <option value="pending" {{$status == 'pending' ? 'selected' : ''}}>{{ __('Pending') }}</option>
                                        <option value="suspend" {{$status == 'suspend' ? 'selected' : ''}}>{{ __('Suspend') }}</option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('script')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script>
        $('select[name="plan_id"]').select2({
            placeholder: 'Choose Plan'
        });
        $('select[name="status"]').select2({
            placeholder: 'Choose Status'
        });
    </script>
@endpush
