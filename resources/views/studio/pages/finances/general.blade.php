@section('title', __('General Information'))
@section('page_title', __('General Information'))
<style>
    .error {
        color: red
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card_title">{{__('General Informations')}}</h4>
                <form action="{{ rrt_route($controllerName . '/postform') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ isset($data['id']) ? $data['id'] : '' }}">
                    <input type="hidden" name="method" value="{{ !empty($method) ? $method : '' }}">
                    <div class="payment-form-inner">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('First Name') }} <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" name="first_name"
                                        value="{{ isset($data['first_name']) ? $data['first_name'] : old('first_name') }}">
                                    @if ($errors->has('first_name'))
                                        <span class="error">{{ $errors->first('first_name') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Last Name') }} <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control"
                                        value="{{ isset($data['last_name']) ? $data['last_name'] : old('last_name') }}"
                                        name="last_name">
                                    @if ($errors->has('last_name'))
                                        <span class="error">{{ $errors->first('last_name') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Email') }} <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control"
                                        value="{{ isset($data['email']) ? $data['email'] : old('email') }}"
                                        name="email">
                                    @if ($errors->has('email'))
                                        <span class="error">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Date of Birth') }} <span class="text-danger">(*)</span></label>
                                    <input type="date" name="date_of_birth" class="form-control"
                                        value="{{ isset($data['date_of_birth']) ? $data['date_of_birth'] : old('date_of_birth') }}">
                                    @if ($errors->has('date_of_birth'))
                                        <span class="error">{{ $errors->first('date_of_birth') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">{{ __('Country') }}</label>
                            <select name="country" class="form-control select2">
                                @include('studio.elements.select_country')
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Address') }} <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control"
                                        value="{{ isset($data['address_1']) ? $data['address_1'] : old('address_1') }}"
                                        name="address_1">
                                    @if ($errors->has('address_1'))
                                        <span class="error">{{ $errors->first('address_1') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Address 2') }}</label>
                                    <input type="text" class="form-control"
                                        value="{{ isset($data['address_2']) ? $data['address_2'] : old('address_2') }}"
                                        name="address_2">
                                    @if ($errors->has('address_2'))
                                        <span class="error">{{ $errors->first('address_2') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('City') }} <span class="text-danger">(*)</span></label>
                                    <input type="text"
                                        value="{{ isset($data['city']) ? $data['city'] : old('city') }}"
                                        class="form-control" name="city">
                                    @if ($errors->has('city'))
                                        <span class="error">{{ $errors->first('city') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Postal Code') }} <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control"
                                        value="{{ isset($data['postal_code']) ? $data['postal_code'] : old('postal_code') }}"
                                        name="postal_code">
                                    @if ($errors->has('postal_code'))
                                        <span class="error">{{ $errors->first('postal_code') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="payment-form-action">
                            <a href="{{ rrt_route($controllerName . '/index') }}" class="btn-back btn btn-default">{{ __('Back') }}</a>
                            <input type="submit" value="{{ __('Next') }}" class="btn-back btn btn-primary">
                            {{-- @if (!empty($method))
                                <a href="#"
                                    onclick="submitFormAndRedirect('{{ rrt_route($controllerName . '/account', ['method' => $method]) }}')"
                                    class="btn-back btn btn-primary">{{ __('Next') }}</a>
                            @else
                                <input type="submit" value="{{ __('Next') }}" class="btn-back btn btn-primary">
                            @endif --}}
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
