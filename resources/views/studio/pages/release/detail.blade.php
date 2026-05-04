@extends('studio.main')
@section('title', 'Order Items')
@section('page_title', 'Order Items #' . $code)
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
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table data-source="{{ rrt_route($controllerName . '/listOrderItem',['order_id' => $id]) }}"
                        class="table table-xlg table-withdrawal-management-botton datatable-ajax">
                        <thead>
                            <tr>
                                <th>{{ __('Track Name') }}</th>
                                <th>{{ __('Licensing') }}</th>
                                <th>{{ __('Deliverables') }}</th>
                                <th>{{ __('Price') }}</th>                                
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
                    let name = data && data.tracks && data.tracks.name  ? data.tracks.name : '-';
                    return name;
                },
                class: "text-left no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data?.contract_track?.contract_setting?.contract?.name || '-';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data?.contract_track?.contract_setting?.deliverables || '-';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.price ? data.price : '';
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
