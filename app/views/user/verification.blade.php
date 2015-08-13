@extends('layouts.master')

@section('title')
    {{{ $title }}}
@stop

@section('css')
    @parent
    <link rel="stylesheet" type="text/css" href="/dist/css/user/verification.css" />
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
            $('#captcha-btn').on('click', function( event ){
                event.preventDefault();

                var captcha_btn = $(this);
                var telephone = $('#telephone').val();

                var on_check_phone_valid = function(){
                    if ( !telephone.match( /^(13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$|17[0-9]{1}[0-9]{8})$/g ) ){
                        alert( '手机号码不对哦' ); return;
                    }

                    captcha_btn.addClass('btn-disabled');
                    captcha_btn.prop( 'disabled', true );
                    
                    var expire = 60;
                    var timer = setInterval(function(){
                        if ( expire == 0 ){
                            stop();
                            return;
                        }else{
                            expire -= 1;
                        }

                        captcha_btn.html( '重新获取:' + expire );

                    }, 1000);

                    function stop(){
                        clearInterval( timer );
                        
                        captcha_btn.removeClass('btn-disabled');
                        captcha_btn.html( '发送验证码' );
                        captcha_btn.prop( 'disabled', false );
                    }

                    $.ajax({
                        url: '/user/send_verification_code',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            telephone: telephone
                        },
                        success: function( result ){
                            alert( result.message );
                            //alert( JSON.stringify( result ) );
                        }
                    });
                }

                $.ajax({
                    url: '/user/check_phone',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        telephone: telephone
                    },
                    success: function ( result ){
                        if ( result.error_code == {{{ $pass_code }}} ){
                            on_check_phone_valid();
                        }else{
                            alert( result.message );
                        }
                    }
                });
                
            });

            $('.form').on('submit', function(event) {
                event.preventDefault();
                
                if ( $('#captcha').val().length == 0 ){
                    alert( '请输入验证码' ); return;
                }

                $.ajax({
                    url: '/user/check_verification_code',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        verification_code: $('#captcha').val()
                    },
                    success: function( json ){
                        if ( json.error_code ){
                            alert( json.message );
                        }else{
                            window.location.href = '{{{ $next_url }}}';
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
    <form class="form clearfix" action="#">
        <div class="input-wrap">
            <img src="/images/icons/phone.png" class="input-icon">
            <input type="text" id="telephone" name="telephone" placeholder="您的手机号码" class="input-box">
        </div>

        <div id="captcha-wrap" class="clearfix">
            <div class="input-wrap">
                <img src="/images/icons/lock.png" class="input-icon">
                <input type="text" id="captcha" name="verification_code" placeholder="输入验证码" class="input-box">
            </div>
            <button id="captcha-btn" class="btn">获取验证码</button>
        </div>

        <div class="submit-wrap">
            <input class="submit-btn btn" type="submit" value="下一步">
        </div>
    </form>
@stop