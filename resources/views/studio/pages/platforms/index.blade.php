@extends('studio.main')
@section('title', 'Platforms')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card_title">{{__('Select Platform')}}</h4>
                    <div class="form-group">
                        <label for="platform_select">{{__('Platform')}}</label>
                        <select class="form-control" id="platform_select" name="platform">
                            @foreach($platforms as $platform)
                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h4 class="card_title">{{__('Platform Analytics')}}</h4>
                    <div id="charts-row" class="row mt-4 d-none">
                        <div class="col-md-6">
                            <canvas id="streamsChart" width="300" height="300"></canvas>
                            <p id="streamsChartNoData" class="text-center mt-2 d-none">{{__('No data available for streams')}}</p>
                        </div>
                        <div class="col-md-6">
                            <canvas id="revenueChart" width="300" height="300"></canvas>
                            <p id="revenueChartNoData" class="text-center mt-2 d-none">{{__('No data available for revenue')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let streamsChartInstance;
        let revenueChartInstance;

        window.onload = function() {
            const platformSelect = document.getElementById('platform_select');
            if (platformSelect.value) {
                fetchPlatformData(platformSelect.value);
            }

            platformSelect.addEventListener('change', function() {
                const platformId = this.value;
                if (platformId) {
                    fetchPlatformData(platformId);
                } else {
                    document.getElementById('charts-row').classList.add('d-none');
                }
            });
        }

        function fetchPlatformData(platformId) {
            $.ajax({
                url: '{{ rrt_route($controllerName.'/data') }}',
                type: 'GET',
                data: { id: platformId },
                success: function(data) {
                    if (data.success) {
                        document.getElementById('charts-row').classList.remove('d-none');
                        updateCharts(data.settings);
                    } else {
                        clearCharts()
                        document.getElementById('charts-row').classList.add('d-none');
                    }
                }
            });
        }

        function updateCharts(settings) {
            let streamCount = settings.stream_count || 0;
            let revenue = settings.revenue || 0;

            // Destroy existing charts if they exist
            if (streamsChartInstance) {
                streamsChartInstance.destroy();
            }
            if (revenueChartInstance) {
                revenueChartInstance.destroy();
            }

            // Show or hide the "No data available" message based on data presence
            document.getElementById('streamsChartNoData').classList.toggle('d-none', streamCount > 0);
            document.getElementById('revenueChartNoData').classList.toggle('d-none', revenue > 0);

            if (streamCount > 0) {
                streamsChartInstance = new Chart(document.getElementById('streamsChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Streams'],
                        datasets: [{
                            label: 'Number of Streams',
                            data: [streamCount],
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            x: {
                                display: true
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            if (revenue > 0) {
                revenueChartInstance = new Chart(document.getElementById('revenueChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Revenue'],
                        datasets: [{
                            label: 'Revenue',
                            data: [revenue],
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            x: {
                                display: true
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }

        function clearCharts() {
            if (streamsChartInstance) {
                streamsChartInstance.destroy();
            }
            if (revenueChartInstance) {
                revenueChartInstance.destroy();
            }

            // Optionally clear the canvas contents if needed
            let streamsCtx = document.getElementById('streamsChart').getContext('2d');
            streamsCtx.clearRect(0, 0, streamsCtx.canvas.width, streamsCtx.canvas.height);

            let revenueCtx = document.getElementById('revenueChart').getContext('2d');
            revenueCtx.clearRect(0, 0, revenueCtx.canvas.width, revenueCtx.canvas.height);
        }
    </script>
@endpush
