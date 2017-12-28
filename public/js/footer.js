$(function(){
    $("input[name='search_txt'] + button").click(function(){
        sear = $("input[name='search_txt']");
        if(!sear.val()) {
            alert("请输入要搜索的内容！");
            return;
        }
        location.href = "/search_"+sear.val()+"_1";
    });

    $("#big-img .left").click(function(event){
        var index = $("#big-img").data("index");
        $("#big-img").data("index", index-1);
        show_img();

        event.stopPropagation();
    });

    $("#big-img .right").click(function(event){
        var index = $("#big-img").data("index");
        $("#big-img").data("index", index+1);
        show_img();

        event.stopPropagation();
    });

    $(".yj-backdrop,#big-img").click(function() {
        $("#img-bigg").removeAttr("src");
        $(".yj-backdrop,#big-img").hide();
    });
});

function show_img() {
    $(".centered").scrollTop(0);
    var index = $("#big-img").data("index"), pindex = $("#big-img").data("pindex"), length = $("ul.weibo>li:eq("+pindex+") ul.weibo-imgs li").length;
    $("#big-img .left, #big-img .right").show();
    if(index == length - 1) {
        $("#big-img .right").hide();
    }

    if(index == 0) {
        $("#big-img .left").hide();
    }

    $("#big-img").show();
    $(".yj-backdrop").show();
    $("#img-big").attr("src", $("ul.weibo>li:eq("+pindex+") ul.weibo-imgs li:eq("+index+") img").attr("src").replace("thumb150", "mw690"));
}

//百度提交
(function(){
    var bp = document.createElement('script');
    var curProtocol = window.location.protocol.split(':')[0];
    if (curProtocol === 'https') {
        bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';
    }
    else {
        bp.src = 'http://push.zhanzhang.baidu.com/push.js';
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
})();
//百度统计
var _hmt = _hmt || [];
(function() {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?ef2742dc05c0e157a799b41794930778";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
})();

//360搜索
var src = (document.location.protocol == "http:") ? "http://js.passport.qihucdn.com/11.0.1.js?c6df82d8117d4f772d9a9ba46a5f87a1":"https://jspassport.ssl.qhimg.com/11.0.1.js?c6df82d8117d4f772d9a9ba46a5f87a1";
    document.write('<script src="' + src + '" id="sozz"><\/script>');
})();