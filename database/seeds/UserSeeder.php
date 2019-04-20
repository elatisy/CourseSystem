<?php

use Illuminate\Database\Seeder;

use App\UserRole;
use App\User;
use App\Tools\Encrypt;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userRoleTable = new UserRole();
        $userTable = new User();

        $userTable->insert([
            'name'          => 'admin',
            'mobile'        => '13081850976',
            'email'         => 'wdnb@acmclub.cn',
            'job'           => '弟弟',
            'student_code'  => '20178955',
            'password'      => Encrypt::encrypt('123456')
        ]);

        $userRoleTable->insert([
            'users_id'  => 1,
            'roles_id'  => 1
        ]);
    }
}
