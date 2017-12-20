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
		setTimeout(refresh_weibo, 60000);
		$("ul.weibo").on("click", "img",  function(){
			$(".yj-backdrop").show();
			$("#big-img").show();
			$("#big-img img").attr("src", $(this).attr("src").replace("thumb150", "mw690"));
		});
		$(".yj-backdrop,#big-img").click(function(){
			$("#big-img img").removeAttr("src");
			$(".yj-backdrop,#big-img").hide();
		});
	});
})(jQuery);

function refresh_weibo() {
	$.ajax({
		url: "/weibo",
		dataType: 'json',
		success: function(data) {
			var html = "";
			for(var i = 0, max = data.length; i < max; i ++) {
				html += ' <li> <div class="weibo_left"><a href="'+data[i]['author_link']+'"><img src="' + data[i]['author_img'] + '"></a></div> <div class="weibo_right">'
						+ '<h4><a href="'+data[i]['author_link']+'">' + data[i]['author_name'] + '</a></h4>'
						+ '<p><a href="'+data[i]['source_url']+'">'+data[i]['pub_time']+'</a></p>' + '<div class="weibo_content">'
						+ data[i]['content'] + '</div>';
				if(data[i]['images']) {
					html += '<ul class="weibo-imgs">';
					var images = data[i]['images'].split(",");
					for(var k=0; k < images.length; k++) {
						html += '<li><img src="' + images[k] + '" alt="'+data[i]['author_name']+'"></li>';
					}

					html += '</ul>';
				}

				html += '</div></li>';
			}

			$("ul.weibo").html(html);
		}
	});
}
