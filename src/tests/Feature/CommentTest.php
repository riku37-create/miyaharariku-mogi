<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログイン済みのユーザーはコメントを送信できる()
    {
        $this->seed();
        $user = User::factory()->create(); // ユーザー作成
        $user = User::find($user->id);
        $product = Product::factory()->create(); //商品作成

        $this->actingAs($user);

        $commentData = [
            'content' => 'テストコメント',
        ];

        $this->post(route('store_comment', ['id' => $product->id]), $commentData);
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'content' => 'テストコメント',
        ]);
    }

    public function test_ログイン前のユーザーはコメントを送信できない()
    {
        $this->seed();
        $product = Product::factory()->create(); //商品作成
        $commentData = [
            'content' => 'テストコメント',
        ];

        $response = $this->post(route('store_comment', ['id' => $product->id]), $commentData);
        $response->assertRedirect(route('login')); //ログインページにリダイレクトされることを確認
        $this->assertDatabaseMissing('comments', [
            'content' => 'テストコメント',
        ]); //データベースにコメントが保存されていないことを確認
    }

    public function test_コメントが入力されていない場合、バリデーションメッセージが表示される()
    {
        $this->seed();
        $user = User::factory()->create(); // ユーザー作成
        $user = User::find($user->id);
        $product = Product::factory()->create(); //商品作成

        $this->actingAs($user);

        $response = $this->post(route('store_comment', ['id' => $product->id]), ['content' => '',]);
        $response->assertSessionHasErrors(['content' => 'コメントを入力してください']);
    }

    public function test_コメントが255字以上の場合、バリデーションメッセージが表示される()
    {
        $this->seed();
        $user = User::factory()->create(); // ユーザー作成
        $user = User::find($user->id);
        $product = Product::factory()->create(); //商品作成

        $this->actingAs($user);

        $longComment = str_repeat('あ', 256); //256文字以上のコメントを送信

        $response = $this->post(route('store_comment', ['id' => $product->id]), [
            'content' => $longComment,
        ]);
        $response->assertSessionHasErrors(['content' => 'コメントは257文字以内で入力してください']);;
    }
}