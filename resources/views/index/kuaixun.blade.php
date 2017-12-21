@extends('layout.app')
@section('js')
	<script src="{{ asset('js/jquery.nivo.slider.js') }}"></script>
@endsection

@section('content')
	<div class="main-content">
		<ul class="list">
			@foreach($kx as $val)
				<li>
					<div class="jin-flash_icon">
						<a href="/kuaixun_{{ $val->id }}" target="_blank"><i class="jin-icon jin-icon_flashNews"></i></a>
					</div>
					<div class="jin-flash_time">{{ $val->publish_time }}</div>
					<div class="jin-flash_b">

						<div class="jin-flash_text is-only-text ">

							<div id="J_flash_text20171221161954030100" class="jin-flash_text-box">
								<p class="J_flash_text">{{ $val->body }}</p>
							</div>
						</div>


					</div>
				</li>
			@endforeach
		</ul>
	</div>
@endsection
