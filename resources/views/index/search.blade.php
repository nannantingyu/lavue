@extends('layout.app')
@section('content')
    <div class="main-content">
        <div class="con-list">
            @if(isset($keywords))
            <div class="left wp70 mpr5">
            @endif
            @if(count($articles) > 0)
                <ul>
                    @foreach($articles as $vo)
                        <li>
                            <div class="title"><a href="{{ $base_url }}blog_{{ $vo->id }}">{{ $vo->title }}</a></div>
                            <div class="time">{{  date("Y-m-d", strtotime($vo->publish_time)) }}</div>
                            <div class="clear"></div>
                        </li>
                    @endforeach
                </ul>
                <div class="page">
                    {{ $articles->links() }}
                </div>
            @else
                <p class="info-msg">
                    实在抱歉，暂时没有您想要的结果，我们的爬虫会尽量帮您抓取的，请稍后再试试吧！<br>
                    如有需要，请联系 <a href="mainto:939259192@qq.com">939259192@qq.com</a>
                </p>
            @endif
            @if(isset($keywords))
            </div>
            @endif

            @if(isset($keywords))
            <div class="left wp25">
                <h2>相关搜索</h2>
                <ul>
                    @foreach($keywords as $val)
                        <li><a href="{{ $base_url }}keys_{{ $val->keyword }}_1" target="_blank">{{ $val->keyword }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
 @endsection