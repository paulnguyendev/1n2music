@extends('studio.pages.account.main')
@push('css')
<style>
    .card-body{
        min-height: 130px;
    }
</style>
@endpush
@section('title', __('Subscription'))
@section('account_title', __('Subscription'))
@section('account_desc', __('Update your plan and scale your business with selected features.'))
@section('account_content')
<div class="row">
    @foreach($subscriptionArrayRender as $subscriptionArray)
        @php
            $active = $subscriptionArray->active;
            $item = $active =='active' ? $subscriptionArray->orderItem : $subscriptionArray->subscription;
            $name = $active == 'active' ? $item->info->name : $item->name;
            $name = $name ? $name : '-';
        @endphp
        @if ($item)
                @php
                    $userId = rrt_get_user_login('id');
                    $plans = [
                        [
                        "plan" => $item->slug,
                        "cycle" => "monthly"
                        ]
                    ]; 
                @endphp
                <div class="col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="card_title mb-3">
                                {{$name}}
                                @if($active == 'not_active')
                                    <a href="javascript:;" class="payment-plan" data-url="{{rrt_route('public/join/seller/postSelling',['user_id' => $userId, 'plans' => $plans ])}}">
                                        {!! rrt_show_status($active ?? '') !!}
                                    </a>
                                @else
                                    {!! rrt_show_status($active ?? '') !!}
                                    <ul>
            
                                        <li>
                                            {{__('Created')}}: {{ rrt_convert_format_date($item->created_at ?? '', 'd-m-Y H:i:s') ?? '-' }} <br>
                                            {{__('Expired')}}:
                                            @php
                                                $cycle = $item->cycle ?? 'annually';
                                                $key = ($cycle == "monthly") ? "day" : "year";
                                                $number = ($cycle == "monthly") ? 30 : 1;
                                            @endphp
                                            {{ rrt_convert_format_date(rrt_get_expired_at($item->created_at ?? '', $key, $number), 'd-m-Y H:i:s') ?? '-' }}
                                        </li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endforeach
</div>
@endsection
@push('script')
<script>
   $(document).on('click', '.payment-plan', function(e) {
        e.preventDefault();

        var url = $(this).data('url');

        $.ajax({
            url: url,
            type: 'POST',
            success: function(response) {
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    // Xử lý khác nếu không có chuyển hướng
                }
            },
            error: function(xhr, status, error) {
                console.error("Đã xảy ra lỗi: ", error);
            }
        });
    });
</script>
@endpush
