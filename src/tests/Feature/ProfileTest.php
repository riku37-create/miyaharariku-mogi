<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_必要な情報が取得できる()
    {
        $this->seed();
        $user = User::factory()->has(Profile::factory())->create(); // ユーザー作成
        $user = User::find($user->id);

        $this->actingAs($user);

        $response = $this->get(route('profile.index'));
        $response->assertSee($user->profile->image); //プロフィール画像
        $response->assertSee($user->profile->name); //ユーザー名

        $response = $this->get('mypage/?page=sell'); //購入した商品一覧
        $response->assertStatus(200);

        $response = $this->get('mypage/?page=buy'); //出品した商品一覧
        $response->assertStatus(200);
    }

    public function test_変更項目が初期値として過去設定されていること()
    {
        $this->seed();
        $user = User::factory()->has(Profile::factory())->create(); // ユーザー作成
        $user = User::find($user->id);

        $this->actingAs($user);

        $response = $this->get('mypage/profile/edit');
        $response->assertSee($user->profile->image); //プロフィール画像
        $response->assertSee($user->profile->name); //ユーザー名
        $response->assertSee($user->profile->post); //郵便番号
        $response->assertSee($user->profile->address); //住所
        $response->assertSee($user->profile->image); //建物名
    }
}