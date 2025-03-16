<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Product;
use App\Models\Comment;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_全商品を取得できる()
    {
        $this->seed();
        $products = Product::all();

        $response = $this->get('/');

        // ビューに商品名が渡されていることを確認
        foreach ($products as $product) {
            $response->assertSee($product->name);
        }
    }

    public function test_購入済み商品は「SOLD」と表示される()
    {
        $this->seed();
        $user = User::factory()->create(); // ユーザーを作成
        $product = Product::factory()->create(); //商品を作成

        Order::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'method'     => 'カード払い',
            'post'       => '123-4567',
            'address'    => '東京都新宿区○○町',
            'building'   => 'テストビル101号',
        ]);

        $response = $this->get('/');
        $response->assertSee('SOLD');
    }

    public function test_自分が出品した商品は表示されない()
    {
        $this->seed();
        $users = User::factory()->create(); // ユーザー作成
        $user = User::find($users->id);
        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]); // 商品作成

        $response = $this->actingAs($user)->get('/'); // 商品一覧画面を開く
        $response->assertDontSee($product->name); // 自分が出品した商品は表示されない
    }

    public function test_いいねした商品だけが表示される()
    {
        $this->seed();
        $users = User::factory()->create(); // ユーザー作成
        $user = User::find($users->id);
        $product = Product::factory()->create(); //商品作成

        $user->likedProducts()->attach($product); //商品をいいねする

        $response = $this->actingAs($user)->get('/?page=mylist'); // マイリストページを開く
        $response->assertSee($product->name); // 「いいね」した商品が表示されていることを確認
    }

    public function test_マイリスト_購入済み商品は「SOLD」と表示される()
    {
        $this->seed();
        $users = User::factory()->create(); // ユーザー作成
        $user = User::find($users->id);
        $product = Product::factory()->create(); //商品作成

        $user->likedProducts()->attach($product->id); //商品をいいねする

        Order::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'method'     => 'カード払い',
            'post'       => '123-4567',
            'address'    => '東京都新宿区○○町',
            'building'   => 'テストビル101号',
        ]);

        $response = $this->actingAs($user)->get('/?page=mylist'); // マイリストページを開く
        $response->assertSee('SOLD');
    }

    public function test_マイリスト_未認証の場合はログイン画面に遷移()
    {
        $this->seed();
        $response = $this->get('/?page=mylist');
        $response->assertRedirect(route('login'));
    }

    public function test「商品名」で部分一致検索ができる()
    {
        $this->seed();
        $product = Product::where('name', '腕時計')->first();

        $response = $this->get(route('product.search', ['input' => '腕',]));
        $response->assertSee($product->name);
    }

    public function test_検索状態がマイリストでも保持されている()
    {
        $this->seed();
        $user = User::factory()->create(); // ユーザー作成
        $user = User::find($user->id);
        $product = Product::where('name', '腕時計')->first();

        $user->likedProducts()->attach($product); //商品をいいねする

        $this->actingAs($user)->get('/?page=mylist');
        $response = $this->get(route('product.search'), ['input' => '腕',]);
        $response->assertSee($product->name); // 「腕時計」が含まれる商品は表示される
    }
}