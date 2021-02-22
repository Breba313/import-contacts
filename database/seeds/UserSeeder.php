<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'email' => 'usertest@test.com',
            'password' => bcrypt('koombea'),
        ]);

        DB::table('users')->insert([
            'email' => 'brebatista@gmail.com',
            'password' => bcrypt('secreta'),
        ]);
    
    }
}
