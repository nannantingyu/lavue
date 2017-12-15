@extends('layout.app')
@section('content')
    <div class="main-content detail-main">
        <div class="content-left">
            <h2>{{ $article->title }}</h2>
            <p class="right-con">
                <span>{{ $article->author }}</span>
                <span>{{ date("Y-m-d H:i", strtotime($article->publish_time)) }}</span>
                <span>{{ $article->favor }}</span>
            </p>
            {!!  $article->body !!}
        </div>
        <div class="content-right">
            <h2 class="sub-title left-con">热门阅读</h2>
            <ul class="news">
                @foreach($hot as $art)
                    <li><a href="{{  $base_url }}blog_{{ $art->id }}">{{ $art->title }}</a><span>{{ date("m-d H:i", strtotime($art->publish_time)) }}</span></li>
                @endforeach
            </ul>
        </div>
        <div class="content-right">
            <h2 class="sub-title left-con">相关推荐</h2>
            <ul class="news">
                @foreach($related as $art)
                    <li><a href="{{  $base_url }}blog_{{ $art->id }}">{{ $art->title }}</a><span>{{ date("m-d H:i", strtotime($art->publish_time)) }}</span></li>
                @endforeach
            </ul>
        </div>
        <div class="content-right">
            <h2 class="sub-title left-con">猜你喜欢</h2>
            <ul class="news">
                @foreach($favor as $art)
                    <li><a href="{{  $base_url }}blog_{{ $art->id }}">{{ $art->title }}</a><span>{{ date("m-d H:i", strtotime($art->publish_time)) }}</span></li>
                @endforeach
            </ul>
        </div>
        <div class="content-right">
            <h2 class="sub-title left-con">最新进展</h2>
            <ul class="news">
                @foreach($latest as $art)
                    <li><a href="{{  $base_url }}blog_{{ $art->id }}">{{ $art->title }}</a><span>{{ date("m-d H:i", strtotime($art->publish_time)) }}</span></li>
                @endforeach
            </ul>
        </div>
    </div>
    <script>
        $.get("/incre_{{ $article->id }}", {}, function(){});
    </script>
    <script src="{{ asset('js/common.js') }}"></script>
@endsection
