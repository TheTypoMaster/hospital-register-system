$(document).ready(function() {

	var checkBtn = $(".table-tr-clickable"),
	    patientMask = $(".page-mask"),
	    patientDetailsMask = $(".page-details-mask"),
	    adviceDetails = $(".patient-details"),
	    addBtn = $(".add-btn"),
	    detailsAdd = $(".table-details-add"),
	    adviceInput = $("#advice_input"),
	    adviceShow = $("#advice_show"),
	    adviceList = $("#advice_list"),
	    adviceSubmit = $(".submit-btn"),
	    adviceContent = $("#advice_content"),
	    jump = $(".jump-link"),
	    paginationCodes = $(".pagination-container").html();

    function showPannel () {
    	checkBtn = $(".table-tr-clickable");
    	checkBtn.on("click", function() {
    		patientDetailsMask.find(".table-details-content span").html($(this).find(".table-td02").html());
    		patientMask.fadeIn();
    		patientDetailsMask.fadeIn();
    	});
    }

    //

    //查看医嘱
	checkBtn.on("click", function() {
    	adviceDetails.css("display", "none");
    	adviceShow.find(".table-details-content span").html($(this).find(".table-td02").html());
    	adviceShow.fadeIn();
		showPannel();
		
	});
	//一级增加医嘱
	addBtn.on("click", function() {
    	adviceDetails.css("display", "none");
    	adviceList.fadeIn();
		showPannel();
		
	});
	//二级增加医嘱
	detailsAdd.on("click", function() {
    	adviceDetails.css("display", "none");
    	adviceInput.fadeIn();
		showPannel();
		
	});

	//隐藏浮层
	patientMask.on("click", function() {
		patientMask.fadeOut();
		patientDetailsMask.fadeOut();
	});

	//提交医嘱
	adviceSubmit.on("click", function() {
		var content = $(adviceContent).val();
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
		$.get("/doc/get_advice", {
			page: parseInt(page),
			date: msgYear + "-" + msgMonth
		}, function (data){
			commentsContent.html("");
			addItems(data["comments"], "#advice_template");
			showContent();
		});
	}
	//分页
	pagination.easyPaging(commentsCount.val(), {
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
		commentsContent.append(codes);
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
			commentsContent.html("");
			addItems(data["comments"], "#advice_template");
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