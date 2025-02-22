<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'content' => '良好'
        ];
        DB::table('conditions')->insert($param);
        $param = [
            'content' => '目立った傷や汚れなし'
        ];
        DB::table('conditions')->insert($param);
        $param = [
            'content' => 'やや傷や汚れあり'
        ];
        DB::table('conditions')->insert($param);
        $param = [
            'content' => '状態が悪い'
        ];
        DB::table('conditions')->insert($param);
    }
}
