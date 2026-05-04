@extends('studio.pages.account.main')
@section('account_title', 'Payment Methods')
@section('account_desc', 'Before connecting services, we need to add your billing information.')
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
        let country = "{{ $user_payment->country ?? 0 }}"
        if (country != 0) {
            $('select[name=country]').val(country)
        }
        $('#save').click(function() {
            $('#form-account-payment').submit()
        })
    </script>
@endpush
