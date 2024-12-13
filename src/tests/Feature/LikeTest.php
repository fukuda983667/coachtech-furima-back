<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;


class LikeTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        // テスト開始前にマイグレーションをリフレッシュし、シーディングを実行 少し時間がかかる
        Artisan::call('migrate:refresh --seed --env=testing');
    }


    // 特定のitemのお気に入り状況を取得できるかテスト
    public function test_it_returns_like_count_for_item()
    {
        // テストユーザ情報
        $testUser = User::where('email', 'test-taro@mail.com')->first();
        $this->assertNotNull($testUser, "テストユーザ 'test-taro@mail.com' が存在しません。");

        // ログインしてリクエスト送信
        $this->actingAs($testUser);
        $response = $this->getJson('/api/likes/1');

        // ステータスコード200を確認
        $response->assertStatus(200);


    }
}
