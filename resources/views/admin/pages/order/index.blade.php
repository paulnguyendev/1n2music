@extends('admin.main')
@section('page_title', __('order.Shopping_Mall'))
@section('title', __('order.Shopping_Mall'))
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-xlg datatable-ajax" data-source="{{ rrt_route($controllerName . '/list') }}"
                        data-destroymulti="{{ rrt_route($controllerName . '/destroyMulti') }}">
                        <thead>
                            <tr>
                                <th class="text-center" width="50"><input type="checkbox" bs-type="checkbox" value="all"
                                        id="inputCheckAll"></th>
                                <th>{{ __('order.Order_Number') }}</th>
                                <th>{{ __('order.Date') }}</th>
                                <th>{{ __('order.Order_status') }} </th>
                                <th>{{ __('order.Payment_method') }} </th>
                                <th>{{ __('order.Member_Info') }} </th>
                                <th>{{ __('order.Total_Item') }}</th>
                                <th>{{ __('order.Total') }}</th>
                                <th width="10"></th>
                                <th width="10"></th>
                                <th width="10"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        var columnDatas = [{
                data: null,
                render: function(data) {
                    return WBDatatables.showSelect(data.id);
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "1",
                render: function(data) {
                    let code = data.code ? data.code : '-';
                    return WBDatatables.showTitle(`#${code}`, data.route_detail, data.created_at,
                        data.created_at);
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },

            {
                data: null,
                name: "2",
                render: function(data) {
                    return data.created_at ? data.created_at : '-';
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },

            {
                data: null,
                name: "3",
                render: function(data) {
                    return data.show_status ? data.show_status : '';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "4",
                render: function(data) {
                    return data.payment_name ? data.payment_name : '';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "5",
                render: function(data) {
                    return data.order_info ? data.order_info : '';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "6",
                render: function(data) {
                    return data.order_items_count ? data.order_items_count : 0;
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "7",
                render: function(data) {
                    return data.show_total ? data.show_total : '';
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "8",
                render: function(data) {
                    return '';
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },
             {
                data: null,

                render: function(data) {
                    let route_form = data.route_detail ? data.route_detail : '#';
                    let btnUpdateOrder = `<a href = '${route_form}'><i class="fa fa-eye"></i></a>`;
                    return btnUpdateOrder;
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,

                render: function(data) {
                    let route_form = data.route_form ? data.route_form : '#';
                    let btnUpdateOrder = `<a href = '${route_form}'><i class="fa fa-edit"></i></a>`;
                    return btnUpdateOrder;
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,

                render: function(data) {

                    return WBDatatables.showRemoveIcon(data.route_remove);
                },
                orderable: false,
                searchable: false
            },
        ];
        var option = {
            // fnInitComplete: renderChangeStatusPopupAfterReload,
            fnDrawCallback: function() {
                // WBForm.init();
                WBForm.uniform();
                WBDatatables.updatePublisedDate();
                WBDatatables.hideSortBtnAtLastAndFirstRow();
                // renderChangeStatusPopupAfterReload();
            },
        };
        let table = WBDatatables.init('.datatable-ajax', columnDatas, option);
        WBDatatables.updateActive();
        WBDatatables.showAction();
        WBDatatables.addDownloadButton(`{{rrt_route($controllerName . "/export")}}`, 'Export');
    </script>
@endpush
