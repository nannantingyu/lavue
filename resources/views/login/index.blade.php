@extends('layout.app_noheader')

@section('css')
    <link href="{{ asset('css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel='stylesheet' type='text/css'/>
    <link href="{{ asset('css/font.css') }}" rel="stylesheet">
@endsection
<div class="video" data-vide-bg="video/water">
    <div class="center-container">
        <h1>粮叔叔-爱你所爱</h1>

        <div class="login-w3l">
            <div class="login-form">
                <h2 class="sub-head-w3-agileits">登录</h2>

                <div action="/login" method="post">
                    <div class="inputs-w3ls">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <input type="email" name="name" placeholder="User Name" required=""/>
                    </div>
                    <div class="inputs-w3ls">
                        <i class="fa fa-key" aria-hidden="true"></i>
                        <input type="password" name="password" placeholder="Password" required=""/>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="submit" value="登录">
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <div class="footer-agileits">
            <p>© 2015-2017 <a href="/">粮叔叔</a>版权所有</p>
        </div>
    </div>
</div>

@section('js')
    <script type="application/x-javascript">
        addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
        $(function(){
            $('input[type="submit"]').click(function(){
                var name = $('input[name="name"]').val(), password = $('input[name="password"]').val(), err_msg='';
                if(!name) {
                    err_msg = '请输入用户名！';
                }
                else if(!/^[\d\w]{6,20}$/.test(name)) {
                    err_msg = '用户名必须为6-20位数字或者字母！';
                }
                else if(!password) {
                    err_msg = '请输入密码！';
                }
                else if(!/^[\d\w]{6,20}$/.test(password)) {
                    err_msg = '密码必须为6-20位数字或者字母！';
                }

                if(err_msg) {
                    alert(err_msg);
                    return;
                }

                $.ajax({
                    url: '/login',
                    type: 'post',
                    data: {name: name, password: password, _token: $('input[name="_token"]').val()},
                    success: function(data) {
                        if(data.state == 1) {
                            window.location.href = "/";
                        }
                        else if(data.state == -1) {
                            alert("密码不正确");
                        }
                        else if(data.state == -2) {
                            alert("用户不存在");
                        }
                    }
                })
            });
        });
    </script>
    <script src="{{ asset('js/jquery.vide.min.js') }}"></script>
@endsection
</body>
</html>