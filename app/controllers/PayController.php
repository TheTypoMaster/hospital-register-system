<?php

require_once 'WxPay/example/WxPay.JsApiPay.php';
require_once 'WxPay/lib/WxPay.Api.php';

class PayController extends BaseController{
    
    public function generate_indent(){

        $tools = new JsApiPay();

        $openId = $tools->GetOpenid();

        // 商户号 + 用户id + uniqid生成的随机字符串
        $out_trade_no = WxPayConfig::MCHID.uniqid( Session::get( 'user.id' ) );

        // 统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody( "test" );
        $input->SetAttach( "test" );
        $input->SetOut_trade_no( $out_trade_no );
        $input->SetTotal_fee( "0.01" );
        $input->SetTime_start( date( "YmdHis" ) );
        $input->SetTime_expire( date( "YmdHis", time() + 3600 ) );
        $input->SetGoods_tag( "test" );
        $input->SetNotify_url( "http://120.25.211.179/pay/notify" );
        $input->SetTrade_type( "JSAPI" );
        $input->SetOpenid( $openId );
        $order = WxPayApi::unifiedOrder($input);

        // 
        try{
            $jsApiParameters = $tools->GetJsApiParameters($order);
        }catch( WxPayException $e ){
            
            return Response::json(array( 'error_code' => 2, 'message' => '支付请求异常' ));
        }catch( Exception $e ){

            return Response::json(array( 'error_code' => 1, 'message' => '未知错误' ));
        }
        

        return Response::json(array( 'error_code' => 0, 'parameters' => $jsApiParameters ));
    }

    public function notify(){

    }

}