@extends('admin.main')
@section('page_title', 'Merchandise')
@section('title', 'Merchandise')
@section('buttons')
    {{-- <a href="{{ rrt_route($controllerName . '/form') }}" class="btn btn-primary">Add Trending</a> --}}
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
                                <th>{{ __('Seller ID') }}</th>
                                <th>{{ __('Seller Info') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Trending') }}</th>
                                <th>{{ __('Recommend') }}</th>
                                <th>{{ __('Feature Track') }}</th>
                                <th>{{ __('Release Date') }}</th>
                                <th>{{ __('Created Date') }}</th>
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
                name: "fullname",
                render: function(data) {
                    return data.user_id ?? '';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "fullname",
                render: function(data) {
                    return data.info ?? '';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "fullname",
                render: function(data) {
                    return data.name ?? '';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            }, {
                data: null,
                name: "fullname",
                render: function(data) {
                    return (!data.track_type_id) ? '' : data.track_type_id;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "fullname",
                render: function(data) {
                    let status = (!data.is_trending) ? '' : data.is_trending;
                    let value = (!data.is_trending) ? '' : data.is_trending;
                    let id = data.id;
                    let route = "{{ rrt_route('admin/merchandise/update') }}" + '/' + id;
                    return `<input data-route="${route}" data-value="${value}" class="is_trending" name="is_trending" data-name="is_trending" data-id="${data.id}" type="checkbox" ${status} >`
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },



            {
                data: null,
                name: "fullname",
                render: function(data) {
                    let status = (!data.is_recommend) ? '' : data.is_recommend;
                    let value = (!data.is_recommend) ? '' : data.is_recommend;
                    let id = data.id;
                    let route = "{{ rrt_route('admin/merchandise/update') }}" + '/' + id;
                    return `<input data-route="${route}" class="is_recommend" data-value="${value}" name="is_recommend" data-name="is_recommend" data-id="${data.id}" type="checkbox" ${status} >`

                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "fullname",
                render: function(data) {
                    let status = (!data.is_featured) ? '' : data.is_featured;
                    let value = (!data.is_featured) ? '' : data.is_featured;
                    let id = data.id;
                    let route = "{{ rrt_route('admin/merchandise/update') }}" + '/' + id;
                    return `<input data-route="${route}" class="is_featured" data-value="${value}" name="is_featured" data-name="is_featured" data-id="${data.id}" type="checkbox" ${status} >`

                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "fullname",
                render: function(data) {

                    return (!data.release_date) ? '' : data.created_at;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "fullname",
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
    <script>
        $('body').on('click', '.is_trending', function() {
            let url = $(this).data('route');
            let id = $(this).data('id');
            let status = $(this).data('value');
            status = (!status) ? 1 : 0;
            $.ajax({
                type: "post",
                url: url,
                data: {
                    id: id,
                    is_trending: status,
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    WBDatatables.reloadData();
                    successNotice('Notification', response.msg);
                }
            });
        })
        $('body').on('click', '.is_recommend', function() {
            let url = $(this).data('route');
            let id = $(this).data('id');
            let status = $(this).data('value') ?? '';
            status = (!status) ? 1 : 0;
            $.ajax({
                type: "post",
                url: url,
                data: {
                    id: id,
                    is_recommend: status,
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    WBDatatables.reloadData();
                    successNotice('Notification', response.msg);
                }
            });
        })
        $('body').on('click', '.is_featured', function() {
            let url = $(this).data('route');
            let id = $(this).data('id');
            let status = $(this).data('value') ?? '';
            status = (!status) ? 1 : 0;
            $.ajax({
                type: "post",
                url: url,
                data: {
                    id: id,
                    is_featured: status,
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    WBDatatables.reloadData();
                    successNotice('Notification', response.msg);
                }
            });
        })
    </script>
@endpush
