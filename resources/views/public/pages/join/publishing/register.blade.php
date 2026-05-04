@extends('public.main')
@section('title', 'Register Publishing')
@section('body_class', 'page-register-selling')
@section('content')
    <style>
        .disabled {
            cursor: not-allowed;
            opacity: 0.3;
        }
    </style>
    <section class="section-selling-register rrt-section">
        <div class="container container-gap">
            <h1 class="title-main">Registerstartion for Publishing</h1>
            <form class="form-register-selling form-register"
                @if (count($user)) data-url="{{ rrt_route('public/join/publishing/postRegister') }}"
            @else
            data-url="{{ rrt_route('public/join/basic/postRegister') }}" @endif>
                @csrf
                <div class="form-section">
                    <h2 class="form-section-title">Account Information</h2>
                    <div class="row-2">
                        <div class="form-group">
                            <label for="">First Name (*)</label>
                            <input type="text" class="form-control"
                                @isset($user['first_name'])
                                value="{{ $user['first_name'] }}"
                                @endisset
                                name="first_name" data-require="1">
                        </div>
                        <div class="form-group">
                            <label for="">Last Name (*)</label>
                            <input type="text" class="form-control"
                                @isset($user['last_name'])
                            value="{{ $user['last_name'] }}"
                            @endisset
                                name="last_name" data-require="1">
                        </div>
                        <div class="form-group">
                            <label for="">Email (*)</label>
                            <input type="email"
                                @isset($user['email'])
                            value="{{ $user['email'] }}"
                            @endisset
                                class="form-control" name="email" data-require="1">
                        </div>
                        <div class="form-group">
                            <label for="">Phone number (*)</label>
                            <input type="text" class="form-control"
                                @isset($user['phone'])
                            value="{{ $user['phone'] }}"
                            @endisset
                                name="phone" data-require="1">
                        </div>
                        <div class="form-group">
                            <label for="">Password (*)</label>
                            <input type="password" class="form-control"
                                @isset($user['password'])
                            value="{{ $user['password'] }}"
                            @endisset
                                name="password" data-require="1">
                        </div>
                        <div class="form-group">
                            <label for="">Re-enter Password (*)</label>
                            <input type="password" class="form-control"
                                @isset($user['password'])
                            value="{{ $user['password'] }}"
                            @endisset
                                name="password_confirm" data-require="1">
                        </div>
                        <div class="form-group">
                            <label for="">Paypal</label>
                            <input type="text" class="form-control" name="paypal"
                                @isset($card_paypal_info['number'])
                            value="{{ $card_paypal_info['number'] }}"
                            @endisset>
                        </div>
                        <div class="form-group">
                            <label for="">Valid Identification</label>
                            <input type="text" class="form-control" name="identification">
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <h2 class="form-section-title">Bank Information</h2>
                    <div class="row-1">
                        <div class="form-group">
                            <label for="">Owner name</label>
                            <input type="text" class="form-control"
                                @isset($card_paypal_info['name_holder'])
                            value="{{ $card_paypal_info['name_holder'] }}"
                            @endisset
                                name="bank_owner">
                        </div>
                        <div class="form-group">
                            <label for="">Bank Number</label>
                            <input type="text" class="form-control" name="bank_number"
                                @isset($card_paypal_info['number'])
                            value="{{ $card_paypal_info['number'] }}"
                            @endisset>
                        </div>
                        <div class="form-group">
                            <label for="">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name"
                                @isset($card_paypal_info['bank_name'])
                            value="{{ $card_paypal_info['bank_name'] }}"
                            @endisset>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <h2 class="form-section-title">Other Information</h2>
                    <div class="row-3">
                        <div class="form-group">
                            <label for="">IPI or CAE #</label>
                            <input type="text" class="form-control" name="ipi_cae"
                                @isset($user['ipi_cae'])
                            value="{{ $user['ipi_cae'] }}"
                            @endisset>
                        </div>
                        <div class="form-group">
                            <label for="">Pro</label>

                            <select name="pro_organization" id="" class="form-control" style="color: #fff">
                                @foreach ($pro_organization as $item)
                                    <option style="color: #000" value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="">Tax Documents</label>
                            {{-- <input type="text" class="form-control" name="tax_documents"> --}}
                            <select name="tax" id="" class="form-control" style="color: #fff">
                                @foreach ($tax as $item)
                                    <option style="color: #000" value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <div class="form-group checkbox-group">
                        <label for="is_agree">Yes I have read the terms of agreement to become Seller on BeatNara</label>
                        <input type="checkbox" name="is_agree" value="1" id="is_agree" data-require="1">
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="page" value="subscription">
                        <input type="hidden" name="subscription_order[subscription_id]" value="{{ $id }}">
                        <button class="btn btn-primary w-100 btn-feature btn-submit disabled ">Register
                            Now</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <input type="hidden" @if (count($user)) value="1" @else value="0" @endif name="is_login">
@endsection
@push('srcipt')
    <script src="{{ asset('public/js') }}/register.js?ver={{ time() }}"></script>
    <script>
        $('#is_agree').click(function(e) {
            if ($(this).is(':checked')) {
                $('.btn-submit').removeClass("disabled");
            } else {
                $('.btn-submit').addClass("disabled");
            }
        });
    </script>
@endpush
