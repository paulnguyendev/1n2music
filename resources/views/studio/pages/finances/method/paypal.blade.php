<div id="form-paypal" data-method="paypal">
    <form action="{{ rrt_route($controllerName . '/postformPaypal') }}" method="post">
        <input type="hidden" name="method" value="paypal">
        <input type="hidden" name="payout_method_id" value="{{ $paymentAccountID ?? '' }}">

        <div class="col-md-8 col-payment-form">
            <div class="payment-method-currency col-center">
                <p><strong>{{ __('Paypal Account Currency') }}</strong></p>
                @php
                    $currency = $paymentMethodInfo['currency'] ?? 'won';
                @endphp
                <select name="currency" id="" class="form-control">
                    <option value="won" {{ $currency == 'won' ? 'selected' : '' }}>{{ __('Won') }}</option>
                    <option value="usd" {{ $currency == 'usd' ? 'selected' : '' }}>{{ __('Dollar') }}</option>
                </select>
            </div>
            <div class="payment-method-form">
                <div class="form-group">
                    <label for="">{{ __('Paypal ID') }}</label>
                    <input name="paypal_id" type="text" class="form-control"
                        value="{{ $paymentMethodInfo['paypal_id'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="">{{ __('Name of Account Holder (as shown on Paypal statement)') }}</label>
                    <input type="text" name="name_holder" class="form-control"
                        value="{{ $paymentMethodInfo['name_holder'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="status">{{ __('Status') }}</label>
                    @php
                        $isActive = $paymentMethod['is_active'] ?? 1;
                    @endphp
                    <select class="form-control" name="is_active" id="status">
                        <option value="1" {{ $isActive == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ $isActive == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
            </div>
            <div class="payment-form-action">
                <a href="{{ rrt_route($controllerName . '/index') }}" class="btn-back btn btn-default">{{ __('Back') }}</a>
                <input type="submit" class="btn-back btn btn-primary" value="{{ __('Save') }}">
            </div>
        </div>
    </form>
</div>
