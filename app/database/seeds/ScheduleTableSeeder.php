<?php

class ScheduleTableSeeder extends Seeder {

    public function run()
    {
        DB::table( 'schedules' )->delete();

        Schedule::create(array(
            'date' => '2015-08-24',
            'period' => 0,
            'doctor_id' => 1,
        ));

        Schedule::create(array(
            'date' => '2015-08-24',
            'period' => 1,
            'doctor_id' => 1,
        ));

        Schedule::create(array(
            'date' => '2015-08-25',
            'period' => 0,
            'doctor_id' => 1,
        ));

        Schedule::create(array(
            'date' => '2015-08-24',
            'period' => 0,
            'doctor_id' => 2
        ));

        Schedule::create(array(
            'date' => '2015-08-25',
            'period' => 1,
            'doctor_id' => 2,
        ));

        Schedule::create(array(
            'date' => '2015-08-24',
            'period' => 1,
            'doctor_id' => 3,
        ));

        Schedule::create(array(
            'date' => '2015-08-25',
            'period' => 0,
            'doctor_id' => 3,
        ));

        Schedule::create(array(
            'date' => '2015-08-26',
            'period' => 0,
            'doctor_id' => 3,
        ));
    }
}
