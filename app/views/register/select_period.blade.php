@extends('layouts.master')

@section('title')
    预约挂号
@stop

@section('css')
    @parent
    <link rel="stylesheet" type="text/css" href="/dist/css/register/select_period.css" />
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

            var period_id = null;
            var pay_parameters = null;

            /**
             * 调用 /pay/generate_indent 接口生成订单
             * @para    int     period_id   挂号的时间段id
             * @return  json                /pay/generate_indent 接口返回的数据解析后的json
             */
            function generate_indent( ){
                var pay_parameters = null;

                $.ajax({
                    url: '/pay/generate_indent',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        period_id: period_id
                    }
					/*
                    success: function( result ){
                        if( result.error_code == 0 ){
							alert( '生成订单成功' );
                            pay_parameters = result;
                        }else{
							alert( result.message );
						}
					}*/
                }).always(function( x, text_status, error ){
					//alert( text_status);
					alert( 'Status' + x.status );
					alert( 'response text: ' + x.responseText );
				});

                return pay_parameters;
            }

            function add_register_record(){
                var is_add_record_ok = false;
                $.ajax({
                    url: '/user/record/add_record',
                    type: 'POST',
                    dataType: 'json',
                    data: { 
                        period_id: period_id
                    },
                    success: function( result ){
                        if ( result.error_code == 0 ){
                            is_add_record_ok = true;
                        }
                        alert( result.message );
                        /*
                        if ( result.error_code != 0 ) {
                            alert( result.message );
                        }
                        else{
                            is_add_record_ok = true;
                            //window.location.href = '/register/success';
                        }*/
                    }
                });
                return is_add_record_ok;
            }
    
            function wxpay_js_call(){
                WeixinJSBridge.invoke(
                    'getBrandWCPayRequest', 
                    pay_parameters,
                    function( response ){
                        if ( response.err_msg == "get_brand_wcpay_request：ok" ){
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

            $('.btn').on( 'click', function( event ){
                event.preventDefault();

                period_id = $(this).attr('period_id');

                pay_parameters = generate_indent( );

                if ( pay_parameters ){
                    // JS调用支付接口
                    call_invoke_func( wxpay_js_call );
                }

                //add_register_record();
            });
        });
    </script>
@stop

@section('body-title')
    挂号
@stop

@section('body-main')
    <div class="doc-info-wrap">
        <div class="doc-info-top clearfix">
            <img class="doc-pic float-left" src="{{{ $doctor['photo'] }}}"/>
            <div class="doc-info-detail float-left">
                <div class="doc-name">{{{ $doctor['name'] }}}</div>
                <div class="doc-title">职称: {{{ $doctor['title'] }}}</div>
                <div class="doc-section">科室：{{{ $doctor['department'] }}}</div>
                <div class="doc-hospital">医院：{{{ $doctor['hospital'] }}}</div>
            </div>
        </div>
        <p class="doc-info-desc">
            {{{ $doctor['specialty'] }}}
        </p>
        <div class="slide-btn">
            <img src="/images/icons/arrow_down.png" />
        </div>
    </div>
@stop

@section('body-bottom')
    <div class="list-wrap">
        <div class="list-head l-grey">
            {{{ $schedule['date'] }}} {{{ $schedule['period'] == 0 ? '上午' : '下午' }}} 号源列表
        </div>

        <table class="register-list">
            @foreach ( $periods as $period )
                <tr class="register-item">
                    <td class="register-time">
                        {{{ $period['start'] }}}-{{{ $period['end'] }}}
                    </td>
                    <td class="register-total">
                        总数：{{{ $period['total'] }}}
                    </td>
                    <td class="register-remain">
                        <span>剩余：</span>
                        <span class="l-orange">{{{ $period['total'] - $period['current'] }}}</span>
                    </td>
                    <td class="register-btn">
                        <button class="btn" period_id="{{{ $period['id'] }}}">
                            <a href="/user/record/add_record">挂号</a>
                        </button>    
                    </td>
                </td>
            @endforeach
        </table>

        <!--
        <ul class="register-list">
            @foreach ( $periods as $period )
                <li class="register-item">
                    <span class="register-time">{{{ $period['start'] }}}-{{{ $period['end'] }}}</span>
                    <span class="register-total">总数：{{{ $period['total'] }}}</span>
                    <span class="register-remain">剩余：<span class="l-orange">{{{ $period['total'] - $period['current'] }}}</span></span>
                    <button class="btn" period_id="{{{ $period['id'] }}}"><a href="/user/record/add_record">挂号</a></button>
                </li>
            @endforeach
        </ul>
        -->
    </div>
@stop
