<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_名前が空の場合()
    {
        $response = $this->post('/register', ['name' => '']);
        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    public function test_メールが空の場合()
    {
        $response = $this->post('/register', ['email' => '']);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_メールアドレスが無効な場合()
    {
        $response = $this->post('/register', ['email' => 'invalid-email']);
        $response->assertSessionHasErrors(['email' => '有効なメールアドレスを入力してください']);
    }

    public function test_メールアドレスがすでに登録されている場合()
    {
        User::factory()->create(['email' => 'test@example.com']);
        $response = $this->post('/register', ['email' => 'test@example.com']);
        $response->assertSessionHasErrors(['email' => 'そのメールアドレスは使用されています']);
    }

    public function test_パスワードが空の場合()
    {
        $response = $this->post('/register', ['password' => '']);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_パスワードが7文字以下の場合()
    {
        $response = $this->post('/register', ['password' => 'pass']);
        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    public function test_パスワードが一致しない場合()
    {
        $response = $this->post('/register',[
            'password' => 'password',
            'password_confirmation' => 'differentPassword',
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワード確認が一致しません']);
    }

    public function test_登録画面から認証誘導画面へ遷移()
    {
        $response = $this->post('/register', [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertRedirect('/email/verify');
    }
}