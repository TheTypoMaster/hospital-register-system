$(document).ready(function(){

	var patientMask = $(".patient-mask"),
	    patientDetailsMask = $(".patient-details-mask"),
	    tableContainer = $(".table-container"),
	    pagination = $(".pagination-container"),
	    detailsPagination = $(".details-pagination-container"),
	    detailsPaginationHtml = detailsPagination.html(),
	    patientBtn = $(".patient-td-btn"),
	    scheduleCount = $("#schedule_count"),
		patientCount = $("#patient_count"),
		scheduleId = 0;
	    jump = $(".jump-link"),
	    paginationCodes = $(".pagination-container").html();

	//显示病人列表
	$(document).on("click", ".patient-td-btn", function() {

		//获取日期
		var btnParent = $(this).parent().parent().find(".patient-td:nth-child(1)").text();
	    patientDetailsMask.find(".patient-details-td01").html(btnParent);

		var id = $(this).attr("data-id");
		$.get("/doc/get_records_bs", {
			page: 1,
			schedule_id: id
		}, function (data){
			if(data.length !== 0){
				$(".patient-details-container").html("");
				addItems(data["patients"], "#patient_list", ".patient-details-container");
			}

			detailsPagination.html(detailsPaginationHtml).easyPaging(data["totality"], {
				onSelect: function(page) {
					// console.log("当前页：" + page);
					//请求指定页数据
					$.get("/doc/get_records_bs", {
						page: page,
						schedule_id: id
					}, function (data){
						if(data.length !== 0){
							$(".patient-details-container").html("");
							addItems(data["patients"], "#patient_list", ".patient-details-container");
						}
					});
				}
			});


		});

		patientMask.fadeIn();
		patientDetailsMask.fadeIn();
	});

	patientMask.on("click", function() {
		patientMask.fadeOut();
		patientDetailsMask.fadeOut();
	});


	//加载数据
	function loadData(page){
		// console.log("当前页：" + page);
		var date = $(".patient-year option:selected").val() + "-" + $(".patient-month option:selected").val();
		//请求指定页数据

		$.get("/doc/get_schedules", {
			page: parseInt(page),
			date: date
		}, function (data){
			$(".table-container").html("");
			addItems(data["schedules"], "#patient_date_list", ".table-container");
			// showList(".patient-td-btn", data["totality"]);// 显示病人列表
		});
	}

	//分页插件使用
	//日期列表
	pagination.easyPaging(parseInt(scheduleCount.val()), {
		onSelect: function(page) {
			loadData(page);
		}
	});

	//给模板代入参数
	function addItems (data, tpl_name, container) {
		var msgContent = $(container);
		var content = _.template($(tpl_name).html());
		var codes = content({
			"array": data
		});
		msgContent.append(codes);
	}

	//跳转到指定的日期条目
	jump.on("click", function (){
		var date = "";
		var tag = 1;
		msgYear = $(".patient-year option:selected").val(),
		msgMonth = $(".patient-month option:selected").val();
		//请求指定页数据
		$.get("/doc/get_schedules", {
			page: 1,
			date: msgYear + "-" + msgMonth
		}, function (data){
			tableContainer.html("");
			addItems(data["schedules"], "#patient_date_list", ".table-container");
			// showList(".patient-td-btn", data["totality"]);// 显示病人列表

			$(".pagination-container").html(paginationCodes);
			pagination.easyPaging(data["totality"], {
				onSelect: function(page) {
					console.log(data["totality"]);
					loadData(page);
					
				}
			});
		});
	});

	//修改就诊状态
	$(document).on("click", ".patient-status-not", function(){
		var _this = $(this);
		$.post("/doc/modify_status", {
			record_id: $(this).parent().attr("data-id"),
			status: 1
		}, function (data){
			if(data["error_code"] == 0){
				_this.hide();
			}
			else{
				alert(data["message"]);
			}
			
		})
	});
	

});