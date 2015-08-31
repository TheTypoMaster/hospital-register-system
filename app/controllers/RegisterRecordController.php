<?php

class RegisterRecordController extends BaseController{

    protected $possible_status;

    protected $possible_period;

    public function __construct(){
        parent::__construct();
        $this->possible_status = array( '未就诊', '已就诊', '需复诊' );
        $this->possible_period = array( '上午', '下午' );
    }

    public function get_records(){

        
        $invoke_func = 'get_records_'.$this->get_return_format();

        return $this->$invoke_func();
    }

    public function get_records_json(){
        $register_accounts = User::find( Session::get( 'user.id' ) )
                                   ->register_accounts()
                                   ->with( 'records' )->get();

        if ( !isset( $register_accounts ) ){
            return Response::json(array( 'error_code' => 1, 'message' => '无记录' ));
        }

        $data = array();

        foreach( $register_accounts as $register_account ){
            $origin_records = $register_account->records;

            foreach ( $origin_records as $record ){
                $doctor             = RegisterRecord::find( $record->id )->doctor;
                $old_comment        = $record->comment()->get();
                $can_be_commented   = $record->status && !isset( $old_comment );

                $period = $record->period()->first();
                $schedule = Schedule::find( $period['schedule_id'] );
                $schedule_info = array(
                    'date'      => $schedule->date,
                    'period'    => $this->possible_period[ $schedule->period ],
                    'start'     => date( 'H:i', strtotime( $period->start ) ),
                    'end'       => date( 'H:i', strtotime( $period->end ) )
                );

                $result_records[]   = array(
                    'id'                => $record->id,
                    'status'            => $record->status,
                    'advice'            => $record->advice,
                    'schedule'          => $schedule_info,
                    'return_date'       => $record->return_date,
                    'created_at'        => $record->created_at->format('Y-m-d H:i'),
                    'start'             => $record->status ? date( 'Y-m-d H:i', strtotime( $record->start ) ) : '',
                    'department'        => $doctor->department->name,
                    'can_be_commented'  => $can_be_commented,
                    'doctor'            => array( 'id' => $doctor->id,
                                                   'name' => $doctor->name, 
                                                   'title' => $doctor->title )
                );
            }

            $data[] = array(
                'id' => $register_account->id,
                'name' => $register_account->name,
                'records' => $result_records
            );
        }

        return Response::json(array( 'error_code' => 0, 'register_accounts' => $data ));
    }

    public function get_records_html(){

        $records = RegisterRecord::where( 'user_id', Session::get( 'user.id' ) )->with( 'doctor' )->get();

        foreach ( $records as $record ){
            $doctor = $record->doctor;
            $period = $record->period()->first();
            $schedule = Schedule::find( $period['schedule_id'] );

            $schedule_info = array(
                'date'      => $schedule->date,
                'period'    => $this->possible_period[ $schedule->period ],
                'start'     => date( 'H:i', strtotime( $period->start ) ),
                'end'       => date( 'H:i', strtotime( $period->end ) )
            );
            $data[] = array(
                'id'                => $record->id,
                'status'            => $this->possible_status[ $record->status ],
                'can_be_canceled'   => $record->status == 0,
                'created_at'        => $record->created_at->format('Y-m-d H:i'),
                'start'             => $record->status ? date( 'Y-m-d H:i', strtotime( $record->start ) ) : '',
                'schedule'          => $schedule_info,
                'department'        => $doctor->department->name,
                'doctor'            => array( 'id' => $doctor->id, 
                                              'name' => $doctor->name, 
                                              'title' => $doctor->title )
            );
        }

        return View::make( 'user.record', array( 'records' => $data ) );
    }

    // Duplicate
    public function add_record(){

        $period_id      = Input::get( 'period_id' );
        $period         = Period::find( $period_id );

        if ( !isset( $period ) ){
            return Response::json(array( 'error_code' => 2, 'message' => '无该时间段，请重新选择' ));
        }

        if ( $period->current >= $period->total ){
            return Response::json(array( 'error_code' => 3, 'message' => '已满人，请重新选择' ));
        }

        $schedule       = $period->schedule;
        $doctor         = $schedule->doctor;
        $user_id        = Session::get( 'user.id' );
        
        if ( Input::has( 'account_id' ) ){
            $account_id = Input::get( 'account_id' );
            $account    = RegisterAccount::find( $account_id );

            if ( !isset( $account ) ){
                return Response::json(array( 'error_code' => 4, 'message' => '不存在该挂号账户' ));
            }

            if ( $account->user_id != $user_id ){
                return Response::json(array( 'error_code' => 5, 'message' => '无效账户' ));
            }
        }
        
        // 无 account_id 参数，则选择该用户默认挂号账户
        else{
            $account = RegisterAccount::where( 'user_id', $user_id )->first();

            if ( !isset( $account ) ){
                return Response::json(array( 'error_code' => 6, 'message' => '请先申请挂号账户' ));
            }
            
            $account_id = $account->id;
        }

        try{
            DB::beginTransaction();

            RegisterRecord::create(array(
                'status'        => 0,
                'fee'           => $doctor->register_fee,
                'period_id'     => $period->id,
                'doctor_id'     => $doctor->id,
                'account_id'    => $account_id,
                'user_id'       => $user_id,
            ));
            $period->current += 1;
            $period->save();

            $message = new Message();
            $message->from_uid = $user_id;
            $message->to_uid = $doctor->user->id;
            $message->content = $account->user->real_name.'挂号';
            $message->timestamp = time();
            $message->status = 3;
            $message->save();

            DB::commit();
        
        }catch( Exception $e ){

            DB::rollback();

            return Response::json(array( 'error_code' => 1, 'message' => '添加失败' ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '添加成功' ));
    }

    public static function create_record( $pay_record, $message ){

        try{

            DB::beginTransaction();

            $pay_record->time_end       = $message['time_end'];
            $pay_record->result_code    = $message['result_code'];
            $pay_record->open_id        = $message['openid'];
            $pay_record->transaction_id = $message['transaction_id'];

            // 查询相应时间段
            $attach_parse   = json_decode( $pay_record->attach, true );
            $account_id     = $attach_parse['account_id'];
            $period_id      = $attach_parse['period_id'];
            $period         = Period::find( $period_id );

                // 判断 result_code
            if ( $message['result_code'] == 'FAIL' ){

                $pay_record->error_code     = $message['err_code'];
                $pay_record->error_message  = $message['err_code_des'];
                $pay_record->status         = 'FAIL';
                        
            }else{
                // 创建挂号记录

                $schedule = $period->schedule;
                $doctor = $schedule->doctor;

                $period->current += 1;
                $period->save();

                $new_record = new RegisterRecord();
                $new_record['status']       = 0;
                $new_record['fee']          = $doctor->register_fee;
//                $new_record['start']        = date( 'Y-m-d H:i:s' );
                $new_record['period_id']    = $period->id;
                $new_record['doctor_id']    = $doctor->id;
                $new_record['account_id']   = $account_id;
                $new_record['user_id']      = $pay_record->user_id;
                $new_record->save();

                $message = new Message();
                $message->from_uid = $user_id;
                $message->to_uid = $doctor->user->id;
                $message->content = $account->user->real_name.'挂号';
                $message->timestamp = time();
                $message->status = 3;
                $message->save();

                $pay_record->record_id = $new_record->id;
                $pay_record->status = 'SUCCESS';  
            }

            $pay_record->save();

            DB::commit();

        }catch( Exception $e ){

            Log::info( 'Error in create record: '.$e->getMessage() );

            DB::rollback();

            return false;
        }

        return ture;
    }

    public function cancel(){

        $record_id = Input::get( 'record_id' );
        $record    = RegisterRecord::find( $record_id );

        // 是否存在该记录
        if ( !isset( $record ) ){
            return Response::json(array( 'error_code' => 2, 'message' => '不存在该挂号' ));
        }

        $register_account = RegisterAccount::find( $record->account_id );

        // 检查该就诊记录是否该用户的
        if ( $register_account->user_id != Session::get( 'user.id' ) ){
            return Response::json(array( 'error_code' => 3, 'message' => '无法取消该挂号' ));
        }

        // 检查就诊状态
        if ( $record->status ){
            return Response::json(array( 'error_code' => 4, 'message' => '已就诊无法取消' ));
        }

        // 取消同时将对应时间段挂号人数减1
        try{

            DB::transaction(function() use ( $record ){
                $period = Period::find( $record->period_id );
                $period->current -= 1;
                $period->save();
                $record->delete();

                $message = new Message();
                $message->from_uid = $record->user_id;
                $message->to_uid = $record->doctor->user_id;
                $message->content = $record->user->real_name.'取消挂号';
                $message->timestamp = time();
                $message->status = 3;
                $message->save(); 
            });

        }catch( Exception $e ){

            return Response::json(array( 'error_code' => -1, 'message' => $e->getMessage() ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '取消成功' ));
    }

    public function modify_status(){

        $record_id = Input::get( 'record_id' );
        $record    = RegisterRecord::find( $record_id );

        // 是否存在该记录
        if ( !isset( $record ) ){
            return Response::json(array( 'error_code' => 2, 'message' => '不存在该挂号' ));
        }

        $register_account = RegisterAccount::find( $record->account_id );

        // 检查该就诊记录是否该用户的
        if ( $register_account->user_id != Session::get( 'user.id' ) ){
            return Response::json(array( 'error_code' => 3, 'message' => '无法修改该挂号' ));
        }

        $status = (int)Input::get( 'status' );
        if ( $status > 2 || $status < 0 ){
            return Response::json(array( 'error_code' => 4, 'message' => '参数错误' ));
        }

        $record->status = $status;
        if ( !$record->save() ){
            return Response::json(array( 'error_code' => 1, 'message' => '修改失败' ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '修改成功' ));
    }

    public function add_return_date(){

        $record = RegisterRecord::find( Input::get( 'record_id' ) );

        // 是否存在该记录
        if ( !isset( $record ) ){
            return Response::json(array( 'error_code' => 2, 'message' => '不存在该挂号记录' ));
        }

        $register_account = RegisterAccount::find( $record->account_id );

        // 检查该就诊记录是否该用户的
        if ( $register_account->user_id != Session::get( 'user.id' ) ){
            return Response::json(array( 'error_code' => 3, 'message' => '无法修改该挂号' ));
        }

        // 检查就诊状态
        /*
        if ( !(int)($record->status) ){
            return Response::json(array( 'error_code' => 4, 'message' => '尚未就诊' ));
        }
        */

        $record->return_date = Input::get( 'date' );

        if ( !$record->save() ){
            return Response::json(array( 'error_code' => 1, 'message' => '设置失败' ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '设置成功' ));
    }
}
