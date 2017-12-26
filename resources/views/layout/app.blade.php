<html>
    <head>
        <title>
            @if(isset($seo_title))
            {{ $seo_title }}
            @else
            {{ $default_seo_title }}
            @endif
        </title>
        <meta charset="utf-8">
        @if(isset($seo_keywords))
        <meta name="keywords" content="{{ $seo_keywords }}">
        @else
        <meta name="keywords" content="{{ $default_seo_keywords }}">
        @endif
        @if(isset($seo_description))
        <meta name="description" content="{{ $seo_description }}">
        @else
        <meta name="description" content="{{ $default_seo_description }}">
        @endif
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="HandheldFriendly" content="true">
        <script src="{{ URL::asset('js/jquery.js') }}"></script>
        <script src="{{ URL::asset('js/show.js') }}"></script>
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-theme.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/style.css') }}">
        @yield('css')
        @yield('js')
    </head>
    <body>
        @include('public.header')
        @yield('content')
        @include('public.footer')
    </body>
</html>