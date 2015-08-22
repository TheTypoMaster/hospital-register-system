<?php

class DoctorPageController extends BaseController {

    public function home(){
        return View::make( 'layouts.master_web' );
    }

}