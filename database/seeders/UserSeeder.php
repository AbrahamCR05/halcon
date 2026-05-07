<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    \App\Models\User::create([
        'name' => 'Administrador Halcon',
        'email' => 'admin@halcon.com',
        'password' => bcrypt('password'), 
        'role' => 'Admin',
        'active' => true,
    ]);
}
}
