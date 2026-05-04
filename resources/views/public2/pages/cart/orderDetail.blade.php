@extends('public2.main')
@section('title', 'Cart')
@push('css')
    <style>
        .paypal-button-container {
            text-align: center;
            margin-top: 20px;
        }

        .paypal-button {
            background-color: #FFC439;
            color: #111820;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .paypal-button:hover {
            background-color: #FFA626;
        }

        .paypal-logo {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }
        .payment-badge {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            z-index: 1000;
            margin: 0 auto;
            text-align: center;
            transition: opacity 0.5s ease, transform 0.5s ease;
            opacity: 1;
            transform: translateY(-20px);
            max-width: 320px;
        }

        .payment-badge.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
@endpush
@section('content')
    <div class="section-cart section-padding">
        <div class="container">
            <div class="cart-title">
                <i class="fa fa-shopping-cart fa-lg"></i> #{{ $code }} - {{ __('Order Information') }}
            </div>
            <div class="cart-items">
                <table class="table-cart-item">
                    <thead>
                        <tr>
                            <th style="width:50%">{{ __('Track Info') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Sub Total') }}</th>
                        </tr>
                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $tracks = $order->tracks ?? [];
                                $contractTrack = $order->contract_track ?? [];
                                $cartSubTotal = $order->price ?? 0;
                            @endphp
                            <tr>
                                <td>
                                    <p>
                                        <strong>{{ $tracks->name ?? '' }}</strong>
                                    </p>
                                    <div class="cart-item-desc">
                                        {{ __('Licensing') }}: {{ $contractTrack->contractSetting->contract->name ?? '' }} - 
                                        {{ $contractTrack->contractSetting->deliverables ?? '' }}
                                    </div>
                                </td>
                                <td class="text-center">{{ $cart->qty ?? 1 }}</td>
                                <td class="text-center">{{ rrt_show_price($order->price ?? 0) }}</td>
                                <td class="text-center">{{ rrt_show_price($cartSubTotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        {{-- <tr>
                            <td colspan="4">{{ __('Sub Total') }}</td>
                            <td class="text-center">{{ rrt_show_price(Cart::subtotal()) }}</td>
                        </tr> --}}
                        {{-- <tr>
                            <td colspan="4">{{ __('Tax') }}</td>
                            <td class="text-center">{{ rrt_show_price(Cart::tax()) }}</td>
                        </tr> --}}
                        <tr>
                            <td colspan="3">{{ __('Order Total') }}</td>
                            <td class="text-center"><strong class="track-meta-price">{{ rrt_show_price($item->total ?? 0) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <p>{{ __('Payment method') }}: {{ $paymentInfo['name'] ?? '' }}</p>
                                @if ($paymentAccount)
                                <p>{{ __('Bank name') }}: {{ $paymentAccount->name  ?? "-" }}</p>
                                <p>{{ $paymentAccount->description  ?? "-" }}</p>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                    </thead>
                </table>
            </div>
            @if($item->status !== 'deliver')
                @if($item->payment_id == 5)
                    <div class="text-center">
                        <form action="{{ rrt_route('public/cart/paymentPaypal') }}" id="paypal-payment-form">
                            <input type="hidden" name="code" value="{{ $item->code ?? '' }}">
                            <input type="hidden" name="total" value="{{ $item->total ?? '' }}">
                            <div class="paypal-button-container">
                                <button class="paypal-button" id="paypal-button">
                                    <img src="https://www.paypalobjects.com/webstatic/icon/pp258.png" alt="PayPal" class="paypal-logo">
                                    {{ __('Pay with PayPal') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @else
                <div id="payment-success-badge" class="payment-badge">
                    <span class="badge-text">{{ __('Payment Successful!') }}</span>
                </div>
            @endif
            <div class="text-center">
                <a href="{{ rrt_route('public/home/index') }}" class="btn btn-feature">{{ __('Back to Home') }}</a>
            </div>
        </div>
    </div>

@endsection
@push('srcipt')
    <script>
        const checkboxPaymentID = $(`input[name=payment_id]`);
        checkboxPaymentID.change(function() {
            let value = $(this).val();
            if ($(this).is(":checked")) {
                checkboxPaymentID.not($(this)).prop('checked', false);
            } else {}

        })
        const btnRemoveCart = $(".btn-remove-cart");
        btnRemoveCart.click(function() {
            let id = $(this).data('id');
            let url = $(this).data('url');
            $.ajax({
                type: "get",
                url: url,
                data: {
                    id: id
                },
                dataType: "html",
                success: function(response) {
                    location.reload();
                }
            });
        })
        const formCart = $("#form-cart");
        formCart.submit(function(e) {
            e.preventDefault();
            let data = getFormData(formCart);
            let url = $(this).data('url');
            let paymentID = data.payment_id ? data.payment_id : "";
            if (!data.fullname) {
                return showNotify('error', 'Error', 'Please Enter Your Name')
            }
            if (!data.phone) {
                return showNotify('error', 'Error', 'Please Enter Your Phone')
            }
            if (!paymentID) {
                return showNotify('error', 'Error', 'Please Choose Payment Method')
            }
            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: "json",
                beforeSend: function() {
                    showLoading();
                },
                success: function(response) {
                    let redirect = response.redirect ? response.redirect : '';
                    window.location.href = redirect;
                },
                complete: function() {
                    hideLoading();
                },
            });

        })
        document.getElementById('paypal-button').addEventListener('click', function(e) {
            e.preventDefault()
            const form = document.getElementById('paypal-payment-form');
            const formData = new FormData(form);
            const actionUrl = form.getAttribute('action');

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    showLoading();
                },
                success: function(response) {
                    if(response.status === 'success') {
                        window.location.href = response.redirect;
                    } else {
                            const msg = response?.message;
                            toastr.error(msg, 'Error');
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while processing the payment.');
                },
                complete: function() {
                    hideLoading();
                },
            });
        });
    </script>
@endpush
