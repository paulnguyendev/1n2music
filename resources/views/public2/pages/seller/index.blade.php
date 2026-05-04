@extends('public2.main')
@section('body_class', 'join-page page-publishing')
@push('css')
    <style>
        /* General Section Styling */
        .section-pricing {
            padding: 50px 0;
            background-color: #f8f8f8;
        }

        /* Ensure that body allows scrolling */
        html, body {
            height: 100%;
            margin: 0;
            overflow-x: hidden; /* Prevent horizontal overflow */
        }

        /* Container Styling */
        .container-gap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Title */
        .title-main {
            font-size: 36px;
            margin-bottom: 20px;
        }

        /* Center Text */
        .text-center {
            text-align: center;
        }

        /* Pricing Switch */
        .pricing-switch {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
        }

        .pricing-switch-title {
            margin: 0 20px;
            font-size: 18px;
            cursor: pointer;
        }

        /* Hide switch input */
        .switch-input {
            display: none;
        }

        /* Pricing List */
        .list-pricing {
            display: flex;
            flex-wrap: nowrap; /* Prevents cards from wrapping to a new row */
            justify-content: center; /* Ensures cards are spaced evenly */
            gap: 20px;
            overflow-x: auto; /* Enable horizontal scrolling if screen is too small */
        }

        /* Pricing Card */
        .pricing-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 24%; /* Four cards per row with space between */
            margin: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            padding: 20px;
            min-height: 500px; /* Uniform height for larger screens */
            justify-content: space-between;
        }

        /* If the item is free, give it a green border */
        .pricing-item.is_free {
            border-color: #28a745;
        }

        /* Top Section with Title and Description */
        .pricing-top {
            text-align: center;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        /* Title */
        .pricing-item-title {
            font-size: 24px;
            margin-bottom: 10px;
        }

        /* Description */
        .pricing-item-desc {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
            min-height: 50px;
        }

        /* Price */
        .pricing-item-price {
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
        }

        /* Call to Action Button */
        .pricing-item-cta {
            margin-top: auto; /* Ensures the button is pushed to the bottom */
        }

        .pricing-item-cta .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .pricing-item-cta .btn:hover {
            background-color: #0056b3;
        }

        /* Bottom Section with Features */
        .pricing-bottom {
            padding: 20px;
            border-top: 1px solid #ddd;
            background-color: #f1f1f1;
            min-height: 260px; /* Ensure a minimum height for consistent layout */
        }

        /* Features */
        .pricing-item-feature {
            font-size: 14px;
            color: #333;
        }

        /* Price Styling */
        .pricing-sale-regular {
            font-size: 23px;
            font-weight: bold;
        }
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 10px; /* Spacing between checkbox items */
            justify-content: center;
        }

        /* Styling for the checkbox input */
        .form-check-input {
            width: 20px!important; /* Customize checkbox size */
            height: 20px!important;
            margin-right: 10px; /* Space between checkbox and label */
            accent-color: #007bff; /* Customize checkbox color */
            cursor: pointer!important; /* Change cursor to pointer for better UX */
            position: inherit!important;
            opacity: 1!important;
        }

        /* Styling for the checkbox label */
        .form-check-label {
            font-size: 16px; /* Customize font size */
            color: #333; /* Customize text color */
            cursor: pointer; /* Clicking on the label should toggle the checkbox */
            margin: 0!important;
        }
        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .pricing-item {
                width: 32%; /* Three cards per row on medium-sized screens */
            }
        }

        @media (max-width: 992px) {
            .pricing-item {
                width: 48%; /* Two cards per row on smaller screens */
            }
        }

        @media (max-width: 768px) {
            .pricing-item {
                width: 100%; /* One card per row on mobile screens */
            }

            .list-pricing {
                flex-wrap: wrap; /* Allow wrapping on small screens */
            }
        }
        .authen-page{
            overflow: auto!important;
        }
    </style>
@endpush
@section('content')
    <section class="section-pricing">
        <div class="container container-gap">
            <h2 class="title-main text-center">{{ __('Choose your path to success') }}</h2>
            <p class="text-center">{{ __('When you get serious about getting paid for your art, it pays for itself.') }}</p>
            <div class="pricing-switch text-center" data-active="pricing_annually">
                <span id="pricing_annually" class="pricing-switch-title active" data-check="false">{{ __('Pay Annually') }}</span>
                <div class="pricing-switch-input">
                    <input type="checkbox" id="switch" class="switch-input" />
                    <label for="switch" class="switch"></label>
                </div>
                <span id="pricing_monthly" class="pricing-switch-title" data-check="true">{{ __('Pay Monthly') }}</span>
            </div>
            <div class="list-pricing">
                @if ($plans)
                    @foreach ($plans as $key => $item)
                        @php
                            $isFree = $item['is_free'] ?? 0;
                            $slug = $item['slug'] ?? '';
                            $price = $item['price'] ?? 0;
                            $priceShow = ($price > 0) ? rrt_show_price($price, "", '/mo') : __('Free');
                            $pricingMonthly = $item['pricing_monthly'] ?? null;
                            $pricingMonthlyShow = ($pricingMonthly > 0) ?  rrt_show_price($pricingMonthly, "", '/mo') : __('Free');
                            $pricingAnnually = $item['pricing_annually'] ?? null;
                            $pricingAnnuallyShow = ($pricingAnnually > 0) ? rrt_show_price($pricingAnnually, "", '/yr') : __('Free');
                            $content = $item['content'] ?? '';
                        @endphp
                        <div class="pricing-item {{ $isFree == '1' ? 'is_free' : '' }}" 
                            id="pricing-item_{{$slug}}" 
                            {{  in_array( $slug,['basic'] ) ? "style=display:none" : "" }}
                            >
                            <div class="pricing-top">
                                <h3 class="pricing-item-title">{{ $item['name'] ?? '' }}</h3>
                                <div class="pricing-item-desc">
                                    {{ $item['description'] ?? '' }}
                                </div>
                                @if ($slug === 'publishing')
                                    <div class="coming-soon" style="text-align: center; font-size: 18px; color: #FF0000;">
                                        {{ __('Coming Soon') }}
                                    </div>
                                @else
                                    <div class="form-check">
                                        <input class="form-check-input plan-selection" type="checkbox" value="{{ $item['slug'] }}"
                                            data-cycle="annually" id="plan-{{ $item['slug'] }}">
                                        <label class="form-check-label" for="plan-{{ $item['slug'] }}">
                                            {{ __('Select this plan') }}
                                        </label>
                                    </div>
                                    <div class="pricing-item-price">
                                        @if ($slug === 'pro')
                                            {{-- Only display one price at a time, default is annually --}}
                                            <p class="pricing-sale-regular"
                                            data-pricing_monthly="{{ $pricingMonthlyShow }}"
                                            data-pricing_annually="{{ $pricingAnnuallyShow }}">
                                                {{ $pricingAnnuallyShow }}
                                            </p>
                                        @else
                                            <p class="pricing-sale-regular" 
                                            data-pricing_monthly="{{ $priceShow }}"
                                            data-pricing_annually="{{ $pricingAnnuallyShow }}">
                                                {{ $pricingAnnuallyShow }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="pricing-bottom">
                                <div class="pricing-item-feature">
                                    <strong>{{ __('All Free features plus') }}:</strong>
                                    {!! $content !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="color:red">{{__('No package available')}}</p>
                @endif
            </div>
            <div class="text-center">
                <button class="btn btn-primary" id="submitPlans">{{ __('Continue') }}</button>
            </div>
        </div>
    </section>
@endsection

@push('srcipt')
    <script>
        let pricingSwitch = $(".pricing-switch");
        let active = pricingSwitch.data('active');
        let currentCycle = 'annually'; // Default cycle is annually
        $('#' + active).addClass('active');
        const pricingSwitchTitle = $(".pricing-switch-title");
        const switchInput = $(".switch-input");
        const submitButton = $('#submitPlans');
        function toggleSubmitButton() {
            let selectedPlansCount = $('.plan-selection:checked').length;
            if (selectedPlansCount > 0) {
                submitButton.prop('disabled', false);
            } else {
                submitButton.prop('disabled', true);
            }
        }
        $(document).ready(function() {
            toggleSubmitButton();
        });

        switchInput.change(function() {
            currentCycle = $(this).is(":checked") ? 'monthly' : 'annually';
            let id = currentCycle === 'monthly' ? 'pricing_monthly' : 'pricing_annually';
            changePrice(id);
        });

        const changePrice = (id) => {
            const price = $(".pricing-sale-regular");
            if (id == 'pricing_monthly') {
                $('#pricing-item_basic').show();
                $('#pricing-item_pro').show();
                $('#pricing-item_distribution').hide();
                $('#pricing-item_publishing').hide();
            }else if(id == 'pricing_annually'){
                $('#pricing-item_basic').hide();
                $('#pricing-item_pro').show();
                $('#pricing-item_distribution').show();
                $('#pricing-item_publishing').show();
            }
            price.each((key, ele) => {
                let priceItem = $(ele).data(id);
                let nextItem = $(ele).next();
                let parent = $(ele).parent();
                let cta = parent.next().find('a');
                let annualUrl = cta.data('url-annually');
                let monthlyUrl = cta.data('url-monthly');
                let url;

                if (priceItem) {
                    $(ele).html(priceItem);
                    url = id === 'pricing_monthly' ? monthlyUrl : annualUrl;
                    if (id === 'pricing_monthly') {
                        nextItem.hide();
                    } else {
                        nextItem.show();
                    }
                    cta.attr('href', url);
                }
            });
        };
        changePrice("pricing_annually");
        pricingSwitchTitle.click(function() {
            $(".pricing-switch-title").removeClass('active');
            $(this).addClass('active');
            currentCycle = $(this).attr('id') === 'pricing_monthly' ? 'monthly' : 'annually';
            let id = currentCycle === 'monthly' ? 'pricing_monthly' : 'pricing_annually';
            changePrice(id);
        });
        const basicSellerSlug = 'basic';
        const proSellerSlug = 'pro';

        // Handle checkbox selection
        $('.plan-selection').change(function () {
            const selectedPlan = $(this).val();
            if (selectedPlan === basicSellerSlug && $(this).is(':checked')) {
                disablePlan(proSellerSlug, true);
            }
            else if (selectedPlan === basicSellerSlug && !$(this).is(':checked')) {
                disablePlan(proSellerSlug, false);
            }
            if (selectedPlan === proSellerSlug && $(this).is(':checked')) {
                disablePlan(basicSellerSlug, true);
            }
            else if (selectedPlan === proSellerSlug && !$(this).is(':checked')) {
                disablePlan(basicSellerSlug, false);
            }
            toggleSubmitButton();
        });
        function disablePlan(planSlug, disable) {
            const planCheckbox = $(`#plan-${planSlug}`);
            if (disable) {
                planCheckbox.prop('disabled', true);
                planCheckbox.closest('.pricing-item').css('opacity', '0.5');
            } else {
                planCheckbox.prop('disabled', false); // Enable the checkbox
                planCheckbox.closest('.pricing-item').css('opacity', '1');
            }
        }

        $('#submitPlans').click(function () {
            let selectedPlans = [];
            $('.plan-selection:checked').each(function() {
                let planSlug = $(this).val();

                let cycle = currentCycle;

                selectedPlans.push({
                    'plan': planSlug,
                    'cycle': cycle
                });
            });
            let data = {
                plans: selectedPlans,
                user_id: '{{ $user_id }}',
                _token: '{{ csrf_token() }}'
            };
            console.log(data)
            $.ajax({
                url: '{{ rrt_route($controllerName . '/postSelling') }}',
                type: 'POST',
                data: data,
                beforeSend: function() {
                    showLoading();
                },
                success: function(response) {
                    if (response.status === 400) {
                        const msg = response?.msg;
                        const firstKey = Object.keys(msg)[0];
                        const firstMsg = msg[firstKey];
                        toastr.error(firstMsg, 'Error');
                    } else {

                        const redirect = response?.redirect;
                        console.log(redirect)
                        if (redirect) {
                            window.location.href = redirect;
                        }
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                },complete: function() {
                    hideLoading();
                }
            });
        });
    </script>
@endpush
