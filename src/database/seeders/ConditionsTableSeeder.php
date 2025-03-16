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
        $conditions = [
            ['id' => 1, 'content' => '良好'],
            ['id' => 2, 'content' => '目立った傷や汚れなし'],
            ['id' => 3, 'content' => '傷ありやや傷や汚れあり'],
            ['id' => 4, 'content' => '状態が悪い']
        ];
        DB::table('conditions')->insert($conditions);
    }
}
