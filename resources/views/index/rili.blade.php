@extends('layout.app')
@section('js')
	<script src="{{ asset('js/jquery.nivo.slider.js') }}"></script>
@endsection

@section('content')
	<div class="main-content">
		<div class="cj-date">
			<ul>
				<li><a href="rili?d={{ $data['date']['pre'] }}">上一周</a></li>
				@foreach($data['date']['w']  as $date)
					<li @if($date['dz'] == 1) class="active" @endif><a href="/rili?d={{ $date['d'] }}">{{ $date['z'] }} {{ date("m/d", strtotime($date['d'])) }}</a></li>
				@endforeach
				<li><a href="rili?d={{ $data['date']['next'] }}">下一周</a></li>
			</ul>
		</div>

		<div class="table">
			<table width="100%" class="cjtb">
				<tr>
					<th>时间</th>
					<th>国/区</th>
					<th>指标名称</th>
					<th>重要性</th>
					<th>前值</th>
					<th>预测值</th>
					<th>公布值</th>
					<th>影响</th>
					<th>解读</th>
				</tr>
				@if(count($data['hdata']) > 0)
				@foreach($data['cjdata'] as $val)
					@foreach($val['_ch'] as $rili)
						<tr>
							@if($rili['title'] == $val['_ch'][0]['title'])
								<td rowspan="{{ count($val['_ch']) }}">{{ substr($val['_ch'][0]['stime'], 10, 6) }}</td>
								<td rowspan="{{ count($val['_ch']) }}"><img src="/images/flag/{{ $val['_ch'][0]['country_cn'] }}.png" alt="{{ $val['_ch'][0]['country_cn'] }}"></td>
							@endif
							<td>{{ $rili['title'].$rili['UNIT'] }}</td>
							<td><div class="jin-star"><i class="jin-star_active @if($rili['idx_relevance'] > 2) jin-star_important @endif" style="width:{{ $rili['idx_relevance']*20 }}%;"></i></div></td>
							<td>{{ $rili['previous_price'] }}</td>
							<td>{{ $rili['surver_price'] }}</td>
							<td>{{ $rili['actual_price'] }}</td>
							<td>{{ $rili['res'] }}</td>
							<td><a href="#">解读</a></td>
						</tr>
					@endforeach
				@endforeach
				@else
					<tr><td colspan="9">今日无重要经济数据</td></tr>
				@endif
			</table>
		</div>

		<div class="table">
			<table width="100%" class="cjtb">
				<tr>
					<th>时间</th>
					<th>国家/地区</th>
					<th>城市</th>
					<th>重要性</th>
					<th>事件</th>
				</tr>
				@if(count($data['sjdata']) > 0)
				@foreach($data['sjdata'] as $val)
					<tr>
						<td>{{ $val['event_time'] }}</td>
						<td><img src="/images/flag/{{ $val['country'] }}.png" alt="{{ $val['country'] }}"></td>
						<td>{{ $val['area'] }}</td>
						<td><div class="jin-star"><i class="jin-star_active @if($val['importance'] > 2) jin-star_important @endif" style="width:{{ $val['importance']*20 }}%;"></i></div></td>
						<td>{{ $val['event_desc'] }}</td>
					</tr>
				@endforeach
				@else
					<tr><td colspan="5">今日无财经大事</td></tr>
				@endif
			</table>
		</div>


		<div class="table">
			<table width="100%" class="cjtb">
				<tr>
					<th>时间</th>
					<th>国家/地区</th>
					<th>市场</th>
					<th>节日名称</th>
					<th>详细安排</th>
				</tr>
				@if(count($data['hdata']) > 0)
				@foreach($data['hdata'] as $val)
					<tr>
						<td>{{ $val['event_time'] }}</td>
						<td>{{ $val['country'] }}</td>
						<td>{{ $val['area'] }}</td>
						<td><div class="jin-star"><i class="jin-star_active @if($val['importance'] > 2) jin-star_important @endif" style="width:{{ $val['importance']*20 }}%;"></i></div></td>
						<td>{{ $val['event_desc'] }}</td>
					</tr>
				@endforeach
				@else
					<tr><td colspan="5">今日无假期休市安排</td></tr>
				@endif
			</table>
		</div>
	</div>
@endsection
