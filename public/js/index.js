(function($){
	$(function(){
		$(".carousel").nivoSlider({ slices: 8 });

		$("input[name='search_txt'] + button").click(function(){
			sear = $("input[name='search_txt']");
			if(!sear.val()) {
				alert("请输入要搜索的内容！");
				return;
			}
			location.href = "/search_"+sear.val()+"_1";
		});
	});
})(jQuery);