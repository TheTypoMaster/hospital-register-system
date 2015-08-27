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

        $schedules = Schedule::where( 'doctor_id', Session::get( 'doctor.id' ) )
                             ->where( 'date', 'like', Input::get( 'date', date( 'Y-m') ).'%' )
                             ->orderBy( 'date' )->paginate( 7 );

        $schedules_map = array();

        foreach( $schedules as $schedule ){

            $date_parse = date( 'm-d', strtotime( $schedule->date ) );

            if ( !array_key_exists( $schedule->date, $schedules_map ) ){
                $schedules_map[ $schedule->date ] = array();
            }

            $schedules_map[ $schedule->date ][ $schedule->period ] = array(
                'id' => $schedule->id,
                'status' => false
            );

            foreach( $schedule->periods as $period ){
                if ( $period->current < $period->total ){
                    $schedules_map[ $schedule->date ][ $schedule->period ]['status'] = true;
                    break;
                }
            }
        }

        return View::make( 'doctor.patient',
                            array( 'name' => Session::get( 'doctor.name' ),
                                   'schedules' => $schedules_map ) );
    }

    public function comment(){

        $comments = Comment::selectRaw( 'comments.content as content, users.id as user_id, users.nickname as user_name' )
                           ->join( 'register_records', 'comments.record_id', '=', 'register_records.id' )
                           ->join( 'doctors', 'register_records.doctor_id', '=', 'doctors.id' )
                           ->join( 'users', 'register_records.user_id', '=', 'users.id' )
                           ->where( 'doctors.id', Session::get( 'doctor.id' ) )
                           ->orWhere( 'comments.created_at', 'like', Input::get( 'date', date( 'Y-m' ) ).'%' )
                           ->orderBy( 'comments.created_at' )->paginate( 7 );

        return View::make( 'doctor.comment', 
                            array( 'name' => Session::get( 'doctor.name' ),
                                   'comments' => $comments ) );
    }

    public function advice(){

        $register_records = RegisterRecord::select( 'register_records.id', 'users.id', 'users.nickname', 'register_records.advice' )
                                          ->join( 'doctors', 'register_records.doctor_id', '=', 'doctors.id' )
                                          ->join( 'users', 'register_records.user_id', '=', 'users.id' )
                                          ->where( 'status', '>', 0 )
                                          ->orWhere( 'created_at', 'like', Input::get( 'date', date( 'Y-m' ) ).'%' )
                                          ->orWhereNotNull( 'advice' )->paginate( 7 );
        
        return View::make( 'doctor.advice', 
                            array( 'name' => Session::get( 'doctor.name' ),
                                    'records' => $register_records ) );
    }

    public function advice_null(){

        $register_records = RegisterRecord::select( 'register_records.id', 'users.id', 'users.nickname' )
                                          ->join( 'doctors', 'register_records.doctor_id', '=', 'doctors.id' )
                                          ->join( 'users', 'register_records.user_id', '=', 'users.id' )
                                          ->where( 'status', '>', 0 )
                                          ->orWhere( 'created_at', 'like', Input::get( 'date', date( 'Y-m' ) ).'%' )
                                          ->orWhereNull( 'advice' )->paginate( 7 );

        return Response::json(array( 'error_code' => 1, 'records' => $register_records ));
    }

    public function message(){

        return View::make( 'doctor.message', array( 'name' => Session::get( 'doctor.name' ) ) );
    }
}
