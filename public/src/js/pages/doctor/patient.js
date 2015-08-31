$(document).ready(function(){

	var checkBtn = $(".patient-td-btn"),
	    patientMask = $(".patient-mask"),
	    patientDetailsMask = $(".patient-details-mask"),
	    tableContainer = $(".table-container"),
	    pagination = $(".pagination-container"),
	    detailsPagination = $(".details-pagination-container");

	checkBtn.on("click", function() {
		patientMask.fadeIn();
		patientDetailsMask.fadeIn();
	});

	patientMask.on("click", function() {
		patientMask.fadeOut();
		patientDetailsMask.fadeOut();
	});

	//分页插件使用
	// 日期列表
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
	// 病人列表
	detailsPagination.easyPaging(100, {
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
			tableContainer.append(codes);
		}
	}

});