<div class="header-area">
    <!--======================*
                   Logo
        *=========================-->
    {{-- <div class="header-area-left">
        <a href="{{ rrt_get_route_studio() }}" class="logo">
            <span>1N2 Music</span>
            <i>
                <img src="{{ asset('public/images/logo_img2.png') }}" alt="" height="22">
            </i>
        </a>
    </div> --}}
    <!--======================*
                  End Logo
        *=========================-->
    <div class="row align-items-center header_right">
        <!--==================================*
                     Navigation and Search
            *====================================-->
        <div class="col-md-6 d_none_sm d-flex align-items-center">
            {{-- <div class="studio-logo pull-left btn-gradient">
              
                <a href="{{ rrt_route('public/studio/home/index') }}">
                    <img src="{{ asset('public/style2/img/icon_studio.svg') }}" alt="">
                    <span>{{ __('Studio') }}</span>
                </a>
             
            </div> --}}
        </div>
        <!--==================================*
                     End Navigation and Search
            *====================================-->
        <!--==================================*
                     Notification Section
            *====================================-->
        <div class="col-md-6 col-sm-12 p-3">
            <ul class="notification-area pull-right col-12 d-flex justify-content-end pr-50 align-items-center">
                <li class="mobile_menu_btn">
                    <span class="nav-btn pull-left d_none_lg">
                        <button class="open-left waves-effect">
                            <i class="fa fa-bars"></i>
                        </button>
                    </span>
                </li>
                <li>
                    <p id="total_banlance_nav" data-url="{{ rrt_route('public/studio/transaction/getBalanceTotal') }}">
                        {{__('Total balance')}}: <span></span></p>
                </li>
                <li class="dropdown">
                    <i class="fa fa-bell dropdown-toggle" data-toggle="dropdown"><span
                            class="badge bg-danger rounded-pill">{{ count($notices) }}</span></i>
                    <div class="dropdown-menu bell-notify-box notify-box">
                        <span class="notify-title">{{__('You have')}} {{ count($notices) }} {{__('new notifications')}} </span>
                        <div class="nofity-list">
                            @if ($notices)
                                @foreach ($notices as $notice)
                                @php
                                    $type = $notice->type ?? '';
                                    $icon = $type == 'follow' ? 'fa fa-heart bg_danger' : 'fa fa-comments bg_info';
                                    $name = rrt_get_fullname_by_user($notice->users ?? null);
                                    $trackTitle = $notice->tracks->name ?? '';
                                    
                                    if ($type == 'follow') {
                                        $title = $name . ' added you to Favorites';
                                    } elseif ($type == 'favourite') {
                                        $title = $name . ' liked your Track "' . $trackTitle . '"';
                                    } else {
                                        $title = $name . ' left a Comment on your Track "' . $trackTitle . '"';
                                    }
                                @endphp
                                    <a href="javascript:;" class="notify-item notify-item-id-{{ $notice->id }} notify-action-mask-read" data-id="{{$notice->id}}" data-action="{{ rrt_route('public/studio/notice/maskAsRead',['id' => $notice->id]) }}">
                                        <div class="notify-thumb"><i class="{{$icon}}"></i></div>
                                        <div class="notify-text">
                                            <h3 class="limit-text-2"> {{$title}} </h3>
                                            <span> {{rrt_get_date_hrd($notice->created_at ?? '')}} </span>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                        @if( count($notices) )
                        <div class="notify-action d-block mt-3 text-center py-4">
                            <a id="notify-action-mask-all-read"
                            data-action="{{ rrt_route('public/studio/notice/maskAsReadAll') }}"
                            href="javascript:;" class="btn btn-sm btn-secondary text-white">{{ __('Marked as read') }}</a>
                        </div>
                        @endif
                    </div>
                </li>
                {{-- <li class="user-dropdown">
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src=" {{ rrt_get_thumb_studio() }}" alt="" class="img-fluid">
                        </button>
                        <div class="dropdown-menu dropdown_user" aria-labelledby="dropdownMenuButton">
                            <div class="dropdown-header d-flex flex-column align-items-center">
                                <div class="user_img mb-3">
                                    <img src="{{ rrt_get_thumb_studio() }}" alt="User Image">
                                </div>
                                <div class="user_bio text-center">
                                    <p class="name font-weight-bold mb-0">{{ rrt_get_fullname() }}</p>
                                    <p class="email text-muted mb-3"><a href="mailto:{{ rrt_get_user_login('email') }}"
                                            class="p-0">{{ rrt_get_user_login('email') }}</a></p>
                                </div>
                            </div>
                            <a class="dropdown-item" href="{{ rrt_route('public/studio/account/index') }}"><i
                                    class="fa fa-user"></i> Account settings</a>
                            <span role="separator" class="divider"></span>
                            <a class="dropdown-item" href="{{ rrt_route('public/auth/logout') }}"><i
                                    class="fa fa-power-off"></i>Logout</a>
                        </div>
                    </div>
                </li> --}}
            </ul>
        </div>
        <!--==================================*
                     End Notification Section
            *====================================-->
    </div>
</div>
@push('script')
    <script>
        $(document).ready(function() {
            const total_banlance_nav = $('#total_banlance_nav');
            let url = total_banlance_nav.data('url');
            $.ajax({
                type: "Get",
                url: url,
                dataType: "json",
                success: function(response) {
                    let total = 0;
                    total = response.total_format;
                    $('#total_banlance_nav span').text(total)
                }
            });
            //Notification actions
            jQuery('#notify-action-mask-all-read').on('click',function(){
                let action = jQuery(this).data('action');
                jQuery.ajax({
                    url: action,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                       jQuery('.nofity-list').empty();
                       window.location.reload();
                    }
                });
            });
            jQuery('.notify-action-mask-read').on('click',function(){
                let noti_id = jQuery(this).data('id');
                let action = jQuery(this).data('action');
                jQuery.ajax({
                    url: action,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        noti_id: noti_id,
                    },
                    success: function(response) {
                       jQuery('.notify-item-id-'+noti_id).remove();
                       window.location.reload();
                    }
                });
            });
        });
    </script>
@endpush
