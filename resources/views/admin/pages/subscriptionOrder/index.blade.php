@extends('admin.main')
@section('page_title', 'Order Subscriptions History')
@section('title', 'Order Subscriptions History')

{{-- @section('buttons')
    <a href="{{ rrt_route($controllerName . '/form') }}" class="btn btn-primary">Create a New FAQ</a>
@endsection --}}

@section('content')
    {{-- <style>
        .switch {
            height: 0;
            width: 0;
            visibility: hidden;
        }

        .label-trending {
            cursor: pointer;
            text-indent: -9999px;
            width: 50px;
            height: 30px;
            background: grey;
            display: block;
            border-radius: 100px;
            position: relative;
        }

        .label-trending:after {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            width: 20px;
            height: 20px;
            background: #fff;
            border-radius: 90px;
            transition: 0.3s;
        }

        input:checked+.label-trending {
            background: var(--indigo);
        }

        input:checked+.label-trending:after {
            left: calc(100% - 5px);
            transform: translateX(-100%);
        }

        .label-trending:active:after {
            width: 20px;
        }
    </style> --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-xlg datatable-ajax" data-source="{{ rrt_route($controllerName . '/list',['slug'=>$slug??'']) }}"
                        data-destroymulti="{{ rrt_route($controllerName . '/destroyMulti') }}">
                        <thead>
                            <tr>
                                <th class="text-center" width="50"><input type="checkbox" bs-type="checkbox" value="all"
                                        id="inputCheckAll"></th>
                                <th>{{ __('User Info') }}</th>
                                <th>{{ __('Subscription Info') }}</th>
                                <th>{{ __('Created Date') }}</th>
                                <th>{{ __('Expired Date') }}</th>
                                <th>{{ __('Status') }}</th>                                        
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
                name: "fullname",
                render: function(data) {
                    return (!data.info) ? '-' : data.info;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },

            {
                data: null,
                name: "fullname",
                render: function(data) {
                    return (!data.subscriptionInfo) ? '' : data.subscriptionInfo;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },

            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.created_at) ? '' : data.created_at;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.subscriptionExpiredAt) ? '' : data.subscriptionExpiredAt;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    let status = data.status ? data.status : "pending";
                    let statusName = data.status_name ? data.status_name : "";
                    let statusClass = data.status_class ? data.status_class : "";
                    let actions = ['active', 'pending'];
                    let actionFilter = actions.filter(filterStatus);

                    function filterStatus(action) {
                        return action != status;
                    }
                    let actionDropdownItems = () => {
                        let xhtml = '';
                        if (actionFilter.length > 0) {
                            actionFilter.forEach(element => {
                                xhtml += `<a class="dropdown-item" href="#">${element}</a>`;
                            });
                        }
                        return xhtml;
                    }
                    let routeUpdate = data.route_update ? data.route_update : "";
                    return `<div class="dropdown" data-url = "${routeUpdate}">
                                    <button class="btn dropdown-toggle btn-outline-${statusClass} btn-change-status" type="button" data-toggle="dropdown" aria-expanded="false">
                                        ${statusName}
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 39px, 0px); top: 0px; left: 0px; will-change: transform;">
                                       ${actionDropdownItems()}
                                    </div>
                                </div>`;
                },
                className: "text-center",
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
        $('body').on('click', '.main-content-inner .dropdown-item', function(e) {
            e.preventDefault();
            let status = $(this).text();
            let parent = $(this).parent().parent();
            let url = parent.data('url');
            $.ajax({
                type: "post",
                url: url,
                data: {
                    status: status
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
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
        let table = WBDatatables.init('.datatable-ajax', columnDatas, option);
        WBDatatables.updateActive();
        WBDatatables.showAction();
    </script>
@endpush
