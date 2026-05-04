@extends('studio.main')
@section('title', 'Order Detail')
@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-primary">
            <i class="fa fa-arrow-left"></i> Back to Orders
        </a>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    @if(isset($order))
                    Order #{{ $order->code }}
                    @elseif(isset($planOrder))
                    Plan Order #{{ $planOrder->id }}
                    @elseif(isset($subscriptionOrder))
                    Subscription Order #{{ $subscriptionOrder->id }}
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <table class="table">
                            <tbody>
                                @if(isset($order))
                                <tr>
                                    <td>Order Number:</td>
                                    <td>#{{ $order->code }}</td>
                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td>{{ $order->created_at }}</td>
                                </tr>
                                <tr>
                                    <td>Status:</td>
                                    <td>{!! rrt_show_status($order->status) !!}</td>
                                </tr>
                                <tr>
                                    <td>Payment Method:</td>
                                    <td>{{ $order->payment->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Total:</td>
                                    <td>{!! rrt_show_price($order->total) !!}</td>
                                </tr>
                                @elseif(isset($planOrder))
                                <tr>
                                    <td>Order Number:</td>
                                    <td>#{{ $planOrder->id }}</td>
                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td>{{ $planOrder->created_at }}</td>
                                </tr>
                                <tr>
                                    <td>Status:</td>
                                    <td>{!! rrt_show_status($planOrder->status) !!}</td>
                                </tr>
                                <tr>
                                    <td>Payment Method:</td>
                                    <td>{{ $planOrder->payment_method ?? 'Paypal' }}</td>
                                </tr>
                                <tr>
                                    <td>Plan:</td>
                                    <td>{{ $planOrder->plan->name ?? 'Unknown Plan' }}</td>
                                </tr>
                                <tr>
                                    <td>Cycle:</td>
                                    <td>{{ ucfirst($planOrder->cycle ?? 'Annually') }}</td>
                                </tr>
                                <tr>
                                    <td>Total:</td>
                                    <td>{!! rrt_show_price($planOrder->total) !!}</td>
                                </tr>
                                @elseif(isset($subscriptionOrder))
                                <tr>
                                    <td>Order Number:</td>
                                    <td>#{{ $subscriptionOrder->id }}</td>
                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td>{{ $subscriptionOrder->created_at }}</td>
                                </tr>
                                <tr>
                                    <td>Status:</td>
                                    <td>{!! rrt_show_status($subscriptionOrder->status) !!}</td>
                                </tr>
                                <tr>
                                    <td>Payment Method:</td>
                                    <td>{{ $subscriptionOrder->payment_method ?? 'Paypal' }}</td>
                                </tr>
                                <tr>
                                    <td>Subscription:</td>
                                    <td>{{ $subscriptionOrder->subscription->name ?? 'Unknown Subscription' }}</td>
                                </tr>
                                <tr>
                                    <td>Cycle:</td>
                                    <td>{{ ucfirst($subscriptionOrder->cycle ?? 'Annually') }}</td>
                                </tr>
                                <tr>
                                    <td>Total:</td>
                                    <td>{!! rrt_show_price($subscriptionOrder->total) !!}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Buyer Information</h6>
                        <table class="table">
                            <tbody>
                                @if(isset($order))
                                <tr>
                                    <td>Name:</td>
                                    <td>{{ $order->fullname }}</td>
                                </tr>
                                <tr>
                                    <td>Phone:</td>
                                    <td>{{ $order->phone }}</td>
                                </tr>
                                <tr>
                                    <td>Email:</td>
                                    <td>{{ $order->email }}</td>
                                </tr>
                                <tr>
                                    <td>Address:</td>
                                    <td>{{ $order->address ?? '-' }}</td>
                                </tr>
                                @elseif(isset($planOrder) && $planOrder->user)
                                <tr>
                                    <td>Name:</td>
                                    <td>{{ $planOrder->user->first_name }} {{ $planOrder->user->last_name }}</td>
                                </tr>
                                <tr>
                                    <td>Phone:</td>
                                    <td>{{ $planOrder->user->phone }}</td>
                                </tr>
                                <tr>
                                    <td>Email:</td>
                                    <td>{{ $planOrder->user->email }}</td>
                                </tr>
                                @elseif(isset($subscriptionOrder) && $subscriptionOrder->user)
                                <tr>
                                    <td>Name:</td>
                                    <td>{{ $subscriptionOrder->user->first_name }} {{ $subscriptionOrder->user->last_name }}</td>
                                </tr>
                                <tr>
                                    <td>Phone:</td>
                                    <td>{{ $subscriptionOrder->user->phone }}</td>
                                </tr>
                                <tr>
                                    <td>Email:</td>
                                    <td>{{ $subscriptionOrder->user->email }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(isset($order) && isset($orderItems) && $orderItems->count() > 0)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6>Order Items</h6>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Type</th>
                                    <th>Files</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderItems as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($item->tracks)
                                        {{ $item->tracks->name }}
                                        @else
                                        {{ $item->product_name ?? 'Track not found' }}
                                        @endif
                                    </td>
                                    <td>{!! rrt_show_price($item->price) !!}</td>
                                    <td>{{ $item->quantity ?? 1 }}</td>
                                    <td>{!! rrt_show_price($item->price * ($item->quantity ?? 1)) !!}</td>
                                    <td>
                                        {{$item->contract_track->contractSetting->contract->name }}
                                    </td>
                                    <td>
                                        @if($order->status == 'deliver')
                                        @if($item->product && $item->product->files)
                                        @foreach($item->product->files as $file)
                                        @if($file->type !== 'thumbnail')
                                        <a href="{{ url($file->url) }}" class="btn btn-sm btn-info mb-1" download>
                                            <i class="fa fa-download"></i> {{ $file->name ?? 'Download' }}
                                        </a>
                                        @endif
                                        @endforeach
                                        @elseif($item->tracks && $item->tracks->file)
                                        @php
                                        $contractDeliverables = $item->contract_track->contractSetting->deliverables ?? '';
                                      
                                        @endphp
                                        @foreach($item->tracks->file as $file)
                                        @if($file->type !== 'thumbnail' && $file->type ==  $contractDeliverables )
                                        <p>{{$file->name ?? '-'}}</p>
                                        <a href="{{ url('public/uploads/tracks/' . rawurlencode($file->name)) }}" class="btn btn-sm btn-info mb-1" download>
                                            <i class="fa fa-download"></i> Download 
                                        </a>
                                        @endif
                                        @endforeach
                                        @endif
                                        @else
                                        <span class="badge badge-warning">Available after delivery</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                                    <td>{!! rrt_show_price($order->total - ($order->shipping_fee ?? 0)) !!}</td>
                                </tr>
                                @if(isset($order->shipping_fee) && $order->shipping_fee > 0)
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Shipping Fee:</strong></td>
                                    <td>{!! rrt_show_price($order->shipping_fee) !!}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                    <td>{!! rrt_show_price($order->total) !!}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection