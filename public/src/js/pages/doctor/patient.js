$(document).ready(function(){

	var checkBtn = $(".patient-td-btn"),
	    patientMask = $(".patient-mask"),
	    patientDetailsMask = $(".patient-details-mask");

	checkBtn.on("click", function() {
		patientMask.fadeIn();
		patientDetailsMask.fadeIn();
	});

	patientMask.on("click", function() {
		patientMask.fadeOut();
		patientDetailsMask.fadeOut();
	});

});