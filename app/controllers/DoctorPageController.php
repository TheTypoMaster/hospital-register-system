<?php

class DoctorPageController extends BaseController {

    public function __construct(){
        parent::__construct();

        $this->default_num_per_page = 7;
    }

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
                             ->orderBy( 'date' )->paginate( $this->default_num_per_page );

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

        return Response::json(array( 'error_code' => 0, 'totality' => $schedules->getTotal(), 'result' => $schedules_map ));
    }

    public function get_records(){
        
        $paginator = RegisterRecord::where( 'doctor_id', Session::get( 'doctor.id' ) )
                                   ->where( 'created_at', 'like', Input::get( 'date', date( 'Y-m-d' ) ).'%' )
                                   ->with('user')->paginate( $this->default_num_per_page );

        $result = array();
        $records = $paginator->getCollection();

        foreach( $records as $record ){
            $user = $record->user;
            $result[] = array(
                'record_id'   => $record->id,
                'user_name'   => $user->real_name,
            );
        }

        return Response::json(array(
                    'error_code'  => 0,
                    'records'     => $result,
                    'last_page'   => $paginator->getLastPage() ));
    }

    public function get_record_detail(){

        $record = RegisterRecord::find( Input::get( 'record_id' ) );

        $doctor = $record->doctor;

        $result = array(
            'record_id'       => $record->id,
            'datetime'        => $record->created_at->format('Y-m-d H:i'),
            'period'          => $record->period->schedule['period'],
            'return_date'     => $record->return_date,
            'doctor'          => array(
                                    'name'        => $doctor->name,
                                    'title'       => $doctor->title,
                                    'department'  => $doctor->department->name ) );

        return Response::json(array( 'error_code' => 0, 'result' => $result ));
    }

    public function get_records_by_schedule(){

        $paginator = RegisterRecord::selectRaw( 'register_records.id as id, periods.start as time, users.real_name as name' )
                                   ->join( 'periods', 'periods.id', '=', 'register_records.period_id' )
                                   ->join( 'schedules', 'schedules.id', '=', 'periods.schedule_id' )
                                   ->join( 'users', 'users.id', '=', 'register_records.user_id' )
                                   ->where( 'schedules.id', Input::get( 'schedule_id' ) )
                                   ->where( 'schedules.doctor_id', Session::get( 'doctor.id' ) )
                                   ->paginate( $this->default_num_per_page );

        return Response::json(array( 'error_code' => 0, 'totality' => $paginator->getTotal(), 'patients' => $paginator->getCollection() ));
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
                           ->orderBy( 'comments.created_at' )->paginate( $this->default_num_per_page );

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
                           ->orderBy( 'comments.created_at' )->paginate( $this->default_num_per_page );

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
                                          ->paginate( $this->default_num_per_page );

        return Response::json(array( 'error_coee' => 0, 'totality' => $register_records->getTotal(), 'advice' => $register_records->getItems() ));
    }

    public function advice(){

        $register_records = RegisterRecord::selectRaw( 'register_records.id as id, register_records.advice as content, users.real_name as name' )
                                          ->join( 'doctors', 'register_records.doctor_id', '=', 'doctors.id' )
                                          ->join( 'users', 'register_records.user_id', '=', 'users.id' )
                                          ->where( 'status', '>', 0 )
                                          ->where( 'doctors.id', Session::get( 'doctor.id' ) )
                                          ->Where( 'register_records.created_at', 'like', Input::get( 'date', date( 'Y-m' ) ).'%' )
                                          ->WhereNotNull( 'advice' )->paginate( $this->default_num_per_page );
        
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
                                          ->WhereNull( 'advice' )->paginate( $this->default_num_per_page );

        return Response::json(array( 'error_code' => 1, 'totality' => $register_records->getTotal(), 'records' => $register_records->getItems() ));
    }

    public function get_messages(){

        $date = Input::get( 'date' ).'-01 00:00:00';

        $timestamp_start = strtotime( $date );

        $timestamp_end = strtotime( '+1 months', $timestamp_start );

        $paginator = $this->__get_messages(array( 3, 4 ), $timestamp_start, $timestamp_end);

        $messages = $paginator->getCollection();

        $result = $messages->toArray();

        DB::transaction(function() use ( $messages ) {
            foreach( $messages as $message ){
                if ( $message->status == 3 ){
                    $message->status = 4;
                    $message->save();
                }
            }
        });

        return Response::json(array( 'error_code' => 0, 'messages' => $result ));
    }

    public function get_unread_messages(){

        $date = Input::get( 'date' ).'-01 00:00:00';

        $timestamp_start = strtotime( $date );

        $timestamp_end = strtotime( '+1 months', $timestamp_start );

        $paginator = $this->__get_messages(array( 3 ), $timestamp_start, $timestamp_end);

        return Response::json(array( 'error_code' => 0, 'messages' => $paginator->getCollection() ));
    }

    protected function __get_messages( $status, $ts, $te ){

        return Message::selectRaw( 'id, content, status, timestamp as time' )
                      ->where( 'to_uid', Session::get( 'user.id' ) )
                      ->where( 'timestamp', '>', $ts )
                      ->where( 'timestamp', '<', $te )
                      ->whereIn( 'status', $status )
                      ->paginate( $this->default_num_per_page );
    }

    public function message(){

        return View::make( 'doctor.message', array( 'name' => Session::get( 'doctor.name' ) ) );
    }
}
