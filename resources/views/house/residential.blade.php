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

    <!-- Modal -->
    <div class="modal fade" id="info-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="residentialinfo">小区详情</h4>
                </div>
                <div class="modal-body">
                    <div id="info">
                        <table class="table table-hover">
                            <tr>
                                <td id="rname"></td>
                                <td><a id="ajk" href="javascript:;">安居客</a></td>
                                <td><a id="lj" href="javascript:;">链家</a></td>
                            </tr>
                            <tr>
                                <td>年代：<span id="year"></span></td>
                                <td>类型：<span id="type"></span></td>
                                <td>区域：<span id="area"></span></td>
                            </tr>
                            <tr>
                                <td>均价：<span id="price"></span></td>
                                <td>在售：<span id="sell"></span></td>
                                <td>在租：<span id="rent"></span></td>
                            </tr>
                        </table>
                        <div id="map"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/house.js') }}?9"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/highcharts.js') }}"></script>
    <script src="{{ asset('js/highcharts-zh_CN.js') }}"></script>
    {{--<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.4&key=cdcc3108ec75857cf1efeea348a68519"></script>--}}
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=8ilGs4NIQN9rxbeACZCteObPswym2EtQ"></script>
@endsection
