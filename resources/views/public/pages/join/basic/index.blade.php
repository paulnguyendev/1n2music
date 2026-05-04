@extends("{$pathViewController}.main")
@section('title', 'Basic Join')
@section('body_class', 'join-basic')
@section('content_basic')
    <h2 class="join-basic-title text-center">Continue with</h2>
    <div class="join-basic-method">
        <div class="form-group">
            <label for="">Email</label>
            <input type="text" class="form-control" placeholder="Type your email" name="account">
        </div>
        <div class="form-group">
            <button class="btn btn-primary w-100 btn-submit-authen" disabled>Continue</button>
        </div>
    </div>
    <div class="join-other-method">
        <div class="other-method-title"><span>OR</span></div>
        <div class="other-method-list">
            <a href="#" class="other-method-item">
                <i class="fab fa-facebook"></i>
                <span>Sign in with Facebook</span>
            </a>
            <a href="#" class="other-method-item">
                <i class="fab fa-twitter"></i>
                <span>Sign in with Twitter</span>
            </a>
            <a href="#" class="other-method-item">
                <i class="fab fa-apple"></i>
                <span>Sign in with Apple</span>
            </a>
            <a href="#" class="other-method-item">
                <i class="fab fa-google"></i>
                <span>Sign in with Google</span>
            </a>
        </div>
    </div>
@endsection
@push('srcipt')
    <script>
        const account = $(`input[name='account']`);
        let btnSubmit = $(".btn-submit-authen");
        account.keyup(function() {
            let value = $(this).val();

            if (value && isEmail(value)) {
                btnSubmit.attr('disabled', false);
            } else {
                btnSubmit.attr('disabled', true);
            }
        })
        const formAuthen = $(".form-basic-join");
        formAuthen.submit(function(e) {
            e.preventDefault();
            let data = getFormData(formAuthen);
            let url = $(this).data('url');
            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: "json",
                beforeSend: function() {
                    // showLoading();
                },
                success: function(response) {
                    let redirect = response.redirect ? response.redirect : "";
                    if (redirect) return window.location.href = redirect;
                },
                complete: function() {
                    // hideLoading();
                }
            });
        })
    </script>
@endpush
