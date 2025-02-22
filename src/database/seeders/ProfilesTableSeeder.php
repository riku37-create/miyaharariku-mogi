<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilesTableSeeder extends Seeder
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
            'name' => 'テスト君',
            'post' => '123-4567',
            'image' => 'profile-img/person.png',
            'address' => '東京都',
            'building' => 'レオパレス',
        ];
        DB::table('profiles')->insert($param);
    }
}
