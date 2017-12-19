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

		//加载微博
		$.ajax({
			url: "/weibo",
			dataType: 'json',
			success: function(data) {
				var html = "";
				for(var i = 0, max = data.length; i < max; i ++) {
					html += ' <li> <div class="weibo_left"><img src="' + data[i]['author_img'] + '"></div> <div class="weibo_right">'
						+ '<h4>' + data[i]['author_name'] + '</h4>' + '<p>'+data[i]['pub_time']+'</p>' + '<div class="weibo_content">'
						+ data[i]['content'] + '</div><ul class="weibo-imgs">';
					var images = data[i]['images'].split(",");
					for(var k=0; k < images.length; k++) {
						html += '<li><img src="' + images[i] + '" alt="'+data[i]['author_name']+'"></li>';
					}

					html += '</ul></div></li>';
				}

				$("ul.weibo").html(html);
			}
		});
	});
})(jQuery);