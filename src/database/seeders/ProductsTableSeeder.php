<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ユーザーを2人作成（メール・パスワードを指定）
        $user1 = User::factory()->has(Profile::factory()->state(['image' => 'profile-img/person.png']))->create([
            'name' => 'Test User1',
            'email' => 'user1@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user2 = User::factory()->has(Profile::factory()->state(['image' => 'profile-img/person2.png']))->create([
            'name' => 'Test User2',
            'email' => 'user2@example.com',
            'password' => Hash::make('password123'),
        ]);

        $products = [
        [
            'id' => 1,
            'user_id' => $user1->id,
            'condition_id' => 1,
            'name' => '腕時計',
            'brand' => 'a',
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'image' => 'product-img/Armani+Mens+Clock.jpg'
        ],
        [
            'id' => 2,
            'user_id' => $user1->id,
            'condition_id' => 2,
            'name' => 'HDD',
            'brand' => 'a',
            'price' => 5000,
            'description' => '高速で信頼性の高いハードディスク',
            'image' => 'product-img/HDD+Hard+Disk.jpg'
        ],
        [
            'id' => 3,
            'user_id' => $user1->id,
            'condition_id' => 3,
            'name' => '玉ねぎ三束',
            'brand' => 'a',
            'price' => 300,
            'description' => '新鮮な玉ねぎ3束のセット',
            'image' => 'product-img/iLoveIMG+d.jpg'
        ],
        [
            'id' => 4,
            'user_id' => $user1->id,
            'condition_id' => 4,
            'name' => '革靴',
            'brand' => 'a',
            'price' => 4000,
            'description' => 'クラシックなデザインの革靴',
            'image' => 'product-img/Leather+Shoes+Product+Photo.jpg'
        ],
        [
            'id' => 5,
            'user_id' => $user1->id,
            'condition_id' => 1,
            'name' => 'ノートPC',
            'brand' => 'a',
            'price' => 45000,
            'description' => '高性能なノートパソコン',
            'image' => 'product-img/Living+Room+Laptop.jpg'
        ],
        [
            'id' => 6,
            'user_id' => $user2->id,
            'condition_id' => 2,
            'name' => 'マイク',
            'brand' => 'a',
            'price' => 8000,
            'description' => '高音質のレコーディング用マイク',
            'image' => 'product-img/Music+Mic+4632231.jpg'
        ],
        [
            'id' => 7,
            'user_id' => $user2->id,
            'condition_id' => 3,
            'name' => 'ショルダーバッグ',
            'brand' => 'a',
            'price' => 3500,
            'description' => 'おしゃれなショルダーバッグ',
            'image' => 'product-img/Purse+fashion+pocket.jpg'
        ],
        [
            'id' => 8,
            'user_id' => $user2->id,
            'condition_id' => 4,
            'name' => 'タンブラー',
            'brand' => 'a',
            'price' => 500,
            'description' => '使いやすいタンブラー',
            'image' => 'product-img/Tumbler+souvenir.jpg'
        ],
        [
            'id' => 9,
            'user_id' => $user2->id,
            'condition_id' => 1,
            'name' => 'コーヒーミル',
            'brand' => 'a',
            'price' => 4000,
            'description' => '手動のコーヒーミル',
            'image' => 'product-img/Waitress+with+Coffee+Grinder.jpg'
        ],
        [
            'id' => 10,
            'user_id' => $user2->id,
            'condition_id' => 2,
            'name' => 'メイクセット',
            'brand' => 'a',
            'price' => 2500,
            'description' => '便利なメイクアップセット',
            'image' => 'product-img/外出メイクアップセット.jpg'
        ]];
        DB::table('products')->insert($products);
    }
}
