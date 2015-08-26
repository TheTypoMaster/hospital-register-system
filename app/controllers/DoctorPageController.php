<?php

class DoctorPageController extends BaseController {

    public function login(){

        return View::make( 'doctor.login' );
    }

    public function account(){

        return View::make( 'doctor.account' );
    }

    public function chat(){

        $sign_package = {
            'token': 'ziruikeji',
            'user_id': Session::get( 'user.id' ),
            'timestamp': time()
        }

        $sign = sha1( json_encode( $sign_package ) );

        $parameter = {
            'uid': $sign_package['user_id'],
            'time': $sign_package['timestamp'],
            'sign': $sing
        }

        $chat_url = 'http://localhost:8000/chat/init?'.http_build_query( $parameter, '', '&' );

        return View::make( 'doctor.chat', array( 'chat_url' => $chat_url ) );
    }
}