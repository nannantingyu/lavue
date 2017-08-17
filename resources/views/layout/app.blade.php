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
        <script src="{{ URL::asset('js/jquery.js') }}"></script>
        <script src="{{ URL::asset('js/show.js') }}"></script>
        <link rel="stylesheet" href="{{ URL::asset('css/style.css') }}">
        @yield('css')
        @yield('js')
    </head>
    <body>
    @php
    @endphp
        @include('public.header')
        @yield('content')
        @include('public.footer')
    </body>
</html>