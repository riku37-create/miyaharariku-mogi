<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログアウトができる()
    {
        $user = User::factory()->create()->first();

        $response = $this->actingAs($user)->post('/logout'); //ログインしてログアウト
        $response->assertRedirect('/'); //商品一覧画面に遷移
        $this->assertGuest(); //ログアウトしていることを確認
    }
}