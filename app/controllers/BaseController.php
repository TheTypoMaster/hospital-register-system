<?php

class BaseController extends Controller {

	protected $data;

	protected $template;

	protected $return_type;

	protected $error_code;

	protected $error_messages;

	protected $postprocess_functions;

	protected $preprocess_functions;

	protected static $default_return_type = 'html';

	protected function get_return_format(){

		if ( Request::wantsJson() ){
			return 'json';
		}

		return self::$default_return_type;
	}

	public function __construct(){
		$this->data = array();
		$this->error_code = 0;
		$this->error_messages = array( 0 => 'ok' );

		$this->postprocess_functions = array();
		$this->preprocess_functions = array();

		$this->template = '';
		$this->return_type = $this->get_return_format(); 
	}

	public function get_url_with_parameters( $parameters ){
		return Request::url().'?'.http_build_query( $parameters, '', '&' );
	}

	public function is_status_ok(){
		return !$this->error_code;
	}

	public function set_data( $data ){
		$this->data = $data;
	}

	public function get_data(){
		return $this->data;
	}

	public function set_return_type( $return_type ){
		$this->return_type = $return_type;
	}

	public function get_return_type(){
		return $this->return_type;
	}

	public function set_template( $template ){
		$this->template = $template;
	}

	public function get_template(){
		return $this->template;
	}

	public function set_error_code( $error_code ){
		$this->error_code = $error_code;
	}

	public function get_error_code( ){
		return $this->error_code;
	}

	public function set_error_message( $code, $message ){
		$this->error_messages[ $code ] = $message;
	}

	public function get_error_message( $code ){
		return $this->error_messages[ $code ];
	}

	public function set_error_code_and_message( $code, $message ){
		$this->error_code = $code;
		$this->error_messages[ $code ] = $message;
	}

	public function set_postprocess_function( $type, $func ){
		$this->postprocess_functions[ $type ] = $func;
	}

	public function get_postprocess_function( $type ){
		return $this->postprocess_functions[ $type ];
	}

	public function set_preprocess_function( $type, $func ){
		$this->preprocess_functions[ $type ] = $func;
	}

	public function get_preprocess_function( $type ){
		return $this->preprocess_functions[ $type ];
	}

	/**
	 * Call preprocess function 
	 *
	 * @var 	$type 	string
	 * @var 	$data   arary
	 * @return  array
	 */
	protected function call_preprocess_function( $type, $data ){
	
		if ( array_key_exists( $type, $this->preprocess_functions ) ){
			$func = $this->preprocess_functions[ $type ];
			
			if ( is_callable( $func ) ){
				return $func( $data );	
			}
		}

		return $data;
	}

	/**
	 * Call post process function 
	 *
	 * @var 	$type 	string
	 * @var 	$data   arary
	 * @return  array
	 */
	protected function call_postprocess_function( $type, $data ){
		
		if ( array_key_exists( $type, $this->postprocess_functions ) ){
			$func = $this->postprocess_functions[ $type ];
			
			if ( is_callable( $func ) ){
				return $func( $data, $this->is_status_ok() );	
			}
		}

		return $data;
	}

	/**
	 * Create response with data
	 *
	 * @var 	$data array
	 * @return  mix
	 */
	public function response( ){

		$data = $this->data;
		
		/**
		 * For json response
		 * 
		 */
		if ( $this->return_type == 'json' ){

			$data = $this->call_preprocess_function( $this->return_type, $data );

			if ( $this->is_status_ok() ){
				$data['error_code'] = $this->error_code;
				$result = $data;
			}else{
				$result = array(
					'error_code' => $this->error_code,
					'message' 	 => $this->error_messages[ $this->error_code ]
				);
			}

			return Response::json( $this->call_postprocess_function( $this->return_type, $result ) );
		}

		/**
		 * For html response
		 * Not any procedure. Just call preprocess and post process functions.
		 */
		else if ( $this->return_type == 'html' ){

			$data = $this->call_preprocess_function( $this->return_type, $data );

			//$result = $this->is_status_ok() ? $data : $this->error_messages[ $this->error_code ];

			return View::make( $this->template, $this->call_postprocess_function( $this->return_type, $data ) );
		}

		return $data;
	}

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( !is_null($this->layout) )
		{
			$this->layout = View::make($this->layout);
		}
	}

	protected static $seconds_per_day 	= 86400;
	protected static $seconds_per_month = 2419200;
	protected static $seconds_per_year 	= 29030400;

	protected static $RANDOM_NUM		= 0x01;
	protected static $RANDOM_ALPHA 		= 0x10;
	protected static $RANDOM_ALPHA_NUM 	= 0x11;

	protected static $ALPHA_LOWER 		= 'abcdefghijklmnopqrstuvwxyz';
	protected static $ALPHA_UPPER		= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	protected static $NUMBER			= '0123456789';
	protected static $TELEPHONE_PREFIX  = [
		'130','131','132','133','134','135','136','137','138','139',
		'180','181','182','183','184','185','186','187','188','189',
		'150','151','152','153','155','156','157','158','159',
		'175','176','177','178','17'
	];

	/*
	 * 
	 */

	public function insert_data(){

		DB::transaction(function(){

			$this->insert_users();

			$this->insert_accounts();

			$this->insert_schedules();

			$this->insert_periods();

			$this->insert_records();

			$this->insert_comments();

		});

		return Response::make( '' );
	}

	protected function insert_users(){

		for ( $i = 0; $i != 20; ++$i ){
			$password = $this->get_random( self::$RANDOM_ALPHA_NUM, 6, 16 );
			Sentry::createUser([
				'password'		=> $password,
				'nickname'		=> $this->get_random( self::$RANDOM_ALPHA, 4, 12 ),
				'real_name'		=> $this->get_random( self::$RANDOM_ALPHA, 4, 12 ),
				'phone'			=> rand( 0, count(self::$TELEPHONE_PREFIX) ) + $this->get_random( self::$RANDOM_NUM, 8, 8 ),
				'account'		=> $this->get_random( self::$RANDOM_ALPHA_NUM, 6, 16 ),
				'role'			=> 1,
				'gender'		=> rand( 0, 1 ),
				'activated'		=> 1
			]);
		}
	}

	protected function insert_accounts(){

		$users = Usre::get();

		foreach( $users as $user ){

			RegisterAccount::create([
				'name'				=> $this->get_random( self::$RANDOM_ALPHA, 4, 12 ),
				'age'				=> rand( 0, 100 ),
				'weight'			=> rand( 10, 100 ),
				'gender'			=> rand( 0, 1 ),
				'blood_type'		=> 'A型',
				'type'				=> '小伙子',
				'phone'				=> rand( 0, count(self::$TELEPHONE_PREFIX) ) + $this->get_random( self::$RANDOM_NUM, 8, 8 ),
				'id_card'			=> '44092319930206' + rand( 1000, 9999 ),
				'emergency_name'	=> $this->get_random( self::$RANDOM_ALPHA, 4, 12 ),
				'emergency_phone' 	=> rand( 0, count(self::$TELEPHONE_PREFIX) ) + $this->get_random( self::$RANDOM_NUM, 8, 8 ),
				'user_id'			=> $user->id
			]);
		}
	}

	protected function insert_schedules(){

		$doctors = Doctor::get();

		foreach( $doctors as $doctor ){

			$count = rand( 100, 200 );
			
			while( $count-- ){

				$date  = $this->get_random_date();

				// 添加早上排班
				if ( rand( 0, 1 ) ){
					Schedule::create([
						'date'		=> $date,
						'period'	=> 0,
						'doctor_id'	=>  $doctor->id
					]);
				}
				
				// 添加下午排班
				if ( rand( 0, 1 ) ){
					Schedule::create([
						'date'		=> $date,
						'period'	=> 1,
						'doctor_id'	=>  $doctor->id
					]);
				}
			}
		}
	}

	protected function insert_periods(){
		$schedules = Schedule::get();

		foreach( $schedules as $schedule ){
			// 1/4几率将该schedule设为无号源
			if ( rand( 0, 3 ) ){
			}
		}
	}

	protected function get_random_date(){

		$offset_year = rand( 0, 2 ) - 1;
		$offset_month = rand( 0, 4 ) - 2;
		$offset_day = rand( 0, 7 ) - 3;

		$total_offset = $offset_year * self::$seconds_per_year + 
						$offset_month * self::$seconds_per_month + 
						$offset_day * self::$seconds_per_day;
		
		return date( 'Y-m-d', time() + $total_offset );
	}

	protected function get_random( $flag, $min, $max ){

		$bs = '';

		if ( $flag & self::$RANDOM_ALPHA ){
			$bs .= self::$ALPHA_UPPER.self::$ALPHA_LOWER;
		}

		if ( $flag & self::$RANDOM_NUM ){
			$bs .= self::$NUMBER;
		}

		$i = 0;
		$rstr = '';
		$bs_count = strlen( $bs );

		while( $i < $max ){

			$rstr .= $bs[ rand( 0, $bs_count - 1 ) ];

			if ( $this->stop() && $i >= $min ){
				return $rstr;
			}

			++$i;
		}

		return $rstr;
	}

	protected function stop(){

		return rand( 0, 1 );
	}
}
