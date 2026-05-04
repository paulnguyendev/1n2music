@extends('studio.main')
@section('page_title', 'Wallet ')
@section('title', 'Wallet ')

<link rel="stylesheet" href="{{ asset('admin/vendors/rrt/css') }}/style_plugin.css?ver={{ time() }}">

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
                                <th>{{ __('Withdrawal Method') }}</th>
                                <th>{{ __('Tax Type') }}</th>
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
                name: "created_at",
                render: function(data) {
                    return (!data.code) ? '' : data.code;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            
            {
                data: null,
                render: function(data) {
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
                render: function(data) {
                    let html = '';
                    if (data.status == 'pending') {
                        html +=
                            `<button class="mb-2" data-url="${data.route_}" data-id="${data.id}" ><a class="btn btn-success " href="${data.route_approve}">Approve</a>  </button>`;

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
    </script>
@endpush
