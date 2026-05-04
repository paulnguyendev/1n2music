<!doctype html>
<html lang="zxx">

<head>
    <!--=========================*
                Met Data
    *===========================-->
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Falr - Bootstrap 4 Admin Dashboard Template">
    <!--=========================*
              Page Title
    *===========================-->
    <title>{{__('Admin Login')}}</title>
    <!--=========================*
                Favicon
    *===========================-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin/images') }}/favicon.png">
    <!--=========================*
            Bootstrap Css
    *===========================-->
    <link rel="stylesheet" href="{{ asset('admin/css') }}/bootstrap.min.css">
    <!--=========================*
              Custom CSS
    *===========================-->
    <link rel="stylesheet" href="{{ asset('admin/css') }}/style.css?ver={{ time() }}">
    <!--=========================*
               Owl CSS
    *===========================-->
    <link href="{{ asset('admin/css') }}/owl.carousel.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/css') }}/owl.theme.default.min.css" rel="stylesheet" type="text/css">
    <!--=========================*
            Font Awesome
    *===========================-->
    <link rel="stylesheet" href="{{ asset('admin/css') }}/font-awesome.min.css">
    <!--=========================*
             Themify Icons
    *===========================-->
    <link rel="stylesheet" href="{{ asset('admin/css') }}/themify-icons.css">
    <!--=========================*
               Ionicons
    *===========================-->
    <link href="{{ asset('admin/css') }}/ionicons.min.css" rel="stylesheet" />
    <!--=========================*
              EtLine Icons
    *===========================-->
    <link href="{{ asset('admin/css') }}/et-line.css" rel="stylesheet" />
    <!--=========================*
              Feather Icons
    *===========================-->
    <link href="{{ asset('admin/css') }}/feather.css" rel="stylesheet" />
    <!--=========================*
              Modernizer
    *===========================-->
    <script src="{{ asset('admin/js') }}/modernizr-2.8.3.min.js"></script>
    <!--=========================*
               Metis Menu
    *===========================-->
    <link rel="stylesheet" href="{{ asset('admin/css') }}/metisMenu.css">
    <!--=========================*
               Slick Menu
    *===========================-->
    <link rel="stylesheet" href="{{ asset('admin/css') }}/slicknav.min.css">
    <!--=========================*
              Flag Icons
    *===========================-->
    <link href="{{ asset('admin/css') }}/flag-icon.min.css" rel="stylesheet" />
    <!--=========================*
            Google Fonts
    *===========================-->
    <!-- Font USE: 'Roboto', sans-serif;-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">
        <link href="{{ asset('admin/vendors/toastr/css') }}/toastr.min.css" rel="stylesheet" /> 
    <!-- HTML5 shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="login-form">
                    @yield('content')
                </div>
                <!--login-form-->
            </div>
            <!--row-->
        </div>
        <!--container-fluid-->
    </div>
    <!--wrapper-->
    <!--=========================*
            Scripts
*===========================-->
    <!-- Jquery Js -->
    <script src="{{ asset('admin/js') }}/jquery.min.js"></script>
    <script src="{{ asset('admin/js') }}/jquery-validate.js"></script>
    <script src="{{ asset('admin/vendors/toastr/js') }}/toastr.min.js"></script>
    <script src="{{ asset('admin/js') }}/form.js?ver={{ time() }}"></script>
    <!-- bootstrap 4 js -->
    <script src="{{ asset('admin/js') }}/popper.min.js"></script>
    <script src="{{ asset('admin/js') }}/bootstrap.min.js"></script>
    <!-- Owl Carousel Js -->
    <script src="{{ asset('admin/js') }}/owl.carousel.min.js"></script>
    <!-- Metis Menu Js -->
    <script src="{{ asset('admin/js') }}/metisMenu.min.js"></script>
    <!-- SlimScroll Js -->
    <script src="{{ asset('admin/js') }}/jquery.slimscroll.min.js"></script>
    <!-- Slick Nav -->
    <script src="{{ asset('admin/js') }}/jquery.slicknav.min.js"></script>
    <!-- Fancy Box Js -->
    <script src="{{ asset('admin/js') }}/jquery.fancybox.pack.js"></script>
    <!-- Variable Js -->
    <script>
        let notification = {
            password_min : "Password at least 6 characters"
        };
    </script>
    <!-- Main Js -->
    <script src="{{ asset('admin/js') }}/main.js"></script>

    @stack('script')
</body>

</html>
