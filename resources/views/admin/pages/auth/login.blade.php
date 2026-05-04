@extends($pathViewController . '.main')
@section('content')
    <form id="form-login" data-url="{{ rrt_route($controllerName . '/postLogin') }}"
        data-done="Submit <i class='ti-arrow-right'></i>">
        <div class="login-form-body">
            <p class="text-center  login-title">{{__('ADMIN LOGIN')}}</p>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email">
            </div>
            <div class="form-group">
                <label for="password">{{__('Password')}}</label>
                <input type="password" class="form-control" name="password" id="password">
            </div>
            <div class="submit-btn-area">
                <button id="form_submit" type="submit" class="btn btn-primary">{{__('Submit')}} <i
                        class="ti-arrow-right"></i></button>
            </div>
        </div>
    </form>
@endsection
@push('script')
    <script>
        submitForm(
            "form-login", {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    minlength: 6,
                    required: true,
                },
            }, {

            },
            (data) => {
                console.log(data);
                let status = data.status ? data.status : 400;
                let msg = data.msg ? data.msg : "";
                let msgUser = msg.user ? msg.user : "";
                let redirectUrl = data.redirectUrl ? data.redirectUrl : "";
                switch (status) {
                    case 400:
                        toastr.error(msgUser, "Error");
                        break;
                    default:
                        toastr.success(msg, "Notification", {
                            timeOut: 1000,
                            progressBar: true,
                            onHidden: function() {
                                window.location.href = redirectUrl;
                            },
                        });
                        break;
                }
            }
        );
    </script>
@endpush
