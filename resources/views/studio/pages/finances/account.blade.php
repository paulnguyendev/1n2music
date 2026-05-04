@extends('studio.main')

@section('title', __('Payment Account Information'))
@section('page_title', __('Payment Account Information'))
<style>
    .payment-method-item {
        cursor: pointer;
    }

    .unactive {
        background: #000;
        opacity: 0.4;
        border: 2px solid #000;
        color: #fff;
    }

    .unactive .icon-check {
        display: none;
    }

    .unactive i {
        color: #fff
    }

    .error {
        color: red
    }
</style>
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card_title">{{__('Bank Information')}}</h4>
                    <div class="payment-form-inner">
                        <div class="alert alert-info col-center payment-general-info" role="alert">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="payment-general-text">
                                    <h4>{{__('General Information')}}</h4>
                                    <p>{{ $data['first_name'] ?? '-' }} &nbsp; {{ $data['last_name'] ?? '-' }}
                                        ({{ $data['email'] ?? '-' }})</p>
                                    <p> {{__('Address')}}: {{ $data['address_1'] ?? '-' }}, {{ $data['country'] ?? '-' }},
                                        {{ $data['postal_code'] ?? '-' }},
                                        {{ $data['city'] ?? '-' }}
                                    </p>
                                </div>
                                <div class="payment-general-action">
                                    {{--
                                    <a href="{{ rrt_route($controllerName . '/form', ['step' => 'general']) }}"
                                        class="btn btn-default">Edit</a> --}}
                                    <a href="{{ rrt_route($controllerName . '/form', ['step' => 'general', 'method' => $method,'can_edit' => 1]) }}"
                                        class="btn btn-default">{{__('Edit')}}</a>

                                </div>
                            </div>
                        </div>
                        <div class="row row-payment-method">
                            <div class="col-md-12 col-payment-method">
                                <h4>{{__('Select Payout Method')}}</h4>
                                <div class="payment-methods col-center">
                                    <a href = "{{ rrt_route($controllerName . '/account', ['method' => 'paypal']) }}"
                                        class="payment-method-item {{ $method == 'paypal' ? 'active' : '' }}"
                                        data-method="paypal">
                                        <i class="fa fa-paypal"></i>
                                        <p>{{__('Paypal Transfer')}}</p>
                                        <div class="icon-check">
                                        </div>
                                    </a>
                                    <a href = "{{ rrt_route($controllerName . '/account', ['method' => 'bank']) }}"
                                        class="payment-method-item {{ $method == 'bank' ? 'active' : '' }}"
                                        data-method="bank">
                                        <i class="fa fa-bank"></i>
                                        <p>{{__('Bank Transfer')}}</p>
                                        <div class="icon-check">
                                        </div>
                                    </a>
                                </div>
                                @include($pathViewController . "/method/{$method}", [
                                    'paymentMethodInfo' => $paymentMethodInfo,
                                    'paymentMethod' => $paymentMethod,
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        // const paymentMethodItem = $(".payment-method-item");
        // paymentMethodItem.click(function() {
        //     $(".payment-method-item").not($(this)).removeClass('active');
        //     $(this).addClass('active');
        // })
        // $('.payment-method-item').click(function() {
        //     const selector = $(this);
        //     let id = $(this).data('id');
        //     let url = $(this).data('url')
        //     let selected = $(this).data('selected');
        //     let method = $(this).data('method')
        //     $.ajax({
        //         type: "POST",
        //         url: url,
        //         data: {
        //             id: id,
        //             selected: selected,
        //             method: method
        //         },
        //         dataType: "json",
        //         success: function(response) {
        //             if (response.status_code == 200) {
        //                 let id = response.result.id;
        //                 $('div[data-method="' + method + '"]').data('id', id);
        //                 if (method == 'paypal') {
        //                     $('div[data-method="paypal"]').addClass('active').removeClass('unactive');
        //                     $('div[data-method="bank"]').addClass('unactive').removeClass('active');
        //                     $('#form-paypal').css('display', 'block')
        //                     $('#form-bank').css('display', 'none')
        //                 } else if (method == 'bank') {
        //                     $('div[data-method="bank"]').addClass('active').removeClass('unactive');
        //                     $('div[data-method="paypal"]').addClass('unactive').removeClass('active');
        //                     $('#form-bank').css('display', 'block')
        //                     $('#form-paypal').css('display', 'none')
        //                 }
        //             } else {
        //                 showNotify("error", "Error", 'System Error')
        //             };
        //         }
        //     });
        // })
    </script>
@endpush
