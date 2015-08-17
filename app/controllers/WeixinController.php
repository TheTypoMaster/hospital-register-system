<?php

class WeixinController extends BaseController{

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
		$response_text = "Error";

		if ( self::check_signature() ){
			$response_text = Input::get( 'echostr' );
		}
		
		return Response::make( $response_text );
	}

    public function response_message(){
	/*	
		$request = Request::instance();

		$message = new SimpleXMLElement( $request->getContent() );
		
		Session::put( 'user.open_id', (string)$message->FromUserName );
	
		//Session::put( 'user.open_id', '123123'  );

		Log::info( Session::all() );

		Log::info( Session::get( 'user.open_id' ) );
	*/
		return Response::make( 'success' );
    }
}
