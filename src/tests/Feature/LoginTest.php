<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;


class LoginTest extends TestCase
{
    use RefreshDatabase;

    // ログインテスト 正常系
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // リクエストを送信
        $response = $this->postJson('/api/login', $data);

        // ステータスコード確認
        $response->assertStatus(200)
            // json形式のmessage内容確認
            ->assertJson(['message' => 'ログイン成功',]);

        // ユーザーが認証されているか確認
        $this->assertAuthenticated();
    }


    // ▼▼▼▼▼ バリデーションエラーのテスト 異常系 ▼▼▼▼▼
    // メールアドレス未入力テスト
    public function test_login_validation_email_required()
    {
        $data = [
            'email' => '',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(422)
                // バリデーションエラーが'email'フィールドで発生していることを確認
                ->assertJsonValidationErrors(['email'])
                ->assertJson([
                    'message' => '入力に誤りがあります',
                    'errors' => [
                        'email' => ['メールアドレスを入力してください'],
                    ],
                ]);

        // 認証が失敗していることを確認
        $this->assertGuest();
    }

    // 無効なメールアドレス入力テスト
    public function test_login_validation_email_invalid()
    {
        $data = [
            'email' => 'invalid-email',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email'])
                ->assertJson([
                    'message' => '入力に誤りがあります',
                    'errors' => [
                        'email' => ['有効なメールアドレスを入力してください。'],
                    ],
                ]);

        $this->assertGuest();
    }

    // パスワード未入力テスト
    public function test_login_validation_password_required()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => '',
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password'])
                ->assertJson([
                    'message' => '入力に誤りがあります',
                    'errors' => [
                        'password' => ['パスワードを入力してください'],
                    ],
                ]);

        $this->assertGuest();
    }

    // 無効な認証情報テスト 未登録の情報でログインしようとする
    public function test_login_with_invalid_credentials()
    {
        // ユーザーを作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'), // 正しいパスワード
        ]);

        // 誤ったパスワードでログイン
        $data = [
            'email' => 'test@example.com',
            'password' => 'password987', // 間違ったパスワード
        ];

        $response = $this->postJson('/api/login', $data);

        // レスポンスの検証
        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'ログイン情報が登録されていません。',
                ]);

        $this->assertGuest();
    }
}
