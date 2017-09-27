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
                <h2 class="sub-head-w3-agileits">注册</h2>

                <div>
                    <div class="inputs-w3ls">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <input type="email" name="email" placeholder="Email" required=""/>
                    </div>
                    <div class="inputs-w3ls">
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <input type="text" name="name" placeholder="User Name" required=""/>
                    </div>
                    <div class="inputs-w3ls">
                        <i class="fa fa-phone" aria-hidden="true"></i>
                        <input type="text" name="phone" placeholder="Phone" required=""/>
                    </div>
                    <div class="inputs-w3ls">
                        <i class="fa fa-key" aria-hidden="true"></i>
                        <input type="password" name="password" placeholder="Password" required=""/>
                    </div>
                    <div class="inputs-w3ls">
                        <i class="fa fa-key" aria-hidden="true"></i>
                        <input type="password" name="repassword" placeholder="RePassword" required=""/>
                    </div>
                    <div class="inputs-w3ls">
                        <input type="text" name="cap" class="cap" placeholder="Captcha" required=""/>
                        <img src="/captcha" alt="验证码">
                    </div>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="submit" value="注册">
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
    </script>
    <script src="{{ asset('js/jquery.vide.min.js') }}"></script>
    <script>
        $(function(){
            $('.inputs-w3ls img').click(function(){
                $(this).attr('src', "/captcha?_r=" + Math.random());
            });
            $('input[type="submit"]').click(function(){
                var err_msg = '', email = $('input[name="email"]').val(), name = $('input[name="name"]').val(), phone = $('input[name="phone"]').val(),
                        password = $('input[name="password"]').val(), repassword = $('input[name="repassword"]').val(), cap = $('input[name="cap"]').val();
                if(!email) {
                    err_msg = '请输入邮箱地址！';
                }
                else if(!name) {
                    err_msg = '请输入用户名！';
                }
                else if(!/^[\d\w]{6,20}$/.test(name)) {
                    err_msg = '用户名必须为6-20位数字或者字母！';
                }
                else if(!phone) {
                    err_msg = '请输入用户名！';
                }
                else if(!/^(13|14|15|17|18)\d{9}$/.test(phone)) {
                    err_msg = '请输入正确的手机号！';
                }
                else if(!password) {
                    err_msg = '请输入密码！';
                }
                else if(!/^[\d\w]{6,20}$/.test(password)) {
                    err_msg = '密码必须为6-20位数字或者字母！';
                }
                else if(password != repassword) {
                    err_msg = '两次输入的密码不一致！';
                }
                else if(!cap) {
                    err_msg = '请输入验证码！';
                }

                if(err_msg) {
                    alert(err_msg);
                    return false;
                }

                $.ajax({
                    url: '/register',
                    type: 'post',
                    data: {
                        email: email,
                        name: name,
                        phone: phone,
                        password: password,
                        cap: cap,
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(data) {
                        if(data.msg) {
                            alert(data.msg);
                        }
                        else {
                            alert("注册成功！");
                            window.location.href = '/';
                        }
                    }
                })
                return true;
            });
        })
    </script>
@endsection
</body>
</html>