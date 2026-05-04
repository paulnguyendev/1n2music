<form id="form-account-payment" action="{{ $route }}" method="post">
    @csrf
    <div class="form-group">
        <label for="">{{__('Company name')}}</label>
        <input type="text" class="form-control" name="company_name"
            @isset($user_payment->company_name)
              value="{{ $user_payment->company_name }}"  
            @endisset
            placeholder="{{__('Company name')}}">
    </div>
    <div class="row mb-0">
        <div class="col-md-6">
            <div class="form-group">
                <label for="">{{__('First name')}}</label>
                <input type="text" name="first_name"
                    @isset($user_payment->first_name)
              value="{{ $user_payment->first_name }}"  
            @endisset
                    class="form-control" placeholder="{{__('First name')}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="">{{__('Last name')}}</label>
                <input type="text" name="last_name"
                    @isset($user_payment->last_name)
          value="{{ $user_payment->last_name }}"  
        @endisset
                    class="form-control" placeholder="{{__('Last name')}}">
            </div>
        </div>
    </div>
    <div class="row mb-0">
        <div class="col-md-6">
            <div class="form-group">
                <label for="">{{__('Phone')}}</label>
                <input type="text" name="phone"
                    @isset($user_payment->phone)
          value="{{ $user_payment->phone }}"  
        @endisset
                    class="form-control" placeholder="{{__('Phone')}}">
            </div>
        </div>
    </div>
    <p class="title-profile">{{__('LOCATION')}}</p>
    <div class="form-group">
        <label for="">Street Address</label>
        <input @isset($user_payment->address_1)
        value="{{ $user_payment->address_1 }}"  
      @endisset
            name="address_1" type="text" class="form-control" placeholder="{{__('Street Address')}}">
    </div>
    <div class="form-group">
        <label for="">{{__('Unit/Apartment')}}</label>
        <input type="text"
            @isset($user_payment->unit)
        value="{{ $user_payment->unit }}"  
      @endisset
            name="unit" class="form-control" placeholder="{{__('Unit/Apartment')}}">
    </div>
    <div class="row mb-0">
        <div class="col-md-6">
            <div class="form-group">
                <label for=""> {{__('City or Town')}} </label>
                <input type="text "
                    @isset($user_payment->city)
                value="{{ $user_payment->city }}"  
              @endisset
                    name="city" class="form-control" placeholder="{{__('City or Town')}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="province"> {{__('State or Province')}} </label>
                <input type="text"
                    @isset($user_payment->province)
                value="{{ $user_payment->province }}"  
              @endisset
                    name="province" id="province" class="form-control" placeholder=" {{__('State or Province')}} ">
            </div>
        </div>
    </div>
    <div class="row mb-0">
        <div class="col-md-6">
            <div class="form-group">
                <label for="">{{__('Country or Territory')}}</label>
                <select name="country" class="form-control">
                    @include("{$pathViewController}.elements.select_country")
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for=""> {{__('Postal Code')}}</label>
                <input type="text"
                    @isset($user_payment->postal_code)
                value="{{ $user_payment->postal_code }}"  
              @endisset
                    name="postal_code" class="form-control" placeholder=" {{__('Postal Code')}}">
            </div>
        </div>
    </div>
    <div class="custom-control custom-checkbox primary-checkbox">
        <input type="checkbox" checked="" class="custom-control-input" id="account_confirm">
        <label class="custom-control-label" for="account_confirm">{{__('My billing & shipping information is the same')}}</label>
    </div>

</form>
