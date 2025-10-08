<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Employee User
        User::create([
            'name' => 'Fadlan Anshari',
            'email' => 'employee@gmail.com',
            'password' => Hash::make('employee123'),
            'role' => 'employee',
        ]);

        // Employee User
        User::create([
            'name' => 'Employee 1',
            'email' => 'employee1@gmail.com',
            'password' => Hash::make('employee123'),
            'role' => 'employee',
        ]);

        // Employee User
        User::create([
            'name' => 'Employee 2',
            'email' => 'employee2@gmail.com',
            'password' => Hash::make('employee123'),
            'role' => 'employee',
        ]);

        // Employee User
        User::create([
            'name' => 'Employee 3',
            'email' => 'employee3@gmail.com',
            'password' => Hash::make('employee123'),
            'role' => 'employee',
        ]);
    }
}
