@extends('layouts.master')

@section('title')
    找回密码
@stop

@section('css')
    @parent
    <link rel="stylesheet" type="text/css" href="/dist/css/user/reset_password.css" />
@stop

@section('js-lib')
    @parent
@stop

@section('js-common')
    @parent
@stop

@section('js-specify')
    <script type="text/javascript">
        $(document).ready(function(){
            $('.form').on('submit', function( event ){
                event.preventDefault();

                var new_password = $('#new_password').val();
                var password_cfm = $('#password_cfm').val();

                if ( !new_password.match( /^[a-zA-Z0-9]\w{5,15}$/g ) ){
                    alert( '密码格式不正确' ); return;
                }

                if ( new_password != password_cfm ){
                    alert( '确认密码错误' ); return;
                }

                $.ajax({
                    url: '/user/reset_password',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        new_password: new_password
                    },
                    success: function ( json ){
                        alert( json.message );

                        if ( json.error_code == 0 ){
                            window.location.href = '/user/login';
                        }
                    }
                });
                
            });
        });
    </script>
@stop

@section('body-title')
@stop

@section('body-main')
    <form class="form" method="post" action="#">
        <div class="form-blk clearfix">
            <span class="input-key">新密码</span><span class="fucking-colon">：</span>
            <input class="input-box" id="new_password" placeholder="6-16位字母、数字、下划线的组合" name="new_password" type="password">
        </div>
        <div class="form-blk clearfix">
            <span class="input-key">确认密码</span><span class="fucking-colon">：</span>
            <input class="input-box" id="password_cfm" placeholder="确认密码" name="password_cfm" type="password">
        </div>
        <input class="btn" type="submit" value="提交">
    </form>
@stop