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
				@foreach($articles as $article)
					<a href="{{ $base_url }}watch_{{ $article->id }}">
						@if(empty($article->image))
							<img src="{{ asset('images/lunbo1.jpg') }}" title="炜煜合作社提供正宗自产自销的稻花香大米，如有需要，请联系18801292741">
						@else
							<img src="http://captain-tu.oss-cn-beijing.aliyuncs.com/{{ json_decode($article->image)[0] }}" title="{{$article->description}}">
						@endif
					</a>
				@endforeach
				<a href="#"><img src="{{ asset('images/lunbo1.jpg') }}" title="This is an example of a caption" ></a>
				<img src="{{ asset('images/lunbo6.jpg') }}" title="This is an example of a caption" />
				<a href="http://dev7studios.com"><img src="{{ asset('images/lunbo7.jpg') }}" title="This is an example of a caption" /></a>
				<img src="{{ asset('images/lunbo4.jpg') }}" title="This is an example of a caption" />
				<img src="{{ asset('images/lunbo5.jpg') }}" title="This is an example of a caption" />
			</section>
			<h2 class="sub-title">房事天下</h2>
			<ul class="news">
				@foreach($house as $article)
					<li>
						<a href="{{ $base_url }}watch_{{ $article->id }}">{{ $article->title }}</a>
						<span>{{ date("m-d H:i", strtotime($article->updated_time)) }}</span>
					</li>
				@endforeach
			</ul>

			<h2 class="sub-title">饮食生活</h2>
			<ul class="news">
				@foreach($food as $article)
					<li>
						<a href="{{ $base_url }}watch_{{ $article->id }}">{{ $article->title }}</a>
						<span>{{ date("m-d H:i", strtotime($article->updated_time)) }}</span>
					</li>
				@endforeach
			</ul>

			<h2 class="sub-title">搞笑中场</h2>
			<div class="sub-img">
				@foreach($live as $article)
					<dl>
						@if(empty($article->image))
							<dd><a href="{{ $base_url }}watch_{{ $article->id }}"><img src="{{ asset('images/lunbo4.jpg') }}"></a></dd>
						@else
							<dd><a href="{{ $base_url }}watch_{{ $article->id }}"><img src="http://captain-tu.oss-cn-beijing.aliyuncs.com/{{ json_decode($article->image)[0] }}"></a></dd>
						@endif
						<dt>
							{{ $article->description }}
						</dt>
					</dl>
				@endforeach

			</div>

			<h2 class="sub-title">健身锻炼</h2>
			<ul class="news">
				@foreach($exercise as $article)
					<li>
						<a href="{{ $base_url }}watch_{{ $article->id }}">{{ $article->title }}</a>
						<span>{{ date("m-d H:i", strtotime($article->updated_time)) }}</span>
					</li>
				@endforeach
			</ul>

			<h2 class="sub-title">每日回龙观</h2>
			<ul class="news">
				@foreach($huilongguan as $article)
					<li>
						<a href="{{ $base_url }}watch_{{$article->id}}">{{ $article->title }}</a>
						<span>{{ date("m-d H:i", strtotime($article->updated_time)) }}</span>
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
				<li class='current'><a href="#">五常大米</a></li>
				<li><a href="#">每日健康</a></li>
				<li><a href="#">北京生活</a></li>
				<div class="clear"></div>
			</ul>
			<section class="hot-blog clear">
				@foreach($rice as $article)
					<div class="one-blog">
						<p class="blog-title"><a href="{{ $base_url }}blog_{{ $article->id }}">{{ $article->title }}</a></p>
						<p class="blog-desc">{{ $article->description }}</p>
						<div class="blog-info">
							<span class="blog-time">{{ date("Y-m-d", strtotime($article->publish_time)) }}</span>
							<span class="blog-author"><a href="#">{{ $article->from_user }}</a></span>
						</div>
					</div>
				@endforeach
			</section>
			<section class="hot-blog clear none">
				@foreach($healthy as $article)
					<div class="one-blog">
						<p class="blog-title"><a href="{{ $base_url }}blog_{{ $article->id }}">{{ $article->title }}</a></p>
						<p class="blog-desc">{{ $article->description }}</p>
						<div class="blog-info">
							<span class="blog-time">{{ date("Y-m-d", strtotime($article->publish_time)) }}</span>
							<span class="blog-author"><a href="#">{{ $article->from_user }}</a></span>
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
							<span class="blog-author"><a href="#">{{ $article->from_user }}</a></span>
						</div>
					</div>
				@endforeach
			</section>
			<img src="{{ asset('images/ad1.jpg') }}">
			<h2 class="sub-title">热卖单品</h2>
			<section class="hot-goods">
				<dl>
					<dd><a href="{{ $base_url }}search_热卖_1"><img src="{{ asset('images/lunbo1.jpg') }}"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">五常稻花香</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="{{ $base_url }}search_稻花香_1"><img src="{{ asset('images/lunbo2.jpg') }}"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">稻花香</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="{{ $base_url }}search_五常大米_1"><img src="{{ asset('images/lunbo3.jpg') }}"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">五常大米</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="{{ $base_url }}search_特产_1"><img src="{{ asset('images/lunbo4.jpg') }}"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">特产</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="{{ $base_url }}search_健身_1"><img src="{{ asset('images/lunbo5.jpg') }}"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">健身</a>
					</dt>
				</dl>
			</section>

			<h2 class="sub-title">昌平生活</h2>
			<section class="healthy">
				<ul>
					@foreach($changping as $article)
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
								@if(empty($article->image))
									<a href="{{ $base_url }}watch_{{ $article->id }}"><img src="{{ asset('images/lunbo4.jpg') }}"></a>
								@else
									<a href="{{ $base_url }}watch_{{ $article->id }}"><img src="http://captain-tu.oss-cn-beijing.aliyuncs.com/{{ json_decode($article->image)[0] }}"></a>
								@endif
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
