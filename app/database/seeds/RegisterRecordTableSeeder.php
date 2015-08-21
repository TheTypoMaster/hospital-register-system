<?php

class RegisterRecordTableSeeder extends Seeder {

    public function run()
    {
        DB::table( 'register_records' )->delete();

        RegisterRecord::create(array(
            'start'         => '2015-07-02 8:30',
            'status'        => 0,
            'fee'           => 1.0,
            'advice'        => '',
            'return_date'   => '2015-08-02',
            'doctor_id'     => 5,
            'account_id'    => 1,
            'user_id'       => 4,
            'period_id'     => 1
        ));

        RegisterRecord::create(array(
            'start'         => '2015-07-02 14:30',
            'status'        => 1,
            'fee'           => 2.0,
            'advice'        => '多喝水',
            'return_date'   => '2015-07-20',
            'doctor_id'     => 6,
            'account_id'    => 2,
            'user_id'       => 3,
            'period_id'     => 1
        ));

        RegisterRecord::create(array(
            'start'         => '2015-07-02 16:30',
            'status'        => 2,
            'fee'           => 3.0,
            'advice'        => '多吃药',
            'return_date'   => '2015-08-20',
            'doctor_id'     => 7,
            'account_id'    => 3,
            'user_id'       => 2,
            'period_id'     => 1
        ));

        RegisterRecord::create(array(
            'start'         => '2015-07-02 8:00',
            'status'        => 1,
            'fee'           => 4.0,
            'advice'        => '多运动',
            'return_date'   => '2015-08-01',
            'doctor_id'     => 8,
            'account_id'    => 4,
            'user_id'       => 1,
            'period_id'     => 1
        ));
    }
}