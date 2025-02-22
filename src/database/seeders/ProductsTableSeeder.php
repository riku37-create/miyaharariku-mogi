<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'user_id' => 1,
            'condition_id' => 1,
            'name' => '腕時計',
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'image' => 'product-img/Armani+Mens+Clock.jpg'
        ];
        DB::table('products')->insert($param);
        $param = [
            'user_id' => 1,
            'condition_id' => 2,
            'name' => 'HDD',
            'price' => 5000,
            'description' => '高速で信頼性の高いハードディスク',
            'image' => 'product-img/HDD+Hard+Disk.jpg'
        ];
        DB::table('products')->insert($param);
        $param = [
            'user_id' => 1,
            'condition_id' => 3,
            'name' => '玉ねぎ三束',
            'price' => 300,
            'description' => '新鮮な玉ねぎ3束のセット',
            'image' => 'product-img/iLoveIMG+d.jpg'
        ];
        DB::table('products')->insert($param);
        $param = [
            'user_id' => 1,
            'condition_id' => 4,
            'name' => '革靴',
            'price' => 4000,
            'description' => 'クラシックなデザインの革靴',
            'image' => 'product-img/Leather+Shoes+Product+Photo.jpg'
        ];
        DB::table('products')->insert($param);
        $param = [
            'user_id' => 1,
            'condition_id' => 1,
            'name' => 'ノートPC',
            'price' => 45000,
            'description' => '高性能なノートパソコン',
            'image' => 'product-img/Living+Room+Laptop.jpg'
        ];
        DB::table('products')->insert($param);
        $param = [
            'user_id' => 1,
            'condition_id' => 2,
            'name' => 'マイク',
            'price' => 8000,
            'description' => '高音質のレコーディング用マイク',
            'image' => 'product-img/Music+Mic+4632231.jpg'
        ];
        DB::table('products')->insert($param);
        $param = [
            'user_id' => 1,
            'condition_id' => 3,
            'name' => 'ショルダーバッグ',
            'price' => 3500,
            'description' => 'おしゃれなショルダーバッグ',
            'image' => 'product-img/Purse+fashion+pocket.jpg'
        ];
        DB::table('products')->insert($param);
        $param = [
            'user_id' => 1,
            'condition_id' => 4,
            'name' => 'タンブラー',
            'price' => 500,
            'description' => '使いやすいタンブラー',
            'image' => 'product-img/Tumbler+souvenir.jpg'
        ];
        DB::table('products')->insert($param);
        $param = [
            'user_id' => 1,
            'condition_id' => 1,
            'name' => 'コーヒーミル',
            'price' => 4000,
            'description' => '手動のコーヒーミル',
            'image' => 'product-img/Waitress+with+Coffee+Grinder.jpg'
        ];
        DB::table('products')->insert($param);
        $param = [
            'user_id' => 1,
            'condition_id' => 2,
            'name' => 'メイクセット',
            'price' => 2500,
            'description' => '便利なメイクアップセット',
            'image' => 'product-img/外出メイクアップセット.jpg'
        ];
        DB::table('products')->insert($param);

    }
}
