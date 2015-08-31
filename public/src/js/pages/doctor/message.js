$(document).ready(function() {

	var checkBtn = $(".table-tr-clickable"),
	    patientMask = $(".page-mask"),
	    patientDetailsMask = $(".page-details-mask"),
	    pagination = $("#message_pagination"),
	    msgContent = $("#message_content"),
	    tdContent = $(".table-td02"),
	    msgDetails = $("#message_details");

	checkBtn.on("click", function() {
		msgDetails.find("span").html(tdContent.html());
		patientMask.fadeIn();
		patientDetailsMask.fadeIn();
	});

	//隐藏浮层
	patientMask.on("click", function() {
		patientMask.fadeOut();
		patientDetailsMask.fadeOut();
		msgDetails.find("span").html("");
	});    

	//分页插件使用
	pagination.easyPaging(100, {
		onSelect: function(page) {
			console.log("当前页：" + page);
			//请求指定页数据
			$.get("/", {
				page: page
			}, function (data){
				msgContent.html("");
				addItems(data, "#message_template");
			});
		}
	});

	//给模板代入参数
	function addItems (data, tpl_name) {
		for(var i = 0; i < data.length; i ++){
			var content = _.template($(tpl_name).html());
			var codes = content({
				"date" : data[i]["date"],
				"content" : date[i]["content"]
			});
			msgContent.append(codes);
		}
	}

});