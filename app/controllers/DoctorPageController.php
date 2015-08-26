<?php

class DoctorPageController extends BaseController {

    public function login(){

        return View::make( 'doctor.login' );
    }

    public function account(){

        $doctor = Doctor::where( 'user_id', Session::get( 'user.id' ) )->first();

        $render_data = array(
            'name'          => $doctor->name,
            'title'         => $doctor->title,
            'department'     => $doctor->department->name,
            'specialty'     => strip_tags( $doctor->specialty ),
            'description'   => strip_tags( $doctor->description )
        );

        return View::make( 'doctor.account', $render_data );
    }

    public function chat(){

        $sign_package = array(
            'token'     => 'ziruikeji',
            'user_id'   => Session::get( 'user.id' ),
            'timestamp' => time()
        );

        ksort( $sign_package );
        $sign = sha1( http_build_query( $sign_package, '', '&' ) );

        $parameter = array(
            'uid'       => $sign_package['user_id'],
            'time'      => $sign_package['timestamp'],
            'sign'      => $sign
        );

        $chat_url = 'http://localhost:8080/chat/validate_login?'.http_build_query( $parameter, '', '&' );

        return View::make( 'doctor.chat', array( 'chat_url' => $chat_url, 'name' => Session::get( 'doctor.name' ) ) );
    }

    public function patient(){

        return View::make( 'doctor.patient', array( 'name' => Session::get( 'doctor.name' ) ) );
    }

    public function comment(){

        return View::make( 'doctor.comment', array( 'name' => Session::get( 'doctor.name' ) ) );
    }

    public function message(){

        return View::make( 'doctor.message', array( 'name' => Session::get( 'doctor.name' ) ) );
    }

    public function advice(){

        return View::make( 'doctor.advice', array( 'name' => Session::get( 'doctor.name' ) ) );
    }
}
