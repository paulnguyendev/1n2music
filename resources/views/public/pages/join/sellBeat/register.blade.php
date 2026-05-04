@extends('public.main')
@section('title', 'Register Selling')
@section('body_class', 'page-register-selling')
@section('content')
    <section class="section-selling-register rrt-section">
        <div class="container container-gap">
            <h1 class="title-main">Registerstartion for Sellers</h1>
            <form class="form-register-selling form-register" data-url="{{rrt_route('public/join/basic/postRegister')}}">
                <div class="form-section">
                    <h2 class="form-section-title">Account Information</h2>
                    <div class="row-2">
                        <div class="form-group">
                            <label for="">First Name (*)</label>
                            <input type="text" class="form-control" name="first_name" data-require="1">
                        </div>
                        <div class="form-group">
                            <label for="">Last Name (*)</label>
                            <input type="text" class="form-control" name="last_name" data-require="1">
                        </div>
                        <div class="form-group">
                            <label for="">Email (*)</label>
                            <input type="email" class="form-control" name="email" data-require="1">
                        </div>
                        <div class="form-group">
                            <label for="">Phone number (*)</label>
                            <input type="text" class="form-control" name="phone" data-require="1">
                        </div>
                        <div class="form-group">
                            <label for="">Password (*)</label>
                            <input type="password" class="form-control" name="password" data-require="1">
                        </div>
                        <div class="form-group">
                            <label for="">Re-enter Password (*)</label>
                            <input type="password" class="form-control" name="password_confirm" data-require="1">
                        </div>
                        <div class="form-group">
                            <label for="">Paypal</label>
                            <input type="text" class="form-control" name="paypal">
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
                            <input type="text" class="form-control" name="bank_owner">
                        </div>
                        <div class="form-group">
                            <label for="">Bank Number</label>
                            <input type="text" class="form-control" name="bank_number">
                        </div>
                        <div class="form-group">
                            <label for="">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name">
                        </div>
                        <div class="form-group checkbox-group">
                            <label for="is_agree">Yes I have read the terms of agreement to become Seller on BeatNara</label>
                            <input type="checkbox" name="is_agree" value="1" id="is_agree" data-require="1" >
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="page" value="sellBeats">
                            <input type="hidden" name="plan_order[plan_id]" value="{{$id}}">
                            <input type="hidden" name="plan_order[cycle]" value="{{$cycle}}">
                            <button class="btn btn-primary w-100 btn-feature btn-submit" disabled>Register Now</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </section>
@endsection
@push('srcipt')
    <script src="{{ asset('public/js') }}/register.js?ver={{ time() }}"></script>
@endpush
