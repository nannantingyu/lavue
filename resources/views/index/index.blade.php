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

			<p class="sub-title"><a href="{{ $base_url }}hotsearch">热门搜索</a></p>
			<ul class="news">
				@foreach($hotkey as $hot)
					<li>
						<a href="{{ $base_url }}{{ $hot->site }}_{{$hot->id}}">{{ $hot->keyword }}</a>
						<span>{{ date("m-d H:i", strtotime($hot->time)) }}</span>
					</li>
				@endforeach
			</ul>

			<h3 class="sub-title"><a href="{{ $base_url }}kuaixun">粮蔬快讯</a></h3>
			<ul class="kuaixun">
				@foreach($kuaixun as $kx)
					<li>
						<a href="/kuaixun_{{ substr($kx->source_site, 0, 2) }}{{ $kx->id }}">{{ date('H:i:s', strtotime($kx->publish_time)) }}</a> {{ $kx->body }}
					</li>
				@endforeach
			</ul>
		</div>

		<div class="main-right">
			<div class="main-middle">
				<div class="middle-left">
					<ul class="tab-title">
						<li class='current'><a href="{{ $base_url }}news">最新资讯</a></li>
						<li><a href="{{ $base_url }}hots">全国热议</a></li>
						<li><a href="{{ $base_url }}list_五常大米">五常大米</a></li>
						<div class="clear"></div>
					</ul>
					<section class="hot-blog clear">
						@foreach($newest as $article)
							<div class="one-blog">
								<p class="blog-title"><a href="{{ $base_url }}blog_{{ $article->id }}">{{ $article->title }}</a></p>
								<p class="blog-desc">{{ $article->description }}</p>
								<div class="blog-info">
									<span class="blog-time">{{ date("Y-m-d", strtotime($article->publish_time)) }}</span>
									<span class="blog-author"><a href="{{ $base_url }}author_{{ $article->author }}">{{ $article->author }}</a></span>
								</div>
							</div>
						@endforeach
					</section>
					<section class="hot-blog clear none">
						@foreach($hotest as $article)
							<div class="one-blog">
								<p class="blog-title"><a href="{{ $base_url }}blog_{{ $article->id }}">{{ $article->title }}</a></p>
								<p class="blog-desc">{{ $article->description }}</p>
								<div class="blog-info">
									<span class="blog-time">{{ date("Y-m-d", strtotime($article->publish_time)) }}</span>
									<span class="blog-author"><a href="{{ $base_url }}author_{{ $article->author }}">{{ $article->author }}</a></span>
								</div>
							</div>
						@endforeach
					</section>
					<section class="hot-blog clear none">
						@foreach($rice as $article)
							<div class="one-blog">
								<p class="blog-title"><a href="{{ $base_url }}blog_{{ $article->id }}">{{ $article->title }}</a></p>
								<p class="blog-desc">{{ $article->description }}</p>
								<div class="blog-info">
									<span class="blog-time">{{ date("Y-m-d", strtotime($article->publish_time)) }}</span>
									<span class="blog-author"><a href="{{ $base_url }}author_{{ $article->author }}">{{ $article->author }}</a></span>
								</div>
							</div>
						@endforeach
					</section>
				</div>
				<div class="middle-right">
					<p class="sub-title"><a href="/weibo">热门微博</a></p>
					<ul class="weibo">
						@foreach($weibos as $weibo)
						<li>
							<div class="weibo_left">
								<a href="{{ $weibo->author_link }}" rel="nofollow">
									<img src="{{ $weibo->author_img }}">
								</a>
							</div>
							<div class="weibo_right">
								<h4> <a href="{{ $weibo->author_link }}" rel="nofollow">{{ $weibo->author_name }} </a></h4>
								<p><a href="{{ $weibo->source_url }}" rel="nofollow">{{ $weibo->pub_time }}</a></p>
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
			<div class="main-middle clear">
				<p class="sub-title"><a href="/rili">财经日历</a></p>
				<section class="healthy">
					<table width="100%" class="cjtb">
					</table>
				</section>

				<img src="{{ asset('images/ad1.jpg') }}" class="ads-img">
				<h3 class="sub-title">粮叔叔热卖</h3>
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
			</div>
		</div>
	</div>

	@include('public.bigimg')
	@include('public.navi')

	<!-- 友情链接 -->
	@include("public.friendlink")
	<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script>
		wx.onMenuShareAppMessage({
			title: '这个是标题哦哦哦哦哦', // 分享标题
			desc: '这个是描述哦哦哦哦哦', // 分享描述
			link: 'https://www.yjshare.cn', // 分享链接
			imgUrl: 'https://image.yjshare.cn/2018/2/5/AvZz7MAy9Bn4ngTyTBok0sdr5U3lVVTcpq0NtwU3aHlecOYltyBBREqfD3NSJjsRFuLicbTCbTq8AlrcK0z6XDA.jpg', // 分享图标
			type: '', // 分享类型,music、video或link，不填默认为link
			dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
			success: function () {
				// 用户确认分享后执行的回调函数
			},
			cancel: function () {
				// 用户取消分享后执行的回调函数
			}
		});
	</script>
@endsection
