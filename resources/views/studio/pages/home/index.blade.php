@php
    use App\Helpers\Subscription;
@endphp
@extends('studio.main')
@section('content')
    @push('css')
        <style>
            .item-card-dashboard .list-group-item:not(:first-child) {
                margin-top: 10px
            }

            .card-no-item .card-inner {
                height: auto;
            }

            .card-inner i {
                font-size: 50px !important
            }

            .item-card-dashboard .badge {
                font-size: 12px;
                line-height: 1;
            }

            .list-group {
                border: none;
            }
            .dashboard-slide-item img{
                width: 100%;
                border-radius: 15px;
                height: 355px;
                object-fit: cover;
            }
        </style>
    @endpush
    <h1 class="name-user"> {{ rrt_get_time_of_day() }}, {{ rrt_get_fullname() }}</h1>
    @if($banners)
        <div class="slide-container desktop-slides">
            <div class="dashboard-slide">
                @foreach($banners as $banner)
                    <div class="dashboard-slide-item">
                        <img src="{{url('')}}/public/uploads/banner/{{$banner['image']??""}}" alt="">
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    @if($tabletBanners)
        <div class="slide-container tablet-slides">
            <div class="dashboard-slide">
            @foreach($tabletBanners as $tabletBanner)
                <div class="dashboard-slide-item">
                    <img src="{{url('')}}/public/uploads/banner/{{$tabletBanner['image']??""}}" alt="">
                </div>
            @endforeach
        </div>
        </div>
    @endif
    @if($mobileBanners)
        <div class="slide-container mobile-slides">
            <div class="dashboard-slide">
            @foreach($mobileBanners as $mobileBanner)
                <div class="dashboard-slide-item">
                    <img src="{{url('')}}/public/uploads/banner/{{$mobileBanner['image']??""}}" alt="">
                </div>
            @endforeach
        </div>
        </div>
    @endif
    <div class="row row-custom-gap mt-5">
        <div class="col-md-6 ">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card_title"> {{__('Quick Stats')}} </h4>
                    <div class="d-flex justify-content-between stats-item mb-2">
                        <span>{{__('Plays')}}</span>
                        <strong>{{ count($tracks) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between stats-item mb-2">
                        <span>{{__('Comments')}}</span>
                        <strong>{{ $count_cmt }}</strong>
                    </div>
                    <div class="d-flex justify-content-between stats-item mb-2">
                        <span>{{__('Free downloads')}}</span>
                        <strong>{{ $count_track_free }}</strong>
                    </div>
                    <div class="d-flex justify-content-between stats-item mb-2">
                        <span>{{__('New sales')}}</span>
                        <strong>0</strong>
                    </div>
                    <div class="d-flex justify-content-between stats-item mb-2">
                        <span>{{__('New followers')}}</span>
                        <strong>{{ $count_follow }}</strong>
                    </div>
                    <div class="latest-sale">
                        <h2 class="fs-24 mb-0">{{__('Latest Sales')}}</h2>
                        <div class="table-responsive-custom">
                            <table class="table-s1">
                                <thead>
                                    <tr>
                                        <th class="text-center">{{ __('Order Number') }}</th>
                                        <th class="text-center">{{ __('Total Track') }}</th>
                                        <th class="text-center">{{ __('Total') }}</th>
                                        <th class="text-center">{{ __('Time') }}</th>
                                        <th class="text-center">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($orderItems) > 0)
                                        <div class="list-group item-card-dashboard">
                                            @foreach ($orderItems as $orderItem)
                                                @php
                                                    $order = $orderItem['order'] ?? [];
                                                    $contractTrack = $orderItem['contract_track'] ?? [];
                                                @endphp
                                                <tr>
                                                    <td class="text-center"><a
                                                            href="{{ rrt_route('public/studio/sale/detail', ['order_id' => $orderItem['order_id']]) }}">#{{ $orderItem['code'] ?? '' }}</a>
                                                    </td>
                                                    <td class="text-center">{{ $orderItem['count'] ?? 0 }}</td>
                                                    <td class="text-center"> {{ $orderItem['total'] ?? 0 }} </td>
                                                    <td class="text-center">
                                                        {{ getTimeDiffHuman($orderItem['created_at'] ?? '') }}
                                                    </td>
                                                    <td class="text-center text-success"> {!! $orderItem['status'] !!} </td>
                                                </tr>
                                            @endforeach
                                        </div>
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">{{__('You haven’t made any sales, yet.')}}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card_title">{{ __('Continue right where you left') }}</h4>
                    <div class="continue-buttons">
                        @if (Subscription::checkUserType() == 'seller' || Subscription::checkUserType() == 'all')
                            <a href="{{ rrt_route('public/studio/content/index') }}">{{ __('Create Track') }}</a>
                            <a href="{{ rrt_route('public/studio/content/index', ['type' => 'soundKit']) }}">{{ __('Create Sound Kit') }}</a>
                        @endif
                        @if (Subscription::checkUserType() == 'subcriber' || Subscription::checkUserType() == 'all')
                            <a href="{{ rrt_route('public/studio/release/index', ['type' => 'album']) }}">{{ __('Create Album') }}</a>
                            <a href="{{ rrt_route('public/studio/release/index', ['type' => 'single']) }}">{{ __('Create Single') }}</a>
                        @endif
                    </div>
                    <div class="latest-sale mt-4">
                        <div class="table-responsive-custom">
                            <table class="table-s1">
                                <tbody>
                                    @if (count($track_drafts) > 0)
                                        <div class="list-group item-card-dashboard">
                                            @foreach ($track_drafts as $track_draft)
                                                @php
                                                    $user = $track_draft->user()->first() ?? null;
                                                    $userName = rrt_get_fullname_by_user($user);
                                                @endphp
                                                <tr>
                                                    <td style="width: 10%" class="text-center">
                                                        <img src="{{ asset('public/style2/img/icon_play.svg') }}" alt="">
                                                    </td>
                                                    <td style="width: 40%" class="text-center"><strong>
                                                        <span class="limit-text-1">{{ $track_draft->name ?? '' }}</span>
                                                    </strong></td>
                                                    <td style="width: 20%" class="text-center">
                                                        <span class="limit-text-1">{{ $userName ?? '-' }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ getTimeDiffHuman($track_draft['created_at'] ?? '') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </div>
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">{{ __('No drafts yet') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="latest-sale mt-5">
                        <h4 class="card_title mb-0">{{ __('Recent comments') }}</h4>
                        <div class="table-responsive-custom ">
                            <table class="table-s1">
                                <tbody>
                                    @if (count($comments) > 0)
                                        <div class="list-group item-card-dashboard">
                                            @foreach ($comments as $comment)
                                                @php
                                                    $user = $comment->user()->first() ?? null;
                                                    $userName = rrt_get_fullname_by_user($user);
                                                @endphp
                                                <tr>
                                                    <td style="width: 10%" class="text-center">
                                                        <img src="{{ asset('public/style2/img/icon_play.svg') }}" alt="">
                                                    </td>
                                                    <td style="width: 40%" class="text-center"><strong>
                                                        {{ $comment->tracks->name ?? '-' }}
                                                    </strong></td>
                                                    <td style="width: 20%" class="text-center">
                                                        <span class="limit-text-1">{{ $comment->content ?? '-' }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ getTimeDiffHuman($comment['created_at'] ?? '') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </div>
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">{{ __('No drafts yet') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $userRole = Subscription::checkUserRole();
        @endphp
        @if(in_array('distribution',$userRole))
        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card_title">{{__('Most played Stream')}}</h4>
                    <div class="chart_container">
                        <canvas id="bar_chart"></canvas>
                    </div>
                </div>

            </div>
        </div>
        @endif
        @if(in_array('publishing',$userRole))
            <div class="col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="card_title">{{__('Single & Album Stream Plays')}}</h4>
                        <div class="chart_container">
                            <canvas id="single_album_barchart"></canvas>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-12 stretched_card mt-mob-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{__('Offline Statistics')}}</h4>
                        <div class="chart_container">
                            <canvas id="line_chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('public/css') }}/slick.css?ver={{ time() }}">
@endpush
@push('script')
    <script src="{{ asset('public/js') }}/slick.js?ver={{ time() }}"></script>
    <script>
        renderChart()
        function renderChart() {
            if ($('#bar_chart').length) {
                $.ajax({
                    url: '{{ rrt_route("public/studio/home/topStreamChart") }}',
                    method: 'GET',
                    success: function(response) {
                        const topStreamData = response.topStreamData;
                        const labels = Object.keys(topStreamData);
                        const colorList = [
                            "#2671FF", "#3C3ACC", "#FF5733", "#33FF57", "#FFC300", "#C70039", "#900C3F",
                            "#581845", "#8E44AD", "#3498DB", "#1ABC9C", "#2ECC71", "#F39C12", "#D35400",
                            "#E74C3C", "#EC7063", "#48C9B0", "#A569BD", "#F4D03F", "#2E86C1"
                        ];

                        const datasets = [];
                        let colorIndex = 0;

                        labels.forEach(month => {
                            const platforms = topStreamData[month];
                            for (const platform in platforms) {
                                const { music_distribution, stream_count, type } = platforms[platform];
                                let dataset = datasets.find(ds => ds.label === `${platform} (#${music_distribution}) - ${type}`);
                                if (!dataset) {
                                    dataset = {
                                        label: `${platform} (#${music_distribution}) - ${type}`,
                                        data: Array(labels.length).fill(0),
                                        backgroundColor: colorList[colorIndex % colorList.length],
                                        borderColor: colorList[colorIndex % colorList.length],
                                        borderWidth: 1,
                                        platform: platform,
                                        musicDistribution: music_distribution,
                                    };
                                    datasets.push(dataset);
                                    colorIndex++;
                                }
                                const monthIndex = labels.indexOf(month);
                                dataset.data[monthIndex] = parseInt(stream_count, 10);
                            }
                        });

                        const ctx = document.getElementById("bar_chart").getContext('2d');
                        if (window.barChart) {
                            window.barChart.destroy();
                        }

                        if (labels.length === 0 || datasets.every(ds => ds.data.every(value => value === 0))) {
                            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                            ctx.font = "20px Arial";
                            ctx.textAlign = "center";
                            ctx.fillStyle = "#71748d";
                            ctx.fillText("No data found", ctx.canvas.width / 2, ctx.canvas.height / 2);
                            return;
                        }

                        window.barChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: datasets
                            },
                            options: {
                                maintainAspectRatio: false,
                                scales: {
                                    xAxes: [{
                                        stacked: true,
                                        ticks: {
                                            fontSize: 14,
                                            fontColor: '#71748d',
                                        }
                                    }],
                                    yAxes: [{
                                        stacked: true,
                                        ticks: {
                                            fontSize: 14,
                                            fontColor: '#71748d',
                                            beginAtZero: true
                                        }
                                    }]
                                },
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        fontColor: '#71748d',
                                        fontSize: 14,
                                    }
                                },
                                tooltips: {
                                    callbacks: {
                                        label: function(tooltipItem, data) {
                                            const dataset = data.datasets[tooltipItem.datasetIndex];
                                            return `${dataset.platform} - ${dataset.musicDistribution} (${dataset.type}): ${tooltipItem.yLabel}`;
                                        }
                                    }
                                }
                            }
                        });
                    },
                    error: function() {
                        var ctx = document.getElementById("bar_chart").getContext('2d');
                        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                        ctx.font = "20px Arial";
                        ctx.textAlign = "center";
                        ctx.fillStyle = "#71748d";
                        ctx.fillText("Failed to load data", ctx.canvas.width / 2, ctx.canvas.height / 2);
                    }
                });
            }
        }

        function renderStreamChart() {
            if ($('#single_album_barchart').length) {
                $.ajax({
                    url: '{{ rrt_route("public/studio/home/streamCountChart") }}',
                    method: 'GET',
                    success: function(response) {
                        const { monthlyData, platformNames } = response;
                        const months = Array.from(new Set(Object.values(monthlyData).flatMap(platform => Object.keys(platform.single).concat(Object.keys(platform.album)))));
                        const colorList = [
                            "#FF5733", "#FF8C66",
                            "#2671FF", "#66A3FF",
                            "#FFC300", "#FFD966",
                            "#8E44AD", "#A569BD",
                            "#3498DB", "#5DADE2",
                            "#1ABC9C", "#48C9B0",
                            "#2ECC71", "#58D68D",
                            "#F39C12", "#F5B041",
                            "#D35400", "#E67E22",
                            "#E74C3C", "#EC7063",
                            "#C70039", "#D98880",
                            "#900C3F", "#AF7AC5"
                        ];

                        let colorIndex = 0;

                        function getNextColor() {
                            const color = colorList[colorIndex % colorList.length];
                            colorIndex++;
                            return color;
                        }

                        const datasets = platformNames.flatMap((platform) => {
                            const singleColor = getNextColor();
                            const albumColor = getNextColor();

                            return [
                                {
                                    label: `${platform} - Single`,
                                    data: months.map(month => monthlyData[platform].single[month] || 0),
                                    backgroundColor: singleColor,
                                    borderColor: singleColor,
                                    borderWidth: 1
                                },
                                {
                                    label: `${platform} - Album`,
                                    data: months.map(month => monthlyData[platform].album[month] || 0),
                                    backgroundColor: albumColor,
                                    borderColor: albumColor,
                                    borderWidth: 1
                                }
                            ];
                        });

                        const ctx = document.getElementById("single_album_barchart").getContext('2d');
                        if (window.streamChart) {
                            window.streamChart.destroy();
                        }

                        window.streamChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: months,
                                datasets: datasets
                            },
                            options: {
                                maintainAspectRatio: false,
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
                                            beginAtZero: true
                                        }
                                    }]
                                },
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        fontColor: '#71748d',
                                        fontSize: 14,
                                    }
                                },
                                tooltips: {
                                    callbacks: {
                                        label: function(tooltipItem, data) {
                                            return `${data.datasets[tooltipItem.datasetIndex].label}: ${tooltipItem.yLabel}`;
                                        }
                                    }
                                }
                            }
                        });
                    },
                    error: function() {
                        const ctx = document.getElementById("single_album_barchart").getContext('2d');
                        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                        ctx.font = "20px Arial";
                        ctx.textAlign = "center";
                        ctx.fillStyle = "#71748d";
                        ctx.fillText("Failed to load data", ctx.canvas.width / 2, ctx.canvas.height / 2);
                    }
                });
            }
        }

        renderStreamChart();
        function renderLineChart(){
            if ($('#line_chart').length){
                var ctx = document.getElementById('line_chart').getContext('2d');

                var myChart = new Chart(ctx, {
                    type: 'line',

                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Main Data',
                            data: [12, 19, 3, 17, 6, 3, 7],
                            backgroundColor: "rgba(54, 68, 255, 0.5)",
                            borderColor: "rgba(54, 68, 255, 0.7)",
                            borderWidth: 2
                        }, {
                            label: 'Basic Data',
                            data: [2, 29, 5, 5, 2, 3, 10],
                            backgroundColor: "rgba(60, 58, 204,0.5)",
                            borderColor: "rgba(60, 58, 204,0.7)",
                            borderWidth: 2
                        }]

                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: {
                            display: true,
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
        }
        renderLineChart()
        $(".dashboard-slide").slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            dots: false,
            infinite: true,
            touchMove: true,
            autoplay: true,
            cssEase: 'linear',
            autoplaySpeed: 5000,
            customPaging: function(slider, i) {
                return '<span class="dot"></span>';
            },
            prevArrow: '<button class="slick-prev"> <i class="fa fa-chevron-left"></i> </button>',
            nextArrow: '<button class="slick-next"> <i class="fa fa-chevron-right"></i> </button>',
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true,
                    },
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    },
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    },
                },
            ],
        });
    </script>
@endpush
