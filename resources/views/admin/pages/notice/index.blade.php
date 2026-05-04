@extends('admin.main')
@section('page_title', 'Notice')
@section('title', 'Notice')
@section('buttons')
      <a href="{{rrt_route($controllerName . "/form")}}" class="btn btn-primary">{{__('Create a New Notice')}}</a>
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
                                <th class="text-center" width="50"><input type="checkbox" bs-type="checkbox" value="all"
                                        id="inputCheckAll"></th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Process') }}</th>
                                <th>{{ __('Failed') }}</th>
                                <th>{{ __('Created Date') }}</th>    
                                <th width="50"></th>
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
                name: 'description.title',
                render: function(data) {
                    return WBDatatables.showTitle(data.name, data.route_edit, data.is_published,
                        data.published_at);
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "description",
                render: function(data) {
                    return (!data.description) ? '' : data.description;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "process",
                render: function(data) {
                    return (!data.process) ? '' : data.process;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "failed",
                render: function(data) {
                    return (!data.failed) ? '' : data.failed;
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
                render: function(data) {
                    const routeEdit = data.route_edit;
                    return (!routeEdit) ? '' :
                        `<a class = 'btn btn-primary btn-sm mr-2' href = '${routeEdit}'>Edit</a> `;
                },
                className: "text-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return `<a data-href="${data.route_remove}" class=" text-danger-600 delete-email" data-title="null" data-message="null"><i class="fa fa-trash"></i></a>`; ;
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
        $('body').on('click', '.delete-email', function(e) {
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
            }).then((willDelete) => {
                if (willDelete) {
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
    </script>
@endpush
