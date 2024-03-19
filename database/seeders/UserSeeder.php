<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $adminRoleId = Role::where('slug', 'admin')->first()->id;

        User::create([
            'role_id'   => $adminRoleId,
            'name'      => 'Admin User', // Add this line
            'email'     => 'admin@apex.test',
            'password'  => bcrypt('password'),
        ]);
    }
}
