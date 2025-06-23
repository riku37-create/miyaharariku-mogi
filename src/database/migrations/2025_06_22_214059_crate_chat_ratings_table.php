<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrateChatRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rater_id'); // 評価した人
            $table->unsignedBigInteger('ratee_id'); // 評価された人（users.id）
            $table->unsignedTinyInteger('rating'); // 1〜5など
            $table->timestamps();

            $table->foreign('rater_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ratee_id')->references('id')->on('users')->onDelete('cascade');
            // 同じ組み合わせで複数回評価できないようにする
            $table->unique(['rater_id', 'ratee_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
