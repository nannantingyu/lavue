<div class="yj-modal" id="big-img">
    <div class="centered">
        <img id="img-big" />
        <div class="left"><img src="/images/left.png" alt="上一个"></div>
        <div class="right"><img src="/images/right.png" alt="下一个"></div>
    </div>
</div>
<div class="yj-backdrop"></div>

<script>
    $(function(){
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
</script>