@extends('layout.app')
@section('js')
	<base target="_blank" />
	<script src="{{ asset('js/jquery.nivo.slider.js') }}"></script>
	<script src="{{ asset('js/index.js') }}"></script>
@endsection

@section('css')
	<link rel="stylesheet" href="{{ asset('css/slider/themes/default/default.css') }}" type="text/css" media="screen" />
	<link rel="stylesheet" href="{{ asset('css/slider/themes/dark/dark.css') }}" type="text/css" media="screen" />
	<link rel="stylesheet" href="{{ asset('css/slider/nivo-slider.css') }}" type="text/css" media="screen" />
	<link rel="stylesheet" href="{{ asset('css/slider/style.css') }}" type="text/css" media="screen" />
@endsection

@section('content')
	<div class="main-content">
		<div class="main-left">
			<div id="wrapper">
				<div class="slider-wrapper theme-default">
					<div id="slider" class="nivoSlider">
						@foreach($articles as $article)
							<a href="{{ $base_url }}watch_{{ $article->id }}">
								@if(empty($article->image))
									<img src="{{ asset('images/lunbo1.jpg') }}" title="炜煜合作社提供正宗自产自销的稻花香大米，如有需要，请联系18801292741">
								@else
									<img src="{{ json_decode($article->image)[0] }}" title="{{$article->description}}">
								@endif
							</a>
						@endforeach
					</div>
					<div id="htmlcaption" class="nivo-html-caption">
						<strong>This</strong> is an example of a <em>HTML</em> caption with <a href="#">a link</a>.
					</div>
				</div>
			</div>
			<script>
				$(window).load(function() {
					$('#slider').nivoSlider();
				});
			</script>

			<h2 class="sub-title"><a href="#">热门搜索</a></h2>
			<ul class="news">
				@foreach($hotkey as $hot)
					<li>
						<a href="{{ $base_url }}{{ $hot->site }}_{{$hot->id}}">{{ $hot->keyword }}</a>
						<span>{{ date("m-d H:i", strtotime($hot->time)) }}</span>
					</li>
				@endforeach
			</ul>

			<h2 class="sub-title"><a href="{{ $base_url }}list_房价">房事天下</a></h2>
			<ul class="news">
			</ul>

			<h2 class="sub-title"><a href="{{ $base_url }}list_饮食">饮食生活</a></h2>
			<ul class="news">
			</ul>

			<h2 class="sub-title">搞笑中场</h2>
			<div class="sub-img">
				{{--@foreach($live as $article)--}}
					{{--<dl>--}}
						{{--@if(empty($article->image))--}}
							{{--<dd><a href="{{ $base_url }}watch_{{ $article->id }}"><img src="{{ asset('images/lunbo4.jpg') }}"></a></dd>--}}
						{{--@else--}}
							{{--<dd><a href="{{ $base_url }}watch_{{ $article->id }}"><img src="http://captain-tu.oss-cn-beijing.aliyuncs.com/{{ json_decode($article->image)[0] }}"></a></dd>--}}
						{{--@endif--}}
						{{--<dt>--}}
							{{--{{ $article->description }}--}}
						{{--</dt>--}}
					{{--</dl>--}}
				{{--@endforeach--}}
			</div>

			<h2 class="sub-title"><a href="{{ $base_url }}list_健身">健身锻炼</a></h2>
			<ul class="news">
			</ul>

			<h2 class="sub-title"><a href="{{ $base_url }}list_回龙观">每日回龙观</a></h2>
			<ul class="news">
			</ul>

		</div>

		<div class="main-right">
			<div class="main-middle">
				<div class="middle-left">
					<ul class="tab-title">
						<li class='current'><a href="{{ $base_url }}list_五常大米">五常大米</a></li>
						<li><a href="{{ $base_url }}list_健康">每日北京</a></li>
						<li><a href="{{ $base_url }}list_北京">中国</a></li>
						<div class="clear"></div>
					</ul>
					<section class="hot-blog clear">
						@foreach($rice as $article)
							<div class="one-blog">
								<p class="blog-title"><a href="{{ $base_url }}blog_{{ $article->id }}">{{ $article->title }}</a></p>
								<p class="blog-desc">{{ $article->description }}</p>
								<div class="blog-info">
									<span class="blog-time">{{ date("Y-m-d", strtotime($article->publish_time)) }}</span>
									<span class="blog-author"><a href="#">{{ $article->author }}</a></span>
								</div>
							</div>
						@endforeach
					</section>
					<section class="hot-blog clear none">
						@foreach($beijing as $article)
							<div class="one-blog">
								<p class="blog-title"><a href="{{ $base_url }}blog_{{ $article->id }}">{{ $article->title }}</a></p>
								<p class="blog-desc">{{ $article->description }}</p>
								<div class="blog-info">
									<span class="blog-time">{{ date("Y-m-d", strtotime($article->publish_time)) }}</span>
									<span class="blog-author"><a href="#">{{ $article->author }}</a></span>
								</div>
							</div>
						@endforeach
					</section>
					<section class="hot-blog clear none">
						@foreach($china as $article)
							<div class="one-blog">
								<p class="blog-title"><a href="{{ $base_url }}blog_{{ $article->id }}">{{ $article->title }}</a></p>
								<p class="blog-desc">{{ $article->description }}</p>
								<div class="blog-info">
									<span class="blog-time">{{ date("Y-m-d", strtotime($article->publish_time)) }}</span>
									<span class="blog-author"><a href="#">{{ $article->author }}</a></span>
								</div>
							</div>
						@endforeach
					</section>
				</div>
				<div class="middle-right">
					<ul class="weibo">
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
				</div>

			</div>

			<img src="{{ asset('images/ad1.jpg') }}" class="ads-img">
			<h2 class="sub-title">热卖单品</h2>
			<section class="hot-goods">
				<dl>
					<dd><a href="{{ $base_url }}login?name=1"><img src="{{ asset('images/rice/IMG_3456.JPG') }}"></a></dd>
					<dt>
					<p>¥100</p>
					<a href="#">五常稻花香</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="{{ $base_url }}login?name=2"><img src="{{ asset('images/rice/IMG_3459.JPG') }}"></a></dd>
					<dt>
					<p>¥158</p>
					<a href="#">稻花香</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="{{ $base_url }}login?name=3"><img src="{{ asset('images/rice/IMG_3481.JPG') }}"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">粥米</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="{{ $base_url }}login?name=4"><img src="{{ asset('images/rice/IMG_3483.JPG') }}"></a></dd>
					<dt>
					<p>¥90</p>
					<a href="#">十斤装</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="{{ $base_url }}login?name=5"><img src="{{ asset('images/rice/IMG_3486.JPG') }}"></a></dd>
					<dt>
					<p>¥180</p>
					<a href="#">二十斤装</a>
					</dt>
				</dl>

				<dl>
					<dd><a href="{{ $base_url }}login?name=6"><img src="{{ asset('images/rice/IMG_3487.JPG') }}"></a></dd>
					<dt>
					<p>¥80</p>
					<a href="#">长粒香20KG</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="{{ $base_url }}login?name=5"><img src="{{ asset('images/rice/IMG_3491.JPG') }}"></a></dd>
					<dt>
					<p>¥148</p>
					<a href="#">礼品装-中国红</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="{{ $base_url }}login?name=5"><img src="{{ asset('images/rice/IMG_3498.JPG') }}"></a></dd>
					<dt>
					<p>¥35</p>
					<a href="#">东北木耳</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="{{ $base_url }}login?name=5"><img src="{{ asset('images/rice/IMG_3503.JPG') }}"></a></dd>
					<dt>
					<p>¥50</p>
					<a href="#">笨榨豆油</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="{{ $base_url }}login?name=5"><img src="{{ asset('images/rice/IMG_3501.JPG') }}"></a></dd>
					<dt>
					<p>¥50</p>
					<a href="#">米砖</a>
					</dt>
				</dl>
			</section>

			<h2 class="sub-title"><a href="{{ $base_url }}list_昌平">昌平生活</a></h2>
			<section class="healthy">
				<ul>
					{{--@foreach($changping as $article)--}}
						{{--<li>--}}
							{{--<div class="info-text">--}}
								{{--<h3><a href="/blog_{{ $article->id }}">{{ $article->title }}</a></h3>--}}
								{{--<p>{{ $article->description }}</p>--}}
								{{--@php--}}
									{{--$keywords = explode(",", $article->type); $length = count($keywords)-1;--}}
								{{--@endphp--}}
								{{--<span>--}}
									{{--@foreach($keywords as $keys)--}}
										{{--<a href="/type_{{ $keys }}_1">{{ $keys }}</a>，--}}
									{{--@endforeach--}}
								{{--</span>--}}
								{{--<span>{{ date("Y-m-d H:i:s", strtotime($article->publish_time)) }}</span>--}}
							{{--</div>--}}
							{{--<div class="info-img">--}}
								{{--@if(empty($article->image))--}}
									{{--<a href="{{ $base_url }}watch_{{ $article->id }}"><img src="{{ asset('images/lunbo4.jpg') }}"></a>--}}
								{{--@else--}}
									{{--<a href="{{ $base_url }}watch_{{ $article->id }}"><img src="http://captain-tu.oss-cn-beijing.aliyuncs.com/{{ json_decode($article->image)[0] }}"></a>--}}
								{{--@endif--}}
							{{--</div>--}}
							{{--<div class="clear"></div>--}}
						{{--</li>--}}
					{{--@endforeach--}}
				</ul>
			</section>
		</div>
	</div>

	<div class="yj-modal" id="big-img">
		<div class="centered">
			<img />
		</div>
	</div>
	<div class="yj-backdrop"></div>

	@include('public.navi')

	<!-- 友情链接 -->
	@include("public.friendlink")
@endsection
