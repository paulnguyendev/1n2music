<!DOCTYPE html>
<html lang="en">
<head>
    @include('public.elements.head')
</head>
<body class="@yield('body_class','rrt-body')">
     @include('public.shared.header')
    <div id="content">
        @yield('content')
    </div>
    @include('public.shared.footer')
    @include('public.shared.loading')
    @include('public.elements.script')
</body>
</html>
