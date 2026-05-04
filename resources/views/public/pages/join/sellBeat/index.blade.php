@extends('public.main')
@section('title', 'Start Selling')
@section('body_class', 'page-start-selling')
@section('content')
    <section class="section-pricing">
        <div class="container container-gap">
            <h2 class="title-main text-center">Choose your path to success</h2>
            <p class="text-center">When you get serious about getting paid for your art, it pays for itself.</p>
            <div class="pricing-switch text-center" data-active="pricing_annually">
                <span id="pricing_annually" class="pricing-switch-title" data-check="false">Pay Annually</span>
                <div class="pricing-switch-input">
                    <input type="checkbox" id="switch" class="switch-input" />
                    <label for="switch" class="switch"></label>
                </div>
                <span id="pricing_monthly" class="pricing-switch-title" data-check="true">Pay Monthly</span>
            </div>
            <div class="list-pricing">
                @if ($plans)
                    @foreach ($plans as $item)
                        @php
                            $isFree = $item['is_free'] ?? 0;
                            $pricingMonthly = $item['pricing_monthly'] ?? 0;
                            $pricingMonthlyShow = rrt_show_price($pricingMonthly, "$", '/mo');
                            $pricingAnnually = $item['pricing_annually'] ?? 0;
                            $pricingAnnuallyShow = rrt_show_price($pricingAnnually, "$", '/mo');
                            $totalPricingAnnually = $pricingAnnually * 12;
                            $totalPricingAnnuallyShow = rrt_show_price($totalPricingAnnually, "$", '/mo');
                            $content = $item['content'] ?? '';
                        @endphp
                        <div class="pricing-item {{ $isFree == '1' ? 'is_free' : '' }}">
                            <div class="pricing-top">
                                <h3 class="pricing-item-title">{{ $item['name'] ?? '' }}</h3>
                                <div class="pricing-item-desc">
                                    {{ $item['description'] ?? '' }}
                                </div>
                                <div class="pricing-item-price">
                                    <p class="pricing-sale-regular" data-pricing_monthly="{{ $pricingMonthlyShow }}"
                                        data-pricing_annually="{{ $pricingAnnuallyShow }}">{{ $pricingAnnuallyShow }}</p>
                                    <p class="pricing-price-regular">{{ $totalPricingAnnuallyShow }}</p>
                                </div>
                                <div class="pricing-item-cta">
                                    <a href="{{ rrt_route($controllerName . '/register', ['plan' => $item['slug'] ?? '']) }}"
                                        data-url-monthly="{{ rrt_route($controllerName . '/register', ['plan' => $item['slug'] ?? '', 'cycle' => 'monthly']) }}"
                                        data-url-annually="{{ rrt_route($controllerName . '/register', ['plan' => $item['slug'] ?? '', 'cycle' => 'annually']) }}"
                                        class="btn btn-primary w-100">Get Started</a>
                                </div>
                            </div>
                            <div class="pricing-bottom">
                                <div class="pricing-item-feature">
                                    <strong>All Free features plus:</strong>
                                    {!! $content !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
@endsection
@push('srcipt')
    <script>
        let pricingSwitch = $(".pricing-switch");
        let active = pricingSwitch.data('active');
        $(`#${active}`).addClass('active');
        const pricingSwitchTitle = $(".pricing-switch-title");
        const switchInput = $(".switch-input");
        let type;
        switchInput.change(function() {
            if ($(this).is(":checked")) {
                type = 'monthly';
                $(this).attr('data-id', 'pricing_monthly');
                $("#pricing_monthly").addClass('active');
                $("#pricing_annually").removeClass('active');
            } else {
                type = 'annually';
                $(this).attr('data-id', 'pricing_annually');
                $("#pricing_monthly").removeClass('active');
                $("#pricing_annually").addClass('active');
            }
            let id = $(this).attr('data-id');
            changePrice(id);
        })
        const changePrice = (id) => {
            const price = $(".pricing-sale-regular");
            price.each((key, ele) => {
                let priceItem = $(ele).data(id);
                let nextItem = $(ele).next();
                let parent = $(ele).parent();
                let cta = parent.next().find('a');
                let annualUrl = cta.data('url-annually');
                let monthlyUrl = cta.data('url-monthly');
                let url;
                console.log(cta);
                $(ele).html(priceItem);
                if (id == 'pricing_monthly') {
                    url = monthlyUrl;
                    nextItem.hide()
                } else {
                    url = annualUrl;
                    nextItem.show();
                }
                cta.attr('href', url);
                console.log(annualUrl);
            });
        }
        pricingSwitchTitle.click(function() {
            $(".pricing-switch-title").removeClass('active');
            $(this).addClass('active');
            let id = $(this).attr('id');
            if (id == 'pricing_monthly') {
                switchInput.prop('checked', true);
            } else {
                switchInput.prop('checked', false);
            }
            changePrice(id);
        })
    </script>
@endpush
