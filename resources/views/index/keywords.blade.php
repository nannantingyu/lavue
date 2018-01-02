@extends('layout.app')
@section('content')
    <div class="main-content">
        <div class="con-list">
                <ul class="keywords">
                    @foreach($keywords  as $val)
                        <li><a href="{{ $base_url }}keys_{{ $val->keyword }}_1" target="_blank">{{ $val->keyword }}</a></li>
                    @endforeach
                </ul>
        </div>
        <div class="page">
            {{ $keywords->links() }}
        </div>
    </div>
 @endsection