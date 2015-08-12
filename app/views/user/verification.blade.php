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

                var telephone = $('#telephone').val();
                if ( !telephone.match( /^(13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$|17[0-9]{1}[0-9]{8})$/g ) ){
                    alert( '手机号码不对哦' ); return;
                }

                $(this).addClass('btn-disabled');
                $(this).prop( 'disabled', true );
                
                var expire = 60;
                var timer = setInterval(function(){
                    if ( expire == 0 ){
                        stop();
                        return;
                    }else{
                        expire -= 1;
                    }

                    $('#captcha-btn').html( '重新获取:' + expire );

                }, 1000);

                function stop(){
                    clearInterval( timer );
                    
                    var send_btn = $('#captcha-btn');
                    send_btn.removeClass('btn-disabled');
                    send_btn.html( '发送验证码' );
                    send_btn.prop( 'disabled', false );
                }

                $.ajax({
                    url: '/user/send_verification_code',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        telephone: telephone
                    },
                    success: function( json ){
                        alert( JSON.stringify( json ) );
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