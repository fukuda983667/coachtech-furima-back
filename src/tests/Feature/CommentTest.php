<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;


class CommentTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        // テスト開始前にマイグレーションをリフレッシュし、シーディングを実行 少し時間がかかる
        Artisan::call('migrate:refresh --seed --env=testing');
    }


    // コメント送信テスト 正常系
    public function test_user_can_comment_on_item()
    {
        $testUser = User::where('email', 'test-taro@mail.com')->first();
        $this->actingAs($testUser);

        // コメント送信前のコメント件数を確認
        $responseBefore = $this->getJson('/api/comments/1');
        $responseBefore->assertStatus(200);
        $initialCount = $responseBefore->json('comment_count');

        $data = [
            'item_id' => 1,
            'comment' => 'テストコメント',
        ];

        // コメント送信リクエスト
        $response = $this->postJson('/api/comments', $data);

        // ステータスコード201を確認
        $response->assertStatus(201);
        $response->assertJson(['message' => 'コメントを追加しました']);

        // コメント送信後のコメント件数を確認
        $responseAfter = $this->getJson('/api/comments/1');
        $responseAfter->assertStatus(200);
        $updateCount = $responseAfter->json('comment_count');

        // コメント送信後のコメント件数が+1されているか確認
        $responseAfter->assertJson(['comment_count' => $initialCount + 1]);

        // データベースにコメントが保存されていることを確認
        $this->assertDatabaseHas('comments', [
            'user_id' => 1,
            'item_id' => 1,
            'comment' => 'テストコメント',
        ]);
    }

    // ▼▼▼▼▼ コメント送信テスト 異常系 ▼▼▼▼▼
    // 未認証ユーザはコメントできない
    public function test_guset_user_can_not_comment_on_item()
    {
        $data = [
            'item_id' => 1,
            'comment' => 'テストコメント',
        ];

        // コメント送信リクエスト
        $response = $this->postJson('/api/comments', $data);

        $response->assertStatus(401);

        // ミドルウェアではじかれたときのメッセージ内容確認
        $response->assertJson(['message' => 'Unauthenticated.']);
    }


    // バリデーションエラー コメント未入力
    public function test_comment_validation_comment_required()
    {
        $testUser = User::where('email', 'test-taro@mail.com')->first();
        $this->actingAs($testUser);

        $data = [
            'item_id' => 1,
            'comment' => '',
        ];

        // コメント送信リクエスト
        $response = $this->postJson('/api/comments', $data);

        $response->assertStatus(422)
                // バリデーションエラーが'email'フィールドで発生していることを確認
                ->assertJsonValidationErrors(['comment'])
                ->assertJson([
                    'message' => '入力に誤りがあります',
                    'errors' => [
                        'comment' => ['コメントを入力してください'],
                    ],
                ]);
    }


    // バリデーションエラー 文字数制限(255文字以下)
    public function test_comment_validation_comment_max()
    {
        $testUser = User::where('email', 'test-taro@mail.com')->first();
        $this->actingAs($testUser);

        $data = [
            'item_id' => 1,
            'comment' => str_repeat('あ', 256), // 256文字のダミーデータ
        ];

        // コメント送信リクエスト
        $response = $this->postJson('/api/comments', $data);

        $response->assertStatus(422)
                // バリデーションエラーが'email'フィールドで発生していることを確認
                ->assertJsonValidationErrors(['comment'])
                ->assertJson([
                    'message' => '入力に誤りがあります',
                    'errors' => [
                        'comment' => ['コメントは255文字以下で入力してください'],
                    ],
                ]);
    }
}
