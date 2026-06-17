<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $param = [
            [
                'name' => '西怜奈',
                'email' => 'user1@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password1'),
                'admin_status' => false,
            ],

            [
                'name' => '山田太郎',
                'email' => 'user2@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password2'),
                'admin_status' => false,
            ],

            [
                'name' => '管理秀一',
                'email' => 'user3@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password3'),
                'admin_status' => true,
            ],
        ];

        DB::table('users')->insert($param);
    }
}
