<div id="form-bank" data-method = "bank">
    <form action="{{ rrt_route($controllerName . '/postformBank') }}" method="post">
        <input type="hidden" name="method" value="bank">
        <input type="hidden" name="payout_method_id" value="{{ $paymentAccountID ?? '' }}">

        <div class="col-md-8 col-payment-form">
            <div class="payment-method-currency col-center">
                <p><strong>{{__('Bank Account Currency')}}</strong></p>
                @php
                    $currency = $paymentMethodInfo['currency'] ?? 'won';
                @endphp
                <select name="currency" id="" class="form-control">
                    <option value="won" {{ $currency == 'won' ? 'selected' : '' }}>{{__('Won')}}</option>
                    <option value="usd" {{ $currency == 'usd' ? 'selected' : '' }}>{{__('Dollar')}}</option>
                </select>
            </div>
            <div class="payment-method-form">
                <div class="form-group">
                    <label for="">{{__('SWIFT/BIC')}} </label>
                    <input name="swift" type="text" class="form-control"
                        value="{{ $paymentMethodInfo['swift'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="">{{__('City')}}</label>
                    <input type="text" name="city" class="form-control"
                        value="{{ $paymentMethodInfo['city'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label for=""> {{__('Province/State')}}</label>
                    <input type="text" name="province" class="form-control"
                        value="{{ $paymentMethodInfo['province'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="">{{__('Address')}}</label>
                    <input type="text" name="address" class="form-control"
                        value="{{ $paymentMethodInfo['address'] ?? '' }}">
                </div>
                
              
                <div class="form-group">
                    <label for="">{{__('Bank name')}}</label>
                    <input name="bank_name" type="text" class="form-control"
                        value="{{ $paymentMethodInfo['bank_name'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="">{{__('Account Number')}}</label>
                    <input type="text" name="number" class="form-control"
                        value="{{ $paymentMethodInfo['number'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="">{{__('Name of Account Holder (as shown on Paypal statement)')}}</label>
                    <input type="text" name="name_holder" class="form-control"
                        value="{{ $paymentMethodInfo['name_holder'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="status">{{__('Status')}}</label>
                    @php
                        $isActive = $paymentMethod['is_active'] ?? 1;
                    @endphp
                    <select class="form-control" name="is_active" id="status">
                        <option value="1" {{ $isActive == 1 ? 'selected' : '' }}>{{__('Active')}} </option>
                        <option value="0" {{ $isActive == 0 ? 'selected' : '' }}>{{__('Inactive')}}</option>
                    </select>
                </div>
            </div>
            <div class="payment-form-action">
                <a href="{{ rrt_route($controllerName . '/index') }}" class="btn-back btn btn-default">{{__('Back')}}</a>
                <input type="submit" class="btn-back btn btn-primary" value="Save">
            </div>
        </div>
    </form>
</div>
