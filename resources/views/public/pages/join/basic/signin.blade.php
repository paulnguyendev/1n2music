@extends("{$pathViewController}.main")
@section('title', 'Basic Join - Sign in')
@section('body_class', 'join-basic sign-up')
@section('form_url', rrt_route($controllerName . '/postSignin'))
@section('content_basic')
    <a href="{{ rrt_route($controllerName . '/logout') }}" class="join-basic-btn-back"><i class="far fa-chevron-left"></i></a>
    <h2 class="join-basic-title text-center">Sign in with</h2>
    <div class="join-basic-info">
        <img src="{{ asset('public/images/default-avatar-circle.svg') }}" alt="" class="basic-info-avatar">
        <div class="basic-info-text">
            <p>E-mail</p>
            <h3>{{ $email }}</h3>
        </div>
    </div>
    <div class="join-basic-method">
        <div class="form-group">
            <label for="">Password</label>
            <input type="password" class="form-control" placeholder="Type your password" name="password">
            <a href="#" id="btn-forget-pass" class="btn-forget-pass">Forget password</a>
        </div>
        <div class="form-group">
            <input type="hidden" name="email" value="{{ $email ?? '' }}">
            <button class="btn btn-primary w-100 btn-submit-authen" disabled>Continue</button>
        </div>
    </div>
@endsection
@push('srcipt')
    <script>
        const btnForgetPass = $(".btn-forget-pass");
        let btnSubmit = $(".btn-submit-authen");
        $("#btn-forget-pass").click(function(e) {
            e.preventDefault();
            let url = "{{ rrt_route($controllerName . '/getForgot') }}";
            let email = $('.basic-info-text > h3').text();

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    email: email
                },
                dataType: "json",
                beforeSend: function() {
                    showLoading();
                },
                success: function(res) {
                    if (res.status == 200) {
                        showNotify("success", "Success", 'Please check your email!')
                    } else {
                        showNotify("error", "Error", 'System Erorr!')
                    }
                },
                complete: function() {
                    hideLoading();
                }
            });
        })
        const password = $(`input[name='password']`);
        password.keyup(function() {
            let value = $(this).val();
            if (value && value.length > 5) {
                btnSubmit.attr('disabled', false);
            } else {
                btnSubmit.attr('disabled', true);
            }
        })
        const formAuthen = $(".form-basic-join");
        formAuthen.submit(function(e) {
            e.preventDefault();
            let url = $(this).data('url');
            let data = getFormData(formAuthen);
            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: "json",
                beforeSend: function() {
                    // showLoading();
                },
                success: function(response) {
                    let msg = response.msg ? response.msg : "";
                    let redirect = response.redirect ? response.redirect : "";
                    let status = response.status ? response.status : 400;
                    if (status == 200) {
                        if (redirect) return window.location.href = redirect;
                    } else {
                        if (msg.password) {
                            showNotify("error", "Error", msg.password)
                        }
                        if (msg.status) {
                            showNotify("error", "Error", msg.status)
                        }

                    }
                    console.log(response);
                },
                complete: function() {
                    // hideLoading();
                }
            });
        })
    </script>
@endpush
