$(function(){
    lists_residential("北辰区");

   $("#residential").on("click", ".area-init", function() {
        if($(this).find("span.up").text() == "∧") {
            $(this).find("span.up").text("∨");
            $(this).siblings(".lists").find("ul").removeClass("show");
        }
        else {
            $(this).find("span.up").text("∧");
            $(this).siblings(".lists").find("ul").addClass("show");
        }
   });

   $(".area li").click(function() {
        let area = $(this).find("a").text();
        lists_residential(area);
        $(this).addClass("cur").siblings("li").removeClass("cur");
   });

});

function lists_residential(area) {
    $.ajax({
        url: "/areahouse",
        data: {area: area},
        success: function (data) {
            let datakeys = Object.keys(data).sort();

            let navhtml = "<ul class='nav'>";
            for(let w of datakeys) {
                navhtml += `<li><a href='#${w}'>${w}</a>`;
            }
            navhtml += `</ul>`;
            $(".nav").html(navhtml);

            let html = "";
            for(let i in datakeys) {
                let w = datakeys[i];
                html += `
                    <div class="area-house">
                        <div class="area-init" id="${w}">
                            <span>${w}</span>
                            <span class="up">${i==0?'∧':'∨'}</span>
                        </div>
                        <div class="lists">`;
                if(i == 0) {
                    html += `<ul class="show">`;
                }
                else {
                    html += `<ul>`;
                }


                for(let info of data[w]) {
                    html += `<li><a href="/house?id=${info['id']}">${info['name']}</a></li>`;
                }

                html += `</ul> </div> </div>`;
            }

            $("#residential").html(html);
        }
    });
}