@extends('layout.app')
@section('js')
	<script src="{{ asset('js/jquery.nivo.slider.js') }}"></script>
@endsection

@section('content')
	<div class="main-content">
		<div class="kx-content">
			<div class="stime">{{ $kx->publish_time }}</div>
			<div class="body">
				{!! $kx->body !!}
			</div>
		</div>

		<div class="related">
			<div class="content-related">
				<h2 class="sub-title left-con">热门阅读</h2>
				<ul class="news">
					@foreach($hot as $art)
						<li><a href="{{  $base_url }}blog_{{ $art->id }}">{{ $art->title }}</a><span>{{ date("m-d H:i", strtotime($art->publish_time)) }}</span></li>
					@endforeach
				</ul>
			</div>

			<div class="content-related">
				<h2 class="sub-title left-con">热门搜索</h2>
				<ul class="news">
					@foreach($hotsearch as $art)
						<li><a href="{{ $base_url }}{{ $art->site }}_{{$art->id}}">{{ $art->keyword }}</a><span>{{ date("m-d H:i", strtotime($art->time)) }}</span></li>
					@endforeach
				</ul>
			</div>
			<div class="content-related">
				<h2 class="sub-title left-con">猜你喜欢</h2>
				<ul class="news">
					@foreach($favor as $art)
						<li><a href="{{  $base_url }}blog_{{ $art->id }}">{{ $art->title }}</a><span>{{ date("m-d H:i", strtotime($art->publish_time)) }}</span></li>
					@endforeach
				</ul>
			</div>
			<div class="content-related">
				<h2 class="sub-title left-con">最新进展</h2>
				<ul class="news">
					@foreach($latest as $art)
						<li><a href="{{  $base_url }}blog_{{ $art->id }}">{{ $art->title }}</a><span>{{ date("m-d H:i", strtotime($art->publish_time)) }}</span></li>
					@endforeach
				</ul>
			</div>
		</div>
	</div>
@endsection
