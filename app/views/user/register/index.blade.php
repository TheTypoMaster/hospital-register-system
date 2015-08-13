@extends('layouts.master')

@section('title')
    注册
@stop

@section('css')
    @parent
    <link rel="stylesheet" type="text/css" href="/dist/css/user/register/index.css" />
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

            $('.register-form').on('submit', function( event ){
                event.preventDefault();

                var password = $('#password').val();
                var password_cfm = $('#password_cfm').val();

                if ( !password.match( /^[a-zA-Z0-9]\w{5,15}$/g ) ){
                    alert( '密码格式错误' ); return;
                }

                if ( password != password_cfm ){
                    alert( '确认密码错误' ); return;
                }

                if ( !$('#pro-check').prop( 'checked' ) ){
                    alert( '请阅读用户协议' ); return;
                }

                $.ajax({
                    url: '/user/register',
                    type: 'POST',
                    dataType: 'json',
                    data: $('.register-form').serialize(),
                    success: function( json ){
                        if ( json.error_code ){
                            alert( json.message );
                        }else{
                            window.location.href = '/user/register/success';
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
    <form class="register-form" method="post" action="#">
        <div class="form-blk clearfix">
            <span class="input-key">昵称</span><span class="fucking-colon">：</span>
            <input class="input-box" id="nickname" placeholder="昵称" name="nickname" type="text">
        </div>
        <div class="form-blk clearfix">
            <span class="input-key">密码</span><span class="fucking-colon">：</span>
            <input class="input-box" id="password"placeholder="6-16位字母、数字、下划线的组合" name="password" type="password">
        </div>
        <div class="form-blk clearfix">
            <span class="input-key">确认密码</span><span class="fucking-colon">：</span>
            <input class="input-box" id="password_cfm" name="password_cfm" type="password">
        </div>
        <div class="form-blk clearfix">
            <span class="input-key">真实姓名</span><span class="fucking-colon">：</span>
            <input class="input-box" placeholder="真实填写，提交后无法修改" name="real_name" type="text">
        </div>
        <div class="form-blk clearfix">
            <span class="input-key">性别</span><span class="fucking-colon">：</span>
            <select class="gender-select" name="gender">
                <option value="0">&nbsp&nbsp&nbsp男</option>
                <option value="1">&nbsp&nbsp&nbsp女</option>
            </select>
        </div>
        <div class="form-blk clearfix">
            <span class="input-key">注册协议</span><span class="fucking-colon">：</span>
            <span class="user-protocol">
                <input id="pro-check" class="checkbox" type="checkbox">
                <span>我已阅读并接受<a class="protocol-link" href="#">用户协议！</a></span>
            </span>
        </div>
        <input class="btn" type="submit" value="提交">
    </form>
@stop