<?php

//require_once 'WxPay/example/WxPay.JsApiPay.php';
//require_once 'WxPay/lib/WxPay.Api.php';

class PayController extends BaseController{
    
    public function generate_indent(){

        // 
        try{
            $tools = new JsApiPay();

			Log::info( Session::all() );
            $open_id = Session::get( 'user.open_id' );//$tools->GetOpenid();

            // 商户号 + 用户id + uniqid生成的随机字符串
            $out_trade_no = WxPayConfig::MCHID.uniqid( Session::get( 'user.id' ) );

			date_default_timezone_set('PRC');
			$time_start = date( 'YmdHis' );
			$time_end   = date( 'YmdHis', time() + 3600 );

            // 统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody( "test" );
            $input->SetAttach( "test" );
            $input->SetOut_trade_no( $out_trade_no );
            $input->SetTotal_fee( 100 );
            $input->SetTime_start( $time_start );
            $input->SetTime_expire( $time_end );
            $input->SetGoods_tag( "test" );
            $input->SetNotify_url( "http://120.25.211.179/pay/notify" );
            $input->SetTrade_type( "JSAPI" );
            $input->SetOpenid( $open_id );
            $order = WxPayApi::unifiedOrder($input);

			Log::info( $open_id );

			return Response::json(array( 'error_code' => 0, 'parameters' => $open_id ));

            $jsApiParameters = $tools->GetJsApiParameters($order);
        }catch( WxPayException $e ){
            
            return Response::json(array( 'error_code' => 2, 'message' => $e->getMessage() ));
        }catch( Exception $e ){

            return Response::json(array( 'error_code' => 1, 'message' => $e->getMessage() ));
        }

        return Response::json(array( 'error_code' => 0, 'parameters' => $jsApiParameters ));
    }

    public function notify(){

    }

}
