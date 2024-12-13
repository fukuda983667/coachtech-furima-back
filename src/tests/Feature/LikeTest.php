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
        // Seederで用意したテストユーザをログイン
        $testUser = User::where('email', 'test-taro@mail.com')->first();
        $this->actingAs($testUser);

        // リクエスト送信
        $response = $this->getJson('/api/likes/7');
        // ステータスコード200を確認
        $response->assertStatus(200);

        // プロパティ名(is_liked)と値(true)を確認 Seederでuser_id:1、item_id:7のlikeレコード作成している。
        $response->assertJson(['is_liked' => true]);
    }


    // お気に入り登録テスト
    public function test_user_can_like_an_item()
    {
        $testUser = User::where('email', 'test-taro@mail.com')->first();
        $this->actingAs($testUser);

        // お気に入り登録前のlike_countを取得しておく
        $responseBefore = $this->getJson('/api/likes/6');
        $responseBefore->assertStatus(200);
        $initialCount = $responseBefore->json('like_count');

        // お気に入り登録
        $response = $this->postJson('/api/likes', ['item_id' => 6]);
        $response->assertStatus(201);

        // お気に入り登録後のお気に入り数が+1されているか確認
        $response->assertJson(['like_count' => $initialCount + 1]);
        // is_likedがtrueか確認
        $response->assertJson(['is_liked' => true]);
    }


    // お気に入り解除テスト
    public function test_user_can_unlike_an_item()
    {
        $testUser = User::where('email', 'test-taro@mail.com')->first();
        $this->actingAs($testUser);

        // お気に入り解除前のlike_countを取得しておく
        $responseBefore = $this->getJson('/api/likes/7');
        $responseBefore->assertStatus(200);
        $initialCount = $responseBefore->json('like_count');

        // お気に入り解除
        $response = $this->postJson('/api/likes', ['item_id' => 7]);
        $response->assertStatus(200);

        // お気に入り解除後のお気に入り数が-1されているか確認
        $response->assertJson(['like_count' => $initialCount - 1]);
        // is_likedがfalseか確認
        $response->assertJson(['is_liked' => false]);
    }
}
