@extends('public2.main')
@section('body_class', 'authen-page')
@section('content')
    <div class="authen-wrap">
        <div class="authen-inner">
            <div class="authen-left">
                <div class="authen-logo">
                   <a href="{{rrt_route('public/home/index')}}"> <img src="{{ asset('public/style2/img/logo-vertical.svg') }}" alt=""></a>
                </div>
                <div class="authen-title">
                    <a href=""><img src="{{ asset('public/style2/img/ic_outline-arrow-back.svg') }}" alt=""></a>
                    <span> {{__('Registerstartion for Sellers')}}</span>
                </div>
                <form id="authen-form" action="{{ rrt_route($controllerName . '/postVerifyCode',['token' => $token]) }}" method="POST">
                    <div class="authen-form">
                        <div class="form-group">
                            <label for="">{{__('Enter the verification code sent to your email')}}</label>
                            <input type="text" name="validate_code" class="form-control" placeholder="">

                        </div>
                        <a href="javascript:;" class="resend-email" id="resend-email" data-token="{{$token }}">Resend Email</a>
                        <div class="form-group" style="margin-top: 10px">
                            <button id="btn-continue" class="btn-authen" disabled>{{__('Continue')}}</button>
                        </div>
                    </div>
                </form>

                <div class="authen-bottom">
                    {{__('Already have account')}}, <a href="{{rrt_route('public/auth/signIn')}}">{{__('Sign In here')}}!</a>
                </div>
            </div>
            <div class="authen-right">
                <div class="authen-bg">
                    <img src="{{ asset('public/style2/img/bg-authen.png') }}" alt="">
                </div>
            </div>
        </div>
    </div>
@endsection
@push('srcipt')
    <script>
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(String(email).toLowerCase());
        }
        function validateForm() {
            const validateCode = $(`input[name='validate_code']`).val();

            if (validateCode) {
                $('#btn-continue').prop('disabled', false);
            } else {
                $('#btn-continue').prop('disabled', true);
            }
        }
        $("input[name='validate_code']").on('input', function() {
            validateForm();
        });
        $('#authen-form').on('submit', function(e) {
            e.preventDefault(); // Ngăn chặn form gửi dữ liệu mặc định
            const validateCode = $(`input[name='validate_code']`).val();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: {
                    _token: $('input[name="_token"]').val(),
                    validate_code: validateCode,

                },
                success: function(response) {
                    // Xử lý khi đăng nhập thành công
                    if (response.status === 400) {
                        const msg = response?.msg;
                        const firstKey = Object.keys(msg)[0];
                        const firstMsg = msg[firstKey];

                        toastr.error(firstMsg, 'Error');
                    } else if( response.status === 200 ) {
                        const redirect = response?.redirect;
                        const msg = response?.msg;
                        toastr.success(msg, 'Success');
                        if(redirect) {
                            setTimeout(() => {
                                window.location.href = redirect;
                            }, 3000);
                        }
                    }else{
                        const redirect = response?.redirect;
                        if(redirect) {
                            window.location.href = redirect;
                        }

                    }
                },
                error: function(xhr) {
                    // Xử lý khi có lỗi xảy ra
                    console.log(xhr.responseText);
                }
            });
        });
        $('#resend-email').on('click', function () {
            const token = $(this).data('token');
            const resendUrl = "{{ rrt_route($controllerName . '/resendEmail') }}";
            console.log(token)
            $.ajax({
                url: resendUrl,
                method: 'POST',
                data: {
                    auth_token: token
                },
                success: function (response) {
                    if (response.status === 200) {
                        const msg = response?.msg || "{{ __('Verification email sent successfully!') }}";
                        toastr.success(msg, 'Success');
                    } else {
                        const msg = response?.msg || "{{ __('Failed to resend verification email.') }}";
                        toastr.error(msg, 'Error');
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    toastr.error("{{ __('An error occurred while resending the email.') }}", 'Error');
                }
            });
        });
    </script>
@endpush
