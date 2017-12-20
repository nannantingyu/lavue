<nav>
    <div class="nav-mod">
        <ul>
            @foreach($keywords as $keyword)
            <li><a href="/keys_{{$keyword->keyword}}_1">{{$keyword->keyword}}</a></li>
            @endforeach
        </ul>
    </div>
    <div class="clear"></div>
</nav>
