<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    // 10 usuarios aleatorios (con roles variados)
        User::factory(10)->create();
    
    // Admin específico (fácil y directo)
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Principal',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
