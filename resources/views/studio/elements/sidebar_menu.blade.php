@php
    use App\Helpers\Subscription;
    $userRolesAndSubs  = Subscription::checkUserRole();
    $menuPermissions = [];

    // Debug: Log what roles we're checking
    \Log::info('Sidebar Debug - User Roles', ['roles' => $userRolesAndSubs]);

    foreach ($userRolesAndSubs as $roleOrSub) {
        $permissions = config('studio_menu_permissions.' . $roleOrSub, []);

        // Debug: Log permissions for each role
        \Log::info('Sidebar Debug - Role Permissions', [
            'role' => $roleOrSub,
            'permissions' => $permissions
        ]);

        $menuPermissions = array_merge($menuPermissions, $permissions);
    }

    // Debug: Log merged permissions before filtering
    \Log::info('Sidebar Debug - Merged Permissions', ['permissions' => $menuPermissions]);

    // Check if user has any distribution subscription (distribution, distribution-basic, distribution-annually, etc.)
    $hasDistribution = !empty(array_filter($userRolesAndSubs, function($role) {
        return str_contains($role, 'distribution');
    }));
    if ($hasDistribution) {
        $menuPermissions = array_diff($menuPermissions, ['digital_distribution_signup']);
    }
    // Check if user has any publishing subscription
    $hasPublishing = !empty(array_filter($userRolesAndSubs, function($role) {
        return str_contains($role, 'publishing');
    }));
    if ($hasPublishing) {
        $menuPermissions = array_diff($menuPermissions, ['publishing_signup']);
    }
    $menuPermissions = array_unique($menuPermissions);

    // Debug: Log final permissions
    \Log::info('Sidebar Debug - Final Permissions', ['permissions' => $menuPermissions]);

    $packageRole = rrt_get_package_with_role();
@endphp
<div class="sidebar-profile">
    <button class="close-sidebar">
        <i class="fa fa-times"></i>
    </button>
    <div class="sidebar-profile-thumb">
        <img src="{{ rrt_get_thumb_studio() }}" alt="">
        <div class="sidebar-profile-text">
            <span>{{ rrt_get_fullname() }}</span>
            <a href="{{ rrt_route('public/auth/logout') }}">{{__('Logout')}}</a>
        </div>
    </div>
    <div class="sidebar-profile-cta">
        <a target="_blank" href="{{ rrt_route('public/home/index') }}">
            <i class="fa fa-home"></i>
            <span>{{__('Go to Home')}}</span>
        </a>
    </div>
</div>
@if(in_array('dashboard', $menuPermissions))
<li>
    <a href="{{ rrt_get_route_studio() }}" aria-expanded="true">
        <i class="fa fa-home"></i>
        <span>{{__('Dashboard')}}</span>
    </a>
</li>
@endif
@if(in_array('ai_services', $menuPermissions))
<li>

    <a href="javascript:void(0)" aria-expanded="true">
        <i class="fa fa-music"></i>
        <span> {{__('My A.I Services')}}</span>
        <span class="float-right arrow"><i class="fa fa-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        @if(in_array('ai_mastering', $menuPermissions))
            @php
             $mustPay = 0;
             if ($packageRole && isset($packageRole['packages'])) {
                        $package = $packageRole['packages']->where('pivot.ai_id', 1)->first();
                        if ($package) {
                            $mustPay = $package->pivot->price??0;
                        }
            }
            @endphp
                <li><a href="{{ rrt_route('public/studio/mastering/index') }}"><i class="fa fa-folder-o"></i><span>{{__('A.I Music Mastering')}}</span></a></li>
        @endif
        @if(in_array('ai_recognition', $menuPermissions))
                @php
                    $mustPay = 0;
                    if ($packageRole && isset($packageRole['packages'])) {
                               $package = $packageRole['packages']->where('pivot.ai_id', 2)->first();
                               if ($package) {
                                   $mustPay = $package->pivot->price??0;
                               }
                   }
                @endphp
                <li><a href="{{ rrt_route('public/studio/recognition/index') }}"><i class="fa fa-folder-o"></i><span>{{__('A.I Music Recognition')}}</span></a></li>
        @endif
{{--        @if(in_array('ai_mastering_pay', $menuPermissions))--}}
{{--            <li><a href="#"><i class="fa fa-folder-o"></i><span>A.I Music Mastering (Pay to use $1)</span></a></li>--}}
{{--        @endif--}}
{{--        @if(in_array('ai_recognition_pay', $menuPermissions))--}}
{{--            <li><a href="#"><i class="fa fa-folder-o"></i><span>A.I Music Recognition (Pay to use $1)</span></a></li>--}}
{{--        @endif--}}
    </ul>
</li>
@endif
@if(in_array('my_contents', $menuPermissions))
<li>
    <a href="javascript:void(0)" aria-expanded="true">
        <i class="fa fa-music"></i>
        <span>{{__('My Content')}}</span>
        <span class="float-right arrow"><i class="fa fa-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        @if(in_array('tracks', $menuPermissions))
            <li><a href="{{ rrt_route('public/studio/content/index') }}"><i
                        class="fa fa-folder-o"></i><span>{{__('Tracks')}}</span></a></li>
        @endif
        @if(in_array('sound_kits', $menuPermissions))
            <li><a href="{{ rrt_route('public/studio/content/index', ['type' => 'soundKit']) }}"><i
                        class="fa fa-folder-o"></i><span>{{__('Sound Kit')}}</span></a></li>
        @endif
        @if(in_array('single', $menuPermissions))
                <li><a href="{{ rrt_route('public/studio/release/index', ['type' => 'single']) }}"><i
                        class="fa fa-folder-o"></i><span>{{__('Single Release')}}</span></a></li>
        @endif
        @if(in_array('album', $menuPermissions))
                <li><a href="{{ rrt_route('public/studio/release/index', ['type' => 'album']) }}"><i
                        class="fa fa-folder-o"></i><span>{{__('Album Release')}}</span></a></li>
        @endif
        {{-- <li><a href="#"><i class="fa fa-folder-o"></i><span>Albums</span></a></li>
        <li><a href="#"><i class="fa fa-folder-o"></i><span>Sound Kits</span></a>
        </li> --}}
    </ul>
</li>
@endif
@if(in_array('my_finances', $menuPermissions))
<li>
    <a href="javascript:void(0)" aria-expanded="true">
        <i class="fa fa-money"></i>
        <span>{{__('My Finances')}}</span>
        <span class="float-right arrow"><i class="fa fa-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        @if(in_array('my_purchase', $menuPermissions))
        <li>
            <a href="{{ rrt_route('public/studio/order/index') }}" aria-expanded="true">
                <i class="fa fa-shopping-cart"></i>
                <span>{{__('My Purchased')}}</span>
            </a>
        </li>
        @endif
        @if(in_array('payment_account', $menuPermissions))
        <li><a href="{{ rrt_route('public/studio/finances/index') }}"><i
                    class="fa fa-folder-o"></i><span>{{__('Payment Accounts')}}</span></a></li>
        @endif
        @if(in_array('wallet', $menuPermissions))
        <li>
            <a href="{{ rrt_route('public/studio/transaction/index') }}" aria-expanded="true">
                <i class="fa fa-folder-o"></i>
                <span>{{__('Wallet')}}</span>
            </a>
        </li>
        @endif
        @if(in_array('withdraw', $menuPermissions))
        <li><a href="{{ rrt_route('public/studio/activity/index') }}"><i
                    class="fa fa-folder-o"></i><span>{{__('Withdraw')}}</span></a>
        </li>
        @endif
    </ul>
</li>
@endif
@if(in_array('my_sales', $menuPermissions))
<li>
    <a href="javascript:void(0)" aria-expanded="true">
        <i class="fa fa-line-chart"></i>
        <span>{{__('My Sales')}}</span>
        <span class="float-right arrow"><i class="fa fa-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        @if(in_array('track_and_sound_kits_sale', $menuPermissions))
            <li>
                <a href="{{ rrt_route('public/studio/sale/index') }}" aria-expanded="true">
                    <i class="fa fa-magic"></i>
                    <span>{{__('Track & Sound Kit Sales')}} </span>
                </a>
            </li>
        @endif
        @if(in_array('single_and_album_sale', $menuPermissions))
            <li>
                <a href="#" aria-expanded="true">
                    <i class="fa fa-line-chart"></i>
                    <span>{{__('Single & Album Sales')}}</span>
                </a>
            </li>
        @endif
    </ul>
</li>
@endif
@if(in_array('digital_distribution', $menuPermissions))
<li>
{{--    <a href="{{ rrt_route('public/join/distribution/index') }}" aria-expanded="true">--}}
    <a href="javascript:void(0)" aria-expanded="true">
        <i class="fa fa-trophy"></i>
        <span>{{__('Digital Distribution')}}</span>
         <span class="float-right arrow"><i class="fa fa-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        <li>
            <a href="#">
                <i class="fa fa-folder-o"></i><span>{{__('Platform Streams')}}</span>
            </a>
        </li>
    </ul>
</li>
@endif
@if(in_array('digital_distribution_signup', $menuPermissions))
    <li>
            <a href="{{ rrt_route('public/join/distribution/index') }}" aria-expanded="true">
                <i class="fa fa-trophy"></i>
                <span>{{__('Digital Distribution')}}</span>
            </a>
    </li>
@endif






@if(in_array('publishing', $menuPermissions))
<li>
{{--    <a href="{{ rrt_route('public/join/publishing/index') }}" aria-expanded="true">--}}
    <a href="javascript:void(0)" aria-expanded="true">
        <i class="fa fa-headphones"></i>
        <span>{{__('Publishing')}}</span>
         <span class="float-right arrow"><i class="fa fa-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        <li>
            <a href="#">
                <i class="fa fa-folder-o"></i><span>{{__('Online Statistics')}}</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa fa-folder-o"></i><span>{{__('Offline Statistics')}}</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa fa-folder-o"></i><span>{{__('Other Statistics')}}</span>
            </a>
        </li>
    </ul>
</li>
@endif
@if(in_array('publishing_signup', $menuPermissions))
    <li>
            <a href="{{ rrt_route('public/join/publishing/index') }}" aria-expanded="true">
            <i class="fa fa-headphones"></i>
            <span>{{__('Publishing')}}</span>
        </a>
    </li>
@endif
{{--<li>--}}
{{--    <a href="{{ rrt_route('public/studio/bulletinBoard/index') }}" aria-expanded="true">--}}
{{--        <i class="fa fa-edit"></i>--}}
{{--        <span>Bulletin board</span>--}}
{{--    </a>--}}
{{--</li>--}}
{{--<li>--}}
{{--    <a href="{{ rrt_route('public/studio/platforms/index') }}" aria-expanded="true">--}}
{{--        <i class="fa fa-bar-chart"></i>--}}
{{--        <span>Platforms</span>--}}
{{--    </a>--}}
{{--</li>--}}
@if(in_array('promote', $menuPermissions))
<li>
    <a href="javascript:void(0)" aria-expanded="true" class="btn-comming-soon">
        <i class="fa fa-arrow-up"></i>
        <span>{{__('Promote')}}</span>
        <span class="float-right arrow"><i class="fa fa-chevron-down"></i></span>
    </a>
    <ul class="submenu">
        @if(in_array('promote_track_or_sound_kits', $menuPermissions))
        <li>
            <a href="#">
                <i class="fa fa-folder-o"></i><span>{{__('Promote Track or Sound Kit')}}</span>
            </a>
        </li>
        @endif
        @if(in_array('promote_single_or_album', $menuPermissions))
        <li>
            <a href="#">
                <i class="fa fa-folder-o"></i><span>{{__('Promote Single or Album')}}</span>
            </a>
        </li>
        @endif
    </ul>
</li>
@endif
@if(rrt_role_buy_package())
<li>
    <a href="{{ rrt_route('public/join/seller/index') }}" aria-expanded="true">
        <i class="fa fa-magic"></i>
        <span>{{__('Start Selling')}}</span>
    </a>
</li>
@endif
@if(in_array('account_setting', $menuPermissions))
<li>
    <a href="{{ rrt_route('public/studio/account/index') }}" aria-expanded="true">
        <i class="fa fa-user"></i>
        <span>{{__('Account Setting')}}</span>
    </a>
</li>
@endif
