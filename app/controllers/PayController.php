<?php

class PayController extends BaseController{

    public function index(){

        $tools = new JsApiPay();

        if ( !Input::has( 'code' ) ){
            $base_url = urlencode( 'http://test.zerioi.com/pay?period_id='.Input::get('period_id') );
			//Log::info( 'redirect url: '.$base_url );
            $url = $tools->CreateOauthUrlForCode( $base_url );
			//Log::info( 'authenticate url: '.$url );
            return Redirect::to( $url );
        }

		$open_id = Session::pull( 'user.open_id' );
		if ( !isset( $open_id ) ){
			$open_id = $tools->GetOpenidFromMp( Input::get( 'code') );
			Session::put( 'user.open_id', $open_id );
		}

		//Log::info( 'Code before: '.Input::get('code') );
        $js_api_parameters = Session::pull( 'user.js_api_parameters' );
        if ( !isset( $js_api_parameters ) ){
            $js_api_parameters = $this->create_order( $open_id );
            Session::put( 'user.js_api_parameters', json_encode( $js_api_parameters ) );
        }else{
            $js_api_parameters = json_decode( $js_api_parameters );
        }
        
        $period_id = Input::get( 'period_id' );
        $period = Period::find( $period_id );

        $schedule = $period->schedule;
        $doctor = $schedule->doctor;

        $possible_period = array( '上午', '下午' );
        $schedule = array(
            'date'      => $schedule->date,
            'period'    => $possible_period[ $schedule->period ]
        );

        $data = array(
            'period'            => $period,
            'schedule'          => $schedule,
            'doctor'            => $doctor,
            'department'        => $doctor->department->name,
            'js_api_parameters' => $js_api_parameters
        );

        return View::make( 'register.pay', $data );
    }

    protected function create_order( $open_id ){

        // 商户号 + 用户id + uniqid生成的随机字符串
        $out_trade_no = WxPayConfig::MCHID.uniqid( Session::get( 'user.id' ) );

        // 需重置下当前时区，PHP配置文件不知为何不起作用
        date_default_timezone_set('PRC');

        // 统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody( "test" );
        $input->SetAttach( "test" );
        $input->SetOut_trade_no( $out_trade_no );
        $input->SetTotal_fee( 1 );
        $input->SetTime_start( date( 'YmdHis' ) );
        $input->SetTime_expire( date( 'YmdHis', time() + 3600 ) );
        $input->SetGoods_tag( "test" );
        $input->SetNotify_url( "http://test.zerioi.com/pay/notify" );
        $input->SetTrade_type( "JSAPI" );
        $input->SetOpenid( $open_id );
        $order = WxPayApi::unifiedOrder($input);
        $js_api_parameters = $tools->GetJsApiParameters($order);

        return $js_api_parameters;
    }

    public function notify(){

        $request = Request::instance();

        $message = $request->getContent();

        Log::info( $message );

        return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
    }

}
