@extends('layouts.master')

@section('title')
    预约挂号
@stop

@section('css')
    @parent
    <link rel="stylesheet" type="text/css" href="/dist/css/register/pay.css" />
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
			
            // 调用添加挂号记录接口
            function add_register_record(){
                var is_add_record_ok = false;
                $.ajax({
                    url: '/user/record/add_record',
                    type: 'POST',
                    dataType: 'json',
					async: false,
                    data: { 
                        period_id: {{{ $period['id'] }}}
                    },
                    success: function( result ){
                        if ( result.error_code == 0 ){
                            is_add_record_ok = true;
                        }
                        alert( result.message );
                    }
                });
                return is_add_record_ok;
            }
    
            function wxpay_js_call(){
				WeixinJSBridge.invoke(
                    'getBrandWCPayRequest', 
					{{ $para }},
                    function( response ){
						//alert( 'Cookies: ' + document.cookie );
						//alert( JSON.stringify( response ) );
                        if ( response.err_msg == "get_brand_wcpay_request:ok" ){
                            alert( '支付成功' );
                            if ( add_register_record() ){
                                window.location.href = '/register/success';
                            }else{
                                alert( '添加挂号记录失败' );
                            }
                        }
                    }
                );
            }

            /**
             * 通用调用接口
             */
            function call_invoke_func( invoke_func ){
				if ( typeof WeixinJSBridge == "undefined" ){
                    if( document.addEventListener ){
						document.addEventListener( 'WeixinJSBridgeReady', invoke_func, false );
                    }else if ( document.attachEvent ){
                        document.attachEvent( 'WeixinJSBridgeReady', invoke_func ); 
                        document.attachEvent( 'onWeixinJSBridgeReady', invoke_func );
                    }
                }else{
                    invoke_func();
                }
            }

            $('.confirm .btn').on( 'click', function( event ){
				event.preventDefault();
				call_invoke_func( wxpay_js_call );
            });
        });
    </script>
@stop

@section('body-title')
    挂号
@stop

@section('body-main')

@stop

@section('body-bottom')
<div class="pay-wrap">
    <div class="fee-wrap">
        挂号需收取<span class="fee">{{{ round( $doctor['register_fee'], 2 ) }}}</span>元
    </div>
    <div class="info-outer">
        <div class="info-wrap">
            <div class="info-item">
                <div class="item-wrap clearfix">
                    <span class="item-key">时间</span>
                    <span class="colon">：</span>
                    <span class="item-value">
                        {{{ $schedule['date'] }}} {{{ $schedule['period'] }}} {{{ $period['start'] }}} - {{{ $period['end'] }}}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="item-wrap clearfix">
                    <span class="item-key">科室</span>
                    <span class="colon">：</span>
                    <span class="item-value">{{{ $department }}}</span>
                </div>
            </div>
            <div class="info-item">
                <div class="item-wrap clearfix">
                    <span class="item-key">主治医师</span>
                    <span class="colon">：</span>
                    <span class="item-value">{{{ $doctor['title'] }}} {{{ $doctor['name'] }}}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="confirm">
        <div class="note">支付方式：微信支付</div>
        <button class="btn">确认支付</button>
    </div>
</div>
@stop
