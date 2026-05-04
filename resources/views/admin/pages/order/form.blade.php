@php
    use App\Helpers\Template;
@endphp
@extends('admin.main')
@section('page_title', __('order.Update_Order'))
@section('title', __('order.Update_Order'))
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{__('Back')}}</a>
    <button class="btn btn-success btn-ladda btn-ladda-spinner btn-send-mail"
        data-url="{{ rrt_route($controllerName . '/sendmail', ['id' => $id]) }}" onclick="sendMail(this)" data-style="zoom-in"
        data-form="formSubmit">{{ __('order.Send_Mail_Again') }}</button>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
        data-form="formSubmit">{{ __('order.Save_Changes') }}</button>
@endsection
@section('content')
    <form id="formSubmit" action = "{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method = "post">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title d-flex justify-content-between">
                            @php
                                $total = $item['total'] ?? 0;
                                $totalPayment = $item['total_payment'] ?? 0;
                                $totalRemain = $total - $totalPayment;
                                $totalRemainClass = $totalRemain >= 0 ? 'text-success' : 'text-danger';
                            @endphp
                            <span>{{__('Order payment details')}}</span>
                            <span class="{{ $totalRemainClass }}">Accounts receivable {{ rrt_show_price($totalRemain) }}
                            </span>
                        </h4>
                        <div class="single-table">
                            <div class="table-responsive">
                                <table class="table text-center">
                                    <thead class="text-uppercase bg-light">
                                        <tr>
                                            <th scope="col">{{ __('order.Order_Number') }}</th>
                                            <th scope="col">{{ __('order.Payment_Method') }} </th>
                                            <th scope="col">{{ __('order.ORDER_TOTAL') }} </th>
                                            <th scope="col">{{ __('order.DELIVERY_FEE') }} </th>
                                            <th scope="col">{{ __('order.POINT_PAYMENT') }} </th>
                                            <th scope="col">{{ __('order.TOTAL_PAYMENT_AMOUNT') }} </th>
                                            <th scope="col">{{ __('order.COUPON') }} </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row"> #{{ $item['code'] ?? '' }} </th>
                                            <td>{{ $item->payment->name ?? '' }}</td>
                                            <td>{{ rrt_show_price($item['total'] ?? 0) }}</td>
                                            <td>{{ rrt_show_price($item['fee_delivery'] ?? 0) }}</td>
                                            <td>{{ $item['point'] ?? 0 }}</td>
                                            <td>{{ rrt_show_price($item['total_payment'] ?? 0) }}</td>
                                            <td>{{ rrt_show_price($item['coupon'] ?? 0) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-3">
                <div class="card ">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('order.Edit_payment_details') }} </h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    @php
                                        $itemStatus = $item['status'] ?? '';
                                    @endphp
                                    <label for="">Change order status (*)</label>
                                    <select name="status" id="status" class="form-control"
                                        {{ $itemStatus == 'deliver' ? 'disabled' : '' }}>
                                        @foreach (Template::showListStatus('order') as $key => $status)
                                            <option {{ $itemStatus == $key ? 'selected' : '' }}
                                                value="{{ $key }}">
                                                {{ $status['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('order.Deposit_amount_without_bankbook') }} </label>
                                    <input type="text" class="form-control" name="total_payment"
                                        value="{{ $item['total_payment'] ?? 0 }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    @php
                                        $itemPayment = $item['payment_id'] ?? '';
                                    @endphp
                                    <label for="">Payment Method (*)</label>
                                    <select name="payment_id" id="payment_id" class="form-control">
                                        @if ($payments)
                                            @foreach ($payments as $payment)
                                                <option
                                                    data-url="{{ rrt_route($controllerName . '/listAccount', ['payment_id' => $payment['id']]) }}"
                                                    {{ $itemPayment == $payment['id'] ? 'selected' : '' }}
                                                    value="{{ $payment['id'] }}">{{ $payment['name'] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    @php
                                        $itemPaymentAccountID = $item['payment_account_id'] ?? '';
                                    @endphp
                                    <label for="">{{ __('order.Account_number') }} (*)</label>
                                    <select name="payment_account_id" id="payment_account_id" class="form-control">
                                        @if ($paymentAccounts)
                                            @foreach ($paymentAccounts as $paymentAccount)
                                                <option
                                                    {{ $itemPaymentAccountID == $paymentAccount['id'] ? 'selected' : '' }}
                                                    value="{{ $paymentAccount['id'] }}">{{ $paymentAccount['name'] }}
                                                    {{ $paymentAccount['description'] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('order.Payment_confirmation_date_and_time') }} </label>
                                    <input type="datetime-local" class="form-control" name="payment_confirmed_at_select"
                                        value="{{ $paymentConfirmedAtFormated }}">
                                    <span class="help-block"></span>
                                </div>
                                <input type="hidden" name="payment_confirmed_at"
                                    value="{{ $item['payment_confirmed_at'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('order.Full_Name') }} (*)</label>
                                    <input type="text" class="form-control" name="fullname"
                                        value="{{ $item['fullname'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('order.Phone_Number') }} (*)</label>
                                    <input type="text" class="form-control" name="phone"
                                        value="{{ $item['phone'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('order.Email') }} (*)</label>
                                    <input type="email" class="form-control" name="email"
                                        value="{{ $item['email'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('order.Point_payment_amount') }} </label>
                                    <input type="number" class="form-control" name="point"
                                        value="{{ $item['point'] ?? 0 }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label
                                        for="">{{ __('order.Payment_cancellation') }}/{{ __('order.refund_amount') }}</label>
                                    <input type="number" class="form-control" name="total_refund"
                                        value="{{ $item['total_refund'] ?? 0 }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('order.Waybill_number') }} </label>
                                    <input type="number" class="form-control" name="waybill_number"
                                        value="{{ $item['waybill_number'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('order.Note') }} </label>
                                    <textarea class="form-control" name="note" rows="5">{{ $item['note'] ?? '' }}</textarea>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-3">
                <div class="card ">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('order.Seller_Commssion') }} </h4>
                        <div class="single-table">
                            <div class="table-responsive">
                                <table class="table text-center">
                                    <thead class="text-uppercase bg-light">
                                        <tr>
                                            <th scope="col">{{ __('order.ID') }}</th>
                                            <th scope="col">{{ __('order.FULLNAME') }} </th>
                                            <th scope="col">{{ __('order.EMAIL') }} </th>
                                            <th scope="col">{{ __('order.PRICE') }} </th>
                                            <th scope="col">{{ __('order.COMISSION') }} </th>
                                            <th scope="col">{{ __('order.TOTAL') }} </th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($listComission as $key => $comissionItem)
                                            @php
                                                $index = $key + 1;
                                            @endphp
                                            <tr>
                                                <th scope="row"> {{ $index }} </th>
                                                <td>{{ $comissionItem['fullname'] ?? '-' }}</td>
                                                <td>{{ $comissionItem['email'] ?? '-' }}</td>
                                                <td>{{ rrt_show_price($comissionItem['price']) ?? '-' }}</td>
                                                <td>{{ $comissionItem['commission'] ?? '-' }}</td>
                                                <td>{{ rrt_show_price($comissionItem['total']) ?? '-' }}</td>

                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-3">
                <div class="card ">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('order.Order_Log') }} </h4>
                        <div class="single-table">
                            <div class="table-responsive">
                                <table class="table text-center">
                                    <thead class="text-uppercase bg-light">
                                        <tr>
                                            <th scope="col">{{ __('order.ID') }}</th>
                                            <th scope="col">{{ __('order.NAME') }} </th>
                                            <th scope="col">{{ __('order.DESCRIPTION') }} </th>
                                            <th scope="col">{{ __('order.Date') }} </th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($listLog)
                                            @foreach ($listLog as $key => $logItem)
                                                @php
                                                    $index = $key + 1;
                                                @endphp
                                                <tr>
                                                    <th scope="row"> {{ $index }} </th>
                                                    <td class="text-left">{{ $logItem['name'] ?? '-' }}</td>
                                                    <td class="text-left">{!! $logItem['description'] ?? '-' !!}</td>
                                                    <td>{{ $logItem['created_at'] ?? '-' }}</td>

                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4">{{ __('order.No_data') }}</td>
                                            </tr>
                                        @endif


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection
@push('script')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        $('select[name="payment_id"]').select2({
            placeholder: 'Choose Payment Method'
        });
        $('select[name="payment_account_id"]').select2({
            placeholder: 'Choose Account'
        });
        $('select[name="status"]').select2({
            placeholder: 'Choose Status'
        });
        $(`select[name=payment_id]`).change(function() {
            let optionSelected = $(this).find('option:selected');
            let url = optionSelected.data('url');
            $.ajax({
                type: "get",
                url: url,
                data: {},
                dataType: "json",
                success: function(response) {
                    let xhtml = '';
                    if (response) {
                        response.forEach(item => {

                            xhtml +=
                                `<option value = "${item.id}">${item.name} ${item.description}</option>`;
                        });
                    }
                    $(`select[name=payment_account_id]`).html(xhtml);
                }
            });
        })

        function formatDate(date) {
            var year = date.getFullYear();
            var month = ('0' + (date.getMonth() + 1)).slice(-2);
            var day = ('0' + date.getDate()).slice(-2);
            var hours = ('0' + date.getHours()).slice(-2);
            var minutes = ('0' + date.getMinutes()).slice(-2);
            var seconds = ('0' + date.getSeconds()).slice(-2);
            return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;
        }
        let datetimeInputSelect = document.querySelector(`input[name=payment_confirmed_at_select]`);
        let datetimeInput = document.querySelector(`input[name=payment_confirmed_at]`);
        datetimeInputSelect.addEventListener('input', function() {
            let selectedDate = new Date(datetimeInputSelect.value);
            let formattedDate = formatDate(selectedDate);
            datetimeInput.value = formattedDate;
        });
        const sendMail = (btn) => {
            let url = $(btn).data("url");

            swal({
                showLoaderOnConfirm: true,
                closeOnConfirm: false,
                title: "Are you sure you want to resend emails to the buyers?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF7043",
                cancelButtonText: "No",
                confirmButtonText: "Yes"
            }, function() {
                swal.close();
                var l = Ladda.create(btn);
                l.start();
                $.ajax({
                    type: "post",
                    url: url,
                    data: {},
                    dataType: "json",
                    success: function(response) {
                        let msg = response.msg ? response.msg : "";
                        let status = response.status ? response.status : "";
                        if (status == 200) {
                            successNotice(msg);
                        } else {
                            errorNotice("Error", msg);
                        }

                    },
                    error: function(data) {
                        errorNotice("Error", "Email failed");
                    },
                    complete: function() {
                        l.stop();
                    }
                });
            });
        }
    </script>
@endpush
