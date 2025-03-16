<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_メールが空の場合()
    {
        $response = $this->post('/login', ['email' => '']);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_パスワードが空の場合()
    {
        $response = $this->post('/login', ['password' => '']);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_入力情報が間違っている場合()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    public function test_正しい情報が入力された場合、ログイン処理が実行される()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $response->assertRedirect('/'); //商品一覧画面へ遷移
        $this->assertAuthenticatedAs($user); //ログイン状態
    }
}