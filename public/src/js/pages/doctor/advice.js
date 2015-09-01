$(document).ready(function() {

	var patientMask = $(".page-mask"),
	    patientDetailsMask = $(".page-details-mask"),
	    adviceDetails = $(".patient-details"),
	    addBtn = $(".add-btn"),
	    detailsAdd = $(".table-details-add"),
	    adviceInput = $("#advice_input"),
	    adviceShow = $("#advice_show"),
	    adviceList = $("#advice_list"),
	    adviceListAdd = adviceList.find(".table-details-content"),
	    adviceSubmit = $(".submit-btn"),
	    adviceContent = $("#advice_content"),
	    pagination = $(".list-pagination"),
	    paginationAdd = $(".add-list-pagination"),
	    paginationAddHtml = $(".add-list-pagination").html(),
	    adviceContainer = $(".advice-content-container"),
	    adviceInputName = $("#advice_input_name");
	    jump = $(".jump-link"),
	    paginationCodes = pagination.html(),
	    curPage = 1;//记录当前页的页码

    function showPannel () {
    	patientMask.fadeIn();
    	patientDetailsMask.fadeIn();
    }

    //查看医嘱
    function showContent() {
    	var checkBtn = $(".table-tr-clickable");
		checkBtn.on("click", function() {
	    	adviceDetails.css("display", "none");
	    	adviceShow.find(".table-details-content span").html($(this).find(".table-td02").html());
	    	adviceShow.fadeIn();
			showPannel();
		});
    }
	
	//一级增加医嘱
	addBtn.on("click", function() {
		$.get("/doc/get_null_advice", {
			page: 1,
			date: $(".patient-year option:selected").val() + "-" + $(".patient-month option:selected").val()
		}, function (data){
			adviceListAdd.html("");
			addItems(data["records"], "#advice_list_template", "#advice_list .table-details-content");
			addBoard();

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
						addBoard();
					});
				}
			});

		});

    	adviceDetails.css("display", "none");
    	adviceList.fadeIn();
		showPannel();
		
	});
	//二级增加医嘱
	function addBoard (){
		detailsAdd = $(".table-details-add");
		var recordId = detailsAdd.attr("data-record-id");
		detailsAdd.on("click", function() {
	    	adviceDetails.css("display", "none");

	    	adviceInputName.html($(this).prev().html()); 

	    	adviceInput.fadeIn();
			showPannel();
			adviceSub(recordId);
		});
	}
	

	//隐藏浮层
	patientMask.on("click", function() {
		patientMask.fadeOut();
		patientDetailsMask.fadeOut();
	});

	//提交医嘱
	function adviceSub(id) {
		adviceSubmit.unbind();
		adviceSubmit.on("click", function() {
			var content = $(adviceContent).val();

			$.post("/doc/modify_advice", {
				record_id: id,
				advice: content
			}, function (data){
				if(data["error_code"] == 0){
					alert("添加医嘱成功");
					// patientMask.fadeOut();
					// patientDetailsMask.fadeOut();
			    	adviceDetails.css("display", "none");
			    	adviceList.css("display", "block");
					showPannel();
				}
				else{
					alert(data["message"]);
				}
			});

		});
	}
	

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
			showContent();
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