@extends('studio.main')
@section('page_title', __('Withdrawal'))
@section('title', __('Withdrawal'))
{{-- @section('buttons')
    <a href="{{ rrt_route($controllerName . '/form') }}" class="btn btn-primary">Create a New FAQ</a>
@endsection --}}
<link rel="stylesheet" href="{{ asset('admin/vendors/rrt/css') }}/style_plugin.css?ver={{ time() }}">
<script>
    var _token = 'NN2qLcQhx0Cv4lMh5Wl8yaKE7XXEdhqtl2VyI22q';
    var base_domain = "{{ env('APP_URL') }}";
    var api_domain = "https://vaiaodaiduyen.com/";
    var assets_url = "https://quantri.vaiaodaiduyen.com/public/assets/";
    var cke_conf_path = assets_url + '/backend/plugins/ckeditor';
    var default_currency = 'đ';
    var default_weight_unit = "kg";
    var storage_url = '';
</script>
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
    <div class="col-md-12 text-right">
        <button data-url="{{ rrt_route($controllerName . '/checkRequestPayout') }}" type="button"
            class="btn btn-primary btn-flat mt-2 btn_add_request">{{ __('Add Request') }}</button>
    </div>
    <div class="col-md-12">

        <div style=" background: rgba(0, 0, 0, 0.7);" class="modal fade" id="add-request" aria-hidden="true"
            style="display: none;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Request Wallet') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    </div>
                    <div class="modal-body">

                        <form action="{{ $controllerName . '/getWithdrawBalance' }}" method="post">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="wallet-balance" class="col-form-label d-block text-justify">{{ __('Wallet balance') }}</label>
                                <input disabled class="form-control" type="number" value="" id="wallet-balance">
                            </div>
                            <div class="form-group mb-3">
                                <label for="wallet-balance" class="col-form-label d-block text-justify">{{ __('Amount can withdraw') }}</label>
                                <input disabled class="form-control" type="number" value="" id="amount-can-withdraw">
                            </div>
                            <div class="form-group mb-3">
                                <label for="withdraw-balance" class="col-form-label d-block text-justify">{{ __('Withdraw balance') }}</label>
                                <input class="form-control" type="number" min="50" max="" value="" id="withdraw-balance">
                            </div>
                            <div class="form-group mb-3">
                                <label for="" class="col-form-label d-block text-justify">{{ __('Receiving account') }}</label>
                                <select name="" id="payment" class="form-control">

                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                            data-url="{{ rrt_route('public/studio/transaction/postRequestWithdrawBalance') }}"
                            class="btn btn-primary btn_widthdraw">{{ __('Submit') }}</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-body">
                <table data-source="{{ rrt_route($controllerName . '/list') }}"
                    class="table table-xlg table-withdrawal-management-botton">
                    <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Withdrawal Method') }}</th>
                            <th>{{ __('Tax Type') }}</th>
                            <th width="120px">{{ __('Withdrawal amount') }}</th>
                            <th width="120px">{{ __('VAT') }}</th>
                            <th width="120px">{{ __('Commision') }}</th>
                            <th width="120px">{{ __('Actual Payment Amount') }}</th>
                            <th width="120px">{{ __('Reported') }}</th>
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
                render: function(data) {
                    let status = data.xhtml_status ? data.xhtml_status : "false";
                    return status;

                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },

            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.time) ? '' : data.time;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return (!data.method_payment) ? 'CARD' : data.method_payment;
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return data.tax ?? '';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {

                    return (!data.amount_request) ? '20.000$' : data.amount_request;
                },
                className: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },

            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.vat) ? ' 3.000$' : data.vat;
                },
                className: "text-center no-padding-right",
                orderable: false,
                searchable: false
            }, {
                data: null,
                render: function(data) {
                    return (!data.amount_tax) ? '60$' : data.amount_tax;
                },
                class: "text-center ",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return (!data.amount_payment) ? '16.940$' : data.amount_payment;
                },
                class: "text-center ",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.amount_report) ? '17.000$' : data.amount_report;

                },
                className: "text-center ",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    let html = '';


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

        $('.btn_add_request').click(function() {
            const url = $(this).data('url');

            checkRequestPaypout(url);

        });

        function checkRequestPaypout(url) {

            $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                success: function(response) {
                    if (response.status == 200) {
                        if (response.check.value == 1) {
                            $('#wallet-balance').val(response.total);
                            //   $('#withdraw-balance').val(response.total_affter_peding);
                            $('#withdraw-balance').attr('max', response.total_affter_peding)
                            $('#amount-can-withdraw').val(response.total_affter_peding);
                            let option = '';
                            $.each(response.method, function(i, v) {
                                option += `<option value="${i}">${v}</option>`
                            });
                            $('#payment').html(option)
                            $('#add-request').modal('show');
                        } else {
                            errorNotice('Notification', response.value);
                        }

                    } else {
                        errorNotice('Notification', response.check.msg);
                    }

                }
            });
        }


        $('.btn_widthdraw').click(function() {
            let withdraw_balance = $('#withdraw-balance').val();
            if (!withdraw_balance) {
                errorNotice('Notification', 'Withdraw balance not empty');
                return;
            }
            let url = $(this).data('url');
            let total_widthdraw = $('#withdraw-balance').val();
            let payment = $('#payment').val();
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    total_widthdraw: total_widthdraw,
                    payment: payment
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == 200) {
                        successNotice('Notification', response.check.msg);
                        $("#add-request").modal('hide');
                        WBDatatables.reloadData();
                    } else {
                        errorNotice('Notification', response.check.msg);
                    }
                }
            });
        })
    </script>
@endpush
