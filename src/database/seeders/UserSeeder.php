<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'テスト太郎',
                'email' => 'test-taro@mail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('test-taro'),
                'postal_code' => '123-4567',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'テスト花子',
                'email' => 'test-hanako@mail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('test-hanako'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 追加のユーザーデータをここに記述
        ]);
    }
}
