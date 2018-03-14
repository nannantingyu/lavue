@extends('layout.app')
@section('content')
    <div class="main-content detail-main">
        <div class="content-left">
            <ul class="area">
                @foreach($areas as $k=>$area)
                    <li @if($k==0) class="cur" @endif><a href="javascript:;">{{ $area }}</a></li>
                @endforeach
            </ul>

            <div class="clear nav">

            </div>
            <div class="content clear" id="residential"></div>
        </div>
        <div class="content-right">
            <h2 class="sub-title left-con">热门阅读</h2>
            <ul class="news"></ul>
        </div>
    </div>
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/house.js') }}?1"></script>
@endsection
