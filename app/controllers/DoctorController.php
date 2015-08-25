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

        Session::put( 'user.id', $user->id );

        return Response::json(array( 'error_code' => 0, 'message' => '登录成功' ));
    }
}