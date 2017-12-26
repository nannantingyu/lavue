@extends('layout.app')
@section('js')
@endsection

@section('content')
	<div class="main-content">
		<div class="left wp40 mpr10 search-list">
			<h2>微博实时热搜</h2>
			<ul class="list">
				@foreach($search['weibo'] as $val)
					<li>
						<a href="{{ $base_url }}weibo_{{$val->id}}">{{ $val->keyword }}</a>
						<span class="right">{{ date("m-d H:i", strtotime($val->time)) }}</span>
					</li>
				@endforeach
			</ul>
		</div>
		<div class="left wp40 search-list">
			<h2>百度实时热搜</h2>
			<ul class="list">
				@foreach($search['baidu'] as $val)
					<li>
						<a href="{{ $base_url }}baidu_{{$val->id}}">{{ $val->keyword }}</a>
						<span class="right">{{ date("m-d H:i", strtotime($val->time)) }}</span>
					</li>
				@endforeach
			</ul>
		</div>
		<div class="page">
			{{ $search['weibo']->links() }}
		</div>
	</div>
@endsection
