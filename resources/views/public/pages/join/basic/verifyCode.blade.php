@extends("{$pathViewController}.main")
@section('title', 'Basic Join - Verify Code')
@section('body_class', 'join-basic sign-up')
@section('form_url', rrt_route($controllerName . '/postVerifyCode'))
@section('content_basic')
    <h2 class="join-basic-title text-center">Enter validation code</h2>
    <div class="join-basic-alert text-center">
        We sent a verification code to: <br>
        {{ $email }}
    </div>
    <div class="join-basic-method">
        <div class="list-form-input">
            <div class="input-code">
                <input type="text" class="form-control" name="code_1">
            </div>
            <div class="input-code">
                <input type="text" class="form-control" name="code_2">
            </div>
            <div class="input-code">
                <input type="text" class="form-control" name="code_3">
            </div>
            <div class="input-code">
                <input type="text" class="form-control" name="code_4">
            </div>
        </div>
        <div class="text-center">
            <button class="btn btn-feature">Verify</button>
        </div>
    </div>
@endsection
@push('srcipt')
    <script>
        const inputCode = $(".input-code input");
        let checkInput = 0;
        let code;
        inputCode.keyup(function() {
            let value = $(this).val();
            let parent = $(this).parent();
            let next = parent.next().find('input');
            let prev = parent.prev().find('input');
            if (value) return next.select();
        })
        const formAuthen = $(".form-basic-join");
        formAuthen.submit(function(e) {
            e.preventDefault();
            let url = $(this).data('url');
            let data = getFormData(formAuthen);
            console.log(data);
            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: "json",
                beforeSend: function() {
                    // showLoading();
                },
                success: function(response) {
                    let status = response.status ? response.status : 400;
                    let msg = response.msg ? response.msg : 400;
                    let redirect = response.redirect ? response.redirect : "";
                    if (status == 200) {
                        if (redirect) return window.location.href = redirect
                    } else {
                        if (msg.validate) {
                            showNotify("error", "Error", msg.validate)
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
