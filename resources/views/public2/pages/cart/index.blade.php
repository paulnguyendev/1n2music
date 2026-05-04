@extends('public2.main')
@section('title', 'Cart')
@section('content')
    <style>
        .return-policy-section {
            margin: 20px 0;
        }

        .return-policy-accordion {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        .return-policy-header {
            background-color: #f8f9fa;
            cursor: pointer;
            padding: 15px;
            position: relative;
        }

        .return-policy-header h5 {
            margin: 0;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .return-policy-toggle-icon {
            transition: transform 0.3s;
        }

        .return-policy-toggle-icon.collapsed {
            transform: rotate(-90deg);
        }

        .return-policy-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background-color: #fff;
        }

        .return-policy-content.active {
            max-height: 300px;
            overflow-y: auto;
        }

        .return-policy-body {
            padding: 15px;
        }

        .return-policy-body>*:is(h2, h3) {
            color: #333;
        }

        .return-policy-body>*:not(:last-child) {
            margin-bottom: 15px;
        }

        .return-policy-body>h2 {
            color: #000;
        }
    </style>
    <form id="form-cart" data-url="{{ rrt_route($controllerName . '/postOrder') }}">
        <div class="section-cart section-padding">
            <div class="container">
                @if (Cart::count() > 0)
                    <div class="cart-title">
                        <i class="fa fa-shopping-cart fa-lg"></i> {{ __('Please check the product you wish to order.') }}
                    </div>
                    <div class="cart-items">
                        <table class="table-cart-item">
                            <thead>
                                <tr>
                                    <th style="width:5%">{{ __('Thumbnail') }}</th>
                                    <th style="width:50%">{{ __('Track Info') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Sub Total') }}</th>
                                </tr>
                            <tbody>
                                @foreach ($carts as $rowID => $cart)
                                    @php
                                        $options = $cart->options ?? [];
                                        $trackThumb = $options->thumbnail ?? '';
                                        $cartSubTotal = $cart->price * $cart->qty ?? 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <img src="{{ $trackThumb }}" alt="">
                                        </td>
                                        <td>
                                            <p>
                                                <strong>
                                                    <a
                                                        href="{{ rrt_route('public/track/detail', ['code' => $options->code ?? '', 'slug' => \Str::slug($cart->name)]) }}">{{ $cart->name ?? '' }}</a>
                                                    <button type="button" class="btn-remove-cart"
                                                        data-id="{{ $rowID }}"
                                                        data-url="{{ rrt_route($controllerName . '/remove', ['id' => $rowID]) }}"><i
                                                            class="far fa-times-circle"></i></button>
                                                </strong>
                                            </p>
                                            <div class="cart-item-desc">
                                                {{ __('Licensing') }}: {{ $options->contract_name ?? '' }} -
                                                {{ $options->contract_deliverables }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            {{ $cart->qty ?? 1 }}
                                        </td>
                                        <td class="text-center">
                                            {{ rrt_show_price($cart->price ?? 0) }}
                                        </td>
                                        <td class="text-center">
                                            {{ rrt_show_price($cartSubTotal) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">{{ __('Sub Total') }}</td>
                                    <td class="text-center">
                                        @php
                                            $subtotal = 0;
                                            foreach ($carts as $cart) {
                                                $subtotal += $cart->price * $cart->qty;
                                            }
                                        @endphp
                                        {{ rrt_show_price($subtotal) }}
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td colspan="4">{{ __('Tax') }}</td>
                                    <td class="text-center">{{ rrt_show_price(Cart::tax()) }}</td>
                                </tr> --}}
                                <tr>
                                    <td colspan="4">{{ __('Order Total') }}</td>
                                    <td class="text-center"><strong
                                            class="track-meta-price">{{ rrt_show_price($subtotal) }}</strong></td>
                                </tr>
                            </tfoot>
                            </thead>
                        </table>
                    </div>

                    <!-- Add hidden field for actual numeric total -->
                    <input type="hidden" name="raw_total" value="{{ $subtotal }}">

                    <!-- Return Policy Section -->
                    <div class="return-policy-section">
                        <div class="return-policy-accordion">
                            <div class="return-policy-header" id="returnPolicyToggle">
                                <h5>
                                    <span>Return Policy</span>
                                    <i class="fas fa-chevron-down return-policy-toggle-icon"></i>
                                </h5>
                            </div>
                            <div class="return-policy-content">
                                <div class="return-policy-body">
                                    @if (isset($policyPage) && $policyPage)
                                        {!! $policyPage->content !!}
                                    @else
                                        <p>Return policy information is not available at this time.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Return Policy Section -->

                    <div class="cart-information">
                        <h3>{{ __('Person paying') }}</h3>
                        <div class="cart-information-inner">
                            <div class="form-group">
                                <label for="">{{ __('Fullname') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('Type your fullname') }}"
                                    name="fullname" value="{{ rrt_get_fullname() }}">
                            </div>
                            <div class="row-phone-group">
                                <div class="form-group">
                                    <label for="">{{ __('Country Code') }}</label>
                                    @php
                                        $countryCode = rrt_get_user_login('country_code');

                                    @endphp
                                    <select name="country_code" id="" class="form-control">
                                        @if ($countries->isNotEmpty())
                                            @foreach ($countries as $country)
                                                @php
                                                    $countryPhoneCode = $country['phone_code'] ?? null;
                                                    $countrySelected =
                                                        $countryPhoneCode == $countryCode ? 'selected' : '';
                                                @endphp
                                                <option {{ $countrySelected }}
                                                    value="{{ $country['phone_code'] ?? '84' }}">
                                                    (+{{ $country['phone_code'] ?? '84' }})
                                                    {{ $country['name'] ?? 'Unknown' }}</option>
                                            @endforeach
                                        @endif

                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">{{ __('Phone') }}</label>
                                    <input type="text" class="form-control" placeholder="{{ __('Type your phone') }}"
                                        name="phone" value="{{ rrt_get_user_login('phone') }}">
                                </div>

                            </div>

                            <div class="form-group">
                                <label for="">{{ __('Email') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('Type your email') }}"
                                    name="email" value="{{ rrt_get_user_login('email') }}">
                            </div>
                        </div>
                    </div>

                    <div class="payment-info">
                        <h3>{{ __('Payment Information') }}</h3>
                        <p>{{ __('Total order amount') }}: <strong>{{ Cart::total() }}</strong></p>
                        <div class="form-group">
                            <label for="">{{ __('Payment Method') }}</label>
                            @if ($payments)
                                <div class="payment-list">
                                    @foreach ($payments as $payment)
                                        @php
                                            $paymentID = $payment['id'] ?? '';
                                            $paymentInputID = "payment_id_{$paymentID}";
                                            $paymentIsDefault = $payment['is_default'] ?? 0;
                                            $logoPath = '';
                                            $logoText = '';
                                            if ($paymentID == 1) {
                                                $logoText = __('Bank Transfer');
                                                $logoPath = rrt_show_upload_url('bank-transfer-logo.png', 'logo');
                                            } elseif ($paymentID == 5) {
                                                $logoPath = rrt_show_upload_url('paypal-logo.png', 'logo');
                                            }
                                        @endphp
                                        <div class="form-group checkbox-group payment-checkbox"
                                            data-url="{{ rrt_route($controllerName . '/paymentAccount') }}">
                                            <input type="checkbox" name="payment_id" id="{{ $paymentInputID }}"
                                                value="{{ $paymentID }}" {{ $paymentIsDefault == 1 ? 'checked' : '' }}>
                                            <label for="{{ $paymentInputID }}">
                                                @if ($logoText)
                                                    <span class="payment-text"> {{ $logoText }}</span>
                                                @else
                                                    <img src="{{ $logoPath }}" alt="{{ __($payment['name'] ?? '') }}"
                                                        class="payment-logo">
                                                @endif

                                                {{--                                                <span class="d-none" style="display:none">{{ __($payment['name'] ?? '') }}</span> --}}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="payment-account"></div>
                            @endif
                        </div>
                    </div>

                    <div class="payment-actions text-center">
                        <button class="btn btn-feature btn-gradient"><span>{{ __('Order Now') }}</span></button>
                    </div>
                @else
                    <div class="cart-title">
                        <i class="fa fa-shopping-cart fa-lg"></i> {{ __('No data in cart') }}
                    </div>
                @endif
            </div>
        </div>
    </form>

@endsection
@push('srcipt')
    <script>
        // Return policy accordion animation
        document.getElementById('returnPolicyToggle').addEventListener('click', function() {
            this.querySelector('.return-policy-toggle-icon').classList.toggle('collapsed');
            var content = this.nextElementSibling;
            if (content.classList.contains('active')) {
                content.classList.remove('active');
            } else {
                content.classList.add('active');
            }
        });

        var msg = "{{ session('payment-success') }}";
        if (msg.trim() !== "") {
            toastr.success(msg);
        }
        const checkboxPaymentID = $(`input[name=payment_id]`);
        checkboxPaymentID.on('change', function() {
            let value = $(this).val();
            let url = $(this).parent().data('url');
            if ($(this).is(":checked")) {
                checkboxPaymentID.not($(this)).prop('checked', false);

                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        payment_id: value
                    },
                    dataType: "json",
                    success: function(response) {
                        let xhtml = response.xhtml ? response.xhtml : '';
                        if (xhtml) {
                            $(".payment-account").css('margin-top', '15px');

                        }
                        $(".payment-account").html(xhtml);
                    }
                });
            } else {}

        });
        checkboxPaymentID.trigger('change');
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
                    console.log(response);

                    window.location.href = redirect;
                },
                complete: function() {
                    hideLoading();
                },
            });
        })
    </script>
@endpush
