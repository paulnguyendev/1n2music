<!DOCTYPE html>
<html class="no-js" lang="zxx">
<head>
    @include('admin.elements.head')
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
        @include('admin.elements.header')
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
                    <ul class="metismenu" id="sidebar_menu">
                        @include('admin.elements.sidebar_menu')
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
                                <h5 class="mr-4 mb-0 font-weight-bold">@yield('page_title', 'Dashboard')</h5>
                                <div class="d-flex align-items-baseline dashboard-breadcrumb">
                                    <p class="text-muted mb-0 mr-1 hover-cursor">{{__('1N2 MUSIC')}}</p>
                                    <i class="mdi mdi-chevron-right mr-1 text-primary"></i>
                                    <p class="text-muted mb-0 mr-1 hover-cursor">@yield('page_title', 'Dashboard')</p>
                                </div>
                            </div>
                            <div class="buttons">
                                @yield('buttons')
                              
                            </div>
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
                Loading Spinner Section
    *===================================-->
        <div class="overlay"></div>
        <div id="loading-spinner">
            <div class="spinner"></div>
        </div>
        <!--=================================*
                End Loading Spinner Section
    *===================================-->
        <!--=================================*
                  Footer Section
    *===================================-->
        @include('admin.elements.footer')
        <!--=================================*
                End Footer Section
    *===================================-->
    </div>
    <!--=========================*
        End Page Container
*===========================-->
    @include('admin.elements.script')
</body>
</html>
