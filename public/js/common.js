$(function(){
    $(".content-left img").parents('p').css("text-align", "center");
    $(".content-left img").each(function(index, img){
        img.onload = function() {
            if($(img).width() > $(".content-left").width()) {
                $(img).removeAttr("style").css("width", "100%");
                img.style.width = "100% !important";
            }
        };
    });
});