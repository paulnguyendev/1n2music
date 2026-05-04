@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <a href="{{ rrt_route($controllerName . "/index") }}" class="btn btn-default">{{__('Back')}}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in" data-form="formSubmit">{{__('Save Changes')}}</button>
@endsection
@section('content')
    <form id="formSubmit" action="{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{__('Platform Information')}}</h4>
                        <div class="form-group">
                            <label for="platform_title">{{__('Platform Title')}} (*)</label>
                            <input type="text" class="form-control" id="platform_title" name="name"
                                   value="{{ old('name', $item->name ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="platform_status">{{__('Platform Status')}}</label>
                            <select class="form-control" id="platform_status" name="status">
                                <option value="auto" {{ (old('status', $item->status ?? '') == 'auto') ? 'selected' : '' }}>{{__('Auto')}}</option>
                                <option value="manual" {{ (old('status', $item->status ?? '') == 'manual') ? 'selected' : '' }}>{{__('Manual')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <h4 class="card_title">{{__('Additional Information')}}</h4>
                        <div id="json-container">
                            @if(!empty($item->settings) && is_array($item->settings))
                                @foreach($item->settings as $key => $value)
                                    <div class="form-row align-items-end mt-2">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control json-key" placeholder="Key" value="{{ $key }}" readonly>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control json-value" placeholder="Value" name="settings[{{ $key }}]" value="{{ $value }}">
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="form-row align-items-end mt-2">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control json-key" placeholder="Key" value="stream_count" readonly>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control json-value" placeholder="Value" name="settings[stream_count]">
                                    </div>
                                </div>
                                <div class="form-row align-items-end mt-2">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control json-key" placeholder="Key" value="revenue" readonly>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control json-value" placeholder="Value" name="settings[revenue]">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('script')
@endpush
