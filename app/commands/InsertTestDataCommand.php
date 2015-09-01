<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InsertTestDataCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'data:insert';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Insert test data';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->insert_data();
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
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

		$users = User::all();

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

		$doctors = Doctor::all();

		foreach( $doctors as $doctor ){

			$count = rand( 300, 500 );
			
			while( $count-- ){

				$date  		= $this->get_random_date();
				$schedules 	= Schedule::where( 'date', $date )
									  ->where( 'doctor_id', $doctor->id )
									  ->get();

				if ( $schedules->count() ){
					continue;
				}

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

		$schedules = Schedule::all();

		foreach( $schedules as $schedule ){
			// 1/4几率将该schedule设为无号源
			if ( rand( 0, 3 ) ){
				if ( $schedule->period == 0 ){
					Period::create([
						'start' => '8:00',
						'end' => '9:30',
						'total' => 10,
						'current' => 0,
						'schedule_id' => $schedule->id
					]);
				}else if ( $schedule->period == 1 ){
					Period::create([
						'start' => '14:00',
						'end' => '15:30',
						'total' => 10,
						'current' => 0,
						'schedule_id' => $schedule->id
					]);
				}
			}
		}
	}

	public function insert_records(){
		
		$periods = Period::all();
		$accounts = RegisterAccount::all();
		$peirod_num = $periods->count();
		$account_num = $accounts->count();

		for ( $i = 0; $i < 2000; ++$i ){
			$record = new RegisterRecord();

			$account = $accounts[ rand( 0, $account_num - 1 ) ];
			$period = $periods[ rand( 0, $peirod_num - 1 ) ];

			$dt = rand( 0, 99 ) < 90 ? $this->get_random_datetime() : date( 'Y-m-d H:i:s' ) ;
			$record->created_at = $dt;
			$record->start = date( 'Y-m-d H:i:s', strtotime( $dt ) + 3600 );
			if ( rand( 0, 1 ) ){
				$record->return_date = $this->get_random_date();
			}
			$record->status = rand( 0, 1 );
			$record->fee = 1.0;

			if ( rand( 0, 1 ) ){
				$record->advice = "abcd1234";
			}

			$record->account_id = $account->id;
			$record->user_id = $account->user_id;
			$record->period_id = $period->id;
			$record->doctor_id = $period->schedule->doctor_id;

			$period->current += 1;
			$period->save();
			$record->save();

			$message = new Message();
			$message->from_uid = $record->user_id;
			$message->to_uid = $record->doctor->user->id;
			$message->content = $record->user->real_name.'挂号';
			$message->timestamp = strtotime( $dt );
			$message->status = 3;
			$message->save();
		}
	}

	protected function insert_comments(){

		$records = RegisterRecord::all();

		foreach( $records as $record ){
			$comment = new Comment();
			$comment->content = $this->get_random( self::$RANDOM_ALPHA_NUM, 10, 20 );
			$comment->record_id = $record->id;
			$comment->created_at = date( 'Y-m-d H:i:s', strtotime( $record->start ) + 3600 );
			$comment->save();
		}
	}

	protected function get_random_timestamp_offset(){
		$offset_year = rand( 0, 2 ) - 1;
		$offset_month = rand( 0, 4 ) - 2;
		$offset_day = rand( 0, 7 ) - 3;

		return 	$offset_year * self::$seconds_per_year + 
				$offset_month * self::$seconds_per_month + 
				$offset_day * self::$seconds_per_day;
	}

	protected function get_random_datetime(){

		return date( 'Y-m-d H:i:s', time() + $this->get_random_timestamp_offset() );
	}

	protected function get_random_date(){	
		
		return date( 'Y-m-d', time() + $this->get_random_timestamp_offset() );
	}

	protected function get_random_time(){

		return date( 'H:i:s', time() + $this->get_random_timestamp_offset() );
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
