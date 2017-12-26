$(function(){
    $("input[name='search_txt'] + button").click(function(){
        sear = $("input[name='search_txt']");
        if(!sear.val()) {
            alert("请输入要搜索的内容！");
            return;
        }
        location.href = "/search_"+sear.val()+"_1";
    });
});

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
var _hmt = _hmt || [];
(function() {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?ef2742dc05c0e157a799b41794930778";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
})();