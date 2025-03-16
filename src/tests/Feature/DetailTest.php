<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_必要な情報が表示される()
    {
        $this->seed();
        $user = User::factory()->create(); // ユーザー作成
        $user = User::find($user->id);
        $product = Product::factory()->create(['user_id' => $user->id,]);

        $likeCount = $product->likedUsers()->count();
        $commentCount = $product->comments()->count();

        $user->profile()->create([
            'image' => 'profile/default.jpg',
            'name' => 'テストユーザー',
            'post' => '123-4567',
            'address' => 'テスト住所',
        ]);

        $this->actingAs($user);

        $formData = ['content' => 'テストコメント',];

        $response = $this->post(route('store_comment', ['id' => $product->id]), $formData);
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'content' => 'テストコメント',
        ]);
        $response->assertRedirect(route('product.detail', ['id' => $product->id]));

        $detailResponse = $this->get(route('product.detail', ['id' => $product->id]));
        $detailResponse->assertStatus(200);

        $detailResponse->assertSee($product->image); //商品画像
        $detailResponse->assertSee($product->name); //商品名
        $detailResponse->assertSee($product->brand); //ブランド名
        $detailResponse->assertSee(number_format($product->price)); //価格
        $detailResponse->assertSee($likeCount); //いいね数
        $detailResponse->assertSee($commentCount); //コメント数
        $detailResponse->assertSee($product->description); //商品説明
        $detailResponse->assertSee($product->condition->content); //商品状態
        $detailResponse->assertSee('テストユーザー'); //コメントしたユーザー情報
        $detailResponse->assertSee('テストコメント'); //コメント内容
    }
}