@extends('studio.main')
@section('page_title', __('Wallet'))
@section('title', __('Wallet'))

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
                    <div class="row align-items-center">
                        <div class="col-md-6" id="info-wallet"
                            data-url="{{ rrt_route($controllerName . '/getBalanceTotal') }}">

                        </div>
                        <div class="col-md-6 text-right">


                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-body">
                    <table data-source="{{ rrt_route($controllerName . '/list') }}"
                        class="table table-xlg table-withdrawal-management-botton">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Date') }}</th>
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
                name: "created_at",
                render: function(data) {
                    return (!data.code) ? '' : data.code;
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    let type = data.type ? data.type : '';
                    let total_format = data.total_format ? data.total_format : 0;
                    if (type == 'in') {
                        return `<p class="text-success">+ ${total_format}</p>`;
                    } else if (type == 'out')
                        return `<p class="text-danger">- ${total_format}</p>`;

                },
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.xhtml_status ? data.xhtml_status : '';

                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    let type = data.type ? data.type : '';
                    if (type == 'in') {
                        return `<p class="text-success">${type}</p>`;
                    } else if (type == 'out')
                        return `<p class="text-danger">- ${type}</p>`;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return data.xhtml_category ? data.xhtml_category : '';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return data.created_at ? data.created_at : '';
                },
                className: "text-left",
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

        const info_wallet = $('#info-wallet');
        let url = info_wallet.data('url');
        $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            success: function(response) {
                info_wallet.html(`<h4 class = 'mb-0 mt-0'>{{__("Total")}}: ${response.total_format}</h4>`)
            }
        });
    </script>
    <script>
        $(document).ready(function() {


        });
    </script>
@endpush
