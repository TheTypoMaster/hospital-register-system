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
	    adviceContent = $("#advice_content");

    var showPannel = function() {
    	patientMask.fadeIn();
    	patientDetailsMask.fadeIn();
    }

    //查看医嘱
	checkBtn.on("click", function() {
    	adviceDetails.css("display", "none");
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

});