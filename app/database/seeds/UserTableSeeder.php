<?php

class UserTableSeeder extends Seeder {

    public function run()
    {

        DB::table( 'users' )->delete();

        Sentry::createUser(array(
            'nickname' => 'hyuyuan',
            'password' => '58085088',
            'real_name' => '黄裕源',
            'phone' => '13580501456',
            'role' => 7,
            'gender' => 1,
            'activated' => 1,
        ));

        Sentry::createUser(array(
            'nickname' => 'Cobb',
            'password' => '123456',
            'real_name' => '李四',
            'phone' => '13512341234',
            'role' => 3,
            'gender' => 1,
            'activated' => 1
        ));

        Sentry::createUser(array(
            'nickname' => 'Alies',
            'password' => '123123',
            'real_name' => '小李子',
            'phone' => '18511112222',
            'role' => 1,
            'gender' => 2,
            'activated' => 1
        ));

        Sentry::createUser(array(
            'nickname' => 'adeng',
            'password' => '8888888',
            'real_name' => '阿登',
            'phone' => '18899990000',
            'role' => 3,
            'gender' => 2,
            'activated' => 1
        ));

        Sentry::createUser(array(
            'nickname' => 'hulin',
            'password' => 'abcdefg',
            'real_name' => '胡琳',
            'phone' => '13250502288',
            'role' => 2,
            'gender' => 1,
            'activated' => 1
        ));

        Sentry::createUser(array(
            'nickname' => 'jiali',
            'password' => 'bcd123',
            'real_name' => '袁嘉丽',
            'phone' => '13322225555',
            'role' => 1,
            'gender' => 2,
            'activated' => 1
        ));

        Sentry::createUser(array(
            'nickname' => 'AAA',
            'password' => 'AAA',
            'real_name' => 'Test',
            'phone' => '13022225555',
            'role' => 2,
            'gender' => 2,
            'activated' => 1
        ));

        Sentry::createUser(array(
            'nickname' => 'BBB',
            'password' => 'BBB',
            'real_name' => 'Test',
            'phone' => '13122225555',
            'role' => 2,
            'gender' => 2,
            'activated' => 1
        ));

        Sentry::createUser(array(
            'nickname' => 'CCC',
            'password' => 'CCC',
            'real_name' => 'Test',
            'phone' => '13222225555',
            'role' => 2,
            'gender' => 2,
            'activated' => 1
        ));
    }
}
