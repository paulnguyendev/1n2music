@extends('admin.main')
@section('page_title', 'Admin Account')
@section('title', 'Admin Account')
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/form') }}" class="btn btn-primary">{{__('Create a New Account')}}</a>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-xlg datatable-ajax" data-source="{{ rrt_route($controllerName . '/list') }}"
                           data-destroymulti="{{ rrt_route($controllerName . '/destroyMulti') }}">
                        <thead>
                        <tr>
                            <th class="text-center" width="50"><input type="checkbox" bs-type="checkbox"
                                                                      value="all" id="inputCheckAll"></th>
                            <th>{{ __('Admin') }}</th>
                            <th>{{ __('Username') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Created Date') }}</th>
                            <th width="50">{{ __('Status') }}</th>
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
                    // Default thumbnail if not available
                    const thumbnailUrl = data.thumbnail ? '/public/uploads/admins/' + data.thumbnail : '/public/assets/public/images/no-image.png';
                    const fullname = data.fullname || 'Unknown';
                    
                    return `<div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <img src="${thumbnailUrl}" alt="${fullname}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                </div>
                                <div>
                                    <a href="${data.route_edit}" class="text-default font-weight-semibold">${fullname}</a>
                                </div>
                            </div>`;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: 'username',
                render: function(data) {
                    return data.username || '';
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "email",
                render: function(data) {
                    return (!data.email) ? '' : data.email;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "phone",
                render: function(data) {
                    return (!data.phone) ? '' : data.phone;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "role",
                render: function(data) {
                    // Display role name with badge
                    let roleName = data.role_name || '';
                    let badgeClass = '';
                    
                    if (data.role === '1') {
                        badgeClass = 'badge-primary';
                    } else if (data.role === '2') {
                        badgeClass = 'badge-info';
                    } else if (data.role === '3') {
                        badgeClass = 'badge-success';
                    }
                    
                    return `<span class="badge ${badgeClass}">${roleName}</span>`;
                },
                className: "text-center",
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
                name: "status",
                render: function(data) {
                    let status = data.status ? data.status : "pending";
                    let statusName = data.status_name ? data.status_name : "";
                    let statusClass = data.status_class ? data.status_class : "";
                    let actions = ['active', 'pending', 'suspend'];
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
                    return `<a data-href="${data.route_remove}" class=" text-danger-600 delete-user" data-title="null" data-message="null"><i class="fa fa-trash"></i></a>`;
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
        $('body').on('click', '.delete-user', function(e) {
            let element = $(this)
            e.preventDefault();
            swal({
                title: "Are you sure to perform the delete operation?",
                text: "You will not be able to get this data back?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF7043",
                cancelButtonText: "No",
                confirmButtonText: "Yes"
            }).then((response) => {
                console.log(response)
                if (response.value) {
                    $.ajax({
                        type: "delete",
                        url: element.data('href'),
                        dataType: "json",
                        success: function(response) {
                            console.log(response);
                            WBDatatables.reloadData();
                            successNotice('Delete data success');
                        }
                    });
                } else {
                }
            });
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
