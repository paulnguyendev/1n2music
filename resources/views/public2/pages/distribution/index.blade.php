@extends('public2.main')
@section('body_class', 'join-page page-distribution')
@section('content')
    <section class="section-subcription">
        <div class="container">
            <div class="row-3">
                <div class="subcription-item">
                    <div class="subcription-item-head">
                        <h2 class="subcription-title">{!! $item['name'] ?? '' !!}</h2>
                        <p class="subcription-price">${{ $item['price'] ?? 0 }}</p>
                        <p class="subcription-duration">year</p>
                    </div>
                    <div class="subcription-item-content">
                        <div class="subcription-item-desc">
                            {!! $item['content'] ?? '' !!}
                        </div>
                        <div class="subcription-item-bottom">
                           
                            <div class="checkbox-container">
                                <input type="checkbox" name="is_agree" value="1" id="agree">
                            
                                <label for="agree">    <span class="custom-checkbox"></span> {{__("I've read and accept the")}} <a href="">{{__('Distribution Services Agreement')}}</a></label>
                            </div>
                            <button class="btn-gradient" id="btn-submit-distribution" disabled data-url="{{rrt_route('public/join/payment/checkout',['subscription_id' => 2])}}"><span>{{__('Subscribe')}}</span></button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
@push('srcipt')
    <script src="{{asset('public/style2/js/cart.js')}}?ver={{time()}}"></script>
    <script>
        var msg = "{{ session('payment-success') }}";
        if(msg.trim() !== ""){
            toastr.success(msg);
        }
        const btnSubmitDistribution = $('#btn-submit-distribution');
        btnSubmitDistribution.click(function(){
            const url = $(this).data('url');
            window.location.href = url
        });
    </script>
@endpush
