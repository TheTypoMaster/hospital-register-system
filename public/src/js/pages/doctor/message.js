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

	//分页插件使用
	$("#message_pagination").easyPaging(100, {
		onSelect: function(page) {
			console.log("当前页：" + page);
		}
	});

});