<?php

class DoctorPageController extends BaseController {

    public function login(){

        return View::make( 'doctor.login' );
    }

    public function account(){

        return View::make( 'doctor.account' );
    }

    public function chat(){
        return View::make( 'doctor.chat' );
    }
}