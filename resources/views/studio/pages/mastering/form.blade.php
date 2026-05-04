@extends('studio.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <div class="buttons-form">
        <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{__('Back')}}</a>
    </div>
@endsection
@section('content')
<form id="formSubmit" action="{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method="post">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card_title">{{ __('Summary') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td>{{ __('Status') }}</td>
                                    <td>{!! rrt_show_status($item->status ?? '') !!}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Time') }}</td>
                                    <td>{{ $item->created_at ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Length') }}</td>
                                    <td>{{ rrt_convert_duration($mastering->container_duration??0) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Mode') }}</td>
                                    <td>{{ __('Easy Mastering') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Automatic Mastering') }}</td>
                                    <td>{{ __('Enabled') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Preset') }}</td>
                                    <td>{{ getPresetInfo($mastering->preset??'general') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Download Full Audio') }}</td>
                                    <td>@if(!empty($mastering->mastered_file_url))
                                            <a href="javascript:void(0)" class="btn btn-primary" id="downloadMaster" data-url="{{ rrt_route('public/studio/mastering/getLinkDownload', ['id' => $id]) }}">
                                                {{ __('Download Mastered File') }}
                                            </a>
                                        @endif</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card_title">{{ __('Statistics') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th></th>
                                <th>{{ __('Original') }}</th>
                                <th>{{ __('Mastered') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{ __('Loudness') }}</td>
                                <td>{{ round($audio->loudness_measured ?? 0, 1) }} dB</td>
                                <td>{{ round($mastering->loudness_measured ?? 0, 1) }} dB</td>
                            </tr>
                            <tr>
                                <td>{{ __('Loudness Range') }}</td>
                                <td>{{ round($audio->loudness_range ?? 0, 1) }} dB</td>
                                <td>{{ round($mastering->loudness_range ?? 0, 1) }} dB</td>
                            </tr>
                            <tr>
                                <td>{{ __('True Peak') }}</td>
                                <td>{{ round($audio->loudness_true_peak ?? 0, 1) }} dB</td>
                                <td>{{ round($mastering->loudness_true_peak ?? 0, 1) }} dB</td>
                            </tr>
                            <tr>
                                <td>{{ __('Stereo Width (Low)') }}</td>
                                <td>0</td>
                                <td>{{ round($mastering->stereo_low_width ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Stereo Width (Mid)') }}</td>
                                <td>0</td>
                                <td>{{ round($mastering->stereo_mid_width ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Stereo Width (High)') }}</td>
                                <td>0</td>
                                <td>{{ round($mastering->stereo_high_width ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Bitrate') }}</td>
                                <td>{{ round($audio->audio_bitrate ?? 0) }} kbps</td>
                                <td>{{ round($mastering->audio_bitrate ?? 0) }} kbps</td>
                            </tr>
                            <tr>
                                <td>{{ __('Sample Rate') }}</td>
                                <td>{{ round($audio->audio_sample_rate ?? 0) }} Hz</td>
                                <td>{{ round($mastering->audio_sample_rate ?? 0) }} Hz</td>
                            </tr>
                            <tr>
                                <td>{{ __('Container Size') }}</td>
                                <td>{{ round(($audio->container_size??0) / 1024, 1) }} MB</td>
                                <td>{{ round(($mastering->container_size??0) / 1024, 1) }} MB</td>
                            </tr>
                            <tr>
                                <td>{{ __('Duration') }}</td>
                                <td>{{ rrt_convert_duration($audio->audio_duration ?? 0)}} s</td>
                                <td>{{ rrt_convert_duration($mastering->audio_duration ?? 0)}} s</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card_title">{{ __('EQ Levels') }}</h4>
                    <canvas id="eqLevelsChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Stereo Width Chart -->
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card_title">{{ __('Stereo Width') }}</h4>
                    <canvas id="stereoWidthChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Loudness Range Chart -->
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card_title">{{ __('Loudness Range') }}</h4>
                    <canvas id="loudnessRangeChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection
@push('script')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script>
        $('select[name="category_id"]').select2({
            placeholder: 'Choose Category'
        });
    </script>
    <!-- Ck Editor Js -->
    <script src="{{ asset('studio/js/nicEdit.js') }}"></script>
    <script type="text/javascript">
        const url = '{{ rrt_route("{$controllerName}/analysis", ['id' => $mastering->id??'']) }}';
        $(document).ready(function() {
            $.getJSON(url, function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    let eqLevels = data.eq_levels;
                    let eqLabels = eqLevels.map((value, index) => `Band ${index + 1}`);

                    let ctx1 = document.getElementById('eqLevelsChart').getContext('2d');
                    let eqLevelsChart = new Chart(ctx1, {
                        type: 'line', // Biểu đồ đường
                        data: {
                            labels: eqLabels, // Nhãn các dải tần số (Bands)
                            datasets: [{
                                label: 'EQ Levels',
                                data: eqLevels, // Dữ liệu EQ Levels
                                borderColor: 'rgba(75, 192, 192, 1)', // Màu của đường
                                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Màu nền bên dưới đường
                                borderWidth: 2, // Độ dày viền
                                fill: true, // Đổ màu dưới đường
                                tension: 0.4 // Tạo độ cong mượt
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    type: 'category',
                                    position: 'bottom'
                                },
                                y: {
                                    beginAtZero: true
                                }
                            },
                            elements: {
                                line: {
                                    cubicInterpolationMode: 'monotone',
                                    tension: 1
                                }
                            }
                        }
                    });

                    let stereoData = [
                        {label: 'Low', value: data.stereo_width.low},
                        {label: 'Mid', value: data.stereo_width.mid},
                        {label: 'High', value: data.stereo_width.high}
                    ];

                    let stereoLabels = stereoData.map(item => item.label);
                    let stereoValues = stereoData.map(item => item.value);

                    let ctx2 = document.getElementById('stereoWidthChart').getContext('2d');
                    let stereoWidthChart = new Chart(ctx2, {
                        type: 'bar',
                        data: {
                            labels: stereoLabels,
                            datasets: [{
                                label: 'Stereo Width',
                                data: stereoValues,
                                backgroundColor: 'rgba(153, 102, 255, 0.5)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    type: 'category',
                                    position: 'bottom'
                                },
                            }
                        }
                    });
                    let loudnessData = [
                        {label: 'Measured', value: data.loudness.measured},
                        {label: 'Range', value: data.loudness.range},
                        {label: 'True Peak', value: data.loudness.true_peak}
                    ];

                    let loudnessLabels = loudnessData.map(item => item.label);
                    let loudnessValues = loudnessData.map(item => item.value);

                    let ctx3 = document.getElementById('loudnessRangeChart').getContext('2d');
                    let loudnessRangeChart = new Chart(ctx3, {
                        type: 'bar',
                        data: {
                            labels: loudnessLabels,
                            datasets: [{
                                label: 'Loudness Range',
                                data: loudnessValues,
                                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    type: 'category',
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                } else {
                    console.error(response.message);
                }
            });
            $(document).on('click', '#downloadMaster', function() {
                let laddaButton = Ladda.create(this);
                laddaButton.start();
                let url = $(this).data('url');
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(response) {
                        console.log(response)
                        if (response.success) {
                            let downloadUrl = response.downloadUrl;
                            window.location.href = downloadUrl;
                            // let link = document.createElement('a');
                            // link.href = response.downloadUrl;
                            // link.download = '';
                            // document.body.appendChild(link);
                            // link.click();
                            // document.body.removeChild(link);
                        } else {
                            showNotify('error', 'Errors', response.message)
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        showNotify('error', 'Errors', error.message)
                    },
                    complete: function() {
                        laddaButton.stop();
                    }
                });
            });
        });
    </script>
@endpush
