@extends('layout.app')
@section('content')
    <div class="main-content detail-main">
        <div class="hq">
            <h2>比特币行情</h2>
            <div id="container_btcusdt" style="min-width:400px;height:600px"></div>
            <h2>以太币行情</h2>
            <div id="container_ethusdt" style="min-width:400px;height:600px"></div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="{{ asset('js/socket.io.js') }}"></script>
    <script src="{{ asset('js/highstock.js') }}"></script>
    <script src="{{ asset('js/highcharts-zh_CN.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/moment-timezone-with-data-2012-2022.min.js') }}"></script>
    <script src="{{ asset('js/chart.js') }}"></script>
@endsection
