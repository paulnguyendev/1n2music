@extends('admin.main')
@section('page_title', 'Payment')
@section('title', 'Payment')
@section('buttons')
    <a href="{{ $previous }}" class="btn btn-primary">{{__('Back')}}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
        data-form="formSubmit">{{__('Save Changes')}}</button>
@endsection
@section('content')
    @push('css')
        <style>
            .card-body.payment {
                min-height: 350px
            }
        </style>
    @endpush
    <form id="formSubmit" action = "{{ rrt_route($controllerName . '/saveList', ['id' => $id]) }}" method = "post">
        <input type="hidden" name="payment_accounts[id]" value="{{ $user_payment->id ?? '' }}">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6>{{ __('Account info') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('First Name') }}</label>
                                    <input type="text" class="form-control" name="payment_accounts[first_name]"
                                        value="{{ $user_payment->first_name ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Last Name') }}</label>
                                    <input type="text" class="form-control" name="payment_accounts[last_name]"
                                        value="{{ $user_payment->last_name ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Email') }}</label>
                                    <input type="text" class="form-control" name="payment_accounts[email]"
                                        value="{{ $user_payment->email ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Date of Birth') }}</label>
                                    <input type="date" class="form-control" name="payment_accounts[date_of_birth]"
                                        value="{{ $user_payment->date_of_birth ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Country') }}</label>
                                    <select name="payment_accounts[country]" class="form-control select2">
                                        @include('studio.elements.select_country', [
                                            'value' => $user_payment->country ?? '',
                                        ])
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Address') }}</label>
                                    <input type="text" class="form-control" name="payment_accounts[address_1]"
                                        value="{{ $user_payment->address_1 ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Address') }}2</label>
                                    <input type="text" class="form-control" name="payment_accounts[address_2]"
                                        value="{{ $user_payment->address_2 ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('City') }}</label>
                                    <input type="text" class="form-control" name="payment_accounts[city]"
                                        value="{{ $user_payment->city ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Postal Code') }}</label>
                                    <input type="text" class="form-control" name="payment_accounts[postal_code]"
                                        value="{{ $user_payment->postal_code ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Commision') }} (%)</label>
                                    @php
                                        $commision = $user_payment->commision ?? '';
                                        $commision = $commision ? $commision * 100 : '';
                                    @endphp
                                    <input type="number" class="form-control" name="payment_accounts[commision]"
                                        value="{{ $commision }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if (isset($user_payment->paymentmethod) && count($user_payment->paymentmethod))
                @foreach ($user_payment->paymentmethod as $item)
                    @php
                        $itemInfo = $item->info ?? null;
                        $itemInfoId = $itemInfo->id ?? '';
                    @endphp
                    @if ($item->method == 'paypal')
                        @isset($item->info)
                            @php
                                $paypal = $item->info;
                            @endphp
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6>{{ __('Paypal Transfer') }}</h6>
                                    </div>
                                    <div class="card-body payment">
                                        <div class="form-group col-sm-12">
                                            <label for="">{{ __('Name of Account Holder') }}</label>
                                            <input type="text" class="form-control"
                                                name="payout_method_info[{{ $itemInfoId }}][name_holder]"
                                                value="{{ $paypal->name_holder }}">
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="">{{ __('Paypal ID') }}</label>
                                            <input type="text" class="form-control" value="{{ $paypal->paypal_id }}"
                                                name="payout_method_info[{{ $itemInfoId }}][paypal_id]">
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="">{{ __('Currency') }}</label>
                                            <select name="payout_method_info[{{ $itemInfoId }}][currency]" id=""
                                                class="form-control">
                                                <option {{ $paypal->currency == 'won' ? 'selected' : '' }} value="won">
                                                    {{ __('Won') }}
                                                </option>
                                                <option {{ $paypal->currency == 'usd' ? 'selected' : '' }} value="usd">
                                                    {{ __('Dolar') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endisset
                    @elseif($item->method == 'bank')
                        @isset($item->info)
                            @php
                                $bank = $item->info;
                            @endphp
                        @endisset
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>{{ __('Bank Transfer') }}</h6>
                                </div>
                                <div class="card-body payment">
                                    <div class="form-group col-sm-12">
                                        <label for="">{{ __('Name of Account Holder') }}</label>
                                        <input type="text"
                                            name="payout_method_info[{{ $itemInfoId }}][name_holder]"
                                            class="form-control" value="{{ $bank->name_holder }}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="">{{ __('SWIFT') }}/{{ __('BIC') }}</label>
                                        <input name="payout_method_info[{{ $itemInfoId }}][swift]"
                                            value="{{ $bank->swift }}" type="text" class="form-control">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="">{{ __('Bank Address') }}</label>
                                        <input name="payout_method_info[{{ $itemInfoId }}][address]" type="text"
                                            class="form-control" value="{{ $bank->address }}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="">{{ __('Bank City') }}</label>
                                        <input name="payout_method_info[{{ $itemInfoId }}][city]"
                                            value="{{ $bank->city }}" type="text" class="form-control">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="">{{ __('Bank Province') }}/{{ __('Bank State') }}</label>
                                        <input name="payout_method_info[{{ $itemInfoId }}][province]"
                                            type="text" class="form-control" value="{{ $bank->province }}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="">{{ __('Account Number') }}</label>
                                        <input type="text" name="payout_method_info[{{ $itemInfoId }}][number]"
                                            class="form-control" value="{{ $bank->number }}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="">{{ __('Currency') }}</label>
                                        <select name="payout_method_info[{{ $itemInfoId }}][currency]"
                                            id="" class="form-control">
                                            <option {{ $bank->currency == 'won' ? 'selected' : '' }} value="won">
                                                {{ __('Won') }}
                                            </option>
                                            <option {{ $bank->currency == 'usd' ? 'selected' : '' }} value="usd">
                                                {{ __('Dolar') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </form>    
@endsection
