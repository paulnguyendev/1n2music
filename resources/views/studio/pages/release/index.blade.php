@extends('studio.main')
@section('title', Request::is('*/album') ? 'Album Release' : 'Single Release')
@section('content')
    <style>
        .main-content .main-content-inner {
            padding: 60px 0px;
            max-width: 100%;
            margin: auto;
        }

        .disabled {
            opacity: 0.6;
            cursor: not-allowed !important;
        }
    </style>
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif 
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-end">

                <a href="{{ rrt_route($controllerName . '/form',['type' => $type,'code' => $code]) }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{__('Add New')}}</a>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-body">

                    <table data-source="{{ rrt_route($controllerName . '/list',['type' => $type]) }}"
                        class="table table-xlg table-withdrawal-management-botton datatable-ajax">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Release name') }}</th>
                                <th>{{ __('Genre') }}</th>
                                <th>{{ __('Shops') }}</th>
                                <th>{{ __('Release date') }}</th>

                                <th>{{ __('Total Tracks') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        var columnDatas = [


            {
                data: null,
                render: function(data) {

                    let code = data.code ? data.code : '-';

                    return WBDatatables.showTitle(`#${code}`, data.routeDetail, data.created_at,
                        data.created_at);
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    let date = data.created_at ? data.created_at : '';
                    return date;
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.status ? data.status : '-';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.name ? data.name : '-';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.generes ? data.generes : '-';
                },
                class: "text-left no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.shopes ? data.shopes : '-';
                },
                class: "text-left no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.release_date ? data.release_date : '-';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },


            {
                data: null,
                render: function(data) {
                    return data.totalTrack ? data.totalTrack : 0;
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },




        ];
        var option = {

            fnDrawCallback: function() {
                WBForm.uniform();
                WBDatatables.updatePublisedDate();
                WBDatatables.hideSortBtnAtLastAndFirstRow();
            },
        };
        let table = WBDatatables.init('.table-withdrawal-management-botton', columnDatas, option);
        WBDatatables.updateActive();
        WBDatatables.showAction();
    </script>
@endpush
