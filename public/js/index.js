(function($){
	$(function(){
		$(".carousel").nivoSlider({ slices: 8 });

		//加载微博
		setInterval(refresh_weibo, 60000);
		setInterval(refresh_kx, 10000);

		$("ul.weibo").on("click", "img",  function(){
			var index = $(this).parent('li').index(), pindex = $(this).parents(".weibo_right").parent("li").index();
			$("#big-img").data("index", index).data("pindex", pindex);
			show_img();
		});

		refresh_rili();
	});
})(jQuery);

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

function refresh_weibo() {
	$.ajax({
		url: "/hotweibo",
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

function refresh_rili() {
	$.ajax({
		url: "/getPastorWillFd",
		data: {limit1: 5, limit2: 5},
		dataType: 'json',
		success: function (data) {
			if(data) {
				html = "<tr> <th>时间</th> <th>国家</th> <th>指标</th> <th>重要性</th> <th>前值</th> <th>预测值</th> <th>公布值</th> <th>影响</th> </tr>";
				for(var i = 0, max = data.length; i < max; i++) {
					html += "<tr> <td>"+data[i]['stime'].substr(10, 6)+"</td>" +
						'<td class="t-center"><img src="/images/flag/'+data[i]['country_cn']+'.png" alt="'+data[i]['country_cn']+'"></td>' +
						'<td>' + data[i]['title'] + ' ' + data[i]['UNIT'] +'</td>';
					var css_class = "jin-star_active";
					if(data[i]['idx_relevance'] > 2) {
						css_class = "jin-star_active jin-star_important"
					}
					html +=	'<td><div class="jin-star"><i class="'+css_class+'" style="width:'+(data[i]['idx_relevance']*20)+'%;"></i></div></td>' +
						'<td>'+(data[i]['previous_price']||"-")+'</td>' +
						'<td>'+(data[i]['surver_price']||"-")+'</td>' +
						'<td>'+(data[i]['actual_price']||"-")+'</td>' +
						'<td>'+(data[i]['res']||"-")+'</td>' +
						"</tr>";
				}

				$(".cjtb").html(html);
			}
		}
	});
}

function refresh_kx() {
	$.ajax({
		url: "/kx",
		dataType: 'json',
		success: function (data) {
			if(data) {
				html = "";
				for(var i = 0, max = data.length; i < max; i++) {
					html += '<li> <a href="/kuaixun_'+data[i]['source_site'].substr(0, 2)+data[i]['id']+'">'+data[i]['publish_time'].substr(11)+'</a> '+data[i]['body']+' </li>';
				}

				$("ul.kuaixun").html(html);
			}
		}
	});
}
