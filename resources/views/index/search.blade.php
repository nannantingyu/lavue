@extends('layout.app')
@section('content')
    <div class="main-content">
        <div class="con-list">
            <ul>
                @foreach($articles as $vo)
                    <li>
                        <div class="title"><a href="{{ $base_url }}blog_{{ $vo->id }}">{{ $vo->title }}</a></div>
                        <div class="time">{{  date("Y-m-d", strtotime($vo->publish_time)) }}</div>
                        <div class="clear"></div>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="page">
            {{ $articles->links() }}
        </div>
    </div>
 @endsection