<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    // ユーザ登録テスト 正常系
    public function test_user_can_register()
    {
        $data = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // リクエストを送信
        $response = $this->postJson('/api/register', $data);

        // ステータスコードが指定の201であるかを確認
        $response->assertStatus(201)
                // 'ユーザー登録成功'の文字がmessageに格納されているかを確認
                ->assertJson(['message' => 'ユーザー登録成功']);

        // データベースの確認
        // 'email' => $data['name']とテストコード自体を間違って記述した場合、
        // 'email' => $data['email']と自動で修正してテストを通過するらしい
        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // パスワードのハッシュ化を確認
        $user = User::where('email', $data['email'])->first();
        $this->assertTrue(Hash::check($data['password'], $user->password));
    }


    // ▼▼▼▼▼ バリデーションエラーのテスト 異常系 ▼▼▼▼▼
    // 名前未入力テスト
    public function test_user_registration_validation_name_required()
    {
        $data = [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
                // バリデーションエラーが'name'フィールドで発生していることを確認
                ->assertJsonValidationErrors(['name'])
                // messageやerrorsに含まれる文字が一致することを確認
                ->assertJson([
                    'message' => '入力に誤りがあります',
                    'errors' => [
                        'name' => ['お名前を入力してください'],
                    ],
                ]);
    }

    // メールアドレス未入力テスト
    public function test_user_registration_validation_email_required()
    {
        $data = [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email'])
                ->assertJson([
                    'message' => '入力に誤りがあります',
                    'errors' => [
                        'email' => ['メールアドレスを入力してください'],
                    ],
                ]);
    }

    // 無効なメールドレス入力テスト @がない
    public function test_user_registration_validation_email_invalid()
    {
        $data = [
            'name' => 'テストユーザー',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email'])
                ->assertJson([
                    'message' => '入力に誤りがあります',
                    'errors' => [
                        'email' => ['有効なメールアドレスを入力してください。'],
                    ],
                ]);
    }

    // メールアドレスの再使用不可テスト
    public function test_user_registration_validation_email_unique()
    {
        // 既存のユーザーを作成
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $data = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com', // 重複メールアドレス
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email'])
                ->assertJson([
                    'message' => '入力に誤りがあります',
                    'errors' => [
                        'email' => ['このメールアドレスはすでに使用されています。'],
                    ],
                ]);
    }

    // パスワード文字数制限テスト // 8文字未満
    public function test_user_registration_validation_password_min()
    {
        $data = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password'])
                ->assertJson([
                    'message' => '入力に誤りがあります',
                    'errors' => [
                        'password' => ['パスワードは8文字以上で入力してください'],
                    ],
                ]);
    }

    // パスワード不一致テスト
    public function test_user_registration_validation_password_confirmation()
    {
        $data = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password987', // 確認パスワードが一致しない
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password'])
                ->assertJson([
                    'message' => '入力に誤りがあります',
                    'errors' => [
                        'password' => ['パスワードと一致しません'],
                    ],
                ]);
    }
}
