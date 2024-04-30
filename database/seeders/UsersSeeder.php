<?php

namespace Database\Seeders;
use DB;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name'=>'Admin',
            'email'=>'admin@designcrm.net',
            'is_employee'=>'2',
            'password'=> bcrypt('admin@021'),
        ]);
    }
}
