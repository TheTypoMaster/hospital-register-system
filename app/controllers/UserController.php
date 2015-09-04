<?php

class UserController extends BaseController{

    protected static $register_pass_code = 0;

    protected static $reset_password_pass_code = 1;

    protected static $verification_code_expire = 3600;

    protected static $verification_expire = 3600;

    protected static $remember_expire = 10800; // 记住用户名密码七天

    protected static $possible_charactors = 'abcdefghijklmnopqrstuvwxyz0123456789';
    
    protected function send_message( $user_telephone, $message ){
        
        $argv = array(
            'name'      => Config::get( 'platform.chuangrui.name' ),
            'pwd'       => Config::get( 'platform.chuangrui.password' ),
            'sign'      => Config::get( 'platform.chuangrui.sign' ),
            'type'      => 'pt',
            'mobile'    => $user_telephone,
            'content'   => $message //'您的验证码为：'.$code
        );

        //$url = 'http://web.cr6868.com/asmx/smsservice.aspx?'.http_build_query( $argv, '', '&' );
        $url = Config::get( 'platform.chuangrui.url' ).http_build_query( $argv, '', '&' );

        $response = file_get_contents( $url );
        $return_code = substr( $response, 0, 1 );

        return $return_code == '0';
    }

    protected function generate_verification_code(){
        $code  =  '';   //验证码
        while( strlen( $code ) < 6 ){
             $code .= substr( self::$possible_charactors, 
                              rand( 0, strlen( self::$possible_charactors ) - 1 ),
                              1 );
        }

        return $code;
    }

    protected function is_verification_failed(){

        $passed = Session::get( 'verification.passed' );

        return empty( $passed );
    }

    protected function is_verification_expired(){

        if ( Session::has( 'verification' ) && Session::get( 'verification.expire' ) > time() ){
            
            return false;
        }

        return true;
    }

    protected function is_verification_code_expired(){

        $code_expire = Session::get( 'verification.code.expire' );

        if ( isset( $code_expire ) && $code_expire > time() ){

            return false;
        }

        return true;
    }

    public function check_phone(){

        if ( !Input::has( 'telephone' ) ){
            return Response::json(array( 'error_code' => 2, 'message' => '请输入手机号码' ));
        }

        try{
            $user = Sentry::findUserByLogin( Input::get( 'telephone' ) );

            return Response::json(array( 'error_code' => self::$reset_password_pass_code, 'message' => '该手机号已被注册' ));            

        }catch( Cartalyst\Sentry\Users\UserNotFoundException $e ){

            return Response::json(array( 'error_code' => self::$register_pass_code, 'message' => '该手机号尚未注册' ));

        }catch( Exception $e ){

            return Response::json(array( 'error_code' => -1, 'message' => 'Unknown Error' ));
        }

    }

    public function send_verification_code(){

        $code = $this->generate_verification_code();
        $user_telephone = Input::get( 'telephone' );

        if ( !preg_match( Config::get( 'regex.telephone' ), $user_telephone ) ){

            return Response::json(array( 'error_code' => 1, 'message' => '手机号码不正确' ));
        }

        $message = '您的验证码为：'.$code;

        // 发送验证码
        if ( !$this->send_message( $user_telephone, $message ) ){
            return Response::json(array( 'error_code' => 3, 'message' => '验证码发送失败' ));
        }
        
        // 设置验证通过标志
        Session::put( 'verification.passed', false );
        
        // 保存用户手机
        Session::put( 'verification.telephone', $user_telephone );
        
        // 保存验证码
        Session::put( 'verification.code.content', $code );
        
        // 设置验证码期限 - 60 seconds
        Session::put( 'verification.code.expire', time() + self::$verification_code_expire );
        
        // 设置该验证有效期 - 60 minutes
        Session::put( 'verification.expire', time() + self::$verification_expire );

        return Response::json(array( 'error_code' => 0, 'message' => '验证码已经发送', 'code' => $code ));
    }

    public function check_verification_code(){

        /**
         * Check if call this method directly
         */
        if ( $this->is_verification_expired() ){

            Session::forget( 'verification' );

            return Response::json(array( 'error_code' => 2, 'message' => '请先获取验证码' ));
        }

        if ( $this->is_verification_code_expired() ){

            Session::forget( 'verification' );

            return Response::json(array( 'error_code' => 3, 'message' => '请重新获取验证码' ));
        }

        if ( !$this->is_verification_failed() ){

            return Response::json(array( 'error_code' => 4, 'message' => '您已验证过了' ));
        }

        $code_from_input = Input::get( 'verification_code' );
        $code_from_session = Session::get( 'verification.code.content' );
        
        if ( $code_from_input != $code_from_session ){

            return Response::json(array( 'error_code' => 1, 'message' => '验证码错误' ));
        }

        // 验证成功
        Session::put( 'verification.passed', true );

        return Response::json(array( 'error_code' => 0, 'message' => '验证码正确' ));
    }

    public function verify_and_reset_password(){

        if ( $this->is_verification_expired() ){
            return Response::json(array( 'error_code' => 2, 'message' => '请先验证手机号' ));
        }

        if ( $this->is_verification_failed() ){
            return Response::json(array( 'error_code' => 3, 'message' => '尚未验证通过' ));
        }

        $new_password = Input::get( 'new_password' );

        if ( !isset( $new_password ) ){
            return Response::json(array( 'error_code' => 4, 'message' => '请输入新密码' ));
        } 

        // 
        try{
            $user = Sentry::findUserByLogin( Session::get( 'verification.telephone' ) );

            if ( $user->attemptResetPassword( $user->getResetPasswordCode(), $new_password ) ){
                
                return Response::json(array( 'error_code' => 0, 'message' => '重置密码成功' ));
            }else{

                return Response::json(array( 'error_code' => 1, 'message' => '重置密码失败' ));    
            }
        }catch( Cartalyst\Sentry\Users\UserNotFoundException $e ){
            
            Session::forget( 'verification' );

            return Response::json(array( 'error_code' => 5, 'message' => '该手机号尚未注册' ));
            
        }catch( Exception $e ){

            return Response::json(array( 'error_code' => -1, 'message' => 'Unknown Error' ));
        }
    }

    public function reset_password_first(){

        return View::make( 
            'user.verification', 
            array( 
                'title' => '找回密码', 
                'next_url' => '/user/reset_password/second',
                'pass_code' => self::$reset_password_pass_code ) );
    }

    public function reset_password_second(){

        if ( $this->is_verification_expired() || $this->is_verification_failed() ){
            
            return Redirect::to( '/user/reset_password/first' );
        }

        return View::make( 'user.reset_password' );
    }

    public function modify_user(){

        $user         = Sentry::findUserById( Session::get( 'user.id' ) );
        $new_password = Input::get( 'new_password' );
        $old_password = Input::get( 'old_password' );
        $nickname     = Input::get( 'nickname' );

        if ( isset( $new_password ) && isset( $old_password ) ) {
            
            if ( $new_password == $old_password ){

                return Response::json(array( 'error_code' => 2, 'message' => '新旧密码不能相同' ));
            }else{

                if ( $user->checkPassword( $old_password ) ){
                
                    $user->password = $new_password;
                }else{
                    return Response::json(array( 'error_code' => 3, 'message' => '旧密码不正确' ));
                }
            }
        }

        if ( isset( $nickname ) ){
            $user->nickname = $nickname;
        }

        if ( !$user->save() ){
            return Response::json(array( 'error_code' => 1, 'message' => '修改失败' ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '修改成功' ));
    }

    public function login_get(){
        
        return View::make( 
            'user.login', 
            array( 
                'remember'          => Cookie::get( 'remember' ),
                'remember_phone'    => Cookie::get( 'phone' ),
                'remember_password' => Cookie::get( 'password' ) ) );
    }

    public function login_post(){

        $phone = Input::get( 'phone' );
        $password = Input::get( 'password' );

        try{
            Sentry::authenticate(array(
                'phone' => $phone,
                'password' => $password
            ), false);

        }catch( Cartalyst\Sentry\Users\LoginRequiredException $e ){

            return Response::json(array( 'error_code' => 1, 'message' => '请输入手机号码' ));

        }catch( Cartalyst\Sentry\Users\PasswordRequiredException $e ){

            return Response::json(array( 'error_code' => 2, 'message' => '请输入密码' ));

        }catch( Cartalyst\Sentry\Users\UserNotFoundException $e ){

            return Response::json(array( 'error_code' => 3, 'message' => '手机号或密码错误' ));

        }catch( Cartalyst\Sentry\Users\WrongPasswordException $e ){

            return Response::json(array( 'error_code' => 4, 'message' => '手机号或密码错误' ));
        
        }catch( Exception $e ){

            return Response::json(array( 'error_code' => -1, 'message' => 'Unknown Error' ));
        }

        $user = Sentry::getUser();

        if ( !( $user->role & 0x01 ) ){
            return Response::json(array( 'error_code' => 5, 'message' => '无效用户' ));
        }

        Session::put( 'user.id', $user->id );

        $error_message = array( 'error_code' => 0, 'message' => '登陆成功' );

        // 通过ajax请求，则返回之前的uri，前端进行跳转
        if ( Request::ajax() ){

            $error_message['uri_before'] = Session::pull( 'uri.before_login' );
            $response = Response::json( $error_message );

            if ( ( $remember = Input::get( 'remember' ) ) == 'true' ){
                $response->headers->setCookie( Cookie::make( 'remember', true, self::$remember_expire ) );
                $response->headers->setCookie( Cookie::make( 'phone', $phone, self::$remember_expire ) );
                $response->headers->setCookie( Cookie::make( 'password', $password, self::$remember_expire ) );
            }else{
                $response->headers->setCookie( Cookie::forget( 'remember' ) );
                $response->headers->setCookie( Cookie::forget( 'phone' ) );
                $response->headers->setCookie( Cookie::forget( 'password' ) );
            }

            return $response;
        }else{

            $error_message['user'] = $user;
        }

        return Response::json( $error_message );
    }

    public function logout(){
        
        if( Sentry::check() ){
            Sentry::logout();
            Session::pull( 'user.id' );
            return Response::json(array('error_code' => 0,'message' => '退出成功!'));
        } else {
            return Response::json(array('error_code' => 1,'message' => '用户未登录'));
        }

    }

    public function register_first(){

        return View::make( 
            'user.verification', 
            array( 
                'title' => '注册', 
                'next_url' => '/user/register/second',
                'pass_code' => self::$register_pass_code ) );
    }

    public function register_second(){

        if ( $this->is_verification_expired() || $this->is_verification_failed() ){
            
            return Redirect::to( '/user/register/first' );
        }

        return View::make( 'user.register.index' );
    }

    public function register_post(){

        if ( $this->is_verification_expired() ){
            return Response::json(array( 'error_code' => 2, 'message' => '请先验证手机号' ));
        }

        if ( $this->is_verification_failed() ){
            return Response::json(array( 'error_code' => 3, 'message' => '尚未验证通过' ));
        }

        if ( !Input::has( 'nickname' ) ){
            return Response::json(array( 'error_code' => 4, 'message' => '请输入昵称' ));
        }    

        if ( !Input::has( 'password' ) ){
            return Response::json(array( 'error_code' => 5, 'message' => '请输入密码'));
        }

        if ( !preg_match( '/^[_0-9a-z]{6,16}$/', Input::get( 'password' ) ) ){
            return Response::json(array( 'error_code' => 6, 'message' => '密码格式不正确' ));
        }

        if ( !Input::has( 'real_name' ) ){
            return Response::json(array( 'error_code' => 7, 'message' => '请输入真实姓名' ));
        }

        if ( !Input::has( 'gender' ) ){
            return Response::json(array( 'error_code' => 8, 'message' => '请输入性别' ));
        }

        try{
            Sentry::createUser(array(
                'nickname'      => Input::get( 'nickname' ),
                'password'      => Input::get( 'password' ),
                'real_name'     => Input::get( 'real_name' ),
                'gender'        => Input::get( 'gender' ),
                'phone'         => Session::get( 'verification.telephone' ),
                'role'          => 1,
                'activated'     => true
            ));   
        }catch( Exception $e ){
            return Response::json(array( 'error_code' => 1, 'message' => '注册失败' ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '注册成功' ));
    }

    public function test(){

        var_dump( Session::all() );die();

/*
        $xml = new simpleXMLElement( '<xml><return_code>return_code</return_code><return_msg><![CDATA[OK]]></return_msg></xml>' );
        var_dump( $xml );die();
*/
        //return View::make( 'user.test' );
    }

    public function upload_head_portrait(){

        if ( !Input::hasFile( 'head_portrait' ) ){

            return Response::json(array( 'error_code' => 2, 'message' => '无文件上传' ));
        }

        if ( !Input::file( 'head_portrait' )->isValid() ){

            return Response::json(array( 'error_code' => 3, 'message' => '文件无效' ));
        }

        $head_portrait = Input::file( 'head_portrait' );

        $file_size = $head_portrait->getSize();

        if ( $file_size > 2 * 1024 * 1024 ){
            return Response::json(array( 'error_code' => 4, 'message' => '文件过大' ));
        }

/*
        $validator = Validator::make(
            array( 'photo' => $head_portrait ),
            array( 'photo' => 'image' ) );

        if ( $validator->fails() ){
            return Response::json(array( 'error_code' => 5, 'message' => '必须为图片文件' ));
        }
*/
        $file_ext = $head_portrait->getClientOriginalExtension();

        $user_id = Session::get( 'user.id' );

        $user = User::find( $user_id );

        try{    

            $photo_path = '/images/upload/';
            $photo_full_name = uniqid( $user_id.time() ).'.'.$file_ext;

            $previous_photo = public_path().$doctor->photo;

            $user->photo = $photo_path.$photo_full_name;

            if ( !$user->save() ){
                return Response::json(array( 'error_code' => 5, 'message' => '错误' ));
            }

            // Save and delete previous photo
            if ( File::exists( $previous_photo ) ){
                File::delete( public_path().$previous_photo );
            }

            $head_portrait->move( public_path().$photo_path , $photo_full_name );
        }

        catch( Exception $e ){

            return Response::json(array( 'error_code' => 1, 'message' => $e->getMessage() ));
        }

        return Response::json(array( 'error_code' => 0, 'message' => '保存成功', 'path' => $user->photo, 'size' => $file_size ));
    }

    public function pay_record(){

        if ( Request::wantsJson() ){
            return $this->pay_record_json();
        }else{
            return $this->pay_record_html();
        }

    }

    public function pay_record_html(){

        $user = User::find( Session::get( 'user.id' ) );
        $records = $user->register_records()->with( 'doctor' )->get();

        if ( $records->count() == 0 ){
           return View::make( 'user.pay_record_empty' );
        }

        $status = array( '未就诊', '已就诊', '需复诊' );
        $period = array( '上午', '下午' );

        $result = array();
        foreach( $records as $record ){
            $doctor = $record->doctor;
            $result[] = array(
                'fee'           => $record->fee,
                'status'        => $status[ $record->status ],
                'start'         => date( 'Y-m-d H:i', strtotime( $record->start ) ),
                'department'    => $doctor->department->name,
                'doctor'        => array( 'name' => $doctor->name, 'title' => $doctor->title )
            );
        }

        return View::make( 'user.pay_record', array( 'records' => $result ));
    }

    public function pay_record_json(){

        $user = User::find( Session::get( 'user.id' ) );
        $records = $user->register_records()->with( 'doctor' )->get();

        if ( !isset( $records ) ){
            return Response::json(array( 'error_code' => 1, 'message' => '无缴费记录' ));
        }

        $result = array();
        foreach ( $records as $record ){
            $doctor = $record->doctor;
            $account = RegisterAccount::find( $record->account_id );
            $result[] = array(
                'id'            => $record->id,
                'doctor' => array(
                    'name'  => $doctor->name,
                    'title' => $doctor->title
                ),
                'fee'           => $record->fee,
                'department'    => $doctor->department->name,
                'created_at'    => $record->created_at->format('Y-m-d H:i:s'),
                'account'       => $account->name
            );
        }

        return Response::json(array( 'error_code' => 0, 'pay_records' => $result ));
    }

    public function register_success(){
        
        return View::make( 'user.register.success' );
    }

    public function user_center(){

        $register_account = RegisterAccount::where( 'user_id', Session::get( 'user.id' ) )->first();

        return View::make( 'user.center', array( 'account' => $register_account ) );
    }

    public function get_chat_package(){
        
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

        return Response::json( $parameter );
    }
}
