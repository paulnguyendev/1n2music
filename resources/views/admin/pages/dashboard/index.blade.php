@php
    use App\Helpers\Template;
    use App\Helpers\Transactions;
@endphp
@extends('admin.main')
@section('page_title', __('dashboard.dashboard'))
@section('title', __('dashboard.dashboard'))
@section('buttons')
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-3 col-md-6 col-lg-12 stretched_card">
            <div class="card mb-mob-4 icon_card primary_card_bg">
                <!-- Card body -->
                <div class="card-body">
                    <p class="card-title mb-0 text-white">{{ __('dashboard.request_payout') }}</p>
                    <div
                        class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                        <h3 class="mb-0 text-white">{{ $data_request_payout['count'] ?? 0 }}</h3>
                        {{-- <div class="arrow_icon"><i class="ion-arrow-up-c text-primary"></i></div> --}}
                    </div>
                    <p class="mb-0 text-white">{{ Template::showPercent($data_request_payout['growth_percentage'] ?? 0) }} <span
                            class="text-white ml-1"><small>({{__('Since last week')}})</small></span></p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-lg-12 stretched_card">
            <div class="card mb-mob-4 icon_card success_card_bg">
                <!-- Card body -->
                <div class="card-body">
                    <p class="card-title mb-0 text-white">{{ __('dashboard.sign_up') }}</p>
                    <div
                        class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                        <h3 class="mb-0 text-white">{{ $data_user['count'] ?? 0 }}</h3>
                        {{-- <div class="arrow_icon"><i class="ion-arrow-down-c text-success"></i></div> --}}
                    </div>
                    <p class="mb-0 text-white">{{ Template::showPercent($data_user['growth_percentage'] ?? 0) }} <span
                            class="text-white ml-1"><small>({{__('Since last week')}})</small></span></p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-lg-12 stretched_card">
            <div class="card mb-mob-4 icon_card warning_card_bg">
                <!-- Card body -->
                <div class="card-body">
                    <p class="card-title mb-0 text-white">{{ __('dashboard.news_track') }}</p>
                    <div
                        class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                        <h3 class="mb-0 text-white">{{ $data_track['count'] ?? 0 }}</h3>
                        {{-- <div class="arrow_icon"><i class="ion-arrow-up-c text-warning"></i></div> --}}
                    </div>
                    <p class="mb-0 text-white">{{ Template::showPercent($data_track['growth_percentage'] ?? 0) }} <span
                            class="text-white ml-1"><small>({{__('Since last week')}})</small></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-lg-12 stretched_card">
            <div class="card mb-mob-4 icon_card info_card_bg">
                <!-- Card body -->
                <div class="card-body">
                    <p class="card-title mb-0 text-white">{{ __('dashboard.order') }}</p>
                    <div
                        class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                        <h3 class="mb-0 text-white">{{ $data_order['count'] ?? 0 }}</h3>
                        {{-- <div class="arrow_icon"><i class="ion-arrow-up-c text-info"></i></div> --}}
                    </div>
                    <p class="mb-0 text-white">{{ Template::showPercent($data_order['growth_percentage'] ?? 0) }}<span
                            class="text-white ml-1"><small>({{__('Since last week')}})</small></span></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card_title">{{ __('dashboard.order_week_ago') }}</h4>
                    <div class="chart_container">
                        <canvas id="bar_chart" data-url="{{ rrt_route($controllerName . '/ajaxGetStatusDayofWeek') }}"
                            width="685" height="437" style="display: block; height: 350px; width: 548px;"
                            class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body" style="height: 454px">
                    <h4 class="card_title">{{ __('dashboard.withdrwal_request') }}</h4>
                    <div class="list-group" style="border: none">
                        @if (isset($request_payouts) && count($request_payouts) > 0)
                        @foreach ($request_payouts as $key => $request_payout)
                        <a href="#"
                            class="list-group-item list-group-item-action flex-column align-items-start {{ $key == 0 ?: 'mt-3' }}">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ rrt_get_fullname_by_user($request_payout->users) }}</h5>
                                <small>{{ Carbon\Carbon::parse($request_payout->created_at)->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{__('Total Amount')}}:
                                {{ Transactions::getTotalAmountPayment($request_payout->amount_request, $request_payout->toArray()) }}.
                            </p>

                        </a>
                    @endforeach
                        @endif
                    


                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body" style="height: 454px">
                    <h4 class="card_title">{{ __('dashboard.album&single') }}</h4>
                    <div class="list-group" style="border: none">
                        @if(isset($music_distribution) && count($music_distribution) > 0)
                            @foreach ($music_distribution as $key => $distribution)
                                <a href="javascript:;"
                                    class="list-group-item list-group-item-action flex-column align-items-start {{ $key == 0 ?: 'mt-3' }}">
                                    <div class="d-flex justify-content-center w-100">
                                        <div class="col-md-4">
                                            <h5 class="mb-1">{{ rrt_get_fullname_by_user($distribution->user) }}</h5>
                                            <p class="mb-1">{{ __('Total Track:') }} {{ $distribution->totalTrack ?? 0 }}</p>
                                        </div>
                                        <div class="col-md-4 text-center mt-4">#{{ $distribution->code ?? 0 }}</div>
                                        <div class="col-md-4">
                                            <!-- <small>{{ Carbon\Carbon::parse($distribution->created_at)->diffForHumans() }}</small> -->
                                            <select name="type" class="form-control changType" style="height:50px;" data-url="{{ rrt_route('admin/dashboard/saveType',['code' => $distribution->code])}}">
                                                <option value="">New</option>
                                                <option value="approved">Approved</option>
                                                <option value="denied">Denied</option>
                                            </select>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                        {{-- $music_distribution->links() --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        jQuery(document).ready(function() {


            if ($('#bar_chart').length) {
                let currentDate = new Date();
                let i = 0;
                let time;
                let date = [];
                while (i <= 6) {
                    let currentDateCopy = new Date(currentDate); // Tạo một bản sao của currentDate
                    currentDateCopy.setDate(currentDateCopy.getDate() - i);
                    let options = {
                        month: '2-digit',
                        day: '2-digit',
                        // weekday: 'long'
                    }
                    let time = new Intl.DateTimeFormat('en-US', options).format(currentDateCopy);

                    date.push(time)
                    i++;
                }
                let data
                let url = $('#bar_chart').data('url')
                $.ajax({
                    type: "Get",
                    url: url,
                    dataType: "json",
                    success: function(response) {
                        let total_success = response.total_success;
                        let total_suppend = response.total_suppend;
                        var ctx = document.getElementById("bar_chart").getContext('2d');
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: date.reverse(),
                                datasets: [{
                                    label: 'Success',
                                    data: total_success,
                                    backgroundColor: "#2671FF",
                                    borderColor: "#2671FF",
                                    borderWidth: 2
                                }, {
                                    label: 'Suppend',
                                    data: total_suppend,
                                    backgroundColor: "#eff3f9",
                                    borderColor: "#eff3f9",
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                legend: {
                                    display: false,
                                    position: 'bottom',

                                    labels: {
                                        fontColor: '#71748d',
                                        fontSize: 14,
                                    }
                                },

                                scales: {
                                    xAxes: [{
                                        ticks: {
                                            fontSize: 14,
                                            fontColor: '#71748d',
                                        }
                                    }],
                                    yAxes: [{
                                        ticks: {
                                            fontSize: 14,
                                            fontColor: '#71748d',
                                        }
                                    }]
                                }
                            }


                        });
                    }
                });




            }
            $('.changType').on('change', function() {
                var selectedType = $(this).val();
                var url = $(this).data('url');
                
                if (selectedType === '') return;

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        type: selectedType,
                        _token: '{{ csrf_token() }}' // Token bảo mật của Laravel
                    },
                    success: function(response) {
                        if (response.status) {
                            successNotice('Notification', response.msg);
                            window.location.reload();
                        }else{
                            errorNotice('Notification', response.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Xử lý khi có lỗi
                        console.error(error);
                    }
                });
            });


        });
        /*======== End Doucument Ready Function =========*/
    </script>
@endpush
