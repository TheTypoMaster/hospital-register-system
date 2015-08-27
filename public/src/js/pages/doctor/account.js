$(document).ready(function() {


	var editBtn = $(".account-edit"),
	    accountInput = $(".account-no-edit"),
	    accountSubmit = $(".account-submit"),
	    uploadBtn = $("#account_upload_btn");

    var uploader = function (options, handlers){
        var callback, config, name, uploader;
        config = $.extend({},{
            runtimes: 'html5,flash,html4',
            browse_button: 'click-file',
            uptoken_url: '/qiniu/getUpToken',
            domain: "http://7xkx3d.com1.z0.glb.clouddn.com/",
            container: 'container',
            max_file_size: '5mb',
            flash_swf_url: '/lib/plupload/Moxie.swf',
            max_retries: 3,
            dragdrop: false,
            drop_element: 'container',
            chunk_size: '4mb',
            auto_start: true,
            unique_names: true,
            save_key: true,
            statusTip: '.image-upload-tips',
            multi_selection: true,
            init: {
              'Error': function(up, err, errTip) {
                return console.log(errTip);
              },
              'BeforeUpload': function(up, file) {
                return $(this.getOption().statusTip).text('准备上传图片');
              },
              'UploadProgress': function(up, file) {
                return $(this.getOption().statusTip).text('正在上传图片');
              },
              'FileUploaded': function(up, file, info) {
                var domain;
                info = $.parseJSON(info);
                return domain = up.getOption('domain');
              },
              'UploadComplete': function() {
                return $(this.getOption().statusTip).text('图片上传成功');
              }
            }
        }, options);
        for (name in handlers) {
          callback = handlers[name];
          config.init[name] = callback;
        }
        uploader = Qiniu.uploader(config);
    }
    //编辑资料
	editBtn.on("click", function(){
		if (accountInput.hasClass("account-no-edit")) {
			accountInput.removeClass("account-no-edit").removeAttr("readonly");
			accountSubmit.fadeIn();
		};
	});

	//确定修改
	accountSubmit.on("click",function() {
		var name = $("#account_name"),
		    quality = $("#account_quality"),
		    room = $("#account_room"),
		    skill = $("#account_skill"),
		    brief = $("#account_brief");


	    accountInput.addClass("account-no-edit").attr("readonly", "readonly");
	    $(this).hide();
	});

	//图片上传
	uploadBtn.on("click", function() {
		uploader({
		    browse_button: "change_avatar",
		    container: "account_upload_btn"
		    // uptoken_url: "/qiniu/getUpToken"
		},{
		    FileUploaded: function (up,file,info) {
		        info = $.parseJSON(info);
		        domain = up.getOption("domain");
		        url = domain + info.key;
		        $(".account-avatar img").attr("src",url);
		        $.post("/user/personal/chang_image",{
		            avatar : url 
		        },function (data){
		            if(data["errCode"] == 0){
		                console.log("头像链接保存成功");
		            }
		            else{
		                console.log(data["message"]);
		                alert("头像保存失败，请重新操作");
		            }
		        },"json");

		    },
		    Error: function(up, err, errTip) {
		            return console.log(errTip);
		      }
		});
	});


});