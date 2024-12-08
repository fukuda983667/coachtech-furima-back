<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;


class LogoutTest extends TestCase
{
    use RefreshDatabase;

    // ログアウトテスト 正常系
    public function test_user_can_logout()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // ログイン
        $this->actingAs($user);

        // ログアウトリクエストを送信
        $response = $this->postJson('/api/logout');

        // ステータスコード確認
        $response->assertStatus(200)
                // メッセージ確認
                ->assertJson(['message' => 'ログアウト成功']);

        // ユーザーがログアウトしているか確認
        $this->assertGuest();
    }


    // ログインしていない状態でログアウトAPIを呼び出す 異常系
    public function test_user_cannot_logout_without_login()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401)
                ->assertJson(['message' => 'ログインしていません']);
    }
}
