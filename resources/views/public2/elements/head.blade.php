<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>@yield('meta_title', '1N2 MUSIC')</title>
<meta name="description" content="@yield('meta_description', '1N2 MUSIC - Digital music distribution and publishing platform')">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="@yield('meta_title', '1N2 MUSIC')">
<meta property="og:description" content="@yield('meta_description', '1N2 MUSIC - Digital music distribution and publishing platform')">
<meta property="og:image" content="@yield('meta_image', asset('public/style2/img/1N2Logo 2.png'))">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ url()->current() }}">
<meta property="twitter:title" content="@yield('meta_title', '1N2 MUSIC')">
<meta property="twitter:description" content="@yield('meta_description', '1N2 MUSIC - Digital music distribution and publishing platform')">
<meta property="twitter:image" content="@yield('meta_image', asset('public/style2/img/1N2Logo 2.png'))">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">

<link rel="stylesheet" href="{{ asset('admin/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/css') }}/all.css?ver={{ time() }}">
<link rel="stylesheet" href="{{ asset('public/css') }}/slick.css?ver={{ time() }}">
<link rel="stylesheet" href="{{ asset('public/style2/css') }}/style.css?ver={{ time() }}">
<link rel="stylesheet" href="{{ asset('public/style2/css') }}/app.css?ver={{ time() }}">
<link rel="stylesheet" href="{{ asset('public/style2/css') }}/responsive.css?ver={{ time() }}">
<link rel="shortcut icon" href="{{ asset('public/style2/img/1N2Logo 2.png') }}" type="image/x-icon">
