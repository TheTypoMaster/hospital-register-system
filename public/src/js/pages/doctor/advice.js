$(document).ready(function() {

	var patientMask = $(".page-mask"),
		patientDetailsMask = $(".page-details-mask"),
		adviceDetails = $(".patient-details"),
		addBtn = $(".add-btn"),
		adviceInput = $("#advice_input"),
		adviceShow = $("#advice_show"),
		adviceList = $("#advice_list"),
		adviceListAdd = adviceList.find(".table-details-content"),
		adviceContent = $("#advice_content"),
		pagination = $(".list-pagination"),
		paginationAdd = $(".add-list-pagination"),
		paginationAddHtml = $(".add-list-pagination").html(),
		adviceContainer = $(".advice-content-container"),
		adviceInputName = $("#advice_input_name");
		jump = $(".jump-link"),
		paginationCodes = pagination.html(),
		recordId = 0;//记录当前页的页码

	function showPannel () {
		patientMask.fadeIn();
		patientDetailsMask.fadeIn();
	}

	//查看医嘱
	$(document).on("click", ".table-tr-clickable", function() {
		adviceDetails.css("display", "none");
		adviceShow.find(".table-details-content span").html($(this).find(".table-td02").html());
		adviceShow.fadeIn();
		showPannel();
	});
	
	//一级增加医嘱
	$(document).on("click", ".add-btn", function() {
		$.get("/doc/get_null_advice", {
			page: 1,
			date: $(".patient-year option:selected").val() + "-" + $(".patient-month option:selected").val()
		}, function (data){
			adviceListAdd.html("");
			addItems(data["records"], "#advice_list_template", "#advice_list .table-details-content");

			paginationAdd.html(paginationAddHtml).easyPaging(data["totality"], {
				onSelect: function (page){
					var date = "";
					// console.log("当前页：" + page);
					msgYear = $(".patient-year option:selected").val(),
					msgMonth = $(".patient-month option:selected").val();
					//请求指定页数据
					$.get("/doc/get_null_advice", {
						page: page,
						date: msgYear + "-" + msgMonth
					}, function (data){
						adviceListAdd.html("");
						addItems(data["records"], "#advice_list_template", "#advice_list .table-details-content");
					});
				}
			});

		});

		adviceDetails.css("display", "none");
		adviceList.fadeIn();
		showPannel();
		
	});
	//二级增加医嘱
	$(document).on("click", ".table-details-add", function() {
		recordId = $(this).attr("data-record-id");
		adviceDetails.css("display", "none");

		adviceInputName.html($(this).prev().html()); 
		
		adviceInput.fadeIn();
		showPannel();
	});

	//隐藏浮层
	patientMask.on("click", function() {
		patientMask.fadeOut();
		patientDetailsMask.fadeOut();
	});

	//提交医嘱
	$(document).on("click", ".submit-btn", function() {
		var content = $(adviceContent).val();

		$.post("/doc/modify_advice", {
			record_id: recordId,
			advice: content
		}, function (data){
			if(data["error_code"] == 0){
				alert("添加医嘱成功");
				// patientMask.fadeOut();
				// patientDetailsMask.fadeOut();
				adviceDetails.css("display", "none");
				adviceContent.val("");
				adviceList.css("display", "block");
				showPannel();
			}
			else{
				alert(data["message"]);
			}
		});

	});

	//加载数据
	function loadData(page){
		var date = "",
			curPage = page;
		// console.log("当前页：" + page);
		msgYear = $(".patient-year option:selected").val(),
		msgMonth = $(".patient-month option:selected").val();
		//请求指定页数据
		$.get("/doc/get_advice", {
			page: parseInt(page),
			date: msgYear + "-" + msgMonth
		}, function (data){
			adviceContainer.html("");
			addItems(data["advice"], "#advice_template", ".advice-content-container");
		});
	}
	//分页
	pagination.easyPaging(20, {
		onSelect: function(page) {
			loadData(page);
		}
	});

	//给模板代入参数
	function addItems (data, tpl_name, container) {
		var container = $(container);
		var content = _.template($(tpl_name).html());
		var codes = content({
			"array": data
		});
		container.append(codes);
	}

	//跳转到指定的日期条目
	jump.on("click", function (){
		var date = "";
		var tag = 1;
		msgYear = $(".patient-year option:selected").val(),
		msgMonth = $(".patient-month option:selected").val();
		//请求指定页数据
		$.get("/doc/get_advice", {
			page: 1,
			date: msgYear + "-" + msgMonth
		}, function (data){
			adviceContainer.html("");
			addItems(data["advice"], "#advice_template");

			$(".pagination-container").html(paginationCodes);
			pagination.easyPaging(data["totality"], {
				onSelect: function(page) {
					
					loadData(page);
					
				}
			});
		});
	});


});