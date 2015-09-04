<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// 给公众号绑定域名所用接口
//Route::get( '/', 'WeixinController@response_token' );
// 消息处理接口
//Route::get( '/', 'WeixinController@response_message' );
Route::post( '/', array( 'before' => 'weixin', 'uses' => 'WeixinController@response_message' ) );

// --------------------------------- 用户端接口 start -------------------------------------------------

// 医院模块
Route::group(array( 'prefix' => 'hospital' ), function()
{
	Route::get( 'introduction', 'HospitalController@introduction' );
    Route::get( 'traffic_guide', 'HospitalController@traffic_guide' );
    Route::get( 'usage', 'HospitalController@usage' );

    // 资讯模块
    Route::group(array( 'prefix' => 'information' ), function(){
        Route::get( 'preview', 'HospitalInformationController@preview' );
        Route::get( 'detail', 'HospitalInformationController@detail' );
    });

    // 诊室模块
    Route::group(array( 'prefix' => 'department' ), function(){
        Route::get( 'overview', 'DepartmentController@overview' );
        Route::get( 'detail', 'DepartmentController@detail' );
    });
});

//用户模块
Route::group(array( 'prefix' => 'user' ), function(){

    Route::get( 'test', 'UserController@test' );
    
    Route::get( 'check_phone', 'UserController@check_phone' );
    Route::post( 'check_verification_code', 'UserController@check_verification_code' );
    Route::post( 'send_verification_code', 'UserController@send_verification_code' );

    Route::get( 'login', 'UserController@login_get' );
    Route::post( 'login', 'UserController@login_post' );
    Route::post( 'logout', 'UserController@logout' );
	Route::post( 'verify_and_reset_password', 'UserController@verify_and_reset_password' );
    
    // 重置密码
	Route::group(array( 'prefix' => 'reset_password' ), function(){
        Route::get( 'first', 'UserController@reset_password_first' );
        Route::get( 'second', 'UserController@reset_password_second' );
    });

    // 注册
    Route::group(array( 'prefix' => 'register' ), function(){
        Route::post( '/', 'UserController@register_post' );
        Route::get( 'first', 'UserController@register_first' );
        Route::get( 'second', 'UserController@register_second' );
        Route::get( 'success', 'UserController@register_success' );
    });

    Route::group(array( 'before' => 'auth.user.is_in' ), function(){
        Route::get( 'center', 'UserController@user_center' );
        Route::get( 'pay_record', 'UserController@pay_record' );
        Route::get( 'get_chat_package', 'UserController@get_chat_package' );
        Route::post( 'modify_user', 'UserController@modify_user' );
        Route::post( 'upload_head_portrait', 'UserController@upload_head_portrait' );        
    });

    // 挂号记录模块
    Route::group(array( 'prefix' => 'record', 'before' => 'auth.user.is_in' ), function(){
        Route::get( 'get_records', 'RegisterRecordController@get_records' );
        Route::post( 'add_record', 'RegisterRecordController@add_record' );
        Route::post( 'add_return_date', 'RegisterRecordController@add_return_date' );
        Route::post( 'modify_status', 'RegisterRecordController@modify_status' );
        Route::post( 'cancel', 'RegisterRecordController@cancel' );
    });

    // 挂号账户模块
    Route::group(array( 'prefix' => 'register_account', 'before' => 'auth.user.is_in' ), function(){
        Route::get( 'get_accounts', 'RegisterAccountController@get_accounts' );
        Route::get( 'detail', 'RegisterAccountController@detail' );
        Route::post( 'modify_account', 'RegisterAccountController@modify_account' );
        Route::post( 'add_account', 'RegisterAccountController@add_account' );
        Route::post( 'delete_account', 'RegisterAccountController@delete_account' );
    });

    // 评论模块
    Route::group(array( 'prefix' => 'comment', 'before' => 'auth.user.is_in' ), function(){
        Route::get( 'get_comments', 'CommentController@get_user_comments' );
        Route::post( 'add_comment', 'CommentController@add_comment' );
    });

    // 反馈模块
    Route::group(array( 'prefix' => 'feedback', 'before' => 'auth.user.is_in' ), function(){
        Route::get( 'index', 'FeedbackController@index' );
        Route::post( 'add_feedback', 'FeedbackController@add_feedback' );
        Route::get( 'success', 'FeedbackController@success' );
    });
});

// 医生模块
Route::group(array( 'prefix' => 'doctor', 'before' => 'auth.user.is_in' ), function(){
    Route::get( 'get_comments', 'CommentController@get_doctor_comments' );
    Route::get( 'get_doctors', 'DoctorController@get_doctors' );
    Route::get( 'get_schedules', 'ScheduleController@get_schedules' );
    Route::get( 'get_periods', 'PeriodController@get_periods' );
    Route::get( 'success', 'RegisterController@success' );
});

// 挂号模块
// for weixin
Route::group(array( 'prefix' => 'register', 'before' => 'auth.user.is_in' ), function(){
    Route::get( 'select_department', 'RegisterController@select_department'  );
    Route::get( 'select_doctor', 'RegisterController@select_doctor' );
    Route::get( 'select_schedule', 'RegisterController@select_schedule' );
    Route::get( 'select_period', 'RegisterController@select_period' );
    Route::get( 'success', 'RegisterController@success' );
});

Route::group(array( 'prefix' => 'pay' ), function(){
    Route::get( 'wxpay_app', array( 'before' => 'auth.is_in', 'uses' => 'PayController@wxpay_app' ) );
    Route::get( 'wxpay_js', 'PayController@wxpay_js' );
    Route::post( 'wxpay_notify', 'PayController@wxpay_notify' );
});

// --------------------------------- 用户端接口 end -------------------------------------------------


// --------------------------------- 医生客户端web接口 start ----------------------------------------

Route::group(array( 'prefix' => 'doc' ), function(){

    Route::get( 'login', 'DoctorPageController@login' );
    Route::post( 'login', 'DoctorController@login' );

    Route::group(array( 'before' => 'auth.doc_is_in' ), function(){

        Route::post( 'modify_account', 'DoctorController@modify_account' );
        Route::post( 'modify_advice', 'DoctorController@modify_advice' );
        Route::post( 'modify_status', 'DoctorController@modify_status' );
        Route::post( 'modify_return', 'DoctorController@modify_return' );
        Route::post( 'modify_message_status', 'DoctorController@modify_message_status' );

        Route::get( 'logout', 'DoctorController@logout' );
        Route::post( 'modify', 'DoctorController@modify_doctor' );
        Route::post( 'upload_portrait', 'DoctorController@upload_portrait' );

        Route::get( 'get_records', 'DoctorPageController@get_records' );
        Route::get( 'get_record_detail', 'DoctorPageController@get_record_detail' );
        Route::get( 'get_records_bs', 'DoctorPageController@get_records_by_schedule');
        Route::get( 'get_schedules', 'DoctorPageController@get_schedules');
        Route::get( 'get_comments', 'DoctorPageController@get_comments' );
        Route::get( 'get_advice', 'DoctorPageController@get_advice' );
        Route::get( 'get_null_advice', 'DoctorPageController@get_null_advice' );
        Route::get( 'get_messages', 'DoctorPageController@get_messages' );

        Route::group(array( 'prefix' => 'home' ), function(){
            Route::get( '/', 'DoctorPageController@home' );
            Route::get( 'chat', 'DoctorPageController@chat' );
            Route::get( 'account', 'DoctorPageController@account' );
            Route::get( 'patient', 'DoctorPageController@patient' );
            Route::get( 'comment', 'DoctorPageController@comment' );
            Route::get( 'advice', 'DoctorPageController@advice' );
            Route::get( 'message', 'DoctorPageController@message' );
        });
    });
});

// --------------------------------- 医生客户端web接口 end ------------------------------------------