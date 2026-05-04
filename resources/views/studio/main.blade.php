<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    @include('studio.elements.head')
    @stack('css')
</head>

<body class="@yield('body_class', 'body-studio')">
    <!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
    <!--=========================*
         Page Container
*===========================-->
    <div id="page-container" class="light-sidebar">
        <!--==================================*
               Header Section
    *====================================-->
        @include('studio.elements.header')
        <!--==================================*
               End Header Section
    *====================================-->
        <!--=========================*
             Side Bar Menu
    *===========================-->
        <div class="sidebar_menu">
            <div class="menu-inner">
                <div id="sidebar-menu">
                    <!--=========================*
                           Main Menu
                *===========================-->
                    <ul class="" id="sidebar_menu">
                        @include('studio.elements.sidebar_menu')
                    </ul>
                    <!--=========================*
                          End Main Menu
                *===========================-->
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <!--=========================*
           End Side Bar Menu
    *===========================-->
        <!--==================================*
               Main Content Section
    *====================================-->
        <div class="main-content page-content">
            <!--==================================*
                   Main Section
        *====================================-->
            <div class="main-content-inner">
                <div class="row mb-4">
                    <div class="col-md-12 grid-margin">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div class="d-flex align-items-center dashboard-header flex-wrap mb-3 mb-sm-0">
                                <h5 class="mr-4 mb-0 font-weight-bold">@yield('page_title', __('Dashboard'))</h5>
                                <div class="d-flex align-items-baseline dashboard-breadcrumb">
                                    @if(isset($aiStudio) && $aiStudio == true)
                                        <p class="text-muted mb-0 mr-1 hover-cursor">{{__($textPriceRole)}}</p>
                                        <span> {{ !empty($usage_count) ? "(". $usage_count . "  songs left)" : '' }} </span>
                                    @else
                                        <p class="text-muted mb-0 mr-1 hover-cursor">{{__('1N2 Music Studio')}}</p>
                                        <i class="mdi mdi-chevron-right mr-1 text-primary"></i>
                                        <p class="text-muted mb-0 mr-1 hover-cursor">@yield('page_title', __('Dashboard'))</p>
                                    @endif
                                </div>
                            </div>
                            @yield('buttons')
                        </div>
                    </div>
                </div>
                @yield('content')
            </div>
            <!--==================================*
                   End Main Section
        *====================================-->
        </div>
        <!--=================================*
           End Main Content Section
    *===================================-->
        <!--=================================*
                  Footer Section
    *===================================-->
        @include('studio.elements.footer')
        <!--=================================*
                End Footer Section
    *===================================-->
    </div>
    <!--=========================*
        End Page Container
*===========================-->
    @include('studio.elements.script')

    <script>
        $('.close-sidebar').on('click', function(event) {
            event.preventDefault();
            $("body").toggleClass("side_collapsed");
        });
    </script>
</body>

</html>
