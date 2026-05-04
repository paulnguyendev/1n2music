@extends('public2.main')
@section('body_class', 'authen-page')
@section('content')
    <div class="authen-wrap">
        <div class="authen-inner">
            <div class="authen-left">
                <div class="authen-logo">
                <a href="{{ rrt_route('public/home/index') }}">
                    <img src="{{ asset('public/style2/img/logo-vertical.svg') }}" alt="">
                </a>
                </div>
                <div class="authen-title">
                    <a href=""><img src="{{ asset('public/style2/img/ic_outline-arrow-back.svg') }}" alt=""></a>
                    <span>{{ __('RESET PASSWORD') }}</span>
                </div>
                <form id="authen-form" action="{{ rrt_route($controllerName . '/postNewPassword',['token' => $email]) }}" method="POST">
                    <div class="authen-form">
                        <div class="form-group">
                            <label for="">{{ __('New Password') }}</label>
                            <input type="password" name="password" class="form-control" >
                        </div>
                        <div class="form-group">
                            <button id="btn-continue" class="btn-authen" disabled>{{ __('Reset Password') }}</button>
                        </div>
                    </div>
                </form>
                <div class="authen-bottom">
                    {{ __('Don’t have an account,') }} <a href="{{ rrt_route('public/auth/signUp') }}">{{ __('Sign Up here!') }}</a>
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
            const password = $(`input[name='password']`).val();
            if (password ) {
                $('#btn-continue').prop('disabled', false);
            }
            else {
                $('#btn-continue').prop('disabled', true);
            }
        }
        $("input[name='password']").on('input', function() {
            validateForm();
        });
        $('#authen-form').on('submit', function(e) {
            e.preventDefault(); // Ngăn chặn form gửi dữ liệu mặc định
            const email = $(`input[name='email']`).val();
            const password = $(`input[name='password']`).val();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: {
                    _token: $('input[name="_token"]').val(),
                    email: email,
                    password: password
                },
                beforeSend: function() {
                    showLoading();
                },
                success: function(response) {
                    // Xử lý khi đăng nhập thành công
                    if (response.status === 400) {
                        const msg = response?.msg;
                        const firstKey = Object.keys(msg)[0];
                        const firstMsg = msg[firstKey];
                        toastr.error(firstMsg, 'Error');
                    }
                    if (response.status === 200) {
                        const msg = response?.msg;
                        toastr.success(msg, 'Success');
                    }
                    const redirect = response?.redirect;
                    if(redirect) {
                        window.location.href = redirect;
                    }
                },
                error: function(xhr) {
                    // Xử lý khi có lỗi xảy ra
                    console.log(xhr.responseText);
                },
                complete: function() {
                    hideLoading();
                }
            });
        });
    </script>
@endpush
