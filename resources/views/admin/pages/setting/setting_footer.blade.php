@extends('admin.main')
@section('page_title', 'Setting Footer')
@section('title', 'Setting Footer')
@section('buttons')
    <a href="" class="btn btn-default">{{__('Back')}}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
        data-form="formSubmit">{{__('Save Changes')}}</button>
@endsection
@section('content')
<div class="container">
    <form id="formSubmit" method="post" class="" action="{{ rrt_route($controllerName . '/form') }}">
        @csrf
        <div class="card">
            <div class="card-header">
                <h6>{{ __('Social Media') }}</h6>
            </div>
            <div class="card-body">
                <div class="mb-3 form-group">
                    <label for="instagram" class="form-label">{{ __('Instagram') }}</label>
                    <input type="text" name="instagram" placeholder="{{ __('Enter instagram') }}" class="form-control"
                        id="instagram" value="">
                </div>
                <div class="mb-3 form-group">
                    <label for="youtube" class="form-label">{{ __('Youtube') }}</label>
                    <input type="text" name="youtube" placeholder="{{ __('Enter youtube') }}" value="" class="form-control"
                        id="youtube">
                </div>
                <div class="mb-3 form-group">
                    <label for="soundclound" class="form-label">{{ __('SoundCloud') }}</label>
                    <input type="text" name="soundclound" placeholder="{{ __('Enter SoundCloud') }}" id="soundclound"
                        value="" class="form-control" id="soundclound">
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <h6>{{ __('Contact Information') }}</h6>
            </div>
            <div class="card-body">
                <div class="mb-3 form-group">
                    <label for="company" class="form-label">{{ __('Company Name') }}</label>
                    <input type="input" placeholder="{{ __('Enter company name') }}" class="form-control" id="company"
                        value="">
                </div>
                <div class="mb-3 form-group">
                    <label for="founders" class="form-label">{{ __('Founders') }}</label>
                    <input type="text" name="founders" placeholder="{{ __('Enter founders') }}" value=""
                        class="form-control" id="founders">
                </div>
                <div class="mb-3 form-group">
                    <label for="Address" class="form-label">{{ __('Address') }}</label>
                    <input type="text" name="address" placeholder="{{ __('Enter address') }}" id="Address" value=""
                        class="form-control" id="Address">
                </div>
                <div class="mb-3 form-group">
                    <label for="digital" class="form-label">{{ __('Digital Sale Registration') }}</label>
                    <input type="text" name="digital" placeholder="{{ __('Enter Digital Sale Registration') }}" id="Address"
                        value="" class="form-control" id="digital">
                </div>
                <div class="mb-3 form-group">
                    <label for="business" class="form-label">{{ __('Business Registration') }}</label>
                    <input type="text" name="business" placeholder="{{ __('Enter Business Registration') }}" id="business" value=""
                        class="form-control" id="business">
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@push('script')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script>
        $('select[name="status"]').select2({
            placeholder: 'Choose Status'
        });
    </script>
@endpush
