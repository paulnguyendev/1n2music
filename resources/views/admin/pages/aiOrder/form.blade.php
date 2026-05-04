@extends('admin.main')
@section('page_title', __('order.Update_Order'))
@section('title', __('order.Update_Order'))
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{__('Back')}}</a>
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
                            <span>Order payment details</span>
                        </h4>
                        <div class="single-table">
                            <div class="table-responsive">
                                <table class="table text-center">
                                    <thead class="text-uppercase bg-light">
                                    <tr>
                                        <th scope="col">{{ __('Order Number') }}</th>
                                        <th scope="col">{{ __('Payment Method') }} </th>
                                        <th scope="col">{{ __('ORDER TOTAL') }} </th>
                                        <th scope="col">{{ __('Usage Count') }} </th>
                                        <th scope="col">{{ __('Days Download available') }} </th>
                                        <th scope="col">{{ __('Ai Service') }} </th>
                                        <th scope="col">{{ __('Payment Status') }} </th>
                                        <th scope="col">{{ __('Usage Status') }} </th>
                                        <th scope="col">{{ __('Order Created At') }} </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th scope="row"> #{{ $item['id'] ?? '' }} </th>
                                        <td>{{ $item['payment_method'] ?? "" }}</td>
                                        <td>{{ rrt_show_price($item['pay_amount'] ?? 0) }}</td>
                                        <td>{{$item['usage_count'] ?? 0}}</td>
                                        <td>{{$item['download_available'] ?? 0}} Days</td>
                                        <td>{{$item->aiService->name ?? '-'}}</td>
                                        @php
                                            use App\Helpers\Template;
                                            $is_payment = $item['is_payment'] ?? 0;
                                            $badgePayment = Template::showStatus('badge','pending');
                                            if ($is_payment == 1){
                                                $badgePayment = Template::showStatus('badge','complete');
                                            }
                                            $is_usage = $item['status'] ?? 0;
                                            $badgeStatus = Template::showStatus('badge','pending');
                                            if ($is_usage == 1){
                                                $badgeStatus = Template::showStatus('badge','complete');
                                            }
                                        @endphp
                                        <td>{!! $badgePayment !!}</td>
                                        <td>{!! $badgeStatus !!}</td>
                                        <td>{{$item['created_at'] ?? ''}}</td>
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
                        <h4 class="card_title">{{ __('Edit payment details') }} </h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    @php
                                        $itemStatus = $item['is_payment'] ?? 0;
                                    @endphp
                                    <label for="">Change order payment status (*)</label>
                                    <select name="is_payment" id="status" class="form-control" {{ $itemStatus == 1 ? 'disabled' : '' }}>
                                        <option value="0" {{$itemStatus == 0 ? 'selected' : ""}}>Pending</option>
                                        <option value="1" {{$itemStatus == 1 ? 'selected' : ""}}>Completed</option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="col-md-12 mt-3">
        <div class="card ">
            <div class="card-body">
                <h4 class="card_title">{{ __('order.Order_Log') }} </h4>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table text-center">
                            <thead class="text-uppercase bg-light">
                            <tr>
                                <th scope="col">{{ __('ID') }}</th>
                                <th scope="col">{{ __('NAME') }} </th>
                                <th scope="col">{{ __('DESCRIPTION') }} </th>
                                <th scope="col">{{ __('Date') }} </th>


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
                                        <td class="text-center">{{ $logItem['name'] ?? '-' }}</td>
                                        <td class="text-center">{!! $logItem['description'] ?? '-' !!}</td>
                                        <td>{{ $logItem['created_at'] ?? '-' }}</td>

                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">{{ __('No data') }}</td>
                                </tr>
                            @endif


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
@endpush
