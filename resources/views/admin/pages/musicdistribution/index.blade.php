@extends('admin.main')
@section('page_title', 'Music distribution')
@section('title', 'Music distribution')
@section('buttons')
    {{-- <a href="{{ rrt_route($controllerName . '/form') }}" class="btn btn-primary">Add Trending</a> --}}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-xlg datatable-ajax" data-source="{{ rrt_route($controllerName . '/list',['type'=>$type,'platform'=>$platform]) }}"
                           data-destroymulti="{{ rrt_route($controllerName . '/destroyMulti') }}">
                        <thead>
                        <tr>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Release name') }}</th>
                            <th>{{ __('Genre') }}</th>
                            <th>{{ __('Shops') }}</th>
                            <th>{{ __('Release date') }}</th>
                            <th>{{ __('Total Tracks') }}</th>
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

                    let code = data.code ? data.code : '-';

                    return WBDatatables.showTitle(`#${code}`, data.routeDetail, data.created_at,
                        data.created_at);
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    let date = data.created_at ? data.created_at : '';
                    return date;
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    let status = data.status ? data.status : "pending";
                    let actions = ['approved', 'new', 'denied'];
                    let statusClass = data.status_class ? data.status_class : "";
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
                                        ${status}
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 39px, 0px); top: 0px; left: 0px; will-change: transform;">
                                       ${actionDropdownItems()}
                                    </div>
                                </div>`;
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.name ? data.name : '-';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.generes ? data.generes : '-';
                },
                class: "text-left no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.shopes ? data.shopes : '-';
                },
                class: "text-left no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.release_date ? data.release_date : '-';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },


            {
                data: null,
                render: function(data) {
                    return data.totalTrack ? data.totalTrack : 0;
                },
                class: "text-center no-padding-right",
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
        WBDatatables.addDownloadButton(`{{rrt_route($controllerName . "/export",['type' => $type,'platform' => $platform])}}`, 'Export');
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
