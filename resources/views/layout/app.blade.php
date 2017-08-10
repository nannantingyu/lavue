<html>
    <head>
        <title>五常大米-{{ $app_name }}</title>
        <meta charset="utf-8">
        <meta name="keywords" content="五常大米">
        <meta name="description" content="五常大米专卖">
        <script src="{{ URL::asset('js/jquery.js') }}"></script>
        <script src="{{ URL::asset('js/show.js') }}"></script>
        <link rel="stylesheet" href="{{ URL::asset('css/style.css') }}">
        @yield('css')
        @yield('js')
    </head>
    <body>
        @include('component.header')
        @yield('content')
        @include('component.footer')
    </body>
</html>