$(document).ready(function(){

	var patientMask = $(".patient-mask"),
	    patientDetailsMask = $(".patient-details-mask"),
	    tableContainer = $(".table-container"),
	    pagination = $(".pagination-container"),
	    detailsPagination = $(".details-pagination-container"),
	    patientBtn = $(".patient-td-btn"),
	    scheduleCount = $("#schedule_count"),
		patientCount = $("#patient_count");
	    	    

	// checkBtn.on("click", function() {
	// 	patientMask.fadeIn();
	// 	patientDetailsMask.fadeIn();
	// });

	//显示病人列表
	function showList (id, count){
		$(id).unbind();
		$(id).on("click", function() {

			var id = $(id).attr("data-id");
			detailsPagination.easyPaging(count, {
				onSelect: function(page) {
					console.log("当前页：" + page);
					//请求指定页数据
					$.get("/doc/get_patients", {
						page: page,
						schedule_id: id
					}, function (data){
						if(data.length !== 0){
							$(".patient-details-container").html("");
							addItems(data, "#patient_list", ".patient-details-container");
						}
					});
				}
			});

			patientMask.fadeIn();
			patientDetailsMask.fadeIn();
		});
	}

	patientMask.on("click", function() {
		patientMask.fadeOut();
		patientDetailsMask.fadeOut();
	});

	//查看病人
	patientBtn.on("click", function(){
		var id = $(this).attr("data-period");
		
	});

	//分页插件使用
	//日期列表
	pagination.easyPaging(parseInt(scheduleCount.val()), {
		onSelect: function(page) {
			console.log("当前页：" + page);
			var date = $(".patient-year option:selected").val() + "-" + $(".patient-month option:selected").val();
			//请求指定页数据
			$.get("/doc/get_schedules", {
				page: parseInt(page),
				date: date
			}, function (data){
				$(".patient-table-content").html("");
				addItems(data, "#patient_date_list", ".patient-table-content");
				showList(".patient-td-btn", data["totality"]);// 显示病人列表
			});
		}
	});
	// 病人列表
	// detailsPagination.easyPaging(100, {
	// 	onSelect: function(page) {
	// 		console.log("当前页：" + page);
	// 		//请求指定页数据
	// 		$.get("/", {
	// 			page: page
	// 		}, function (data){
	// 			msgContent.html("");
	// 			addItems(data, "#patient_list");
	// 		});
	// 	}
	// });

	//给模板代入参数
	function addItems (data, tpl_name, container) {
		var msgContent = $(container);
		var content = _.template($(tpl_name).html());
		var codes = content({
			"array": data
		});
		msgContent.append(codes);
	}

});