<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role();
        $role->insert([
            [
                'id'    => 1,
                'name'  => '教师'
            ],[
                'id'    => 2,
                'name'  => '学生'
            ]
        ]);
    }
}
