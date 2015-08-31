<?php

class DoctorPageController extends BaseController {

    public function login(){

        return View::make( 'doctor.login' );
    }

    public function account(){

        $doctor = Doctor::where( 'user_id', Session::get( 'user.id' ) )->first();

        $hospital_id = $doctor->department->hospital_id;
        $default_department = $doctor->department_id;
        $departments = Department::where( 'hospital_id', $hospital_id )->get();

        $render_data = array(
            'name'                => $doctor->name,
            'title'               => $doctor->title,
            'specialty'           => strip_tags( $doctor->specialty ),
            'description'         => strip_tags( $doctor->description ),
            'default_department'  => $doctor->department_id,
            'departments'         => $departments
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

    public function get_schedules(){

        $schedules = Schedule::where( 'doctor_id', Session::get( 'doctor.id' ) )
                             ->where( 'date', 'like', Input::get( 'date', date( 'Y-m') ).'%' )
                             ->orderBy( 'date' )->paginate( 7 );

        echo json_encode( $schedules->getItems() ).PHP_EOL;

        $schedules_map = array();

        foreach( $schedules as $schedule ){

            $date_parse = date( 'm-d', strtotime( $schedule->date ) );

            if ( !array_key_exists( $schedule->date, $schedules_map ) ){
                $schedules_map[ $schedule->date ] = array();
            }

            $schedules_map[ $schedule->date ][ $schedule->period ] = array(
                'id' => $schedule->id,
                'period' => $schedule->period,
                'status' => false
            );

            foreach( $schedule->periods as $period ){
                if ( $period->current > 0 ){
                    $schedules_map[ $schedule->date ][ $schedule->period ]['status'] = true;
                    break;
                }
            }
        }

        return Response::json(array( 'error_code' => 0, 'result' => $schedules_map ));
    }

    public function get_patients(){
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
                'period' => $schedule->period,
                'status' => false
            );

            foreach( $schedule->periods as $period ){
                if ( $period->current > 0 ){
                    $schedules_map[ $schedule->date ][ $schedule->period ]['status'] = true;
                    break;
                }
            }
        }

        return Response::json(array( 'error_code' => 0, 'totality' => $schedules->getTotal(), 'patients' => $schedules_map ));
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
                'period' => $schedule->period,
                'status' => false
            );

            foreach( $schedule->periods as $period ){
                if ( $period->current > 0 ){
                    $schedules_map[ $schedule->date ][ $schedule->period ]['status'] = true;
                    break;
                }
            }
        }

//        return Response::json(array( 'error_code' => 0, 'result' => $schedules_map ));

        return View::make( 'doctor.patient',
                            array( 'name' => Session::get( 'doctor.name' ),
                                   'schedules' => $schedules_map ) );
    }

    public function get_comments(){
        $comments = Comment::selectRaw( 'comments.content as content, users.id as user_id, users.nickname as user_name' )
                           ->join( 'register_records', 'comments.record_id', '=', 'register_records.id' )
                           ->join( 'doctors', 'register_records.doctor_id', '=', 'doctors.id' )
                           ->join( 'users', 'register_records.user_id', '=', 'users.id' )
                           ->where( 'doctors.id', Session::get( 'doctor.id' ) )
                           ->Where( 'comments.created_at', 'like', Input::get( 'date', date( 'Y-m' ) ).'%' )
                           ->orderBy( 'comments.created_at' )->paginate( 7 );

        //var_dump( DB::getQueryLog() );

        return Response::json(array( 'error_code' => 0, 'totality' => $comments->getTotal(), 'comments' => $comments->getItems() ));
    }

    public function comment(){

        $comments = Comment::selectRaw( 'comments.content as content, users.id as user_id, users.nickname as user_name' )
                           ->join( 'register_records', 'comments.record_id', '=', 'register_records.id' )
                           ->join( 'doctors', 'register_records.doctor_id', '=', 'doctors.id' )
                           ->join( 'users', 'register_records.user_id', '=', 'users.id' )
                           ->where( 'doctors.id', Session::get( 'doctor.id' ) )
                           ->Where( 'comments.created_at', 'like', Input::get( 'date', date( 'Y-m' ) ).'%' )
                           ->orderBy( 'comments.created_at' )->paginate( 7 );

        return View::make( 'doctor.comment',  
                            array( 'name' => Session::get( 'doctor.name' ),
                                   'total' => $comments->getTotal() ) );
    }

    public function get_advice(){

        $register_records = RegisterRecord::selectRaw( 'register_records.id as id, register_records.advice as content, users.real_name as name' )
                                          ->join( 'doctors', 'register_records.doctor_id', '=', 'doctors.id' )
                                          ->join( 'users', 'register_records.user_id', '=', 'users.id' )
                                          ->where( 'status', '>', 0 )
                                          ->where( 'doctors.id', Session::get( 'doctor.id' ) )
                                          ->Where( 'register_records.created_at', 'like', Input::get( 'date', date( 'Y-m' ) ).'%' )
                                          ->WhereNotNull( 'advice' )
                                          ->paginate( 7 );

        return Response::json(array( 'error_coee' => 0, 'totality' => $register_records->getTotal(), 'advice' => $register_records->getItems() ));
    }

    public function advice(){

        $register_records = RegisterRecord::selectRaw( 'register_records.id as id, register_records.advice as content, users.real_name as name' )
                                          ->join( 'doctors', 'register_records.doctor_id', '=', 'doctors.id' )
                                          ->join( 'users', 'register_records.user_id', '=', 'users.id' )
                                          //->where( 'status', '>', 0 )
                                          ->where( 'doctors.id', Session::get( 'doctor.id' ) )
                                          ->Where( 'register_records.created_at', 'like', Input::get( 'date', date( 'Y-m' ) ).'%' )
                                          ->WhereNotNull( 'advice' )->paginate( 7 );
        
        return View::make( 'doctor.advice', 
                            array( 'name' => Session::get( 'doctor.name' ),
                                    'records' => $register_records ) );
    }

    public function get_null_advice(){

        $register_records = RegisterRecord::selectRaw( 'register_records.id as record_id, users.id as user_id, users.real_name as user_name' )
                                          ->join( 'doctors', 'register_records.doctor_id', '=', 'doctors.id' )
                                          ->join( 'users', 'register_records.user_id', '=', 'users.id' )
                                          ->where( 'status', '=', 0 )
                                          ->Where( 'register_records.created_at', 'like', Input::get( 'date', date( 'Y-m' ) ).'%' )
                                          ->WhereNull( 'advice' )->paginate( 7 );

        return Response::json(array( 'error_code' => 1, 'totality' => $register_records->getTotal(), 'records' => $register_records->getItems() ));
    }

    public function get_messages(){

    }

    public function message(){

        return View::make( 'doctor.message', array( 'name' => Session::get( 'doctor.name' ) ) );
    }
}
