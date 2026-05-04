@extends('studio.pages.account.main')
@section('account_title', 'Addresses')
@section('account_desc', 'Billing Address')
@section('account_content')
    @include("{$pathViewController}.elements.form-billing-shipping")
@endsection
@section('account_footer')
    <div class="card-inner-footer">
        <div class="text-right">
            <button id="save" class="btn btn-primary">{{__('Save Changes')}}</button>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $('#save').click(function() {
            $('#form-account-payment').submit()
        })
    </script>
@endpush
