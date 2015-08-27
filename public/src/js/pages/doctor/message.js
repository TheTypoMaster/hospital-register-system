$(document).ready(function() {

	var checkBtn = $(".table-tr-clickable"),
	    patientMask = $(".page-mask"),
	    patientDetailsMask = $(".page-details-mask");

	checkBtn.on("click", function() {
		patientMask.fadeIn();
		patientDetailsMask.fadeIn();
	});

	//隐藏浮层
	patientMask.on("click", function() {
		patientMask.fadeOut();
		patientDetailsMask.fadeOut();
	});    

});