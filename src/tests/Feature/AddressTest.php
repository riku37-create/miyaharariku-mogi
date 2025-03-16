<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

        public function test_住所変更後に購入画面に正しく反映される()
    {
        $this->seed();
        $user = User::factory()->create();
        $user = User::find($user->id);
        $product = Product::factory()->create();
        $this->actingAs($user);

        // 住所更新
        $addressData = [
            'post' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101'
        ];
        $this->post(route('address.update', ['id' => 1]), $addressData);

        // 購入画面を開く
        $response = $this->get(route('product.purchase', ['id' => 1]));

        // セッションに正しく保存されているか確認
        $response->assertSessionHas('temp_address', $addressData);

        // 画面に住所が表示されているか確認
        $response->assertSee('〒123-4567');
        $response->assertSee('東京都新宿区');
        $response->assertSee('テストビル101');
    }
}