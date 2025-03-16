<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_いいねアイコンを押下することによって、いいねした商品として登録することができる()
    {
        $this->seed();
        $user = User::factory()->create(); // ユーザー作成
        $user = User::find($user->id);
        $product = Product::factory()->create(); //商品作成

        $this->actingAs($user)->post(route('product.like', ['id' => $product->id]));;

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]); // データベースにレコードが追加されたか確認
    }

    public function test_追加済みのアイコンは色が変化する()
    {
        $this->seed();
        $user = User::factory()->create(); // ユーザー作成
        $user = User::find($user->id);
        $product = Product::factory()->create(); //商品作成

        $this->actingAs($user)->post(route('product.like', ['id' => $product->id]));;

        $response = $this->actingAs($user)->get(route('product.detail', ['id' => $product->id]));
        $response->assertSee('<i class="icon fa-regular fa-star" style="color: #ff0000;"></i>', false); // 5. HTML に「いいね済み（赤色のアイコン）」が含まれていることを確認
    }

    public function test_再度いいねアイコンを押下することによって、いいねを解除することができる()
    {
        $this->seed();
        $user = User::factory()->create(); // ユーザー作成
        $user = User::find($user->id);
        $product = Product::factory()->create(); //商品作成
        $this->actingAs($user);
        $this->post(route('product.like', ['id' => $product->id]));
        $this->post(route('product.unlike', ['id' => $product->id]));
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }
}