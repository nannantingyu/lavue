@extends('layout.app')
@section('js')
	<script src="{{ asset('js/jquery.nivo.slider.js') }}"></script>
@endsection

@section('content')
	<div class="main-content">
		<div class="left wp70 weibo-list-left">
			<ul class="weibo weibo-list">
				@foreach($weibos as $weibo)
					<li>
						<div class="weibo_left">
							<a href="{{ $weibo->author_link }}">
								<img src="{{ $weibo->author_img }}">
							</a>
						</div>
						<div class="weibo_right">
							<h4> <a href="{{ $weibo->author_link }}">{{ $weibo->author_name }} </a></h4>
							<p><a href="{{ $weibo->source_url }}">{{ $weibo->pub_time }}</a></p>
							<div class="weibo_content">
								{!! $weibo->content !!}
							</div>
							@if($weibo->images)
								<ul class="weibo-imgs">
									@foreach(explode(",", $weibo->images) as $img)
										<li><img src="{{$img}}" alt="{{ $weibo->author_name }}"></li>
									@endforeach
								</ul>
							@endif
						</div>
					</li>
				@endforeach
			</ul>
			<div class="page">
				{{ $weibos->links() }}
			</div>
		</div>
		<div class="left wp30 weibo-list-right">
			<h2 class="sub-title"><a href="#">热门搜索</a></h2>
			<ul class="news">
				@foreach($hotsearch as $hot)
					<li>
						<a href="{{ $base_url }}{{ $hot->site }}_{{$hot->id}}">{{ $hot->keyword }}</a>
						<span>{{ date("m-d H:i", strtotime($hot->time)) }}</span>
					</li>
				@endforeach
			</ul>
		</div>
	</div>
	@include('public.bigimg')

	<script>
		$("ul.weibo").on("click", "img",  function(){
			var index = $(this).parent('li').index(), pindex = $(this).parents(".weibo_right").parent("li").index();
			$("#big-img").data("index", index).data("pindex", pindex);
			show_img();
		});
	</script>
@endsection
