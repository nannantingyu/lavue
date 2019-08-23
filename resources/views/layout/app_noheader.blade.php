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
        <meta name="keywords" content="{{ $default_seo_title }}">
        @endif
        @if(isset($seo_description))
        <meta name="description" content="{{ $seo_description }}">
        @else
        <meta name="description" content="{{ $default_seo_description }}">
        @endif
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="HandheldFriendly" content="true">
        <meta name="shenma-site-verification" content="fd28a192f28f88f803811ece04994b7c_1514423713">
        <script src="{{ URL::asset('js/jquery.js') }}"></script>
        <script src="{{ URL::asset('js/show.js') }}"></script>
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-theme.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/style.css') }}">
        @yield('css')
        @yield('js')
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
        @yield('content')
    </body>
</html>