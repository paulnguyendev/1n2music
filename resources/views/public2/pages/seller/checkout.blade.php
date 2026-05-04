@extends('public2.main')
@section('body_class', 'authen-page')
@section('content')
    <div class="section-cart section-padding">
        <div class="container">
            <div class="row-checkout row-2">
                <div class="col-lg-6">
                    <div class="cart-title">
                        <i class="fa fa-user fa-lg"></i> {{__('Customer Information')}}
                    </div>
                    <div class="customer-info mt-30">
                        <div class="form-group">
                            <label for="email">{{__('Name')}}</label>
                            <input type="text" id="fullname" name="fullname" class="form-control" value="{{$user->fullname}}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">{{__('Phone')}}</label>
                            <input type="text" id="phone" name="phone" class="form-control" value="{{ $user->phone }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">{{__('Email Address')}}</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" readonly required>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="cart-title">
                        <i class="fa fa-shopping-cart fa-lg"></i> {{ __('Review Checkout') }}
                    </div>
                    <div class="cart-items">
                        <table class="table-cart-item">
                            <thead>
                                <tr>
                                    <th style="width:50%">{{ __('Subscription/Plan') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Cycle') }}</th>
                                    <th>{{ __('Price') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalPrice = 0;
                                @endphp
                                @foreach ($pendingPlans as $orderPlan)
                                    @php
                                        $cycle = $orderPlan->cycle ?? 'annually';
                                        $orderPlanPrice = ($cycle === 'monthly') ? $orderPlan->plan->pricing_monthly : $orderPlan->plan->pricing_annually;
                                        $totalPrice += $orderPlanPrice;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $orderPlan->plan->name ?? '' }}</strong>
                                        </td>
                                        <td class="text-center">
                                            {{ __(ucfirst($orderPlan->status??'')) }}
                                        </td>
                                        <td class="text-center">
                                            {{ __(ucfirst($cycle)) }}
                                        </td>
                                        <td class="text-center">
                                            {{ rrt_show_price($orderPlanPrice) }}
                                        </td>
                                    </tr>
                                    <input type="hidden" name="plan_ids[]" value="{{ $orderPlan->id ?? '' }}">
                                @endforeach
                                @foreach ($pendingOrders as $order)
                                    @php
                                        $cycle = $order->cycle ?? 'annually';
                                        $price = ($cycle === 'monthly') ? $order->subscription->price : $order->subscription->pricing_annually;
                                        $totalPrice += $price;
                                    @endphp
                                    <tr>
                                        <td>
                                            <p><strong>{{ $order->subscription->name ?? '' }}</strong></p>
                                            <div class="cart-item-desc">
                                                {{ $order->subscription->description ?? '' }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            {{ __(ucfirst($order->status??'')) }}
                                        </td>
                                        <td class="text-center">
                                            {{ __(ucfirst($cycle)) }}
                                        </td>
                                        <td class="text-center">
                                            {{ rrt_show_price($price) }}
                                        </td>
                                    </tr>
                                    <input type="hidden" name="subscription_ids[]" value="{{ $order->id ?? '' }}">
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">{{ __('Total') }}</td>
                                    <td class="text-center"><strong>{{ rrt_show_price($totalPrice) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="text-left" style="margin-bottom:0px;">
                <div class="checkout-title" style="margin-bottom:0px;">
                    <i class="fa fa-money fa-lg"></i> {{__('Payment Method')}}
                </div>
                <form id="checkout-form" action="{{ rrt_route($controllerName.'/postCheckout') }}" method="POST">
                    @csrf
                    @foreach ($pendingPlans as $orderPlan)
                    <input type="hidden" name="plan_ids[]" value="{{ $orderPlan->id ?? '' }}">
                    @endforeach
                    @foreach ($pendingOrders as $order)
                    <input type="hidden" name="subscription_ids[]" value="{{ $order->id ?? '' }}">
                    @endforeach

                    <div class="form-group">
                        <div class="checkbox-container">
                            <input style="display:none;" class="custom-checkbox" type="radio" name="payment_method" value="paypal" id="paypal" checked>
                            <label for="paypal" class="btn bg-light mt-15 custom-label">
                                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAxcHgiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAxMDEgMzIiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaW5ZTWluIG1lZXQiIHhtbG5zPSJodHRwOiYjeDJGOyYjeDJGO3d3dy53My5vcmcmI3gyRjsyMDAwJiN4MkY7c3ZnIj48cGF0aCBmaWxsPSIjMDAzMDg3IiBkPSJNIDEyLjIzNyAyLjggTCA0LjQzNyAyLjggQyAzLjkzNyAyLjggMy40MzcgMy4yIDMuMzM3IDMuNyBMIDAuMjM3IDIzLjcgQyAwLjEzNyAyNC4xIDAuNDM3IDI0LjQgMC44MzcgMjQuNCBMIDQuNTM3IDI0LjQgQyA1LjAzNyAyNC40IDUuNTM3IDI0IDUuNjM3IDIzLjUgTCA2LjQzNyAxOC4xIEMgNi41MzcgMTcuNiA2LjkzNyAxNy4yIDcuNTM3IDE3LjIgTCAxMC4wMzcgMTcuMiBDIDE1LjEzNyAxNy4yIDE4LjEzNyAxNC43IDE4LjkzNyA5LjggQyAxOS4yMzcgNy43IDE4LjkzNyA2IDE3LjkzNyA0LjggQyAxNi44MzcgMy41IDE0LjgzNyAyLjggMTIuMjM3IDIuOCBaIE0gMTMuMTM3IDEwLjEgQyAxMi43MzcgMTIuOSAxMC41MzcgMTIuOSA4LjUzNyAxMi45IEwgNy4zMzcgMTIuOSBMIDguMTM3IDcuNyBDIDguMTM3IDcuNCA4LjQzNyA3LjIgOC43MzcgNy4yIEwgOS4yMzcgNy4yIEMgMTAuNjM3IDcuMiAxMS45MzcgNy4yIDEyLjYzNyA4IEMgMTMuMTM3IDguNCAxMy4zMzcgOS4xIDEzLjEzNyAxMC4xIFoiPjwvcGF0aD48cGF0aCBmaWxsPSIjMDAzMDg3IiBkPSJNIDM1LjQzNyAxMCBMIDMxLjczNyAxMCBDIDMxLjQzNyAxMCAzMS4xMzcgMTAuMiAzMS4xMzcgMTAuNSBMIDMwLjkzNyAxMS41IEwgMzAuNjM3IDExLjEgQyAyOS44MzcgOS45IDI4LjAzNyA5LjUgMjYuMjM3IDkuNSBDIDIyLjEzNyA5LjUgMTguNjM3IDEyLjYgMTcuOTM3IDE3IEMgMTcuNTM3IDE5LjIgMTguMDM3IDIxLjMgMTkuMzM3IDIyLjcgQyAyMC40MzcgMjQgMjIuMTM3IDI0LjYgMjQuMDM3IDI0LjYgQyAyNy4zMzcgMjQuNiAyOS4yMzcgMjIuNSAyOS4yMzcgMjIuNSBMIDI5LjAzNyAyMy41IEMgMjguOTM3IDIzLjkgMjkuMjM3IDI0LjMgMjkuNjM3IDI0LjMgTCAzMy4wMzcgMjQuMyBDIDMzLjUzNyAyNC4zIDM0LjAzNyAyMy45IDM0LjEzNyAyMy40IEwgMzYuMTM3IDEwLjYgQyAzNi4yMzcgMTAuNCAzNS44MzcgMTAgMzUuNDM3IDEwIFogTSAzMC4zMzcgMTcuMiBDIDI5LjkzNyAxOS4zIDI4LjMzNyAyMC44IDI2LjEzNyAyMC44IEMgMjUuMDM3IDIwLjggMjQuMjM3IDIwLjUgMjMuNjM3IDE5LjggQyAyMy4wMzcgMTkuMSAyMi44MzcgMTguMiAyMy4wMzcgMTcuMiBDIDIzLjMzNyAxNS4xIDI1LjEzNyAxMy42IDI3LjIzNyAxMy42IEMgMjguMzM3IDEzLjYgMjkuMTM3IDE0IDI5LjczNyAxNC42IEMgMzAuMjM3IDE1LjMgMzAuNDM3IDE2LjIgMzAuMzM3IDE3LjIgWiI+PC9wYXRoPjxwYXRoIGZpbGw9IiMwMDMwODciIGQ9Ik0gNTUuMzM3IDEwIEwgNTEuNjM3IDEwIEMgNTEuMjM3IDEwIDUwLjkzNyAxMC4yIDUwLjczNyAxMC41IEwgNDUuNTM3IDE4LjEgTCA0My4zMzcgMTAuOCBDIDQzLjIzNyAxMC4zIDQyLjczNyAxMCA0Mi4zMzcgMTAgTCAzOC42MzcgMTAgQyAzOC4yMzcgMTAgMzcuODM3IDEwLjQgMzguMDM3IDEwLjkgTCA0Mi4xMzcgMjMgTCAzOC4yMzcgMjguNCBDIDM3LjkzNyAyOC44IDM4LjIzNyAyOS40IDM4LjczNyAyOS40IEwgNDIuNDM3IDI5LjQgQyA0Mi44MzcgMjkuNCA0My4xMzcgMjkuMiA0My4zMzcgMjguOSBMIDU1LjgzNyAxMC45IEMgNTYuMTM3IDEwLjYgNTUuODM3IDEwIDU1LjMzNyAxMCBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwOWNkZSIgZD0iTSA2Ny43MzcgMi44IEwgNTkuOTM3IDIuOCBDIDU5LjQzNyAyLjggNTguOTM3IDMuMiA1OC44MzcgMy43IEwgNTUuNzM3IDIzLjYgQyA1NS42MzcgMjQgNTUuOTM3IDI0LjMgNTYuMzM3IDI0LjMgTCA2MC4zMzcgMjQuMyBDIDYwLjczNyAyNC4zIDYxLjAzNyAyNCA2MS4wMzcgMjMuNyBMIDYxLjkzNyAxOCBDIDYyLjAzNyAxNy41IDYyLjQzNyAxNy4xIDYzLjAzNyAxNy4xIEwgNjUuNTM3IDE3LjEgQyA3MC42MzcgMTcuMSA3My42MzcgMTQuNiA3NC40MzcgOS43IEMgNzQuNzM3IDcuNiA3NC40MzcgNS45IDczLjQzNyA0LjcgQyA3Mi4yMzcgMy41IDcwLjMzNyAyLjggNjcuNzM3IDIuOCBaIE0gNjguNjM3IDEwLjEgQyA2OC4yMzcgMTIuOSA2Ni4wMzcgMTIuOSA2NC4wMzcgMTIuOSBMIDYyLjgzNyAxMi45IEwgNjMuNjM3IDcuNyBDIDYzLjYzNyA3LjQgNjMuOTM3IDcuMiA2NC4yMzcgNy4yIEwgNjQuNzM3IDcuMiBDIDY2LjEzNyA3LjIgNjcuNDM3IDcuMiA2OC4xMzcgOCBDIDY4LjYzNyA4LjQgNjguNzM3IDkuMSA2OC42MzcgMTAuMSBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwOWNkZSIgZD0iTSA5MC45MzcgMTAgTCA4Ny4yMzcgMTAgQyA4Ni45MzcgMTAgODYuNjM3IDEwLjIgODYuNjM3IDEwLjUgTCA4Ni40MzcgMTEuNSBMIDg2LjEzNyAxMS4xIEMgODUuMzM3IDkuOSA4My41MzcgOS41IDgxLjczNyA5LjUgQyA3Ny42MzcgOS41IDc0LjEzNyAxMi42IDczLjQzNyAxNyBDIDczLjAzNyAxOS4yIDczLjUzNyAyMS4zIDc0LjgzNyAyMi43IEMgNzUuOTM3IDI0IDc3LjYzNyAyNC42IDc5LjUzNyAyNC42IEMgODIuODM3IDI0LjYgODQuNzM3IDIyLjUgODQuNzM3IDIyLjUgTCA4NC41MzcgMjMuNSBDIDg0LjQzNyAyMy45IDg0LjczNyAyNC4zIDg1LjEzNyAyNC4zIEwgODguNTM3IDI0LjMgQyA4OS4wMzcgMjQuMyA4OS41MzcgMjMuOSA4OS42MzcgMjMuNCBMIDkxLjYzNyAxMC42IEMgOTEuNjM3IDEwLjQgOTEuMzM3IDEwIDkwLjkzNyAxMCBaIE0gODUuNzM3IDE3LjIgQyA4NS4zMzcgMTkuMyA4My43MzcgMjAuOCA4MS41MzcgMjAuOCBDIDgwLjQzNyAyMC44IDc5LjYzNyAyMC41IDc5LjAzNyAxOS44IEMgNzguNDM3IDE5LjEgNzguMjM3IDE4LjIgNzguNDM3IDE3LjIgQyA3OC43MzcgMTUuMSA4MC41MzcgMTMuNiA4Mi42MzcgMTMuNiBDIDgzLjczNyAxMy42IDg0LjUzNyAxNCA4NS4xMzcgMTQuNiBDIDg1LjczNyAxNS4zIDg1LjkzNyAxNi4yIDg1LjczNyAxNy4yIFoiPjwvcGF0aD48cGF0aCBmaWxsPSIjMDA5Y2RlIiBkPSJNIDk1LjMzNyAzLjMgTCA5Mi4xMzcgMjMuNiBDIDkyLjAzNyAyNCA5Mi4zMzcgMjQuMyA5Mi43MzcgMjQuMyBMIDk1LjkzNyAyNC4zIEMgOTYuNDM3IDI0LjMgOTYuOTM3IDIzLjkgOTcuMDM3IDIzLjQgTCAxMDAuMjM3IDMuNSBDIDEwMC4zMzcgMy4xIDEwMC4wMzcgMi44IDk5LjYzNyAyLjggTCA5Ni4wMzcgMi44IEMgOTUuNjM3IDIuOCA5NS40MzcgMyA5NS4zMzcgMy4zIFoiPjwvcGF0aD48L3N2Zz4" data-v-eea6b9fe="" alt="" role="presentation" class="paypal-logo paypal-logo-paypal paypal-logo-color-blue">
                            </label>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn text-center" style="background:#ffc439;">{{ __('Proceed to Payment') }}</button>
                    </div>
                    <input type="hidden" name="user_id" value="{{ $user_id }}">
                    <input type="hidden" name="total_price" value="{{ $totalPrice }}">
                </form>
            </div>
            <div class="text-center">
                <a href="{{ rrt_route('public/home/index') }}" class="btn mt-15" style="background:white;">{{ __('Back to Home') }}</a>
            </div>
        </div>
    </div>
    <style>
        .custom-label {
            display: inline-block;
            padding: 10px;
            background: #e3eeff;
            border: 2px solid transparent;
            cursor: pointer;
            transition: border 0.3s ease;
        }
        .custom-checkbox:checked + .custom-label , .custom-label:hover {
            border: 2px solid #003087;
        }
        .form-input-error {
            border-color: red;
        }
        @media (max-width: 767px) {
            .row-checkout {
                --f-columns: 1;
            }
        }
    </style>
@endsection
@push('srcipt')
    <script src="{{asset('public/style2/js/cart.js')}}?ver={{time()}}"></script>
    <script>
        const formCheckout = $("#checkout-form");
        formCheckout.submit(function(e) {
            e.preventDefault();

            let data = {
                user_id: $(`input[name='user_id']`).val(),
                subscription_ids: $(`input[name='subscription_ids[]']`).map(function(){ return $(this).val(); }).get(),
                plan_ids: $(`input[name='plan_ids[]']`).map(function(){ return $(this).val(); }).get(),
                payment_method: $(`input[name='payment_method']:checked`).val(),
                total_price: $(`input[name='total_price']`).val(),
                _token: $('input[name="_token"]').val(),
                fullname: $('#fullname').val(),
                phone: $('#phone').val(),
            };

            $('.form-input-error').removeClass('form-input-error')
            if( !data.fullname ){
                $('#fullname').addClass('form-input-error');
            }
            if( !data.phone ){
                $('#phone').addClass('form-input-error');
            }
            if (!data.user_id || !data.subscription_ids.length && !data.plan_ids.length || !data.payment_method || !data.total_price || !data.fullname || !data.phone) {
                return showNotify('error', 'Error', 'Please fill out all required fields.');
            }

            console.log(data)
            $.ajax({
                type: "POST",
                url: formCheckout.attr('action'),
                data: data,
                dataType: "json",
                beforeSend: function () {
                    showLoading();
                },
                success: function (response) {
                    if(response.status == 'success'){
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            showNotify('error', 'Error', 'Payment processing failed.');
                        }
                    }
                    else{
                        showNotify('error', 'Error', response.message ?? 'Payment processing failed.');
                    }
                },
                error: function (xhr) {
                    showNotify('error', 'Error', 'An error occurred during payment processing.');
                },
                complete: function () {
                    hideLoading();
                }
            });
        });
    </script>
@endpush
