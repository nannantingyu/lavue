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
		$(".detail-main p img").css("width", "100%").parents("p").css("margin", "0");
		$(".detail-main *").css("background-image", "none");
		$('section').css("width", "100%");
	}
});