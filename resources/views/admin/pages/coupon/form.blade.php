@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{__('Back')}}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
        data-form="formSubmit">{{__('Save Changes')}}</button>
@endsection
@section('content')
    <form id="formSubmit" action="{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method="post">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Information') }}</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Name') }} (*)</label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{ $item['name'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Coupon type') }} (*)</label>
                                    <select name="type" id="" class="form-control">
                                        <option value="">{{ __('Choose Coupon Type') }}</option>
                                        <option value="product">{{ __('Individual product discount') }}</option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Applicable Tracks') }} (*)</label>
                                    <select name="track_id" id="" class="form-control">
                                        <option value="">{{ __('Choose track') }}</option>
                                        @foreach ($tracks as $track)
                                            <option value="{{ $track->id ?? '' }}">{{ $track->name ?? '' }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Member ID') }} (*)</label>
                                    <select name="user_id" id="" class="form-control">
                                        <option value="">{{ __('Choose member') }}</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id ?? '' }}">
                                                {{ $user->email . ' - ' . $user->username ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Start date of use') }} (*)</label>
                                    <input type="date" class="form-control" name="start_date"
                                        value="{{ $item['start_date'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Use end date') }} (*)</label>
                                    <input type="date" class="form-control" name="end_date"
                                        value="{{ $item['end_date'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Discount type') }} (*)</label>
                                    <select name="discount_type" id="" class="form-control">
                                        <option value="">{{ __('Choose Discount Type') }}</option>
                                        <option value="number">{{ __('Discount Number') }}</option>
                                        <option value="percentage">{{ __('Discount Percentage') }}</option>
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
        $('select[name="type"]').select2({
            placeholder: 'Choose Coupon Type'
        });
        $('select[name="track_id"]').select2({
            placeholder: 'Choose track'
        });
        $('select[name="user_id"]').select2({
            placeholder: 'Choose member'
        });
        $('select[name="discount_type"]').select2({
            placeholder: 'Choose distcount type'
        });
    </script>
@endpush
