@extends('admin.main')
@section('page_title', __('member.Update_Account'))
@section('title', __('member.Update_Account'))
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{ __('member.back') }}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
        data-form="formSubmit">{{ __('member.save_change') }}</button>
@endsection
@section('content')
<form id="formSubmit" action = "{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method = "post">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card_title">{{ __('Information') }}</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">{{ __('First Name') }} (*)</label>
                                <input type="text" class="form-control" name="first_name"
                                    value="{{ $item['first_name'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">{{ __('Last Name') }} (*)</label>
                                <input type="text" class="form-control" name="last_name"
                                    value="{{ $item['last_name'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">{{ __('Email') }} (*)</label>
                                <input type="email" class="form-control" name="email"
                                    value="{{ $item['email'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">{{ __('Phone') }} (*)</label>
                                <input type="tel" class="form-control" name="phone"
                                    value="{{ $item['phone'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">{{ __('Date Of Birth') }}</label>
                                <input type="date" class="form-control" name="date_of_birth"
                                       value="{{ $item['date_of_birth'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        @if(!$id)
                            @if($type==='seller')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Plan Type') }}</label>
                                    <select class="form-control" name="plan_type" id="plan_type">
                                        <option value="free_seller">{{ __('Free Seller') }}</option>
                                        <option value="pro_seller">{{ __('Pro Seller') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Cycle') }}</label>
                                    <select class="form-control" name="cycle" id="cycle">
                                        <option value="monthly">{{ __('Monthly') }}</option>
                                        <option value="annually">{{ __('Annually') }}</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>

                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card_title">{{ __('SNS Info') }}</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ __('SNS ID') }} (*)</label>
                                <input type="text" class="form-control" name="sns_id"
                                       value="{{ $item['sns_id'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ __('SNS Platform') }} (*)</label>
                                <input type="password" class="form-control" name="sns_platform"
                                       value="{{ $item['sns_platform'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card_title">{{ __('Login info') }}</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ __('Username') }} (*)</label>
                                <input type="text" class="form-control" name="username"
                                    value="{{ $item['username'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ __('Password') }} (*)</label>
                                <input type="password" class="form-control" name="password"
                                    value="{{ $item['password'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ __('Status') }}</label>
                                @php
                                    $status = $item['status'] ?? '';
                                @endphp
                                <select name="status" class="form-control" id="">
                                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>
                                        {{ __('Active') }}</option>
                                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>
                                        {{ __('Pending') }}
                                    </option>
                                    <option value="suspend" {{ $status == 'suspend' ? 'selected' : '' }}>
                                        {{ __('Suspend') }}
                                    </option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card_title">{{ __('Subscription') }}</h4>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">{{ __('Expiration Date') }} (*)</label>
                                <input type="date" class="form-control" name="expiration_date"
                                    value="{{ $item['expiration_date'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">{{ __('IPI') }} (*)</label>
                                <select class="form-control" name="tax_type" id="">

                                    @foreach ($pro_organizations as $pro_organization)
                                        <option
                                            @isset($item['pro_organization'])
                                        {{ $pro_organization->id == $item['pro_organization'] ? 'selected' : '' }}
                                        @endisset
                                            value="{{ $pro_organization->id }}">{{ $pro_organization->name }}</option>
                                    @endforeach

                                </select>
                                {{-- <input type="text" class="form-control" name="pro_organization"
                                        value="{{ $item['pro_organization'] ?? '' }}"> --}}
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">{{ __('Pro') }} (*)</label>
                                <input type="text" class="form-control" name="pro"
                                    value="{{ $item['pro'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">{{ __('Tax Type') }} (*)</label>
                                <select class="form-control" name="tax_type" id="">

                                    @foreach ($taxs as $tax)
                                        <option
                                            @isset($item['tax_type'])
                                        {{ $tax->id == $item['tax_type'] ? 'selected' : '' }}
                                        @endisset
                                            value="{{ $tax->id }}">{{ $tax->name }}</option>
                                    @endforeach

                                </select>

                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">{{ __('Comment') }} (*)</label>
                                <select class="form-control" name="is_comment" id="">
                                    @if (isset($item->is_comment))
                                        <option {{ $item->is_comment == 1 ? 'selected' : '' }} value="1">
                                            {{ __('Allow Comment') }}
                                        </option>
                                        <option {{ $item->is_comment == 0 ? 'selected' : '' }} value="0">
                                            {{ __('Disallow Comment') }}</option>
                                    @else
                                        <option value="1"> {{ __('Allow Comment') }}
                                        </option>
                                        <option value="0">{{ __('Disallow Comment') }}</option>
                                    @endif

                                </select>

                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">{{ __('Payment') }} (*)</label>

                                <select class="form-control" id="payment" name="main_payment_method">
                                    <option value="paypal" {{ (empty($item) || !isset($item['main_payment_method']) || $item['main_payment_method'] === "paypal") ? "selected" : "" }}>PayPal</option>
                                    <option value="bank" {{ (empty($item) || !isset($item['main_payment_method']) || $item['main_payment_method'] === "bank") ? "selected" : "" }}>Bank</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card_title">{{ __('Bank Info') }}</h4>
                    <div class="col-sm-12" id="bankDetails" style="display: none;">
                    <div class="form-group">
                        <label for="bank_name">{{ __('Bank Name') }} (*)</label>
                        <input type="text" class="form-control" name="bank_name" value="{{ $item['bank_name'] ?? '' }}">
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="bank_owner">{{ __('Bank Owner') }} (*)</label>
                        <input type="text" class="form-control" name="bank_owner" value="{{ $item['bank_owner'] ?? '' }}">
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="bank_number">{{ __('Bank Number') }} (*)</label>
                        <input type="text" class="form-control" name="bank_number" value="{{ $item['bank_number'] ?? '' }}">
                        <span class="help-block"></span>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="account_type" value="{{ $type }}">
</form>
@endsection
@push('script')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script>
        $('select[name="plan_id"]').select2({
            placeholder: 'Choose Plan'
        });
        $('select[name="status"]').select2({
            placeholder: 'Choose Status'
        });
        $(document).ready(function() {
            function toggleBankDetails() {
                const paymentMethod = $('#payment').val();
                if (paymentMethod === 'bank') {
                    $('#bankDetails').show();
                } else {
                    $('#bankDetails').hide();
                }
            }
            toggleBankDetails();
            $('#payment').change(function() {
                toggleBankDetails();
            });
        });
    </script>
@endpush
