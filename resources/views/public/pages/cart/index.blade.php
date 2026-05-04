@extends('public.main')
@section('title', 'Cart')
@section('content')
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
                                            foreach($carts as $cart) {
                                                $subtotal += $cart->price * $cart->qty;
                                            }
                                        @endphp
                                        {{ rrt_show_price($subtotal) }}
                                    </td>
                                </tr>
                                {{-- <tr>
                                <td colspan="4">Tax</td>
                                <td class="text-center">{{ rrt_show_price(Cart::tax()) }}</td>
                            </tr> --}}
                                <tr>
                                    <td colspan="4">{{ __('Order Total') }}</td>
                                    <td class="text-center">
                                        <strong class="track-meta-price">{{ rrt_show_price($subtotal) }}</strong>
                                    </td>
                                </tr>
                            </tfoot>
                            </thead>
                        </table>
                    </div>

                    <div class="cart-information">
                        <h3>{{ __('Person paying') }}</h3>
                        <div class="cart-information-inner">
                            <div class="form-group">
                                <label for="">{{ __('Fullname') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('Type your fullname') }}" name="fullname"
                                    value="{{ rrt_get_fullname() }}">
                            </div>
                            <div class="form-group">
                                
                                <label for="">{{ __('Phone') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('Type your phone') }}" name="phone"
                                    value="{{ rrt_get_user_login('phone') }}">
                            </div>
                            <div class="form-group">
                                <label for="">{{ __('Email') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('Type your email') }}" name="email"
                                    value="{{ rrt_get_user_login('email') }}">
                            </div>
                        </div>
                    </div>

                    <div class="payment-info bg-dark">
                        <h3>{{ __('Payment Information') }}</h3>
                        <p>{{ __('Total order amount') }} : <strong>{{ rrt_show_price(Cart::total()) }}</strong></p>
                        <div class="form-group">
                            <label for="">{{ __('Payment Method') }}</label>
                            @if ($payments)
                                <div class="payment-list">
                                    @foreach ($payments as $payment)
                                        @php
                                            $paymentID = $payment['id'] ?? '';
                                            $paymentInputID = "payment_id_{$paymentID}";
                                            $paymentIsDefault = $payment['is_default'] ?? 0;
                                        @endphp
                                        <div class="form-group checkbox-group"
                                            data-url="{{ rrt_route($controllerName . '/paymentAccount') }}">
                                            <input type="checkbox" name="payment_id" id="{{ $paymentInputID }}"
                                                value="{{ $paymentID }}" {{ $paymentIsDefault == 1 ? 'checked' : '' }}>
                                            <label for="{{ $paymentInputID }}">
                                                {{ $payment['name'] ?? '' }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="payment-account"></div>
                            @endif
                        </div>
                    </div>
                    <div class="payment-actions text-center">
                        <button class="btn btn-feature">{{ __('Order Now') }}</button>
                    </div>
                @else
                    <div class="cart-title">
                        <i class="fa fa-shopping-cart fa-lg"></i>{{ __('No data in cart') }}
                    </div>
                @endif
            </div>
        </div>
    </form>

@endsection
@push('srcipt')
    <script>
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
                        if(xhtml) {
                            $(".payment-account").css('margin-top','15px');
                          
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
