@extends('studio.main')
@section('title', __('Account Setting'))
@section('page_title', __('Account Setting'))
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-account">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card-body-sidebar">
                            <ul>
                                @include("{$pathViewController}.elements.account_menu", [
                                    'list_route' => [
                                        'route_account' => [
                                            'url' => rrt_route($controllerName . '/index'),
                                            'name' => __('Biography'),
                                        ],
                                        [
                                            'url' => rrt_route($controllerName . '/credentials'),
                                            'name' => __('Credential'),
                                        ],
                                        [
                                            'url' => rrt_route($controllerName . '/social'),
                                            'name' => __('SNS'),
                                        ],
                                        [
                                            'url' => rrt_route($controllerName . '/subscription'),
                                            'name' => __('Subscription'),
                                        ],
                                        [
                                            'url' => rrt_route($controllerName . '/languages'),
                                            'name' => __('Languages'),
                                        ],
                                        // [
                                        //     'url' => rrt_route($controllerName . '/payment'),
                                        //     'name' => 'Payment Methods',
                                        // ],
                                        // [
                                        //     'url' => rrt_route($controllerName . '/addresses'),
                                        //     'name' => 'Addresses ',
                                        // ],
                                    ],
                                ])
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card-inner">
                            <div class="card-inner-head">
                                <h4 class="card_title mb-0">@yield('account_title', __('Profile'))</h4>
                                <p class="text-muted">@yield('account_desc', __('Manage your 1N2 MUSIC profile.'))</p>
                            </div>
                            <div class="card-inner-body">
                                @yield('account_content')
                            </div>
                            @yield('account_footer')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
