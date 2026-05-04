@section('title', 'Bank Information')
@section('page_title', 'Bank Information')
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
@if (session('status'))
    @push('script')
        <script>
            $(document).ready(function() {
                showNotify("success", "Success", 'Updated Successfull')
            });
        </script>
    @endpush
@endif
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card_title">{{ __('Bank Information') }}</h4>
                <div class="payment-form-inner">
                    <div class="alert alert-info col-center payment-general-info" role="alert">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="payment-general-text">
                                <h4>{{ __('General Information') }}</h4>
                                <p>{{ $data['first_name'] }} &nbsp; {{ $data['last_name'] }} ({{ $data['email'] }})</p>
                                <p>{{ __('Address:') }} {{ $data['address_1'] }}, {{ $data['country'] }}, {{ $data['postal_code'] }}, {{ $data['city'] }}</p>
                            </div>
                            <div class="payment-general-action">
                                <a href="{{ rrt_route($controllerName . '/form', ['step' => 'general']) }}" class="btn btn-default">{{ __('Edit') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="row row-payment-method">
                        <div class="col-md-12 col-payment-method">
                            <h4>{{ __('Select Payout Method') }}</h4>
                            <div class="payment-methods col-center">
                                <div class="payment-method-item {{ $selected == 'paypal' ? 'active' : 'unactive' }}"
                                    data-url="{{ rrt_route($controllerName . '/activeMethod') }}"
                                    data-id="{{ isset($method['paypal']) ? $method['paypal']['id'] : '' }}"
                                    data-selected="{{ isset($method['selected_paypal']) ? $method['selected_paypal']['id'] : '' }}"
                                    data-method="paypal">
                                    <i class="fa fa-paypal"></i>
                                    <p>{{ __('Paypal Transfer') }}</p>
                                    <div class="icon-check"></div>
                                </div>
                                <div class="payment-method-item {{ $selected == 'bank' ? 'active' : 'unactive' }}"
                                    data-url="{{ rrt_route($controllerName . '/activeMethod') }}"
                                    data-id="{{ isset($method['bank']) ? $method['bank']['id'] : '' }}"
                                    data-selected="{{ isset($method['selected_bank']) ? $method['selected_bank']['id'] : '' }}"
                                    data-method="bank">
                                    <i class="fa fa-bank"></i>
                                    <p>{{ __('Bank Transfer') }}</p>
                                    <div class="icon-check"></div>
                                </div>
                            </div>

                            <div id="form-bank" style="display: {{ $selected == 'bank' ? 'block' : 'none' }} " class="payment-method-desc col-center">
                                <form action="{{ rrt_route($controllerName . '/postformBank') }}" method="post">
                                    <input type="hidden" name="method" value="bank">
                                    @isset($data_bank_info['id'])
                                        <input type="hidden" name="payout_method_id" value="{{ $data_bank_info['id'] }}">
                                    @endisset
                                    <div class="col-md-6 col-payment-form">
                                        <div class="payment-method-currency col-center">
                                            <p><strong>{{ __('Bank Account Currency') }}</strong></p>
                                            <select name="bank_currency" id="" class="form-control">
                                                <option value="won" {{ $data_bank_info['currency'] == 'won' ? 'selected' : '' }}>{{ __('Won') }}</option>
                                                <option value="usd" {{ $data_bank_info['currency'] == 'usd' ? 'selected' : '' }}>{{ __('Dollar') }}</option>
                                            </select>
                                        </div>
                                        <div class="payment-method-form">
                                            <div class="form-group">
                                                <label for="">{{ __('SWIFT/BIC') }}</label>
                                                <input name="bank_swift_bic" value="{{ $data_bank_info['swift'] ?? '' }}" type="text" class="form-control">
                                                @if ($errors->has('bank_swift_bic'))
                                                    <span class="error">{{ $errors->first('bank_swift_bic') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{ __('Bank Address') }}</label>
                                                <input name="bank_paymemt_address" type="text" class="form-control" value="{{ $data_bank_info['address'] ?? '' }}">
                                                @if ($errors->has('bank_paymemt_address'))
                                                    <span class="error">{{ $errors->first('bank_paymemt_address') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{ __('Bank City') }}</label>
                                                <input name="bank_paymemt_city" type="text" class="form-control" value="{{ $data_bank_info['city'] ?? '' }}">
                                                @if ($errors->has('bank_paymemt_city'))
                                                    <span class="error">{{ $errors->first('bank_paymemt_city') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{ __('Bank Province/State') }}</label>
                                                <input name="bank_paymemt_province" type="text" class="form-control" value="{{ $data_bank_info['province'] ?? '' }}">
                                                @if ($errors->has('bank_paymemt_provinces'))
                                                    <span class="error">{{ $errors->first('bank_paymemt_provinces') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{ __('Account Number') }}</label>
                                                <input type="text" name="bank_paymemt_number" class="form-control" value="{{ $data_bank_info['number'] ?? '' }}">
                                                @if ($errors->has('bank_paymemt_number'))
                                                    <span class="error">{{ $errors->first('bank_paymemt_number') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{ __('Name of Account Holder (as shown on Paypal statement)') }}</label>
                                                <input type="text" name="bank_name_holder_card" class="form-control" value="{{ $data_bank_info['name_holder'] ?? '' }}">
                                                @if ($errors->has('bank_name_holder_card'))
                                                    <span class="error">{{ $errors->first('bank_name_holder_card') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="status">{{ __('Status') }}</label>
                                                <select class="form-control" name="is_active" id="">
                                                    <option value="1" {{ $data_bank_info['is_active'] == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                    <option value="0" {{ $data_bank_info['is_active'] == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="payment-form-action">
                                            <a href="{{ rrt_route($controllerName . '/form', ['step' => 'general']) }}" class="btn-back btn btn-default">{{ __('Back') }}</a>
                                            <input type="submit" class="btn-back btn btn-primary" value="{{ __('Save') }}">
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div id="form-paypal" style="display: {{ $selected == 'paypal' ? 'block' : 'none' }} " class="payment-method-desc col-center">
                                <form action="{{ rrt_route($controllerName . '/postformPaypal') }}" method="post">
                                    <input type="hidden" name="method" value="paypal">
                                    @isset($data_paypal_info['id'])
                                        <input type="hidden" name="payout_method_id" value="{{ $data_paypal_info['id'] }}">
                                    @endisset
                                    <div class="col-md-6 col-payment-form">
                                        <div class="payment-method-currency col-center">
                                            <p><strong>{{ __('Paypal Account Currency') }}</strong></p>
                                            <select name="paypal_currency" id="" class="form-control">
                                                <option value="won" {{ $data_paypal_info['currency'] == 'won' ? 'selected' : '' }}>{{ __('Won') }}</option>
                                                <option value="usd" {{ $data_paypal_info['currency'] == 'usd' ? 'selected' : '' }}>{{ __('Dollar') }}</option>
                                            </select>
                                        </div>
                                        <div class="payment-method-form">
                                            <div class="form-group">
                                                <label for="">{{ __('Paypal ID') }}</label>
                                                <input name="paypal_id" type="text" class="form-control" value="{{ $data_paypal_info['paypal_id'] ?? '' }}">
                                                @if ($errors->has('paypal_id'))
                                                    <span class="error">{{ $errors->first('paypal_id') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{ __('Name of Account Holder (as shown on Paypal statement)') }}</label>
                                                <input type="text" name="paypal_name_holder_card" class="form-control" value="{{ $data_paypal_info['name_holder'] ?? '' }}">
                                                @if ($errors->has('paypal_name_holder_card'))
                                                    <span class="error">{{ $errors->first('paypal_name_holder_card') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="status">{{ __('Status') }}</label>
                                                <select class="form-control" name="is_active" id="status">
                                                    <option value="1" {{ $data_paypal_info['is_active'] == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                    <option value="0" {{ $data_paypal_info['is_active'] == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="payment-form-action">
                                            <a href="{{ rrt_route($controllerName . '/form', ['step' => 'general']) }}" class="btn-back btn btn-default">{{ __('Back') }}</a>
                                            <input type="submit" class="btn-back btn btn-primary" value="{{ __('Save') }}">
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
            const selector = $(this);
            let id = $(this).data('id');
            let url = $(this).data('url')
            let selected = $(this).data('selected');
            let method = $(this).data('method')
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    id: id,
                    selected: selected,
                    method: method
                },
                dataType: "json",
                success: function(response) {
                    if (response.status_code == 200) {
                        let id = response.result.id;
                        $('div[data-method="' + method + '"]').data('id', id);
                        if (method == 'paypal') {
                            $('div[data-method="paypal"]').addClass('active').removeClass('unactive');
                            $('div[data-method="bank"]').addClass('unactive').removeClass('active');
                            $('#form-paypal').css('display', 'block')
                            $('#form-bank').css('display', 'none')
                        } else if (method == 'bank') {
                            $('div[data-method="bank"]').addClass('active').removeClass('unactive');
                            $('div[data-method="paypal"]').addClass('unactive').removeClass('active');
                            $('#form-bank').css('display', 'block')
                            $('#form-paypal').css('display', 'none')
                        }

                    } else {
                        showNotify("error", "Error", 'System Error')
                    };
                }
            });

        })
    </script>
@endpush
