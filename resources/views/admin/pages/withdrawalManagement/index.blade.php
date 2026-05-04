@extends('admin.main')
@section('page_title', 'Withdrawwal Management')
@section('title', 'Withdrawwal Management')
{{-- @section('buttons')
    <a href="{{ rrt_route($controllerName . '/form') }}" class="btn btn-primary">Create a New FAQ</a>
@endsection --}}
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

        .datatable-scroll {
            overflow-x: scroll;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-xlg datatable-ajax" data-source="{{ rrt_route($controllerName . '/list') }}"
                        data-destroymulti="{{ rrt_route($controllerName . '/destroyMulti') }}">
                        <thead>
                            <tr>
                                <th>{{ __('Division') }}</th>
                                <th>{{ __('Total Sales Amount') }}</th>
                                <th>{{ __('Total commission') }}</th>
                                <th>{{ __('Total points') }}</th>
                                <th>{{ __('Total net sales/net savings') }}</th>
                                <th>{{ __('Total incentive') }}</th>
                                <th>{{ __('Total shipping cost') }}</th>
                                <th>{{ __('Total accumulated') }}</th>
                                <th>{{ __('Total Payment') }}</th>
                                <th>{{ __('Payment Request') }}</th>
                                <th>{{ __('Current balance') }}</th>                                
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>{{__('Seller')}}</th>
                                <th>{{ rrt_show_price(330000) }}</th>
                                <th>{{ rrt_show_price(35000) }}</th>
                                <th>0</th>
                                <th> {{ rrt_show_price(295000) }}</th>
                                <th>0</th>
                                <th>0</th>
                                <th>{{ rrt_show_price(295000) }}</th>
                                <th> {{ rrt_show_price(190000) }}</th>
                                <th>0</th>
                                <th> {{ rrt_show_price(105000) }}</th>
                            </tr>
                            <tr>
                                <th>{{__('Marketer')}} </th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
            <div class="card mt-4">
                <div class="card-body">
                    <table data-source="{{ rrt_route($controllerName . '/list') }}"
                        class="table table-xlg table-withdrawal-management-botton">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Owner') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Role') }}</th>
                                <th>{{ __('Withdrawal Method') }}</th>
                                <th>{{ __('Tax Type') }}</th>
                                <th width="120px">{{ __('Withdrawal amount') }}</th>
                                <th width="120px">{{ __('Supply price') }}</th>
                                <th width="120px">{{ __('VAT') }}</th>
                                <th width="120px">{{ __('TAX') }}</th>
                                <th width="120px">{{ __('Actual Payment Amount') }}</th>
                                <th width="120px">{{ __('Reported') }}</th>
                                <th>{{ __('Action') }}</th>                                
                            </tr>
                        </thead>
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
                    return data.id;
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.xhtml_status ? data.xhtml_status : "false";
                    let status = data.status ? data.status : "false";
                    if (status == 'pending') {
                        return `<span class="badge badge-info">${status}</span>`;
                    } else {
                        return `<span class="badge badge-success">${status}</span>`;
                    }
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return data.manager ? data.manager : 'YUD COGN';

                },
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.time) ? '' : data.time;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.seller) ? 'Seller' : data.seller;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },

            {
                data: null,
                render: function(data) {
                    return (!data.method_payment) ? 'CARD' : data.method_payment;
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.tax ?? 'personal';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {

                    return (!data.amount_request) ? '20.000$' : data.amount_request;
                },
                className: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.amount_supply) ? '17.000$' : data.amount_supply;
                },
                className: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.vat) ? ' 3.000$' : data.vat;
                },
                className: "text-center no-padding-right",
                orderable: false,
                searchable: false
            }, {
                data: null,
                render: function(data) {
                    return (!data.amount_tax) ? '60$' : data.amount_tax;
                },
                class: "text-center ",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return (!data.amount_payment) ? '16.940$' : data.amount_payment;
                },
                class: "text-center ",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.amount_report) ? '17.000$' : data.amount_report;

                },
                className: "text-center ",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    let html = '';

                    if (data.status == 'pending') {
                        html +=
                            `<button data-url="${data.route_approve}" class="mb-2 btn-approve"  data-id="${data.id}" ><a class="btn btn-success " href="#">Approve</a>  </button>`;
                        html +=
                            `<button data-url="${data.route_cancel}" class="mb-2 btn-cancel"  data-id="${data.id}" ><a class="btn btn-dark " href="#">Cancel</a>  </button>`;

                    }


                    html +=
                        `<button data-url="${data.route_}" data-id="${data.id}" ><a class="btn btn-primary " href="${data.route_detail}">Detail</a>  </button>`;
                    return html;
                },
                orderable: false,
                searchable: false
            },

        ];
        $('body').on('click', '.accept', function(e) {

            e.preventDefault();
            let id = $(this).data('id');
            let url = $(this).data('url');
            console.log(url);
            $.ajax({
                type: "post",
                url: url,
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    WBDatatables.reloadData();
                    successNotice('Notification', response.msg);
                }
            });
        });
        $('body').on('change', '.change-plan', function(e) {
            e.preventDefault();
            let plan_id = $(this).val();
            let plan_order_id = $(this).data('id');
            let plan_status = $(this).data('status');
            let user_id = $(this).data('user-id');
            let url = $(this).data('url');
            if (plan_id) {
                swal({
                    title: "Are you sure?",
                    text: "Are you sure to perform the change this plan?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF7043",
                    cancelButtonText: "No",
                    confirmButtonText: "Yes"
                }, function() {
                    $.ajax({
                        type: "post",
                        url: url,
                        data: {
                            plan_order_id: plan_order_id,
                            plan_id: plan_id,
                            plan_status: plan_status,
                            user_id: user_id,
                        },
                        dataType: "json",
                        success: function(response) {
                            console.log(response);
                            WBDatatables.reloadData();
                            successNotice('Notification', response.msg);
                        }
                    });
                });
            } else {
                alert("Please choose plan");
            }
        });
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
        let table = WBDatatables.init('.table-withdrawal-management-botton', columnDatas, option);
        WBDatatables.updateActive();
        WBDatatables.showAction();
        $('body').on('click', '.btn-approve', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let url = $(this).data('url');
            Swal.fire({
                title: "Are you sure?",
                text: "Are you sure to approve this withdrawal request?",
                type: "success",
                showCancelButton: true,
                confirmButtonColor: "#FF7043",
                cancelButtonText: "No",
                confirmButtonText: "Yes"
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            id: id
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.status == 200) {
                                successNotice('Notification', response.msg);
                                WBDatatables.reloadData();
                            } else {
                                errorNotice('Notification', response.msg);
                            }
                        }
                    });
                }
            });


        })
        $('body').on('click', '.btn-cancel', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let url = $(this).data('url');
            Swal.fire({
                title: "Are you sure?",
                text: "Are you sure to cancel this withdrawal request?",
                buttons: true,
                dangerMode: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF7043",
                cancelButtonText: "No",
                confirmButtonText: "Yes"
            }).then((result) => {

                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            id: id
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.status == 200) {
                                successNotice('Notification', response.msg);
                                WBDatatables.reloadData();
                            } else {
                                errorNotice('Notification', response.msg);
                            }

                        }
                    });
                }
            });

        })
    </script>
@endpush
