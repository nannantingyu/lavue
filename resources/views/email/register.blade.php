<html style="height: 100%; width: 100%; padding: 0; margin: 0;">
    <body style="height: 100%; width: 100%; padding: 0; margin: 0;">
    <div style="width: 100%; height: 550px; position: relative;">
        <div class="top" style="width: 100px;  height: 100px;  background: url('{{asset('images/top.png')}}');  background-size: 100% 100%;  top: 7%;  left: 23%;  position: absolute;  z-index: 999;  transform:rotate(-30deg);  -ms-transform:rotate(-30deg);  -moz-transform:rotate(-30deg);  -webkit-transform:rotate(-30deg);  -o-transform:rotate(-30deg);"></div>
        <div class="content" style="filter: url(content.svg#content);  -webkit-filter: blur(2px);  -moz-filter: blur(2px);  -ms-filter: blur(2px);  filter: blur(2px);  filter: progid:DXImageTransform.Microsoft.Blur(PixelRadius=2, MakeShadow=false);  border: 1px solid #ccc;  position: absolute;  top: 10%;  left: 25%;  width: 30%;  height: 40%;  padding: 10%;  background: url('{{ asset('images/register.jpg') }}') no-repeat;  background-size: 100% 100%;"></div>
        <div class="title" style="position: absolute;  width: 30%;  padding: 10%;  left: 25%;  top: -10%;">
            <h2 style="text-align: center;">完成邮箱验证</h2>
        </div>
        <div style="position: absolute; top:33%; left: 33%; width: 50%;">
            <p style="font-weight: 800;">这是系统邮件，请注意查收，切勿回复！</p>
            <p style="font-weight: 800;">请点击以下链接完成验证：</p>
            <a href="{{ $base_url }}check/{{$user->check_str}}">{{ $base_url }}check/{{$user->check_str}}</a>
            <p style="font-weight: 800;">联系：18551652502@163.com</p>
            <p style="font-weight: 800;">地址：<a href="http://www.yjshare.cn">粮叔叔</a></p>
        </div>
    </div>
    </body>
</html>