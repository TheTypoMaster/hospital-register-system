$(document).ready(function() {

	var patientMask = $(".page-mask"),
	    patientDetailsMask = $(".page-details-mask"),
	    pagination = $(".pagination-container"),
	    commentsContent = $(".comments-content-wrapper"),
	    commentsCount = $("#comment_count"),
	    jump = $(".jump-link"),
	    paginationCodes = $(".pagination-container").html();

    //显示评论内容
    function showContent (){
    	checkBtn = $(".table-tr-clickable");
    	checkBtn.on("click", function() {
    		patientDetailsMask.find(".table-details-content span").html($(this).find(".table-td02").html());
    		patientMask.fadeIn();
    		patientDetailsMask.fadeIn();
    	});
    }
	

	patientMask.on("click", function() {
		patientMask.fadeOut();
		patientDetailsMask.fadeOut();
	});

	//加载数据
	function loadData(page){
		var date = "";
		// console.log("当前页：" + page);
		msgYear = $(".patient-year option:selected").val(),
		msgMonth = $(".patient-month option:selected").val();
		//请求指定页数据
		$.get("/doc/get_comments", {
			page: parseInt(page),
			date: msgYear + "-" + msgMonth
		}, function (data){
			commentsContent.html("");
			addItems(data["comments"], "#comment_template");
			showContent();
		});
	}
	//分页
	pagination.easyPaging(commentsCount.val(), {
		onSelect: function(page) {
			console.log("总数：" + commentsCount.val());
			loadData(page);
		}
	});

	//给模板代入参数
	function addItems (data, tpl_name) {
		var content = _.template($(tpl_name).html());
		var codes = content({
			"array": data
		});
		commentsContent.append(codes);
	}

	//跳转到指定的日期条目
	jump.on("click", function (){
		var date = "";
		var tag = 1;
		msgYear = $(".patient-year option:selected").val(),
		msgMonth = $(".patient-month option:selected").val();
		//请求指定页数据
		$.get("/doc/get_comments", {
			page: 1,
			date: msgYear + "-" + msgMonth
		}, function (data){
			commentsContent.html("");
			addItems(data["comments"], "#comment_template");
			showContent();

			$(".pagination-container").html(paginationCodes);
			pagination.easyPaging(data["totality"], {
				onSelect: function(page) {

					loadData(page);
					
				}
			});
		});
	});

});