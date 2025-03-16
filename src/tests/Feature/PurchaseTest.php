<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_「購入する」ボタンを押下すると購入が完了する()
    {
        $this->seed();
        $user = User::factory()->has(Profile::factory())->create(); // ユーザー作成
        $user = User::find($user->id);
        $product = Product::factory()->create(); //商品作成

        $this->actingAs($user);

        // Checkoutリクエスト送信
        session([
            'purchase_product_id' => $product->id,
            'purchase_method' => 'カード払い',
            'purchase_address' => [
                'post' => '123-4567',
                'address' => '東京都渋谷区',
                'building' => 'テストビル'
            ]
        ]);

        $response = $this->get(route('checkout.success'));
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'method' => 'カード払い',
        ]);
        $response->assertViewIs('payment.success'); //購入成功画面を表示する

        $response = $this->get(route('product.index'));
        $response->assertSee('SOLD');

        $response = $this->get('mypage/?page=buy');
        $response->assertSee($product->name);
    }
}