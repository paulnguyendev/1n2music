@extends("{$pathViewController}.main")
@section('title', 'Basic Join - Sign up')
@section('body_class', 'join-basic sign-up')
@section('form_url', rrt_route($controllerName . '/postSignup'))
@section('content_basic')

    <a href="{{ rrt_route($controllerName . '/index') }}" class="join-basic-btn-back"><i class="far fa-chevron-left"></i></a>
    <h2 class="join-basic-title text-center">Sign up with</h2>
    <div class="join-basic-info">
        <img src="{{ asset('public/images/default-avatar-circle.svg') }}" alt="" class="basic-info-avatar">
        <div class="basic-info-text">
            <p>E-mail</p>
            <h3>{{ $email }}</h3>
        </div>
    </div>
    <div class="join-basic-method">
        <div class="form-group">
            <label for="">Create Password</label>
            <input type="password" class="form-control" placeholder="Type your password" name="password">
        </div>
        <div class="form-group">
            <label for="">Confirm password</label>
            <input type="password" class="form-control" placeholder="Type your password again" name="confirm_password">
        </div>
        <div class="form-group join-type-wrap">
            <div class="join-type-list">
                <div class="form-group checkbox-group">
                    <input type="checkbox" name="join_type"  id="join_type_basic" value="basic">
                    <label for="join_type_basic">
                        Basic Seller
                    </label>
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" name="join_type" id="join_type_distribution" value="distribution">
                    <label for="join_type_distribution">
                        Distribution
                    </label>
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" name="join_type" id="join_type_publishing" value="publishing">
                    <label for="join_type_publishing">
                        Publishing
                    </label>
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" name="join_type" id="join_type_pro_seller" value="pro_seller">
                    <label for="join_type_pro_seller">
                        Pro Seller
                    </label>
                </div>
            </div>

        </div>
        <div class="form-group checkbox-group">
            <input type="checkbox" name="is_agree" id="is_agree" value="1">
            <label for="is_agree">
                I have read and agree to the BeatNara <br>
                <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
            </label>
        </div>
        <div class="form-group">
            <input type="hidden" name="email" value="{{ $email }}">
            <button class="btn btn-primary w-100 btn-submit-authen" disabled>Continue</button>
        </div>
    </div>
@endsection
@push('srcipt')
    <script>
        const btnForgetPass = $(".btn-forget-pass");
        let btnSubmit = $(".btn-submit-authen");
        let checkData = {
            password: false,
            confirm_password: false,
            is_agree: false,
            join_type: false,
        };
        let totalCheck = Object.keys(checkData).length
        let isSubmit = 0;
        const countTotalData = (data) => {
            let result = 0;
            for (let key in data) {
                let value = data[key] ? data[key] : false;
                if (value == true) {
                    result = result + 1;
                }
            }
            return result;
        }
        const checkSubmit = (totalCurrent) => {
            isSubmit = totalCheck == totalCurrent ? 1 : 0;
            if (isSubmit == 1) {
                btnSubmit.attr('disabled', false);
            } else {
                btnSubmit.attr('disabled', true);
            }
        }
        btnForgetPass.click(function(e) {
            e.preventDefault();
        })
        const password = $(`input[name='password']`);
        const passwordConfirm = $(`input[name='confirm_password']`);
        password.keyup(debounce(function() {
            let value = $(this).val();
            let valueConfirm = $(passwordConfirm).val();
            if (value && value.length > 5) {
                checkData.password = true;
            } else {
                checkData.password = false;
            }
            if (valueConfirm != '' && value != valueConfirm) {
                showNotify('error', 'Error', 'Password not match');
                checkData.confirm_password = false;
            } else {
                checkData.confirm_password = true;
            }
            let totalCurrent = countTotalData(checkData);
            checkSubmit(totalCurrent);
        }, 1000))
        passwordConfirm.keyup(debounce(function() {
            let value = $(this).val();
            let valuePass = $(password).val();
            if (value && value.length > 5) {
                checkData.confirm_password = true;
            } else {
                checkData.confirm_password = false;
            }
            if (value != valuePass) {
                showNotify('error', 'Error', 'Password not match');
                checkData.confirm_password = false;
            } else {
                showNotify('success', 'success', 'Password match');
                checkData.confirm_password = true;
            }
            let totalCurrent = countTotalData(checkData);
            checkSubmit(totalCurrent);
        }, 1000));
        const checkIsAgree = $(`input[name='is_agree']`);
        checkIsAgree.change(function() {
            if ($(this).is(":checked")) {
                checkData.is_agree = true;
            } else {
                checkData.is_agree = false;
            }
            let totalCurrent = countTotalData(checkData);
            checkSubmit(totalCurrent);
            console.log(isSubmit);
        })
        const checkboxJoinType = $(`input[name=join_type]`);
        checkboxJoinType.change(function() {
            let value = $(this).val();
            if ($(this).is(":checked")) {
                checkboxJoinType.not($(this)).prop('checked', false);
                checkData.join_type = true;
            }
            else {
                checkData.join_type = false;
            }
            let totalCurrent = countTotalData(checkData);
            checkSubmit(totalCurrent);


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
                    showLoading();
                },
                success: function(response) {
                    let redirect = response.redirect ? response.redirect : "";
                    if (redirect) return window.location.href = redirect
                },
                complete: function() {
                    hideLoading();
                }
            });
        })
    </script>
@endpush
