<?php

class DoctorController extends BaseController {

    public function get_doctors(){
        
        $department = Department::find( Input::get( 'department_id' ) );

        if ( !isset( $department ) ){
            return Response::json(array( 'error_code' => 1, 'message' => '不存在该诊室' ));
        }

        $doctors = $department->doctors;

        if ( !isset( $doctors ) ){
            return Response::json(array( 'error_code' => 2, 'message' => '该诊室无医生...' ));
        }

        $result = array();
        foreach ( $doctors as $doctor ){
            $result[] = array(
                'id'                => $doctor->id,
                'name'              => $doctor->name,
                'title'             => $doctor->title,
                'photo'             => $doctor->photo,
                'specialty'         => strip_tags( $doctor->specialty ),
                'can_be_registered' => $this->can_be_registered( $doctor->id ),
                'is_consultable'    => $doctor->is_consultable
            );
        }

        return Response::json(array( 'error_code' => 0, 'doctors' => $result ));
    }

    protected function can_be_registered( $doctor_id ){
        $periods = Doctor::find( $doctor_id )->schedules()->with('periods')->get();

        foreach ( $periods as $period ) {
            if ( $period->current < $period->total ){
                return true;
            }
        }

        return false;
    }

    /***************** 医生客户端web接口 *********************/

    public function login(){

        $account = Input::get( 'account' );
        $password = Input::get( 'password' );

        if ( !isset( $account ) ){
            return Response::json(array( 'error_code' => 1, 'message' => '请输入账户' ));
        }

        if ( !isset( $password )){
            return Response::json(array( 'error_code' => 2, 'message' => '请输入密码' ));
        }

        $user = User::where( 'account', $account )->first();

        if ( !isset( $user ) ){
            return Response::json(array( 'error_code' => 3, 'message' => '用户名不存在' ));
        }

        if ( !( $user->role & 0x02 ) ){
            return Response::json(array( 'error_code' => 4, 'message' => '无效用户' ));
        }

        try{
            Sentry::authenticate(array(
                'phone' => $user->phone,
                'password' => $password
            ));
        }catch( Cartalyst\Sentry\Users\UserNotFoundException $e ){

            return Response::json(array( 'error_code' => 5, 'message' => '用户名或密码错误' ));

        }catch( Cartalyst\Sentry\Users\PasswordRequiredException $e ){

            return Response::json(array( 'error_code' => 5, 'message' => '用户名或密码错误' ));
        }

        $doctor = Doctor::where( 'user_id', $user->id )->first();
        Session::put( 'user.id', $user->id );
        Session::put( 'doctor.id', $doctor->id );
        Session::put( 'doctor.name', $doctor->name );

        return Response::json(array( 'error_code' => 0, 'message' => '登录成功' ));
    }

    public function logout(){

        Session::forget( 'user.id' );
        Session::forget( 'doctor.name' );

        return Redirect::to( '/doc/login' );
    }

    public function modify_account(){
        
        $doctor = Doctor::find( Session::get( 'doctor.id' ) );

        $inputs = array(
            'name'          => Input::get( 'name' ),
            'title'         => Input::get( 'title' ),
            'specialty'     => Input::get( 'specialty' ),
            'description'   => Input::get( 'description' ),
            'department'    => Input::get( 'department' )
        );

        foreach( $inputs as $key => $value ){
            if ( isset( $value ) ){
                if ( $key == 'department' ){
                    $doctor->department_id = (int)$value;
                    echo 'ok';
                }
                else if ( $key == 'specialty' || $key == 'description' ){
                    $doctor[ $key ] = '<p>'.$value.'</p>';
                }else{
                    $doctor[ $key ] = $value;
                }
            }
        }

        if ( !$doctor->save() ){
            return Response::json(array( 'error_code' => 1, 'message' => '保存失败' ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '保存成功' ));
    }

    public function upload_portrait(){
        if ( !Input::hasFile( 'portrait' ) ){

            return Response::json(array( 'error_code' => 2, 'message' => '无文件上传' ));
        }

        if ( !Input::file( 'portrait' )->isValid() ){

            return Response::json(array( 'error_code' => 3, 'message' => '文件无效' ));
        }

        $portrait = Input::file( 'portrait' );

        $file_size = $portrait->getSize();

        if ( $file_size > 2 * 1024 * 1024 ){
            return Response::json(array( 'error_code' => 4, 'message' => '文件过大' ));
        }

        $file_ext = $portrait->getClientOriginalExtension();

        $user_id = Session::get( 'user.id' );

        $doctor = Doctor::where( 'user_id', $user_id )->first();

        try{    

            $photo_path = '/images/upload/';
            $photo_full_name = uniqid( $user_id.time() ).'.'.$file_ext;

            if ( isset( $doctor->photo ) ){
                $previous_photo = $doctor->photo;
            }

            $doctor->photo = $photo_path.$photo_full_name;

            if ( !$doctor->save() ){
                return Response::json(array( 'error_code' => 5, 'message' => '错误' ));
            }

            // Save and delete previous photo
            if ( isset( $previous_photo ) ){
                File::delete( $previous_photo );
            }

            $portrait->move( public_path().$photo_path , $photo_full_name );
        }

        catch( Exception $e ){

            return Response::json(array( 'error_code' => 1, 'message' => $e->getMessage() ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '保存成功', 'path' => $doctor->photo, 'size' => $file_size ));
    }

    public function modify_advice(){

        if ( !Input::has( 'advice' ) ){
            return Response::json(array( 'error_code' => 1, 'message' => '不能为空' ));
        }

        $advice = Input::get( 'advice' );
        $record = RegisterRecord::find( Input::get( 'record_id' ) )->where( 'doctor_id', Session::get( 'doctor.id' ) )->first();

        if ( !isset( $record ) ){
            return Response::json(array( 'error_code' => 2, 'message' => '不存在该挂号' ));
        }

        $record->advice = $advice;

        if ( !$record->save() ){
            return Response::json(array( 'error_code' => 3, 'message' => '添加失败' ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '添加成功' ));
    }

    public function modify_status(){

        $record = RegisterRecord::find( Input::get( 'record_id' ) )->where( 'doctor_id', Session::get( 'doctor.id' ) )->first();

        // 是否存在该记录
        if ( !isset( $record ) ){
            return Response::json(array( 'error_code' => 2, 'message' => '不存在该挂号' ));
        }

        $status = (int)(Input::get( 'status' ));
        if ( $status > 2 || $status < 0 ){
            return Response::json(array( 'error_code' => 3, 'message' => '参数错误' ));
        }

        $record->status = $status;
        if ( !$record->save() ){
            return Response::json(array( 'error_code' => 1, 'message' => '修改失败' ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '修改成功' ));
    }

    public function modify_return(){

        $record = RegisterRecord::find( Input::get( 'record_id' ) );

        // 是否存在该记录
        if ( !isset( $record ) ){
            return Response::json(array( 'error_code' => 2, 'message' => '不存在该挂号记录' ));
        }

        // 检查该就诊记录是否该医生的
        if ( $record->doctor_id != Session::get( 'doctor.id' ) ){
            return Response::json(array( 'error_code' => 3, 'message' => '无法修改该挂号', '1' => Session::get( 'doctor.id' ), '2' => $record->doctor_id ));
        }

        // 检查就诊状态
        if ( !(int)($record->status) ){
            return Response::json(array( 'error_code' => 4, 'message' => '尚未就诊' ));
        }

        $record->return_date = Input::get( 'date' );
        $record->status      = 2;                       // 修改状态 --> 2 - 需复诊

        if ( !$record->save() ){
            return Response::json(array( 'error_code' => 1, 'message' => '设置失败' ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '设置成功', 'return_date' => $record->return_date ));
    }

    public function modify_message_status(){
        
        $message = Message::find( Input::get( 'message_id' ) );
        $status = (int)(Input::get( 'status' ));

        if ( $status != 3 || $status != 4 ){
            return Response::json(array( 'error_code' => 1, 'message' => '参数错误' ));
        }

        if ( !isset( $message ) ){
            return Response::json(array( 'error_code' => 2, 'message' => '不存在该消息' ));
        }

        if ( $message->to_uid != Session::get( 'user.id' ) ){
            return Response::json(array( 'error_code' => 3, 'message' => '无效消息' ));
        }

        $message->status = $status;

        if ( !$message->save() ){
            return Response::json(array( 'error_code' => 1, 'message' => '修改失败' ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '修改成功' ));
    }
}