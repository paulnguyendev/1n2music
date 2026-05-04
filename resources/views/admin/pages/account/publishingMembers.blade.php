@extends('admin.main')
@section('page_title', 'Publishing Members')
@section('title', 'Publishing Members')
@section('buttons')
    <!-- <a data-url="{{ rrt_route('admin/tools/packageUsage', ['account_type' => 'publishing']) }}"
        data-toggle="modal" 
        data-target="#packageUsageModal"
        class="btn btn-primary text-white">{{ __('Add/Minus usage Ai') }}
    </a> -->
    <!-- Thêm lượt dùng Ai cho tất cả người dùng trong package  -->
    <a href="{{ rrt_route($controllerName . '/form', ['account_type' => 'publishing']) }}" class="btn btn-primary">{{__('Create a New Account')}}</a>
@endsection
@section('content')
    <style>
        .a {
            overflow-x: scroll !important;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body a">
                    <table class="table table-xlg datatable-ajax"
                        data-source="{{ rrt_route($controllerName . '/list', ['account_type' => 'publishing']) }}"
                        data-destroymulti="{{ rrt_route($controllerName . '/destroyMulti') }}">
                        <thead>
                            <tr>
                                <th class="text-center" width="50"><input type="checkbox" bs-type="checkbox"
                                        value="all" id="inputCheckAll"></th>
                                <th>{{ __('Username') }}</th>
                                <th>{{ __('Fullname') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Homepage') }}</th>
                                <th>{{ __('Join Type') }}</th>
                                <th>{{ __('Expiration Date') }}</th>
                                <th>{{ __('IPI') }}</th>
                                <th>{{ __('Tax type') }}</th>
                                <th width="50">{{ __('Status') }}</th>
                                <th>{{ __('AI Mastering') }}</th>
                                <th>{{ __('AI Recognition') }}</th>
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
    @include('admin.elements.modal_usageAi')
    @include('admin.elements.modal_packageUsage')
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
                name: 'description.title',
                render: function(data) {
                    return WBDatatables.showTitle(data.username, data.route_edit, data.is_published,
                        data.published_at);
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "fullname",
                render: function(data) {
                    return (!data.fullname) ? '' : data.fullname;
                },
                className: "text-left",
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
                name: "email",
                render: function(data) {
                    return (!data.phone) ? '' : data.phone;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    let select = (!data.is_homepage) ? '' : 'checked';
                    let value = (!data.is_homepage) ? 0 : 1;
                    return `
                        <span>
                            <input data-id="${data.id }" type="checkbox" value="${value}" class="homepage" bs-type="checkbox" ${select} >
                            </span>
                          `;
                },
                className: "text-left ",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.join_type) ? '' : data.join_type;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.expriration_date) ? '' : data.expriration_date;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.pro_organization) ? '' : data.pro_organization;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.taxtypes) ? '' : data.taxtypes.name;
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
            },{
                data: null,
                name: "ai_usage_count",
                render: function(data) {
                    return (!data.ai_usage_count) ? 0 : data.ai_usage_count;
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "ai_usage_count_reconize",
                render: function(data) {
                    return (!data.ai_usage_count_reconize) ? 0 : data.ai_usage_count_reconize;
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return `<a class="text-primary-600" 
                                data-title="{{ __('AI Usage Count')}}" 
                                data-mastering="${(!data.ai_usage_count) ? 0 : data.ai_usage_count}"
                                data-reconize="${(!data.ai_usage_count_reconize) ? 0 : data.ai_usage_count_reconize}"
                                data-user-id="${(!data.id) ? 0 : data.id}"
                                data-toggle="modal" 
                                data-target="#usageaiModal">
                                <i class="fa fa-plus"></i>
                            </a>`;
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return `<a href="${data.route_list_payment}" class=" text-primary-600" data-title="null" data-message="null"><i class="fa fa-eye"></i></a>`;
                },
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
        WBDatatables.addDownloadButton(`{{rrt_route($controllerName . "/export",['type' => 'publishing'])}}`, 'Export Users');
        $('body').on('change', '.homepage', function(e) {
            let value = $(this).val();
            is_homepage = (value == 1) ? 0 : 1;
            let url = "{{ rrt_route('admin/account/updateIsHomepage') }}";
            let id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    is_homepage: is_homepage,
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                dataType: "json",
                success: function(response) {

                    WBDatatables.reloadData();
                    successNotice('Notification', response.message);
                }
            });
        })
        $('body').on('click', '.delete-user', function(e) {
            console.log(11);
            element = $(this)
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
                console.log(response);
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
                    // Do nothing
                }
            });
        });
        $(document).ready(function() {
            $('#ai-usage-form').on('keydown', function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                }
            });
            $('#usageaiModal').on('show.bs.modal', function(event) {
                var link = $(event.relatedTarget);
                var userId = link.data('user-id');
                var mastering = link.data('mastering');
                var reconize = link.data('reconize');

                var modal = $(this);
                modal.find('input[name="user_id"]').val(userId);
                modal.find('#ai-mastering').text(`AI Mastering: ${mastering}`);
                modal.find('#ai-reconize').text(`AI Reconize: ${reconize}`);
            });
            $('#save-changes').on('click', function(e) {
                e.preventDefault();
                var userId = $('#user-id').val();
                var aiSelect = $('#ai-select').val();
                var aiCount = $('#ai-count').val();

                $('.error-message').remove();
                var hasError = false;
                if (!aiCount || isNaN(aiCount) || aiCount == 0) {
                    $('#ai-count').after('<span class="error-message text-danger">Please enter a valid number</span>');
                    hasError = true;
                } 
                if (!hasError) {
                    $.ajax({
                        url: $('#ai-usage-form').attr('action'),
                        type: 'POST',
                        data: {
                            user_id: userId,
                            ai_select: aiSelect,
                            ai_count: aiCount,
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                toastr.success(response.message);
                                $('#usageaiModal').modal('hide');
                                WBDatatables.reloadData();
                            } else if (response.status === 'error') {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = xhr.responseJSON && xhr.responseJSON.message 
                                ? xhr.responseJSON.message 
                                : 'Has some error. Please try again later!';
                            toastr.error(errorMessage);
                        }
                    });
                }
            });

            $('#package-ai-usage-form').on('keydown', function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                }
            });
            $('#packageUsageModal').on('show.bs.modal', function(event) {
                var link = $(event.relatedTarget);
                var url = link.data('url');
                var modal = $(this);
                modal.find('form').attr('action', url);
            });
            $('#save-changes-package').on('click', function(e) {
                e.preventDefault();
                var aiSelect = $('#ai-select-package').val();
                var aiCount = $('#ai-count-package').val();
                
                $('.error-message').remove();
                var hasError = false;
                if (!aiCount || isNaN(aiCount) || aiCount == 0) {
                    $('#ai-count-package').after('<span class="error-message text-danger">Please enter a valid number</span>');
                    hasError = true;
                } 
                if (!hasError) {
                    $.ajax({
                        url: $('#package-ai-usage-form').attr('action'),
                        type: 'POST',
                        data: {
                            ai_select: aiSelect,
                            ai_count: aiCount,
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                toastr.success(response.message);
                                $('#packageUsageModal').modal('hide');
                                WBDatatables.reloadData();
                            } else if (response.status === 'error') {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = xhr.responseJSON && xhr.responseJSON.message 
                                ? xhr.responseJSON.message 
                                : 'Has some error. Please try again later!';
                            toastr.error(errorMessage);
                        }
                    });
                }
            });
        });
    </script>
@endpush
