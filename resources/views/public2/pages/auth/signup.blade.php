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
                    <span>{{ __('SIGN UP WITH') }}</span>
                </div>
                <form id="authen-form" action="{{ rrt_route($controllerName . '/postSignup') }}" method="POST">
                    <div class="authen-form">
                        <div class="form-group">
                            <label for="">{{ __('Email') }}</label>
                            <input type="email" name="email" class="form-control" placeholder="{{ __('example@gmail.com') }}">
                        </div>
                        <div class="form-group">
                            <label for="">{{ __('Password') }}</label>
                            <input type="password" name="password" class="form-control" placeholder="{{ __('Type your password') }}">
                        </div>
                        <div class="form-group">
                            <label for="">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirm" class="form-control" placeholder="{{ __('Type your password') }}">
                        </div>
                        <div class="form-group list-plan-group">
                            <div class="checkbox-container">
                                <input type="checkbox" name="start_selling" value="1" id="basic" @if($startSelling) checked @endif>

                                <label for="basic">
                                    <span class="custom-checkbox"></span>
                                    {{ __('Start Selling') }}
                                </label>
                            </div>
                            {{-- <div class="checkbox-container"> --}}
                            {{--     <input type="checkbox" name="join_types[]" value="basic" id="basic"> --}}
                            {{--     <span class="custom-checkbox"></span> --}}
                            {{--     <label for="basic">Basic Seller - Free</label> --}}
                            {{-- </div> --}}
                            {{-- <div class="checkbox-container"> --}}
                            {{--     <input type="checkbox" name="join_types[]" value="pro_seller" id="pro"> --}}
                            {{--     <span class="custom-checkbox"></span> --}}
                            {{--     <label for="pro">Pro Seller - $50/Year</label> --}}
                            {{-- </div> --}}
                            {{-- <div class="checkbox-container"> --}}
                            {{--     <input type="checkbox" name="join_types[]" value="distribution" id="distribution"> --}}
                            {{--     <span class="custom-checkbox"></span> --}}
                            {{--     <label for="distribution">Distribution - $50/Year</label> --}}
                            {{-- </div> --}}
                            {{-- <div class="checkbox-container"> --}}
                            {{--     <input type="checkbox" name="join_types[]" value="publishing" id="publishing"> --}}
                            {{--     <span class="custom-checkbox"></span> --}}
                            {{--     <label for="publishing">Publishing - $100/Year</label> --}}
                            {{-- </div> --}}
                        </div>
                        <div class="form-group">
                            <div class="checkbox-container">
                                <input type="checkbox" name="terms" id="terms" required>
                                <label for="terms">
                                    <span class="custom-checkbox"></span>
                                    {{ __('I have read and agree to the 1N2 MUSIC') }}
                                    <a href="{{$urlTermOfService}}">{{ __('Terms of Service') }}</a> {{ __('and') }}
                                    <a href="{{$urlPrivacyPolicy}}">{{ __('Privacy Policy') }}</a>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="6Le1WL4qAAAAALCP-Eg8KF5Lh5n0tuZA7epAlAzR"></div>
                        </div>
                        <div class="form-group">
                            <button id="btn-continue" class="btn-authen" disabled>{{ __('Continue') }}</button>
                        </div>
                    </div>
                </form>
                <div class="authen-social">
                    <div class="authen-social-title"><span>{{ __('Or') }}</span></div>
                    <div class="authen-social-list">
{{--                        <a href=""><img src="{{ asset('public/style2/img/icon_fb.svg') }}" alt=""></a>--}}
                        <a href="{{rrt_route('public/auth/google')}}"><img src="{{ asset('public/style2/img/icon_gg.svg') }}" alt=""></a>
                        <a href="#"><img src="{{ asset('public/style2/img/icon-microsoft.svg') }}" alt=""></a>
{{--                        <a href=""><img src="{{ asset('public/style2/img/icon_apple.svg') }}" alt=""></a>--}}
{{--                        <a href=""><img src="{{ asset('public/style2/img/icon_twiter.svg') }}" alt=""></a>--}}
                    </div>
                </div>
                <div class="authen-bottom">
                    {{ __('Already have account,') }} <a href="{{ rrt_route('public/auth/signIn') }}">{{ __('Sign In here!') }}</a>
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
            const email = $(`input[name='email']`).val();
            const password = $(`input[name='password']`).val();
            const isValidEmail = validateEmail(email);
            // const planChecked = $("input[name='join_types[]']:checked").length > 0;
            const termsAccepted = $("#terms").is(":checked");
            const passwordConfirm = $("input[name='password_confirm']").val();
            const passwordsMatch = (password === passwordConfirm);
            const recaptchaResponse = grecaptcha.getResponse();
            if (isValidEmail && password && passwordConfirm && passwordsMatch  && termsAccepted) {
                $('#btn-continue').prop('disabled', false);
            } else {
                $('#btn-continue').prop('disabled', true);
            }
        }
        function handlePlanSelection() {
            const basicChecked = $("#basic").is(":checked");
            const proChecked = $("#pro").is(":checked");
            if (basicChecked && proChecked) {
                alert("You can only select either Basic Seller or Pro Seller, not both.");
                if ($(this).attr('id') === 'basic') {
                    $("#basic").prop("checked", false);
                } else {
                    $("#pro").prop("checked", false);
                }
            }
        }
        $("input[name='email'], input[name='password'], input[name='join_types[]'], #terms").on('input change', function() {
            validateForm();
        });
        $("input[name='join_types[]']").on('change', handlePlanSelection);
        $('#authen-form').on('submit', function(e) {
            e.preventDefault(); // Ngăn chặn form gửi dữ liệu mặc định
            const email = $(`input[name='email']`).val();
            const password = $(`input[name='password']`).val();
            let startSelling = 0;
            if ($("input[name='start_selling']").is(':checked')) {
                startSelling = 1;
            }
            const recaptchaResponse = grecaptcha.getResponse();
            // const joinTypes = $("input[name='join_types[]']:checked").map(function() {
            //     return $(this).val();
            // }).get();
            const data = {
                email,
                password,
                startSelling,
                // joinTypes,
                recaptchaResponse,
                _token: $('input[name="_token"]').val(),
            }
            console.log(data)
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data:data,
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
                    } else {

                        const redirect = response?.redirect;
                        console.log(redirect)
                        if (redirect) {
                            window.location.href = redirect;
                        }
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
