@extends('public2.main')
@section('body_class', 'join-page page-distribution')
@section('content')
    <div class="section-checkout section-padding">
        <div class="container">
            <div class="checkout-title" style="margin-bottom: 15px">
                <i class="fa fa-shopping-cart fa-lg"></i> {{__('Checkout - Order Information')}}
            </div>
            <div class="checkout-items">
                <table class="table-checkout-item">
                    <thead>
                    <tr>
                        <th class="text-left">{{ __('Package') }}</th>
                        <th>{{ __('Usage Count') }}</th>
                        <th>{{ __('Days Available for Download') }}</th>
                        <th>{{ __('Price') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <p>
                                <strong>{{$aiPackage->aiService->name??''}} - {{$aiPackage->role->name??""}}</strong>
                            </p>
                        </td>
                        <td class="text-center">
                            <p>
                                <strong>{{$aiPackage->usage_count??0}}</strong>
                            </p>
                        </td>
                        <td class="text-center">
                            <p>
                                <strong>{{$aiPackage->download_available??0}} Days</strong>
                            </p>
                        </td>
                        <td class="text-center">
                            {{ rrt_show_price($aiPackage->price ?? 0) }}
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3">{{__('Order Total')}}</td>
                        <td class="text-center" colspan="1"><strong
                                class="track-meta-price">{{ rrt_show_price($aiPackage->price ?? 0) }}</strong></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="text-left">
                <form id="checkout-form" action="{{rrt_route($controllerName.'/create')}}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{rrt_get_user_login('id')}}">
                    <input type="hidden" name="package_id" value="{{$aiPackage->id??""}}">
                    <input type="hidden" name="ai_id" value="{{$aiPackage->ai_id??""}}">
                    <input type="hidden" name="pay_amount" value="{{$aiPackage->price ?? 0}}">
                    <input type="hidden" name="usage_count" value="{{$aiPackage->usage_count ?? 0}}">
                    <input type="hidden" name="download_available" value="{{$aiPackage->download_available ?? 0}}">
                    <div class="form-group">
                        <label for="email">{{__('Email Address')}}</label>
                        <input type="email" name="email" class="form-control" placeholder="example@gmail.com" value="{{rrt_get_user_login('email')}}" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="payment_id">{{__('Payment Method')}}</label>
                        <div class="checkbox-container">
                            <input type="radio" name="payment_method" value="paypal" id="paypal" checked>
                            <label for="paypal">{{__('PayPal')}}</label>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-feature text-center">{{__('Proceed to Payment')}}</button>
                    </div>
                </form>
                <div class="text-center">
                    <a href="{{ rrt_route('public/home/index') }}" class="btn btn-secondary mt-3">{{__('Back to Home')}}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('srcipt')
    <script src="{{asset('public/style2/js/cart.js')}}?ver={{time()}}"></script>
    <script>
        const checkboxPayment = $(`input[name=payment_method]`);
        checkboxPayment.change(function() {
            let value = $(this).val();
            if ($(this).is(":checked")) {
                checkboxPayment.not($(this)).prop('checked', false);
            } else {}

        })
        const formCheckout = $("#checkout-form");
        formCheckout.submit(function (e) {
            e.preventDefault();

            // Lấy dữ liệu từ form
            let data = {
                user_id: $("input[name='user_id']").val(),
                package_id: $("input[name='package_id']").val(), // Chỉnh lại từ subscription_id thành package_id
                ai_id: $("input[name='ai_id']").val(), // Lấy ai_id từ form
                email: $("input[name='email']").val(),
                payment_method: $("input[name='payment_method']:checked").val(),
                pay_amount: $("input[name='pay_amount']").val(), // Lấy giá trị từ pay_amount
                usage_count: $("input[name='usage_count']").val(), // Lấy usage_count từ form
                download_available: $("input[name='download_available']").val(), // Lấy download_available từ form
                _token: $('input[name="_token"]').val()
            };

            // Kiểm tra dữ liệu trước khi gửi
            if (!data.user_id || !data.package_id || !data.ai_id || !data.email || !data.payment_method || !data.pay_amount) {
                return showNotify('error', 'Error', 'Please fill out all required fields.');
            }

            $.ajax({
                type: "POST",
                url: formCheckout.attr('action'), // URL từ action của form
                data: data,
                dataType: "json",
                beforeSend: function () {
                    showLoading();
                },
                success: function (response) {
                    if (response.status == 'success') {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            showNotify('error', 'Error', 'Payment processing failed.');
                        }
                    } else {
                        showNotify('error', 'Error', response.message ?? 'Payment processing failed.');
                    }
                },
                error: function (xhr) {
                    showNotify('error', 'Error', 'An error occurred during payment processing.');
                },
                complete: function () {
                    hideLoading();
                }
            });
        });

    </script>
@endpush
