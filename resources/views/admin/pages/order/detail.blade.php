@extends('admin.main')
@section('page_title', 'Order Items')
@section('title', 'Order Items')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-xlg datatable-ajax"
                        data-source="{{ rrt_route($controllerName . '/listItem', ['id' => $id]) }}"
                        data-destroymulti="{{ rrt_route($controllerName . '/destroyMulti') }}">
                        <thead>
                            <tr>

                                <th>{{__('Track Name')}}</th>
                                <th>{{__('Licensing')}}</th>
                                <th>{{__('Deliverables')}} </th>
                                <th>{{__('Price')}} </th>
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
                name: "1",
                render: function(data) {

                    return data && data.tracks && data.tracks.name ? data.tracks.name : '-';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "2",
                render: function(data) {
                    return data?.contract_track?.contract_setting?.contract?.name || '-';
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },

            {
                data: null,
                name: "3",
                render: function(data) {
                    return data?.contract_track?.contract_setting?.deliverables || '-';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "4",
                render: function(data) {
                    return data.show_price ? data.show_price : '';
                },
                className: "text-left",
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
