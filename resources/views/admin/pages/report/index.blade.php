@extends('admin.main')
@section('page_title', 'Report')
@section('title', 'Report')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card_title">{{__('Sale')}}</h4>
                    <div class="chart_container">
                        <canvas id="doughnut_chart"></canvas>
                    </div>
                </div>
            </div>
        </div>   
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card_title">{{__('Month Sales')}}</h4>
                    <div class="chart_container">
                        <canvas id="line_chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
@endpush
@push('script')
    <script src="{{ asset('admin') }}/vendors/charts/charts-bundle/Chart.bundle.js"></script>
    <script src="{{ asset('admin') }}/js/init/chart-js.js?ver={{ time() }}"></script>
@endpush
