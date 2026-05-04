<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{__('1N2 MUSIC')}}</title>
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
    @stack('css')
</head>

<body>
    <p>hello</p>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('public/js') }}/slick.js?ver={{ time() }}"></script>
    <script src="{{ asset('public/js') }}/slider.js?ver={{ time() }}"></script>
    @stack('srcipt')

</body>

</html>
