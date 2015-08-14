<?php

require_once 'WxPay/example/WxPay.JsApiPay.php';
require_once 'WxPay/lib/WxPay.Api.php';

class PayController extends BaseController{
    
    public function generate_indent(){
        $tools = new JsApiPay();

        $openId = $tools->GetOpenid();

        // 统一下单
        $input = new WxPayUnifiedOrder();
        $input = new WxPayUnifiedOrder();
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://120.25.211.179/pay/notify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);

        return Response::json( $jsApiParameters );
    }

    public function notify(){

    }

}