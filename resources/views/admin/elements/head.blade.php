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
    <title>@yield('title', 'OTT Admin')</title>
    <!--=========================*
                Favicon
    *===========================-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin/images') }}/favicon.png">
    <!--=========================*
            Bootstrap Css
    *===========================-->
    <link rel="stylesheet" href="{{ asset('admin/vendors/rrt/css') }}/plugin.css">
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
    <link rel="stylesheet" href="{{ asset('admin/vendors/font-awesome6pro/css') }}/all.css">
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
              Flag Icons
    *===========================-->
    <link href="{{ asset('admin/css') }}/flag-icon.min.css" rel="stylesheet" />
    <!--=========================*
              Modernizer
    *===========================-->
    <script src="{{ asset('studio/js') }}/modernizr-2.8.3.min.js"></script>
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
               AM Chart
    *===========================-->
    <link rel="stylesheet" href="vendors/am-charts/{{ asset('admin/css') }}/am-charts.css" type="text/css"
        media="all" />
    <!--=========================*
               Morris Css
    *===========================-->
    <link rel="stylesheet" href="vendors/charts/morris-bundle/morris.css">
    <!--=========================*
            Google Fonts
    *===========================-->
    <!-- Font USE: 'Roboto', sans-serif;-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- HTML5 shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.css"
        integrity="sha512-3uVpgbpX33N/XhyD3eWlOgFVAraGn3AfpxywfOTEQeBDByJ/J7HkLvl4mJE1fvArGh4ye1EiPfSBnJo2fgfZmg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('admin/vendors/rrt/css') }}/style_plugin.css?ver={{ time() }}">
    <!--=========================*
               Loading Css
    *===========================-->
    <link rel="stylesheet" href="{{ asset('admin/css') }}/loading.css">
    <script>
        var _token = 'NN2qLcQhx0Cv4lMh5Wl8yaKE7XXEdhqtl2VyI22q';
        var base_domain = "{{ env('APP_URL') }}";
        var api_domain = "https://vaiaodaiduyen.com/";
        var assets_url = "https://quantri.vaiaodaiduyen.com/public/assets/";
        var cke_conf_path = assets_url + '/backend/plugins/ckeditor';
        var default_currency = 'đ';
        var default_weight_unit = "kg";
        var storage_url = '';
    </script>
    @stack('css')
