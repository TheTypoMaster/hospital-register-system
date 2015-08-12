<?php

class ScheduleTableSeeder extends Seeder {

    public function run()
    {
        DB::table( 'schedules' )->delete();

        Schedule::create(array(
            'date' => '2015-08-17',
            'period' => 0,
            'doctor_id' => 1,
        ));

        Schedule::create(array(
            'date' => '2015-08-17',
            'period' => 1,
            'doctor_id' => 1,
        ));

        Schedule::create(array(
            'date' => '2015-08-18',
            'period' => 0,
            'doctor_id' => 1,
        ));

        Schedule::create(array(
            'date' => '2015-08-17',
            'period' => 0,
            'doctor_id' => 2
        ));

        Schedule::create(array(
            'date' => '2015-08-18',
            'period' => 1,
            'doctor_id' => 2,
        ));

        Schedule::create(array(
            'date' => '2015-08-17',
            'period' => 1,
            'doctor_id' => 3,
        ));

        Schedule::create(array(
            'date' => '2015-08-18',
            'period' => 0,
            'doctor_id' => 3,
        ));

        Schedule::create(array(
            'date' => '2015-08-19',
            'period' => 0,
            'doctor_id' => 3,
        ));
    }
}