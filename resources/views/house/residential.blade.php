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

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">小区历史价格</h4>
                </div>
                <div class="modal-body">
                    <div id="chartx"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/house.js') }}?2"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/highcharts.js') }}"></script>
    <script src="{{ asset('js/highcharts-zh_CN.js') }}"></script>
@endsection
