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
        <script src="https://cdn.bootcss.com/jquery/2.1.4/jquery.min.js"></script>
        <script src="{{ URL::asset('js/show.js') }}?1"></script>
        <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="{{ URL::asset('css/style.css') }}?4">
        <script>
            var _hmt = _hmt || [];
            (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?07aae959e6155a4d795d6509dfa44f08";
            var s = document.getElementsByTagName("script")[0]; 
            s.parentNode.insertBefore(hm, s);
            })();
        </script>

    </head>
    <body>
        @include('public.header')
        @yield('content')
        @include('public.footer')
        @yield('css')
        @yield('js')
    </body>
</html>
