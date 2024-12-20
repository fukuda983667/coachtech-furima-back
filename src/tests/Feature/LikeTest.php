<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;


class LikeTest extends TestCase
{
    use RefreshDatabase;

    // 特定のitemのお気に入り状況を取得できるかテスト
    public function test_it_returns_like_count_for_item()
    {
        // アイテム作成
        $item = Item::factory()->create();

        // テストユーザ作成してログイン
        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        // リクエスト送信
        $response = $this->getJson("/api/likes/{$item->id}");
        $response->assertStatus(200);

        // お気に入りしていない状態
        $response->assertJson(['is_liked' => false]);


        // お気に入り登録したことにする
        Like::factory()->create([
            'user_id' => $testUser->id,
            'item_id' => $item->id,
        ]);

        // リクエスト送信
        $response = $this->getJson("/api/likes/{$item->id}");
        $response->assertStatus(200);

        // お気に入り登録した状態
        $response->assertJson(['is_liked' => true]);
    }


    // お気に入り登録テスト
    public function test_user_can_like_an_item()
    {
        $item = Item::factory()->create();

        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        Like::factory()->count(3)->create([
            'item_id' => $item->id,
        ]);

        // お気に入り登録前のlike_countを取得しておく
        $responseBefore = $this->getJson("/api/likes/{$item->id}");
        $responseBefore->assertStatus(200);
        $initialCount = $responseBefore->json('like_count');

        // お気に入り登録
        $response = $this->postJson('/api/likes', ['item_id' => $item->id]);
        $response->assertStatus(201);

        // お気に入り登録後のお気に入り数が+1されているか確認
        $response->assertJson(['like_count' => $initialCount + 1]);
        // is_likedがtrueか確認
        $response->assertJson(['is_liked' => true]);
    }


    // お気に入り解除テスト
    public function test_user_can_unlike_an_item()
    {
        $item = Item::factory()->create();

        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        Like::factory()->count(3)->create([
            'item_id' => $item->id,
        ]);

        // ユーザは$itemをお気に入りしている状態にしておく
        Like::factory()->create([
            'user_id' => $testUser->id,
            'item_id' => $item->id,
        ]);

        // お気に入り解除前のlike_countを取得しておく
        $responseBefore = $this->getJson("/api/likes/{$item->id}");
        $responseBefore->assertStatus(200);
        $initialCount = $responseBefore->json('like_count');

        // お気に入り解除
        $response = $this->postJson('/api/likes', ['item_id' => $item->id]);
        $response->assertStatus(200);

        // お気に入り解除後のお気に入り数が-1されているか確認
        $response->assertJson(['like_count' => $initialCount - 1]);
        // is_likedがfalseか確認
        $response->assertJson(['is_liked' => false]);
    }


    // ゲストユーザはお気に入り登録できない
    public function test_guest_user_can_not_like_an_item()
    {
        $item = Item::factory()->create();

        // ログインせずにお気に入り登録リクエスト
        $response = $this->postJson('/api/likes', ['item_id' => $item->id]);

        $response->assertStatus(401);

        // ミドルウェアではじかれたときのメッセージ内容確認
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
