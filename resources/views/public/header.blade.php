<header>
    <div class="content">
        <div class="navbar-brand">
            <a href="/"><img src="{{ asset('images/logo.png') }}" alt="炜煜合作社提供正宗自产自销的稻花香大米，如有需要，请联系18801292741"></a>
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
            @if(empty(session('user')))
                <a href="/login">登录</a>
                <a href="/register">注册</a>
            @elseif(session('user')->state == 1)
                <span>{{ session('user')->name }}</span>
                <a href="/logout">退出</a>
            @elseif(session('user')->state == 0)
                <span>{{ session('user')->name }}(未验证，<a href="{{ session('user')->email_link }}">去验证</a>)</span>
                <a href="/logout">退出</a>
            @endif

        </div>
        <div class="clear"></div>
    </div>
</header>