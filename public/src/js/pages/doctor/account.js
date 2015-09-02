$(document).ready(function() {


	var editBtn = $(".account-edit"),
	    accountInput = $(".account-no-edit"),
	    accountSubmit = $(".account-submit"),
	    uploadBtn = $("#account_upload_btn"),
	    accountRoom = $("#account_room"),
	    avatarImg = document.getElementById("change_avatar");

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
			accountRoom.removeAttr("disabled");
			accountSubmit.fadeIn();
		};
	});

	//确定修改
	accountSubmit.on("click",function() {
		var name = $("#account_name").val(),
		    quality = $("#account_quality").val(),
		    room = parseInt("10", $("#account_room option:selected").val()),
		    skill = $("#account_skill").val(),
		    brief = $("#account_brief").val();
		    // console.log(room);
	    $.post("/doc/modify_account", {
	    	name: name,
	    	title: quality,
	    	department: room,
	    	specialty: skill,
	    	description: brief
	    }, function (msg){
	    	if(msg["error_code"] == 0){
	    		$(".top-right .name").text(name);
	    		alert("修改个人资料成功");
	    	}
	    	else{
	    		alert(msg["message"]);
	    	}
	    	// console.log(msg["error_code"]);
	    });


	    accountInput.addClass("account-no-edit").attr("readonly", "readonly");
	    accountRoom.attr("disabled", "disabled");
	    $(this).hide();
	});

	// $(avatarImg).on("change", function(){
	// 	console.log(
	// 		avatarImg["value"] + "\n" + 
	// 		avatarImg["accept"] + "\n" + 
	// 		avatarImg["accessKey"] + "\n" + 
	// 		avatarImg["defaultValue"] + "\n" + 
	// 		avatarImg["id"] + "\n" + 
	// 		avatarImg["name"] + "\n" + 
	// 		avatarImg["className"] + "\n"
	// 		);
	// });

	$("#change_avatar").fileupload({
		url: "/doc/upload_portrait",
		type: "post",
		done: function(e, data){
			console.log(data.result.path);
			$(".account-avatar img, .top-right .photo").attr("src", data.result.path);
		}
	});

	//图片上传
	// uploadBtn.on("click", function() {
	// 	uploader({
	// 	    browse_button: "change_avatar",
	// 	    container: "account_upload_btn"
	// 	    // uptoken_url: "/qiniu/getUpToken"
	// 	},{
	// 	    FileUploaded: function (up,file,info) {
	// 	        info = $.parseJSON(info);
	// 	        domain = up.getOption("domain");
	// 	        url = domain + info.key;
	// 	        $(".account-avatar img").attr("src",url);
	// 	        $.post("/user/personal/chang_image",{
	// 	            avatar : url 
	// 	        },function (data){
	// 	            if(data["errCode"] == 0){
	// 	                console.log("头像链接保存成功");
	// 	            }
	// 	            else{
	// 	                console.log(data["message"]);
	// 	                alert("头像保存失败，请重新操作");
	// 	            }
	// 	        },"json");

	// 	    },
	// 	    Error: function(up, err, errTip) {
	// 	            return console.log(errTip);
	// 	      }
	// 	});
	// });


});