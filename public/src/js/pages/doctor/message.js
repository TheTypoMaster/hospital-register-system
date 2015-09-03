$(document).ready(function() {

	var patientMask = $(".page-mask"),
	    patientDetailsMask = $(".page-details-mask"),
	    pagination = $("#message_pagination"),
	    msgContent = $("#message_content"),
	    msgDetails = $("#message_details"),
	    jump = $(".jump-link"),
	    count = $("#message_count").val(),
	    paginationCodes = $("#message_pagination").html();

	//显示内容
	$(document).on("click", ".table-tr-clickable", function() {
		var _this = $(this);
		// console.log("点击");
		// msgDetails.find("span").html(_this.find(".table-td02").html());
		// patientMask.fadeIn();
		// patientDetailsMask.fadeIn();
		//修改信息阅读状态
		if(_this.attr("data-status") == 3){
			$.post("/doc/modify_message_status", {
				message_id: _this.attr("data-id"),
				status: 4
			},function (data){
				if(data["error_code"] == 0){
					_this.find(".message-tag").css("display", "none");
					_this.find(".table-td02").addClass('al-read');
					_this.attr("data-status","4");
				}
				else{
					alert(data["message"]);
				}
			});
		}

	});

	//隐藏浮层
	patientMask.on("click", function() {
		patientMask.fadeOut();
		patientDetailsMask.fadeOut();
		msgDetails.find("span").html("");
	});    

	//初始化请求页

	//加载数据
	function loadData(page){
		var date = "";
		msgYear = $(".patient-year option:selected").val(),
		msgMonth = $(".patient-month option:selected").val();
		//请求指定页数据
		$.get("/doc/get_messages", {
			page: parseInt(page),
			date: msgYear + "-" + msgMonth
		}, function (data){
			msgContent.html("");
			addItems(data["messages"], "#message_template");
		});
	}
	//分页插件使用
	pagination.easyPaging(count, {
		onSelect: function(page) {
			loadData(page);
		}
	});

	//给模板代入参数
	function addItems (data, tpl_name) {
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
		$.get("/doc/get_messages", {
			page: 1,
			date: msgYear + "-" + msgMonth
		}, function (data){
			msgContent.html("");
			addItems(data["messages"], "#message_template");

			$("#message_pagination").html(paginationCodes);
			pagination.easyPaging(data["totality"], {
				onSelect: function(page) {

					loadData(page);

				}
			});
		});
	});

	

});