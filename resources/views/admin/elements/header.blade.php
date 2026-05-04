 <div class="header-area">
     <!--======================*
                   Logo
        *=========================-->
     <div class="header-area-left">
         <a href="{{rrt_get_route_admin()}}" class="logo">
             <span>{{__('1N2 MUSIC ADMIN')}}</span>
             <i>
                 <img src="{{ asset('public/images/logo_img2.png') }}" alt="" height="22">
             </i>
         </a>
     </div>
     <!--======================*
                  End Logo
        *=========================-->
     <div class="row align-items-center header_right">
         <!--==================================*
                     Navigation and Search
            *====================================-->
         <div class="col-md-6 d_none_sm d-flex align-items-center">
             <div class="nav-btn button-menu-mobile pull-left">
                 <button class="open-left waves-effect">
                     <i class="ion-android-menu"></i>
                 </button>
             </div>
         </div>
         <!--==================================*
                     End Navigation and Search
            *====================================-->
         <!--==================================*
                     Notification Section
            *====================================-->
         <div class="col-md-6 col-sm-12">
             <ul class="notification-area pull-right">
{{--                 <li class="language-dropdown" style="display: inline-block!important">--}}
{{--                     <button class="btn dropdown-toggle" type="button" id="languageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                         Language--}}
{{--                     </button>--}}
{{--                     <div class="dropdown-menu" aria-labelledby="languageDropdown">--}}
{{--                         <a class="dropdown-item language-item" href="#" data-language="en">English</a>--}}
{{--                         <a class="dropdown-item language-item" href="#" data-language="ko">Korean</a>--}}
{{--                     </div>--}}
{{--                 </li>--}}
                 <li class="mobile_menu_btn">
                     <span class="nav-btn pull-left d_none_lg">
                         <button class="open-left waves-effect">
                             <i class="ion-android-menu"></i>
                         </button>
                     </span>
                 </li>
                 <li>
                    <a target="_blank" href="{{rrt_route('public/home/index')}}">
                        <i class="feather ft-home"></i>
                        <span>{{__('Go to Main site')}}</span>
                    </a>
                 </li>
                 <li class="user-dropdown">
                     <div class="dropdown">
                         <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton"
                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                             <img src="{{ asset('studio/images') }}/user.jpg" alt="" class="img-fluid">
                         </button>
                         <div class="dropdown-menu dropdown_user" aria-labelledby="dropdownMenuButton">
                             <div class="dropdown-header d-flex flex-column align-items-center mt-4">
                                 <div class="user_bio text-center">
                                     <p class="name font-weight-bold mb-0">{{rrt_get_fullname()}}</p>
                                     <p class="email text-muted"><a href="mailto:{{rrt_get_admin_login('email')}}"
                                             class="p-0">{{rrt_get_admin_login('email')}}</a></p>
                                 </div>
                             </div>
                             <span role="separator" class="divider"></span>
                             <a class="dropdown-item" href="{{ rrt_route('admin/auth/logout') }}"><i class="ti-power-off"></i>{{__('Logout')}}</a>
                         </div>
                     </div>
                 </li>
             </ul>
         </div>
         <!--==================================*
                     End Notification Section
            *====================================-->
     </div>
 </div>
