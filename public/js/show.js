$(function(){
	$(".tab-title li").mouseover(function(){
		var _this = $(this);
		_this.addClass("current").siblings().removeClass("current");
		child_content = _this.parent().siblings(".hot-blog");
		child_content.hide();
		$(child_content[_this.index()]).show();
	});

	if(screen.width > 480){
		$(window).scroll(function(){
			var top = $(document).scrollTop();
			if(top >= 100) {
				$("header").addClass("fixed");
			}
			else {
				$("header").removeClass("fixed");
			}
		});
	}
	else{
		$(".detail-main img").css("width", "100%").parents("p").css({"margin": "0", "padding-left": "0", "padding-right": "0"});
		$(".detail-main *").css("background-image", "none");
		$('section').css("width", "100%");
	}
});

//360搜索
(function(){
	var src = (document.location.protocol == "http:") ? "http://js.passport.qihucdn.com/11.0.1.js?c6df82d8117d4f772d9a9ba46a5f87a1":"https://jspassport.ssl.qhimg.com/11.0.1.js?c6df82d8117d4f772d9a9ba46a5f87a1";
	$("body").append('<script src="' + src + '" id="sozz"><\/script>');
})();