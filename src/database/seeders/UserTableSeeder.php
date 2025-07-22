<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [];

        $users[] = User::factory()->has(Profile::factory()->state(['image' => 'profile-img/person.png']))->create([
            'name' => 'Test User1',
            'email' => 'user1@example.com',
            'password' => Hash::make('password123'),
        ]);

        $users[] = User::factory()->has(Profile::factory()->state(['image' => 'profile-img/person2.png']))->create([
            'name' => 'Test User2',
            'email' => 'user2@example.com',
            'password' => Hash::make('password123'),
        ]);

        // ユーザーの配列を返す（オプション: ProductSeeder用）
        return $users;
    }
}
