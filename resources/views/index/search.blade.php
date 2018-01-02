@extends('layout.app')
@section('content')
    <div class="main-content">
        <div class="con-list">
            @if(isset($keywords))
            <div class="left wp80 mpr5">
            @endif
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
            @if(isset($keywords))
            </div>
            @endif

            @if(isset($keywords))
            <div class="left mp20">
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