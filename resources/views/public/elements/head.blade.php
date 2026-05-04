 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 {{-- <meta name="viewport" content="width=1200px, initial-scale=1.0"> --}}
 <meta http-equiv="X-UA-Compatible" content="ie=edge">
 <title>@yield('title', rrt_get_config_core('title', 'main'))</title>
 <link rel="shortcut icon" href="{{ asset('public/images') }}/favicon.png" type="image/x-icon">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
 <link rel="stylesheet" href="{{ asset('admin/css/font-awesome.min.css') }}">
 <link rel="stylesheet" href="{{ asset('public/css') }}/all.css?ver={{ time() }}">
 <link rel="stylesheet" href="{{ asset('public/css') }}/slick.css?ver={{ time() }}">
 <link rel="stylesheet" href="{{ asset('public/css') }}/style.css?ver={{ time() }}">
 <link rel="stylesheet" href="{{ asset('public/css') }}/app.css?ver={{ time() }}">
 <link rel="stylesheet" href="{{ asset('public/css') }}/responsive.css?ver={{ time() }}">
 @stack('css')
