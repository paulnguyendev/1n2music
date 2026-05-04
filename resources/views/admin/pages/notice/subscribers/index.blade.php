@extends('admin.main')
@section('page_title', 'Newsletter Subscribers')
@section('title', 'Newsletter Subscribers')
@section('buttons')
    <a href="{{rrt_route($controllerName . "/form")}}" class="btn btn-primary">{{__('Send Notice to Subscribers')}}</a>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-xlg datatable-ajax" data-source="{{ rrt_route($controllerName . '/subscribersList') }}"
                        data-destroymulti="{{ rrt_route($controllerName . '/subscriberDeleteMulti') }}">
                        <thead>
                            <tr>
                                <th class="text-center" width="50"><input type="checkbox" bs-type="checkbox" value="all"
                                        id="inputCheckAll"></th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Created Date') }}</th>    
                                <th width="50">{{ __('Send Mail') }}</th>
                                <th width="50">{{ __('Action') }}</th>
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
                name: 'email',
                render: function(data) {
                    return data.email || '';
                },
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
                    return `<a href="{{ rrt_route('admin/notice/other/form') }}?email=${data.email}" class="btn btn-sm btn-primary" title="Send Email">{{ __('Send Mail') }}</a>`;
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return `<a data-href="${data.route_remove}" class="text-danger-600 delete-subscriber" data-title="null" data-message="null"><i class="fa fa-trash"></i></a>`;
                },
                className: "text-center",
                orderable: false,
                searchable: false
            },
        ];
        
        var option = {
            fnDrawCallback: function() {
                WBForm.uniform();
                WBDatatables.updatePublisedDate();
                WBDatatables.hideSortBtnAtLastAndFirstRow();
            },
        };
        
        let table = WBDatatables.init('.datatable-ajax', columnDatas, option);
        WBDatatables.updateActive();
        WBDatatables.showAction();
        
        $('body').on('click', '.delete-subscriber', function(e) {
            element = $(this)
            e.preventDefault();
            swal({
                title: "Are you sure to delete this subscriber?",
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
                            successNotice('Delete subscriber success');
                        }
                    });
                }
            });
        });
    </script>
@endpush 