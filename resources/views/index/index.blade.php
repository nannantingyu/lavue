@extends('layout.app')
@section('js')
	<script src="{{ asset('js/jquery.nivo.slider.js') }}"></script>
	<script src="{{ asset('js/index.js') }}"></script>
@endsection

@section('css')
	<link rel="stylesheet" href="{{ asset('css/nivo-slider.css') }}">
@endsection

@section('content')
	@include('public.navi')
	<div class="main-content">
		<div class="main-left">
			<section class="carousel">
				<a href="#"><img src="{{ asset('images/lunbo1.jpg') }}" title="This is an example of a caption" ></a>
				<img src="{{ asset('images/lunbo6.jpg') }}" title="This is an example of a caption" />
				<a href="http://dev7studios.com"><img src="{{ asset('images/lunbo7.jpg') }}" title="This is an example of a caption" /></a>
				<img src="{{ asset('images/lunbo4.jpg') }}" title="This is an example of a caption" />
				<img src="{{ asset('images/lunbo5.jpg') }}" title="This is an example of a caption" />
			</section>
			<h2 class="sub-title">即时资讯</h2>
			<ul class="news">
				@foreach($articles as $article)
					<li>
						<a href="{{ $base_url }}watch_{{ $article->id }}">{{ $article->title }}</a>
						<span>{{ $article->publish_time }}</span>
					</li>
				@endforeach
			</ul>

			<h2 class="sub-title">种植技术</h2>
			<ul class="news">
				@foreach($articles as $article)
					<li>
						<a href="{{ $base_url }}watch_{{ $article->id }}">{{ $article->title }}</a>
						<span>{{ $article->publish_time }}</span>
					</li>
				@endforeach
			</ul>

			<h2 class="sub-title">搞笑中场</h2>
			<div class="sub-img">
				<dl>
					<dd><img src="{{ asset('images/lunbo4.jpg') }}"></dd>
					<dt>
						去练车的时候，有个美女把安全带系到了副驾驶上
						教练问她没感觉不对啊
						姑娘说没有啊
						教练指着安全带的卡带说
					</dt>
				</dl>
				<dl>
					<dd><img src="{{ asset('images/lunbo4.jpg') }}"></dd>
					<dt>
						去练车的时候，有个美女把安全带系到了副驾驶上
						教练问她没感觉不对啊
						姑娘说没有啊
						教练指着安全带的卡带说
					</dt>
				</dl>
			</div>

			<ul class="news">
				@foreach($articles as $article)
					<li>
						<a href="{{ $base_url }}watch_{{ $article->id }}">{{ $article->title }}</a>
						<span>{{ $article->publish_time }}</span>
					</li>
				@endforeach
			</ul>

			<h2 class="sub-title">健康讲堂</h2>
			<ul class="news">
				@foreach($articles as $article)
					<li>
						<a href="{{ $base_url }}watch_{{$article->id}}">{{ $article->title }}</a>
						<span>{{ $article->publish_time }}</span>
					</li>
				@endforeach
			</ul>

			<h2 class="sub-title">微博微言</h2>
			<ul class="news">
				<li><a href="#">恒大官宣穆里奇自由身回归签半年</a><span>10:00</span></li>
				<li><a href="#">AC米兰热身赛多将发挥抢眼！中场补腰势在必行</a><span>11:00</span></li>
				<li><a href="#">《极限》导演揭秘张艺兴为何节目录了还没签约</a><span>12:00</span></li>
				<li><a href="#">温网场边太太团闪耀！众星女伴大盘点</a><span>13:00</span></li>
				<li><a href="#">印度山地军后方出现3万游击队：袭击印军缴获武器</a><span>14:00</span></li>
				<li><a href="#">印度山地军后方出现3万游击队：袭击印军缴获武器</a><span>14:00</span></li>
				<li><a href="#">印度山地军后方出现3万游击队：袭击印军缴获武器</a><span>14:00</span></li>
			</ul>
		</div>

		<div class="main-right">
			<ul class="tab-title">
				<li class='current'><a href="#">热门博文</a></li>
				<li><a href="#">种植技术</a></li>
				<li><a href="#">天下猎奇</a></li>
				<div class="clear"></div>
			</ul>
			<section class="hot-blog clear">
				@foreach($articles as $article)
					<div class="one-blog">
						<p class="blog-title"><a href="{{ $base_url }}blog_{{ $article->id }}">{{ $article->title }}</a></p>
						<p class="blog-desc">{{ $article->description }}</p>
						<div class="blog-info">
							<span class="blog-time">{{ date("Y-m-d", strtotime($article->publish_time)) }}</span>
							<span class="blog-author"><a href="#">别山举水</a></span>
						</div>
					</div>
				@endforeach
			</section>
			<section class="hot-blog clear none">
				@foreach($articles as $article)
					<div class="one-blog">
						<p class="blog-title"><a href="{{ $base_url }}blog_{{ $article->id }}">{{ $article->title }}</a></p>
						<p class="blog-desc">{{ $article->description }}</p>
						<div class="blog-info">
							<span class="blog-time">{{ date("Y-m-d", strtotime($article->publish_time)) }}</span>
							<span class="blog-author"><a href="#">别山举水</a></span>
						</div>
					</div>
				@endforeach
			</section>
			<section class="hot-blog clear none">
				@foreach($articles as $article)
					<div class="one-blog">
						<p class="blog-title"><a href="{{ $base_url }}blog_{{ $article->id }}">{{ $article->title }}</a></p>
						<p class="blog-desc">{{ $article->description }}</p>
						<div class="blog-info">
							<span class="blog-time">{{ date("Y-m-d", strtotime($article->publish_time)) }}</span>
							<span class="blog-author"><a href="#">别山举水</a></span>
						</div>
					</div>
				@endforeach
			</section>
			<img src="{{ asset('images/ad1.jpg') }}">
			<h2 class="sub-title">热卖单品</h2>
			<section class="hot-goods">
				<dl>
					<dd><a href="#"><img src="{{ asset('images/lunbo1.jpg') }}"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">五常稻花香</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="#"><img src="{{ asset('images/lunbo2.jpg') }}"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">五常稻花香</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="#"><img src="{{ asset('images/lunbo3.jpg') }}"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">五常稻花香</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="#"><img src="{{ asset('images/lunbo4.jpg') }}"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">五常稻花香</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="#"><img src="{{ asset('images/lunbo5.jpg') }}"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">五常稻花香</a>
					</dt>
				</dl>
			</section>

			<h2 class="sub-title">健康频道</h2>
			<section class="healthy">
				<ul>
					@foreach($articles as $article)
						<li>
							<div class="info-text">
								<h3><a href="/blog_{{ $article->id }}">{{ $article->title }}</a></h3>
								<p>{{ $article->description }}</p>
								@php
									$keywords = explode(",", $article->type); $length = count($keywords)-1;
								@endphp
								<span>
									@foreach($keywords as $keys)
										<a href="/search_{{ $keys }}_1">{{ $keys }}</a>，
									@endforeach
								</span>
								<span>{{ date("Y-m-d H:i:s", strtotime($article->publish_time)) }}</span>
							</div>
							<div class="info-img">
								<img src="{{ asset('images/lunbo4.jpg') }}">
							</div>
							<div class="clear"></div>
						</li>
					@endforeach
				</ul>
			</section>
		</div>
	</div>

	<!-- 友情链接 -->
	@include("public.friendlink")
@endsection
