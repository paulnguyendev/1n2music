@extends('admin.main')
@section('page_title', __($title))
@section('title', __($title))
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-xlg datatable-ajax" data-source="{{ rrt_route($controllerName . '/list') }}">
                        <thead>
                        <tr>
                            <th>{{ __('Order Number') }}</th>
                            <th>{{ __('User Info') }}</th>
                            <th>{{ __('Pay Amount') }} </th>
                            <th>{{ __('Usage Count') }} </th>
                            <th>{{ __('Days download available') }} </th>
                            <th>{{ __('AI Service') }}</th>
                            <th>{{ __('Payment method') }}</th>
                            <th>{{ __('Payment Status') }}</th>
                            <th>{{ __('Usage Status') }}</th>
                            <th>{{ __('Order Created At') }}</th>
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
        var columnDatas = [
            {
                data: null,
                render: function(data) {
                    return data.id;
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    let userInfo = `
                        Fullname: ${data.userInfo.fullname ? data.userInfo.fullname : 'Not updated Yet'}<br>
                        Email: ${data.userInfo.email ? data.userInfo.email : 'Not updated Yet'}<br>
                        Phone: ${data.userInfo.phone ? data.userInfo.phone : 'Not updated Yet'}
                    `;
                    return userInfo;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return (data.pay_amount ? data.pay_amount : 0) + "$";
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.usage_count ? data.usage_count : 0;
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.download_available ? data.download_available : 0;
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.aiServiceName ? data.aiServiceName : '-';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.payment_method ? data.payment_method : '-';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.badgePaymentStatus ? data.badgePaymentStatus : '-';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.badgeStatus ? data.badgeStatus : '-';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.created_at ? data.created_at : '-';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    let route_form = data.route_update ? data.route_update : '#';
                    return `<a href='${route_form}'><i class="fa fa-edit"></i></a>`;
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
    </script>
@endpush
