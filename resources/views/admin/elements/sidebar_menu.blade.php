<li><a href="{{ rrt_route('admin/dashboard/index') }}"> <i class="feather ft-home"></i><span>{{ __('Dash Board') }}</span></a></li>
<li>
    <a href="{{ rrt_route('admin/admin/index') }}">
        <i class="feather ft-home"></i>
        <span>{{ __('Admin Management') }}</span>
    </a>
</li>
<li>
    <a href="{{ rrt_route('admin/account/index') }}" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>{{ __('Membership Management') }}</span>
        <span class="float-right arrow"><i class="ion ion-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        <li><a href="{{ rrt_route('admin/account/basicMembers') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Free User') }}</span></a></li>

        <li><a href="{{ rrt_route('admin/account/generalMembers') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Seller Members') }} <b>({{ __('Free & Pro') }})</b></span></a></li>
        <li><a href="{{ rrt_route('admin/account/distributionMembers') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Distribution Members') }}</span></a></li>
        <li><a href="{{ rrt_route('admin/account/publishingMembers') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Publishing Members') }}</span></a></li>
    </ul>
</li>
{{-- <li>
    <a href="javascript:void(0)" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>Bulletin board</span>
        <span class="float-right arrow"><i class="ion ion-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        <li><a href="{{ rrt_route('admin/search/index') }}"><i class="ion-ios-folder-outline"></i><span>Popular search</span></a></li>
        <li><a href="{{ rrt_route('admin/faqCategory/index') }}"><i class="ion-ios-folder-outline"></i><span>FAQ Management</span></a></li>
    </ul>
</li> --}}
{{-- <li>
    <a href="{{route('admin/password/index')}}" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>Password</span>
    </a>
</li> --}}
<li>
    <a href="javascript:void(0)" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>{{ __('Shopping Mall Management') }}</span>
        <span class="float-right arrow"><i class="ion ion-chevron-down"></i></span>
    </a>
    <ul class="submenu">
{{--        <li><a href="{{ rrt_route('admin/order/index') }}"><i class="ion-ios-folder-outline"></i><span>Order Details</span></a></li>--}}
        <li><a href="{{ rrt_route('admin/merchandise/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Merchandise') }}</span></a></li>
{{--        <li><a href="{{ rrt_route('admin/music-distribution/index',['type'=>'album']) }}"><i class="ion-ios-folder-outline"></i><span>Album</span></a></li>--}}
{{--        <li><a href="{{ rrt_route('admin/music-distribution/index',['type'=>'single']) }}"><i class="ion-ios-folder-outline"></i><span>Single</span></a></li>--}}
{{--        <li><a href="{{ rrt_route('admin/download/index') }}"><i class="ion-ios-folder-outline"></i><span>Download history</span></a></li>--}}
        {{-- <li><a href="#"><i class="ion-ios-folder-outline"></i><span> Order Licensing History</span></a></li> --}}
        {{-- <li><a href="{{ rrt_route('admin/genres/index') }}"><i class="ion-ios-folder-outline"></i><span> Track genres</span></a></li> --}}
        {{-- <li><a href="{{ rrt_route('admin/invs/index') }}"><i class="ion-ios-folder-outline"></i><span> Track Instruments & Vocals</span></a></li> --}}
        {{-- <li><a href="{{ rrt_route('admin/moods/index') }}"><i class="ion-ios-folder-outline"></i><span> Track moods</span></a></li> --}}
        <li><a href="{{ rrt_route('admin/coupon/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Coupon') }}</span></a></li>
        <li><a href="{{ rrt_route('admin/page/index',['type' => 'shop-policy']) }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Policy ') }}</span></a></li>
    </ul>
</li>
<li>
    <a href="javascript:void(0)" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>{{ __('Distribution Management') }}</span>
        <span class="float-right arrow"><i class="ion ion-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        <li><a href="{{ rrt_route('admin/music-distribution/index',['type'=>'album']) }}"><i class="ion-ios-folder-outline"></i><span>
                    {{__('Album')}}</span></a></li>
        <li><a href="{{ rrt_route('admin/music-distribution/index',['type'=>'single']) }}"><i class="ion-ios-folder-outline"></i><span>
                    {{__('Single')}}</span></a></li>
        <li><a href="{{ rrt_route('admin/download/index') }}"><i class="ion-ios-folder-outline"></i><span> {{__('Download history')}}</span></a></li>
        <li>
            <a href="javascript:void(0)">
                <i class="ion-ios-folder-outline"></i>
                <span>Platforms</span>
                <span class="float-right arrow"><i class="ion ion-chevron-down"></i></span>
            </a>
            @php
                $platforms = rrt_getListFlatform();
            @endphp
            <ul class="submenu">
                @forelse($platforms as $platform)
                    <li><a href="{{ rrt_route('admin/music-distribution/index',['platform'=>$platform->id]) }}"><i class="ion-ios-folder-outline"></i><span> {{$platform->name ??""}}</span></a></li>
                @empty
                @endforelse
            </ul>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:void(0)" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>{{ __('AI Services') }}</span>
        <span class="float-right arrow"><i class="ion ion-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        <li><a href="{{ rrt_route('admin/aiPackage/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('AI package') }}</span></a></li>
    </ul>
</li>
<li>
    <a href="{{ rrt_route('admin/aiPlans/index') }}" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>{{ __('Subscriptions') }}</span>
    </a>
</li>
{{-- <li>
    <a href="{{ rrt_route('admin/download/index') }}" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>Download history</span>
    </a>
</li> --}}
<li>
    <a href="javascript:void(0)" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>{{ __('Financials') }}</span>
        <span class="float-right arrow"><i class="ion ion-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        <li><a href="javascript:void(0)"><i class="ion-ios-folder-outline"></i>
                <span>{{ __('Income management') }}</span>
                <span class="float-right arrow"><i class="ion ion-chevron-down"></i></span>
            </a>
            <ul class="submenu">
                <li><a href="{{ rrt_route('admin/order/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Shopping Mall') }}</span></a></li>
                <li><a href="{{rrt_route('admin/orderAI/index',['ai_id' => \App\Models\AIService::AIServiceAIMastering])}}"><i class="ion-ios-folder-outline"></i><span>{{ __('A.I Mastering Use') }}</span></a></li>
                <li><a href="{{rrt_route('admin/orderAI/index',['ai_id' => \App\Models\AIService::AIServiceAIRecognition])}}"><i class="ion-ios-folder-outline"></i><span>{{ __('A.I Music Recognition Use') }}</span></a></li>
                <li><a href="#"><i class="ion-ios-folder-outline"></i><span>{{ __('Promotion') }}</span></a></li>
                <li><a href="#"><i class="ion-ios-folder-outline"></i><span>{{ __('Subscription Plan Fees') }}</span></a></li>
            </ul>
        </li>
        <li><a href="{{ rrt_route('admin/withdrawalManagement/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Withdrawal management') }}</span></a></li>
        <li><a href="{{ rrt_route('admin/planOrder/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Seller Plan history') }}</span></a></li>
        <li>
            <a href="javascript:void(0)">
                <i class="ion-ios-folder-outline"></i>
                <span>{{ __('Subscriptions History') }}</span>
                <span class="float-right arrow"><i class="ion ion-chevron-down"></i></span>
            </a>
            <ul class="submenu">
                <li><a href="{{ rrt_route('admin/subscriptionOrder/index',['slug'=>'distribution']) }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Distribution Plan History') }}</span></a></li>
                <li><a href="{{ rrt_route('admin/subscriptionOrder/index',['slug'=>'publishing']) }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Publishing Plan History') }}</span></a></li>
            </ul>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:void(0)" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>{{ __('Site Management') }}</span>
        <span class="float-right arrow"><i class="ion ion-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        <li><a href="{{ rrt_route('admin/relatedContents/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Related Contents') }}</span></a></li>
        <li><a href="{{ rrt_route('admin/banner/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Banner') }}</span></a></li>
        <li><a href="{{ rrt_route('admin/popup/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Pop Up') }}</span></a></li>
        <li><a href="{{ rrt_route('admin/bulletinBoard/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Bulletin Board Management') }}</span></a></li>
        <li><a href="{{ rrt_route('admin/freeBoard/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Free Board Management') }}</span></a></li>
        <li><a href="{{ rrt_route('admin/search/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Popular search') }}</span></a></li>
        <li><a href="{{ rrt_route('admin/faqCategory/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('FAQ') }}</span></a></li>
        <li><a href="{{ rrt_route('admin/genres/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Genres') }}</span></a></li>
        <li><a href="{{ rrt_route('admin/moods/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Moods') }}</span></a></li>
        <li>
            <a href="{{ rrt_route('admin/setting/form') }}" aria-expanded="true">
                <i class="feather ft-home"></i>
                <span>{{ __('Footer') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ rrt_route('admin/page/index') }}" aria-expanded="true">
                <i class="feather ft-home"></i>
                <span>{{ __('Pages') }}</span>
            </a>
        </li>
    </ul>
</li>

<li>
    <a href="javascript:void(0)" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>{{ __('SMS & Email') }}</span>
        <span class="float-right arrow"><i class="ion ion-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        <li><a href="{{ rrt_route('admin/notice/index') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Membership') }}</span></a></li>
        <li><a href="{{ rrt_route('admin/notice/other') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Other') }}</span></a></li>
      
        <li><a href="{{ rrt_route('admin/notice/subscribers') }}"><i class="ion-ios-folder-outline"></i><span>{{ __('Newsletter Subscribers') }}</span></a></li>
    </ul>
</li>
<li>
    <a href="{{ rrt_route('admin/platform/index') }}" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>{{ __('Manage Platform') }}</span>
    </a>
</li>

<li>
    <a href="#" aria-expanded="true">
        <i class="feather ft-home"></i>
        <span>{{ __('Setting') }}</span>
        <span class="float-right arrow"><i class="ion ion-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        <li>
            <a href="{{ rrt_route('admin/maintenance/settings') }}" aria-expanded="true">
                <i class="feather ft-home"></i>
                <span>Maintenance mode</span>
            </a>
        </li>
        <li>
            <a href="{{ rrt_route('admin/tax/index') }}" aria-expanded="true">
                <i class="feather ft-home"></i>
                <span>{{ __('Tax Type') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ rrt_route('admin/language/index') }}" aria-expanded="true">
                <i class="feather ft-home"></i>
                <span>{{ __('Language') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ rrt_route('admin/commission/settings') }}" aria-expanded="true">
                <i class="feather ft-home"></i>
                <span>{{ __('Commission') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ rrt_route('admin/boardCategories/index') }}" aria-expanded="true">
                <i class="feather ft-home"></i>
                <span>{{ __('Board Categories') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ rrt_route('admin/limitUpload/settings') }}" aria-expanded="true">
                <i class="feather ft-home"></i>
                <span>{{ __('Limit Upload Tracks') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ rrt_route('admin/page/index',['type' => 'term']) }}" aria-expanded="true">
                <i class="feather ft-home"></i>
                <span>{{ __('Terms & Condition') }}</span>
            </a>
        </li>
    </ul>
</li>
