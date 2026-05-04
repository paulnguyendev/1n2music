@section('title', 'Paypal Information')
@section('page_title', 'Paypal Information')
<style>
    .payment-method-item {
        cursor: pointer;
    }

    .unactive {
        background: #000;
        opacity: 0.4;
        border: 2px solid #000;
        color: #fff;
    }

    .unactive .icon-check {
        display: none;
    }

    .unactive i {
        color: #fff
    }

    .error {
        color: red
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card_title">{{__('Bank Information')}}</h4>
                <div class="payment-form-inner">
                    <div class="alert alert-info col-center payment-general-info" role="alert">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="payment-general-text">
                                <h4>{{__('General Information')}}</h4>
                                <p>{{ $data['first_name'] }} &nbsp; {{ $data['last_name'] }} ({{ $data['email'] }})</p>
                                <p> {{__('Address')}}: {{ $data['address_1'] }}, {{ $data['country'] }},
                                    {{ $data['postal_code'] }},
                                    {{ $data['city'] }}
                                </p>
                            </div>
                            <div class="payment-general-action">
                                <a href="{{ rrt_route($controllerName . '/form', ['step' => 'general']) }}"
                                    class="btn btn-default">{{__('Edit')}}</a>
                            </div>
                        </div>

                    </div>
                    <div class="row row-payment-method">
                        <div class="col-md-12 col-payment-method">
                            <h4>{{__('Select Payout Method')}}</h4>
                            <div class="payment-methods col-center">
                                <div class="payment-method-item active">
                                    <i class="fa fa-paypal"></i>
                                    <p>{{__('Paypal Transfer')}}</p>
                                    <div class="icon-check">

                                    </div>
                                </div>
                                <div class="payment-method-item unactive">
                                    <i class="fa fa-bank"></i>
                                    <p>{{__('Bank Transfer')}}</p>
                                    <div class="icon-check">

                                    </div>
                                </div>
                            </div>
                            <div id="form-bank" style="display: none" class="payment-method-desc col-center">
                                <form action="{{ rrt_route($controllerName . '/postformBank') }}" method="post">
                                    @isset($data_bank_info['id'])
                                        <input type="hidden" name="payout_method_id" value="{{ $data_bank_info['id'] }}">
                                    @endisset
                                    <div class="col-md-6 col-payment-form">
                                        <div class="payment-method-currency col-center">
                                            <p><strong>{{__('Bank Account Currency')}}</strong></p>
                                            <select name="bank_currency" id="" class="form-control">

                                                <option
                                                    @isset($data_bank_info['currency'])
                                                {{ $data_bank_info['currency'] == 'won' ? 'selected' : '' }}
                                                @endisset
                                                    value="won">{{__('Won')}}
                                                </option>
                                                <option
                                                    @isset($data_bank_info['currency'])
                                                {{ $data_bank_info['currency'] == 'usd' ? 'selected' : '' }}
                                                @endisset
                                                    value="usd">{{__('Dolar')}}</option>
                                            </select>
                                        </div>
                                        <div class="payment-method-form">
                                            <div class="form-group">
                                                <label for="">{{__('SWIFT/BIC')}}</label>
                                                <input name="bank_swift_bic"
                                                    @isset($data_bank_info['swift'])   
                                                    value="{{ $data_bank_info['swift'] }}"
                                                    @endisset
                                                    type="text" class="form-control">
                                                @if ($errors->has('bank_swift_bic'))
                                                    <span class="error">{{ $errors->first('bank_swift_bic') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{__('Bank Address')}}</label>
                                                <input name="bank_paymemt_address" type="text" class="form-control"
                                                    @isset($data_bank_info['address'])
                                                   value="{{ $data_bank_info['address'] }}"
                                                    @endisset>
                                                @if ($errors->has('bank_paymemt_address'))
                                                    <span
                                                        class="error">{{ $errors->first('bank_paymemt_address') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{__('Bank City')}}</label>
                                                <input name="bank_paymemt_city" type="text" class="form-control"
                                                    @isset($data_bank_info['city'])
                                              value="{{ $data_bank_info['city'] }}"
                                                    @endisset>
                                                @if ($errors->has('bank_paymemt_city'))
                                                    <span
                                                        class="error">{{ $errors->first('bank_paymemt_city') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{__('Bank Province/State')}}</label>
                                                <input name="bank_paymemt_province" type="text" class="form-control"
                                                    @isset($data_bank_info['province'])value="{{ $data_bank_info['province'] }}"@endisset>
                                                @if ($errors->has('bank_paymemt_provinces'))
                                                    <span
                                                        class="error">{{ $errors->first('bank_paymemt_provinces') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{__('Account Number')}}</label>
                                                <input type="text" name="bank_paymemt_number" class="form-control"
                                                    @isset($data_bank_info['number'])
                                                    value="{{ $data_bank_info['number'] }}"
                                                    @endisset>
                                                @if ($errors->has('bank_paymemt_number'))
                                                    <span
                                                        class="error">{{ $errors->first('bank_paymemt_number') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{__('Name of Account Holder (as shown on Paypal statement)')}}</label>
                                                <input type="text" name="bank_name_holder_card" class="form-control"
                                                    @isset($data_bank_info['name_holder'])
                                                  value="{{ $data_bank_info['name_holder'] }}"
                                                    @endisset>
                                                @if ($errors->has('bank_name_holder_card'))
                                                    <span
                                                        class="error">{{ $errors->first('bank_name_holder_card') }}</span>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="payment-form-action">
                                            <a href="{{ rrt_route($controllerName . '/form', ['step' => 'general']) }}"
                                                class="btn-back btn btn-default">{{__('Back')}}</a>
                                            <input type="submit" class="btn-back btn btn-primary" value="Save">
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="alert alert-warning" role="alert">
                                            <p><strong>Important</strong></p>
                                            <ul>
                                                <li>Please provide your <strong>full name</strong> (or business legal
                                                    name
                                                    for
                                                    businesses), exactly as shown on your bank statement. Failure to do
                                                    so
                                                    may
                                                    result in a returned or delayed payment.</li>
                                            </ul>
                                        </div>
                                    </div> --}}

                                </form>
                            </div>
                            <div id="form-paypal" style="display: block" class="payment-method-desc col-center">
                                <form action="{{ rrt_route($controllerName . '/postformPaypal') }}" method="post">
                                    @isset($data_paypal_info['id'])
                                        <input type="hidden" name="payout_method_id"
                                            value="{{ $data_paypal_info['id'] }}">
                                    @endisset
                                    <div class="col-md-6 col-payment-form">
                                        <div class="payment-method-currency col-center">
                                            <p><strong>{{__('Paypal Account Currency')}}</strong></p>
                                            <select name="paypal_currency" id="" class="form-control">
                                                <option
                                                    @isset($data_paypal_info['currency'])
                                                {{ $data_paypal_info['currency'] == 'won' ? 'selected' : '' }}
                                                @endisset
                                                    value="won">{{__('Won')}}
                                                </option>
                                                <option
                                                    @isset($data_paypal_info['currency'])
                                                {{ $data_paypal_info['currency'] == 'usd' ? 'selected' : '' }}
                                                @endisset
                                                    value="usd">{{__('Dolar')}}</option>
                                            </select>
                                        </div>
                                        <div class="payment-method-form">
                                            {{-- <div class="form-group">
                                                <label for="">SWIFT/BIC</label>
                                                <input name="paypal_swift_bic"
                                                    @isset($data_paypal_info['swift'])   value="{{ $data_paypal_info['swift'] }}"@endisset
                                                    type="text" class="form-control">
                                                @if ($errors->has('paypal_swift_bic'))
                                                    <span
                                                        class="error">{{ $errors->first('paypal_swift_bic') }}</span>
                                                @endif
                                            </div> --}}
                                            <div class="form-group">
                                                <label for="">{{__('Paypal ID')}}</label>
                                                <input name="paypal_id" type="text" class="form-control"
                                                    @isset($data_paypal_info['paypal_id'])
                                                   value=" {{ $data_paypal_info['paypal_id'] }}"
                                                    @endisset>
                                                @if ($errors->has('paypal_id'))
                                                    <span
                                                        class="error">{{ $errors->first('paypal_paymemt_address') }}</span>
                                                @endif
                                            </div>
                                            {{-- <div class="form-group">
                                                <label for="">Paypal City</label>
                                                <input name="paypal_paymemt_city" type="text" class="form-control"
                                                    @isset($data_paypal_info['city'])
                                              value="{{ $data_paypal_info['city'] }}"
                                                    @endisset>
                                                @if ($errors->has('paypal_paymemt_city'))
                                                    <span
                                                        class="error">{{ $errors->first('paypal_paymemt_city') }}</span>
                                                @endif
                                            </div> --}}
                                            {{-- <div class="form-group">
                                                <label for="">Paypal Province/State</label>
                                                <input name="paypal_paymemt_province" type="text"
                                                    class="form-control"
                                                    @isset($data_paypal_info['province'])value="{{ $data_paypal_info['province'] }}"@endisset>
                                                @if ($errors->has('paypal_paymemt_provinces'))
                                                    <span
                                                        class="error">{{ $errors->first('paypal_paymemt_provinces') }}</span>
                                                @endif
                                            </div> --}}
                                            {{-- <div class="form-group">
                                                <label for="">Account Number</label>
                                                <input type="text" name="paypal_paymemt_number"
                                                    class="form-control"
                                                    @isset($data_paypal_info['number'])
                                                    value="  {{ $data_paypal_info['number'] }}"
                                                    @endisset>
                                                @if ($errors->has('paypal_paymemt_number'))
                                                    <span
                                                        class="error">{{ $errors->first('paypal_paymemt_number') }}</span>
                                                @endif
                                            </div> --}}
                                            <div class="form-group">
                                                <label for="">{{__('Name of Account Holder (as shown on Paypal statement)')}}</label>
                                                <input type="text" name="paypal_name_holder_card"
                                                    class="form-control"
                                                    @isset($data_paypal_info['name_holder'])
                                                  value="{{ $data_paypal_info['name_holder'] }}"
                                                    @endisset>
                                                @if ($errors->has('paypal_name_holder_card'))
                                                    <span
                                                        class="error">{{ $errors->first('paypal_name_holder_card') }}</span>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="payment-form-action">
                                            <a href="{{ rrt_route($controllerName . '/form', ['step' => 'general']) }}"
                                                class="btn-back btn btn-default">{{__('Back')}}</a>
                                            <input type="submit" class="btn-back btn btn-primary" value="Save">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        $('.payment-method-item').click(function() {
            $('.payment-method-item').each(function() {
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active').addClass('unactive');
                }
            })
            $(this).removeClass('unactive').addClass('active');
            if ($(this).children('i').hasClass('fa-paypal')) {

                $('#form-paypal').css('display', 'block')
                $('#form-bank').css('display', 'none')
            } else {
                $('#form-paypal').css('display', 'none')
                $('#form-bank').css('display', 'block')
            }
        })
    </script>
@endpush
