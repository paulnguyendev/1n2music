@extends('admin.main')
@section('page_title', 'Setting Commission')
@section('title', 'Setting Commission')
@section('buttons')
@endsection
@section('content')
    <div class="container">
        <h1>{{__('Adjust Commission Settings')}}</h1>
        <form action="{{rrt_route($controllerName.'/saveSettings')}}" id="formSubmit" method="POST">
            @csrf
            <div class="form-group">
                <label for="commission_seller">{{__('Commission Seller')}} %</label>
                <input type="text" class="form-control" id="commission_seller" name="commission_seller" value="{{ ($commissionSeller??0)*100 }}">
            </div>

            <div class="form-group">
                <label for="commission_subscriber">{{__('Commission Publishing')}} %</label>
                <input type="text" class="form-control" id="commission_publishing" name="commission_publishing" value="{{ ($commissionPublishing??0)*100 }}">
            </div>
            <div class="form-group">
                <label for="commission_subscriber">{{__('Commission Distribute')}} %</label>
                <input type="text" class="form-control" id="commission_distribute" name="commission_distribute" value="{{ ($commissionDistribute??0)*100 }}">
            </div>

            <button type="button" onclick="nav_submit_form(this)"  data-form="formSubmit" class="btn btn-info btn-ladda btn-ladda-spinner">{{__('Save Changes')}}</button>
        </form>
    </div>
@endsection
@push('script')
@endpush
