@extends('studio.pages.account.main')
@section('title', __('Biography'))
@section('account_title', __('Biography'))
@section('account_content')
    <form data-url="{{ rrt_route('public/studio/account/postProfile') }}" enctype="multipart/form-data" method="post"
        id="formProfile">
        @csrf
        <div class="row">
            <div class="col-sm-12">
                <img class="d-block" id="preview-avatar"
                    @if ($user['thumbnail']) src="{{ url('public/uploads/users/' . $user['thumbnail']) }}" @else src="{{ rrt_show_thumbnail() }}" @endif
                    style="height: 100px" width="112px" alt="" srcset="">
                <input data-url="{{ rrt_route('public/studio/account/uploadAvatar', ['id' => $user['id']]) }}"
                    type="file" id="avatar" name="avatar" class="d-none">
                <button style="width: 110px" class="btn btn-primary mb-2 mt-4 btn-upload" value="">{{__('Upload')}}</button>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{__('First name')}}</label>
                    <input type="text" name="first_name" value="{{ $user['first_name'] ?? '' }}" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{__('Last name')}}</label>
                    <input value="{{ $user['last_name'] ?? '' }}" name="last_name" type="text" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{__('PRO')}}</label>
                    <select name="pro" id="pro" class="form-control">
                        <option value="">{{__('Please choose')}}</option>
                        @foreach ($dataPro as $valuePro => $itemPro)
                            <option {{ $userPro == $valuePro ? 'selected' : '' }} value="{{ $valuePro }}">
                                {{ $itemPro }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('IPI') }}</label>
                    <input value="{{ $user['ipi_cae'] ?? '' }}" name="ipi_cae" type="text" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="">{{ __('Biography') }}</label>
                    <textarea name="bio" id="" placeholder="{{ __('Write something about you...') }}" rows="3" class="form-control">{{ $user['bio' ?? ''] }}</textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Street') }}</label>
                    <input type="text" value="{{ $user['address'] ?? '' }}" name="address" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Type Currency') }}</label>
                    <select name="currency" id="currency" class="form-control">
                        <option value="">
                            {{ __('Please choose Type Currency') }}
                        </option>
                        @foreach ($dataCurrency as $key => $currency)
                            <option {{ $user->currency == $currency['value'] ? 'selected' : '' }} value="{{ $currency['value'] }}">
                                {{  $currency['unit'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ __('Zip Code') }}</label>
                    <input type="text" value="{{ $user['zip_code'] ?? '' }}" name="zip_code" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ __('City') }}</label>
                    <input type="text" value="{{ $user['city'] ?? '' }}" name="city" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ __('Country') }}</label>
                    <input type="text" value="{{ $user['country'] ?? '' }}" name="country" class="form-control">
                </div>
            </div>
        </div>
        <div class="card-inner-footer">
            <div class="text-right">
                <input type="hidden" name="id" value="{{ $user['id'] ?? '' }}">
                <button type="submit" class="btn btn-primary ladda-button" data-style="expand-left">
                    <span class="ladda-label">{{ __('Save Changes') }}</span>
                </button>
            </div>
        </div>
    </form>
@endsection
@push('script')
    <script>
        const formProfile = $("#formProfile");
        formProfile.submit(function(e) {
            e.preventDefault();
            let url = $(this).data('url');

            let data = getFormData(formProfile);
            if (!data.first_name) {
                return showNotify('error', '{{ __("Error") }}', '{{ __("Please Enter Your First Name") }}');
            }
            if (!data.last_name) {
                return showNotify('error', '{{ __("Error") }}', '{{ __("Please Enter Your Last Name") }}');
            }
            const btnSubmit = $(this).find(`button[type=submit]`);
            const laddaInstance = Ladda.create(btnSubmit[0]);
            laddaInstance.start();
            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: "json",
                beforeSend: function() {

                },
                success: function(response) {
                    if (response.status == 200) {
                        showNotify('success', '{{ __("Success") }}', response.msg);
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        showNotify('error', '{{ __("Error") }}', response.msg);
                    }


                },
                complete: function() {
                    laddaInstance.stop();
                }
            });
        })
        const btn_upload = $('.btn-upload');
        const file = $("input[name=avatar]");
        btn_upload.click(function(e) {
            e.preventDefault()
            file.click()
        });
        file.change(function() {
            previewImage();

            var input = document.getElementById('avatar');
            let file_img = input.files[0];

            if (!file_img) {
                alert("{{ __('Please select a file.') }}");
                return;
            }

            // Create FormData object and append file to it
            var formData = new FormData();
            formData.append('file', file_img);
            let url = file.data('url');
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                dataType: "json",
                contentType: false, // Important: Prevent jQuery from setting content type
                processData: false, // Important: Prevent jQuery from processing data
                beforeSend: function() {
                    // You can add any code that needs to be executed before the request is sent
                },
                success: function(response) {
                    // Handle successful response
                    showNotify('success', '{{ __('Success') }}', '{{ __('Update Profile Successfully') }}');
                },

                complete: function() {
                    // This will be called regardless of success or failure
                    laddaInstance.stop();
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(xhr.responseText);
                    showNotify('error', '{{ __("Error") }}', '{{ __("Failed to update profile.") }}');
                }
            });

        })

        function previewImage() {
            var input = document.getElementById('avatar');
            var preview = document.getElementById('preview-avatar');

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {

                    preview.src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
