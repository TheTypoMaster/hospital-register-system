$(document).ready(function() {

	var checkBtn = $(".table-tr-clickable"),
	    patientMask = $(".page-mask"),
	    patientDetailsMask = $(".page-details-mask"),
	    adviceDetails = $(".patient-details"),
	    addBtn = $(".add-btn"),
	    detailsAdd = $(".table-details-add"),
	    adviceInput = $("#advice_input"),
	    adviceShow = $("#advice_show"),
	    adviceList = $("#advice_list");

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

	patientMask.on("click", function() {
		patientMask.fadeOut();
		patientDetailsMask.fadeOut();
	});

});