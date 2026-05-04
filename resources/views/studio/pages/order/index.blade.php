@extends('studio.main')
@section('title', 'My Purchased')
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
                    <table data-source="{{ rrt_route($controllerName . '/list') }}"
                           class="table table-xlg table-withdrawal-management-botton datatable-ajax">
                        <thead>
                        <tr>
                            <th>{{ __('Order Number') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Order status') }}</th>
                            <th>{{ __('Payment method') }}</th>
                            <th>{{ __('Order Buyer Info') }}</th>
                            <th>{{ __('Total Items') }}</th>
                            <th>{{ __('Total') }}</th>                            
                            <th>{{ __('Action') }}</th>
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

                    let type = data.type ? data.type : '-';

                    return type;
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
                    return data.status ? data.status : '';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.paymentName ? data.paymentName : '';
                },
                class: "text-left no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.orderBuyerInfo ? data.orderBuyerInfo : '-';
                },
                class: "text-left no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.count ? data.count : '0';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.total ? data.total : 0;
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    let detailUrl = data.routeDetail;
                    return `<a href="${detailUrl}" class="btn btn-sm btn-primary">View Detail</a>`;
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            }
        ];
        var option = {
            pageLength: 5,
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
