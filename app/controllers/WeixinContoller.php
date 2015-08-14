<?php

class WeixinContoller extends BaseController{

    protected static $token = "ziruikeji";

    protected static function check_signature(){
        $signature = Input::get( 'signature' );
        $timestamp = Input::get( 'timestamp' );
        $nonce = Input::get( 'nonce' );

        $tmpArr = array( self::$token, $timestamp, $nonce );
        sort( $tmpArr, SORT_STRING );
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        return $tmpStr == $signature;
    }
    
    public function response_token(){

        return Response::make( self::checkInput::get( 'echostr ') : 'Error' );
    }
}