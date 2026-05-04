@extends('admin.main')
@section('page_title', 'AI Packages')
@section('title', 'AI Packages')
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/form') }}" class="btn btn-primary">{{__('Add Package')}}</a>
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
                            <th>{{__('Name')}}</th>
                            <th>{{__('AI Service')}}</th>
                            <th>{{__('Created Date')}}</th>
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
            name: "fullname",
            render: function(data) {
                return data.name ?? '';
            },
            className: "text-left",
            orderable: false,
            searchable: false
        },

            {
                data: null,
                name: "fullname",
                render: function(data) {
                    return data.ai_service.name ?? '';
                },
                className: "text-left",
                orderable: false,
                searchable: false
            }
            , {
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
                    let route_form = data.route_edit ? data.route_edit : '#';
                    let btnUpdateOrder = `<a href = '${route_form}'><i class="fa fa-edit"></i></a>`;
                    return btnUpdateOrder;
                },
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
        $(document).on('click', 'a.remove_item', function (e) {
            var url_remove = $(this).attr('href');
            let rowspan = $(this).closest('td').attr('rowspan') || 0;
            let $current_row = $(this).closest('tr');
            var data = $(this).data();
            data._token = _token;
            swal({
                showLoaderOnConfirm: true,
                closeOnConfirm: false,
                title: data.title?data.title:"Are you sure to perform the delete operation?",
                text: data.message?data.message:"You will not be able to get this data back!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF7043",
                cancelButtonText: "No",
                confirmButtonText: "Yes"
            }).then((result) => {
                if(result.value){
                    $.ajax({
                        url: url_remove,
                        type: 'DELETE',
                        dataType: 'json',
                        data: data,
                        success: function (response) {
                            if(response.success == false) {
                                warningNotice(response.message);
                            }else{
                                if(response.hasOwnProperty('message')) {
                                    successNotice(response.message);
                                }
                            }
                            swal.close();
                            if (data.redirect) {
                                window.location = data.redirect;
                            } else if (response.redirect) {
                                window.location = response.redirect;
                            } else if (response.reload) {
                                WBDatatables.reloadData();
                            } else {
                                for (let i = 1; i < rowspan; i++) {
                                    $current_row.next('tr').remove();
                                }
                                $current_row.remove();
                                WBDatatables.reloadData();
                            }
                        },
                        error: function () {
                            swal.close();
                        }
                    });
                }
                else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swal("Cancelled", "Your imaginary file is safe :)", "error")
                }
            });
        });
    </script>
@endpush
