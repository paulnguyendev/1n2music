@extends('public2.main')
@section('body_class', 'authen-page')
@push('css')
<style>
.form-group .row {
    display: flex;
    gap: 10px;
}
.form-group .col-3 {
    flex: 0 0 25%;
    max-width: 25%;
}
.form-group .col-4 {
    flex: 0 0 33.33%;
    max-width: 33.33%;
}
.form-group .col-9 {
    flex: 0 0 75%;
    max-width: 75%;
}

</style>
@endpush
@section('content')
    <div class="authen-wrap">
        <div class="authen-inner">
            <div class="authen-left">
                <div class="authen-logo">
                    <a href="{{rrt_route('public/home/index')}}"> <img src="{{ asset('public/style2/img/logo-vertical.svg') }}" alt=""></a>
                </div>
                <div class="authen-title">
                    <a href=""><img src="{{ asset('public/style2/img/ic_outline-arrow-back.svg') }}" alt=""></a>
                    <span> {{__('Update Information')}}</span>
                </div>
                <form id="authen-form" action="{{ rrt_route('public/auth/postUpdateInfo') }}" method="POST">
                    <div class="authen-form">
                        <div class="form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <input type="text" name="email" class="form-control" placeholder="{{ __('Enter Your Email') }}" value="{{ $user->email ?? '' }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="first_name">{{ __('First Name') }}</label>
                            <input type="text" name="first_name" class="form-control" placeholder="{{ __('Enter Your First Name') }}" value="{{ $user->first_name ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label for="last_name">{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" class="form-control" placeholder="{{ __('Enter Your Last Name') }}" value="{{ $user->last_name ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label for="dob">{{ __('Date of birth') }}</label>
                            <input type="date" name="dob" class="form-control" placeholder="{{ __('Your BirthDay') }}" value="{{ $user->date_of_birth ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label for="country_code">{{ __('Country Code') }}</label>
                            <select type="text" name="country_code"id="country_code_id_input" style="height: 50px" class="form-control" placeholder="{{ __('Enter Your Country Code') }}" value="{{ $user->country_code ?? '' }}">
                                <option value="">{{__('Please Choose Country')}}</option>
                                @foreach($countries as $country)
                                    <option data-phone-example="{{$country->phone_example}}"  value="{{$country->phone_code}}" {{( $country->phone_code) == ($user->country_code) ? 'selected' : ""}}>{{$country->name. " ". "(+{$country->phone_code})"}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">{{ __('Phone') }}</label>
                            <input type="text" name="phone" placeholder="097 272 2398" maxlength="15" id="phone" class="form-control" placeholder="{{ __('Enter Your Phone') }}" value="{{ $user->phone ?? '' }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="address">{{ __('Street') }}</label>
                            <input type="text" name="address" class="form-control" placeholder="{{ __('Enter Your Street') }}" value="{{ $user->address ?? '' }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="zip_code">{{ __('Zip Code') }}</label>
                            <input type="text" name="zip_code" class="form-control" placeholder="{{ __('Enter Your Zip Code') }}" value="{{ $user->zip_code ?? '' }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="city">{{ __('City') }}</label>
                            <input type="text" name="city" class="form-control" placeholder="{{ __('Enter Your City') }}" value="{{ $user->city ?? '' }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="country">{{ __('Country') }}</label>
                            <select type="text" name="country" style="height: 50px" class="form-control" value="{{ $user->country_code ?? '' }}">
                                <option value="">{{__('Please Choose Country')}}</option>
                                @foreach($countries as $country)
                                    <option value="{{$country->name}}" {{$country->name == $user->country ? 'selected' : ""}}>{{$country->name}}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" name="country" class="form-control" placeholder="{{ __('Enter Your Country') }}" value="{{ $user->country ?? '' }}"> --}}
                        </div>
                        
                        <div class="form-group">
                            <label for="pro">{{ __('PRO') }}</label>
                            <select name="pro" id="pro" class="form-control" style="height: 50px">
                                <option value="">{{ __('Select Your PRO') }}</option>
                                @foreach($dataPro as $pro)
                                    <option value="{{$pro}}" {{ $user->pro == $pro ? 'selected' : '' }}>{{$pro}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tax_type">{{ __('Tax Type') }}</label>
                            <select name="tax_type" class="form-control" id="tax_type" style="height: 50px">
                                <option value="1" @if($user->tax_type === 1) selected @endif>{{ __('Personal') }}</option>
                                <option value="2" @if($user->tax_type === 2) selected @endif>{{ __('Business') }}</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <h4 style="margin-bottom: 40px">{{ __('Main Payment Method') }}</h4>
                            <div style="display: flex; align-items: center; gap: 30px">
                                <div style="display: flex; align-items: center; margin-right: 20px;">
                                    <label for="paypal" style="margin-bottom: 0!important;">{{ __('Paypal') }}</label>
                                    <input type="radio" name="payment_method" id="paypal" value="paypal" {{ ($user->main_payment_method ?? 'paypal') === 'paypal' ? 'checked' : '' }} style="margin-left: 5px;">
                                </div>
                                <div style="display: flex; align-items: center;">
                                    <label for="bank" style="margin-bottom: 0!important;">{{ __('Bank') }}</label>
                                    <input type="radio" name="payment_method" id="bank" value="bank" {{ ($user->main_payment_method === 'bank') ? 'checked' : '' }} style="margin-left: 5px;">
                                </div>
                            </div>
                        </div>
                        <div class="bank-info form-group" style="display: none;">
                            <h4 style="margin-bottom: 40px">{{ __('Bank Information') }}</h4>
                            <label for="bank_name">{{ __('Bank Name') }}</label>
                            <label>
                                <input type="text" name="bank_name" class="form-control mb-2" placeholder="{{ __('Enter Your Bank name') }}" value="{{ $user->bank_name ?? '' }}">
                            </label>
                            <label for="bank_owner">{{ __('Bank Owner') }}</label>
                            <label>
                                <input type="text" name="bank_owner" class="form-control mb-2" placeholder="{{ __('Enter Your Bank Owner') }}" value="{{ $user->bank_owner ?? '' }}">
                            </label>
                            <label for="bank_number">{{ __('Bank Number') }}</label>
                            <label>
                                <input type="text" name="bank_number" class="form-control mb-2" placeholder="{{ __('Enter Your Bank Number') }}" value="{{ $user->bank_number ?? '' }}">
                            </label>
                        </div>
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group">
                            <button id="btn-continue" class="btn-authen">{{ __('Continue') }}</button>
                        </div>
                    </div>
                </form>
                
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
        $("#country_code_id_input").on('change', function(){
            let phone_example = $(this).find("option:selected").data('phone-example');
            $("#phone").prop("placeholder", phone_example ? phone_example : "000 000 000");
            $('#phone').val('');
        })
        const phoneInput = document.getElementById('phone');
        phoneInput.value = formatPhone($("#phone").val());
        function formatPhone(input) {
            console.log("input: ",input)
            const regex = /^[a-zA-Z0-9]*$/;
          
            if(input){
                if (!regex.test(input)) {
                    input = input.replace(/[^a-zA-Z0-9]/g, '');
                }
                return input.replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3');
            }
            return input;
        }
       formatPhone();
        phoneInput.addEventListener('keyup', function() {
            // Remove non-digit characters
            console.log('formatPhone($("#phone").val()): ', formatPhone($("#phone").val()))
            phoneInput.value = formatPhone($("#phone").val());
        });
        $('input[name="payment_method"]').change(function() {
            if ($(this).val() === 'bank') {
                $('.bank-info').show();
            } else {
                $('.bank-info').hide();
            }
        });
        $('input[name="payment_method"]:checked').change();
        $('#authen-form').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 400) {
                        const msg = response?.msg;
                        const firstKey = Object.keys(msg)[0];
                        const firstMsg = msg[firstKey];

                        toastr.error(firstMsg, 'Error');
                    } else {
                        const redirect = response?.redirect;
                        if (redirect) {
                            window.location.href = redirect;
                        }
                    }
                },
                error: function(xhr) {
                    // Handle errors
                    console.log(xhr.responseText);
                }
            });
        });
    </script>
@endpush
