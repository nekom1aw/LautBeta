<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Laut',
                'email' => 'admin@laut.local',
                'password_plaintext' => 'admin123',
                'role' => 'admin',
            ],
            [
                'name' => 'User Laut',
                'email' => 'user@laut.local',
                'password_plaintext' => 'user123',
                'role' => 'user',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => Hash::make($user['password_plaintext']),
                    'password_plaintext' => $user['password_plaintext'],
                    'role' => $user['role'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
