<header>
    <div class="content">
        <div class="navbar-brand">
            <a href="/"><img src="{{ asset('images/logo.png') }}" alt="粮叔叔logo"></a>
            <h1 class="none">粮叔叔</h1>
        </div>
        <div class="navbar-search">
            <input type="text" name="search_txt" placeholder="输入搜索的内容">
            <button></button>
        </div>
        <div class="navbar-follow line">
            <span>关注我们</span>
            <img src="{{ asset('images/erweima.png') }}" alt="关注粮叔叔">
        </div>
        <div class="navbar-login line">
            @if(empty($user))
                <a href="/login">登录</a>
                <a href="/register">注册</a>
            @else
                <span>{{ $user }}</span>
                <a href="/logout">退出</a>
            @endif

        </div>
        <div class="clear"></div>
    </div>
</header>