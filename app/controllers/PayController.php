<?php

class PayController extends BaseController{

    public function wxpay_js(){

        $tools = new JsApiPay();

        if ( !Input::has( 'code' ) ){
            $data = array( 'period_id' => Input::get('period_id' ), 'user_id' => Session::get( 'user.id' ) );
            $base_url = urlencode( 'http://test.zerioi.com/pay/wxpay_js?'.http_build_query( $data, '', '&' ) );
            //$base_url = urlencode( 'http://test.zerioi.com/pay/wxpay?period_id='.Input::get('period_id') );
            $url = $tools->CreateOauthUrlForCode( $base_url );
            return Redirect::to( $url );
        }

		$open_id = Session::get( 'user.open_id' );
		if ( !isset( $open_id ) ){
			$open_id = $tools->GetOpenidFromMp( Input::get( 'code') );
			Session::put( 'user.open_id', $open_id );
		}else{
			Session::forget( 'user.open_id' );
		}

        // 选择用户默认账户
        $user_id = Input::get( 'user_id' );
        $account = RegisterAccount::where( 'user_id', $user_id )->first();

        if ( !isset( $account ) ){
            return Response::make( '请先申请挂号账户' );
        }

        $account_id = $account->id;
	
        $period_id = Input::get( 'period_id' );
        $period = Period::find( $period_id );

        if ( !$this->validate_peirod( $period ) ){
            return Response::make( '无效时间段，请重新选择' );
        }

        /**
         * 创建订单时先将对应时间段的挂号数加1，
         * 以避免微信支付异步通知顺序不一致导致
         * 挂号记录创建顺序不一致
         */

        $period->start = date( 'H:i', strtotime( $period->start ) );
        $period->end   = date( 'H:i', strtotime( $period->end ) );

        $schedule = $period->schedule;
        $doctor = $schedule->doctor;

        $possible_period = array( '上午', '下午' );
        $schedule = array(
            'date'      => $schedule->date,
            'period'    => $possible_period[ $schedule->period ]
        );

        // 附加信息
        $attach = array(
            'period_id' => (int)Input::get( 'period_id' ),
            'account_id' => $account_id
        );

        // 创建订单
        $order = $this->create_order( 
            Input::get( 'user_id' ), 
            'JSAPI', 
            json_encode( $attach ), 
            (int)($doctor->register_fee * 100),
            $open_id  );
        $para  = $tools->GetJsApiParameters( $order );

        $data = array(
            'period'            => $period,
            'schedule'          => $schedule,
            'doctor'            => $doctor,
            'department'        => $doctor->department->name,
            'para'				=> $para
        );

        return View::make( 'register.pay', $data );
    }

    /*
     * 微信支付APP后端接口
     * 生成订单，参数传回IOS APP前端
     */
    public function wxpay_app(){

        $period_id      = Input::get( 'period_id' );
        $period         = Period::find( $period_id );

        // 判断时间段有效性
        if ( !isset( $period ) ){
            return Response::json(array( 'error_code' => 2, 'message' => '无该时间段，请重新选择' ));
        }

        if ( $period->current >= $period->total ){
            return Response::json(array( 'error_code' => 3, 'message' => '已满人，请重新选择' ));
        }

        $schedule       = $period->schedule;
        $doctor         = $schedule->doctor;
        $user_id        = Session::get( 'user.id' );
        
        // 选择指定挂号账户
        if ( Input::has( 'account_id' ) ){
            $account_id = Input::get( 'account_id' );
            $account    = RegisterAccount::find( $account_id );

            if ( !isset( $account ) ){
                return Response::json(array( 'error_code' => 4, 'message' => '不存在该挂号账户' ));
            }

            if ( $account->user_id != $user_id ){
                return Response::json(array( 'error_code' => 5, 'message' => '无效账户' ));
            }
        }
        
        // 无 account_id 参数，则选择该用户默认挂号账户
        else{
            $account = RegisterAccount::where( 'user_id', $user_id )->first();

            if ( !isset( $account ) ){
                return Response::json(array( 'error_code' => 6, 'message' => '请先申请挂号账户' ));
            }
            
            $account_id = $account->id;
        }

        $user_id = Session::get( 'user.id' );
        $attach = array(
            'period_id' => (int)Input::get( 'period_id' ),
            'account_id' => $account_id
        );

        try{
            
            $period = Period::find( Input::get( 'period_id' ) );

            $schedule = $period->schedule;
            $doctor = $schedule->doctor;

            $order = $this->create_order( $user_id, 'APP', json_encode( $attach ), (int)($doctor->register_fee * 100) );

            $para = array(
                'appid'         => $order['appid'],
                'partnerid'     => WxPayConfig::MCHID,
                'prepayid'      => $order['prepay_id'],
                'package'       => 'Sign=WXPay',
                'noncestr'      => WxPayApi::getNonceStr(),
                'timestamp'     => time(),
            );

            $wxpay_result = new WxPayResults();
            $wxpay_result->FromArray( $para );
            $wxpay_result->SetSign();

            $package = $wxpay_result->GetValues();

        }catch( Exception $e ){

            return Response::json(array( 'error_code' => 1, 'message' => $e->getMessage() ));
        }        

        return Response::json(array( 'error_code' => 0, 'package' => $package ));
    }

    protected function validate_peirod( $period ){
        
        if ( !isset( $period ) ){
            return false;
        }        

        if ( $period->current >= $period->total ){
            return false;
        }

        // 检查日期
        return true;
    }

    protected function create_order( $user_id, $trade_type, $attach, $fee, $open_id = null ){

        // 商户号 + 用户id + uniqid生成的随机字符串
        $out_trade_no = WxPayConfig::MCHID.uniqid( $user_id );

        // 需重置下当前时区，PHP配置文件不知为何不起作用
        //date_default_timezone_set('PRC');

        // 统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody( "挂号费" );
        $input->SetAttach( $attach );
        $input->SetOut_trade_no( $out_trade_no );

        // 测试用1分钱
        $input->SetTotal_fee( 1 );

        $current_time   = time();
        $start          = date( 'YmdHis', $current_time );
        $expire         = date( 'YmdHis', $current_time + 3600 );

        // 下单时间：当前时间
        $input->SetTime_start( $start );
        
        // 失效时间：测试用一个小时
        $input->SetTime_expire( $expire );
        
        $input->SetGoods_tag( "挂号费" );
        $input->SetNotify_url( "http://test.zerioi.com/pay/wxpay_notify" );
        $input->SetTrade_type( $trade_type );
        
        // JSAPI调用支付需设置open_id
        if ( $open_id ){
            $input->SetOpenid( $open_id );
        }

        $record = new WeixinPay();
        $record->trade_no       = $out_trade_no;
        $record->trade_type     = $trade_type;
        $record->time_start     = $start;
        $record->time_expire    = $expire;
        $record->user_id        = $user_id;
        $record->open_id        = $open_id;
        $record->attach         = $attach;
        $record->total_fee      = $fee;
        $record->status         = 'UNFINISHED';

        if ( !$record->save() ){
            throw new Exception( "Could not save pay parameters" );
        }

        return WxPayApi::unifiedOrder( $input );
    }

    protected function __make_sign( $para ){

        // 排序
        ksort( $para );
        // 拼接
        $string = $this->__to_url_params( $para );
        // 加入key
        $string = $string.'$key='.WxPayConfig::KEY;
        // MD5
        $string = md5( $string );

        return strtoupper( $string );
    }

	protected function __to_url_params( $values ){
		$buff = "";

		foreach ( $values as $k => $v ){
			if( $k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}

		return trim($buff, "&");
	}

    public function wxpay_notify(){

        $request = Request::instance();

        Log::info( $request->getContent() );

        $wxpay_notify_controller = new WxPayNotifyController();

        $result = $wxpay_notify_controller->Handle( $request->getContent() );

		Log::info( $result );

        return Response::make( $result );
    }

}

class WxPayNotifyController extends WxPayNotify{

    public function NotifyProcess( $message, &$msg ){

        if ( $message['return_code'] == 'SUCCESS' ){

            // 通过 out_trade_no 获取相应订单记录
			$pay_record = WeixinPay::where( 'trade_no', $message['out_trade_no'] )->first();

            if ( !isset( $pay_record ) ){
                $msg = 'Invalid out_trade_no';
                return false;
            }

            if ( $pay_record->status != 'UNFINISHED' ){
                $msg = 'Already processed';
                return true;
            }

            // 对比附加数据
            if ( $pay_record->attach != $message['attach']  ){
                $msg = 'Attach Error'; 
                return false;
            }
            
			return RegisterRecordController::create_record( $pay_record, $message );

        }else{ // if ( $message['return_code'] == 'FAIL' )

            return false;
        }

        return true;
    }
}
