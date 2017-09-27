<style>
    html,body{height: 100%; padding: 0; margin: 0;}
    .content{
        filter: url(content.svg#content); /* FireFox, Chrome, Opera */

        -webkit-filter: blur(2px); /* Chrome, Opera */
        -moz-filter: blur(2px);
        -ms-filter: blur(2px);
        filter: blur(2px);

        filter: progid:DXImageTransform.Microsoft.Blur(PixelRadius=2, MakeShadow=false);

        border: 1px solid #ccc;
        position: absolute;
        top: 10%;
        left: 25%;
        width: 30%;
        height: 40%;
        padding: 10%;
        background: url('{{ asset('images/register.jpg') }}') no-repeat;
        background-size: 100% 100%;
    }

    p{
        font-weight: 800;
    }
    .top{
        width: 100px;
        height: 100px;
        background: url('{{asset('images/top.png')}}');
        background-size: 100% 100%;
        top: 7%;
        left: 23%;
        position: absolute;
        z-index: 999;
        transform:rotate(-30deg);
        -ms-transform:rotate(-30deg); 	/* IE 9 */
        -moz-transform:rotate(-30deg); 	/* Firefox */
        -webkit-transform:rotate(-30deg); /* Safari 和 Chrome */
        -o-transform:rotate(-30deg); 	/* Opera */
    }
    .title{
        position: absolute;
        width: 30%;
        padding: 10%;
        left: 25%;
    }
    .title h2{text-align: center;}
</style>
<div style="width: 100%; height: 550px; overflow-y: hidden; position: relative;">
    <div class="top"></div>
    <div class="content"></div>
    <div class="title">
        <h2>完成邮箱验证</h2>
    </div>
    <div style="position: absolute; top:33%; left: 40%; width: 50%;">
        <p>这是系统邮件，请注意查收，切勿回复！</p>
        <p>请点击以下链接完成验证：</p>
        <a href="{{ $base_url }}check/{{$user->check_str}}">{{ $base_url }}check/{{$user->check_str}}</a>
        <p>联系：18551652502@163.com</p>
        <p>地址：<a href="http://www.yjshare.cn">粮叔叔</a></p>
    </div>
</div>
