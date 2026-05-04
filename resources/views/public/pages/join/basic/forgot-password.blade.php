@extends("{$pathViewController}.main")
@section('title', 'Basic Join - Sign in')
@section('body_class', 'join-basic sign-up')
@section('form_url', rrt_route($controllerName . '/postForgot', ['token' => request()->token]))
@section('content_basic')

    <h2 class="join-basic-title text-center">Create new password</h2>
    <div class="join-basic-method">
        <div class="form-group">
            <label for="">Password</label>
            <input type="password" class="form-control" placeholder="Type your password" name="password" id="pw">

        </div>
        <div class="form-group">
            <label for="">Password confirm</label>
            <input type="password" class="form-control" placeholder="Type your password confim" id="pw_cf">

        </div>
        <div class="form-group">
            <input type="hidden" name="code" value="{{ $code ?? '' }}">
            <button class="btn btn-primary w-100 btn-submit-authen" disabled>Continue</button>
        </div>
    </div>
@endsection
@push('srcipt')
    <script>
        const pass_cf = $('#pw_cf');
        const pass = $('#pw');
        pass_cf.keyup(function(e) {
            let val_pw = pass.val();
            let val_pw_cf = pass_cf.val();
            if (val_pw.length == 0) {
                showNotify("error", "Error", 'Password not empty')
            } else {

                if (val_pw.length > 5 && val_pw != val_pw_cf) {
                    showNotify("error", "Error", 'Password incorrect')
                } else {
                    $('.btn-submit-authen').prop('disabled', false)
                }
            }
        });
        const btnForgetPass = $(".btn-forget-pass");
        let btnSubmit = $(".btn-submit-authen");
        btnForgetPass.click(function(e) {
            e.preventDefault();
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
                        if (msg) {
                            showNotify("error", "Error", msg)
                        }


                    }

                },
                complete: function() {
                    // hideLoading();
                }
            });
        })
    </script>
@endpush
