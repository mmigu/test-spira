<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'lastname' => 'Admin',
            'phone' => '7777777',
            'password' => Hash::make('123456789'),
            'role_id' => 1,
            'email' => 'admin@email.com'
        ]);
    }
}
