<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();
        User::create([
            'name'=>'Jose Rivero',
            'email'=>'jarh18@gmail.com',
            'password'=>bcrypt('123456789'),
        ])->assignRole('Admin');
        User::factory(3)->create();
        User::create([
            'name'=>'Admin',
            'email'=>'admin@gmail.com',
            'password'=>bcrypt('123456789'),
        ])->assignRole('Admin');
        
    }
}
