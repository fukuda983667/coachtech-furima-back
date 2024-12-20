<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\Category;
use App\Models\ItemCondition;
use App\Models\Item;
use App\Models\Like;
use App\Models\Comment;


class ItemDetailTest extends TestCase
{
    use RefreshDatabase;


    // 商品詳細情報を取得できる。→フロントエンドで商品詳細画面を表示するために必要なデータを取得できる。
    public function test_it_returns_correct_item_details()
    {
        // conditionレコード作成
        $conditions = ItemCondition::factory()->createMany([
            ['name' => '良好', ],
            ['name' => '目立った傷や汚れなし', ],
            ['name' => 'やや傷や汚れあり', ],
            ['name' => '状態が悪い', ],
        ]);

        // テストユーザ作成
        $testUser = User::factory()->create();

        // カテゴリ作成
        $categories = Category::factory()->count(2)->sequence(
            ['name' => 'メンズ'],
            ['name' => '時計'],
        )->create();

        // 商品作成
        $item = Item::factory()
            ->for($testUser, 'user')
            ->for($conditions->first(), 'condition')
            ->hasAttached($categories)
            ->create([
                'name' => '腕時計',
                'brand' => '愛知時計',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'image_path' => '1.jpg',
            ]);

        // APIリクエスト
        $response = $this->getJson("/api/items/{$item->id}");
        $response->assertStatus(200);

        // ログにレスポンス表示
        fwrite(STDOUT, "\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");

        // 環境設定からベースURLを取得
        $baseUrl = config('app.url') . '/storage/items/';

        // レスポンスのプロパティ名、データが一致するか確認
        $response->assertJson([
            'item' => [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => $item->price,
                'user_id' => $testUser->id,
                'image_path' => $baseUrl . $item->image_path,
                'condition_id' => $conditions->first()->id,
                'brand' => $item->brand,
                'categories' => $categories->map(fn($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                ])->toArray(),
                'condition' => [
                    'id' => $conditions->first()->id,
                    'name' => $conditions->first()->name,
                ],
            ],
        ]);
    }


    // 特定のitemのお気に入り数を取得できるかテスト
    public function test_it_returns_like_count_for_item()
    {
        // 商品作成
        $item = Item::factory()->create();

        // likeレコード3つ作成
        $likeCount = 3;
        Like::factory()->count($likeCount)->create([
            'item_id' => $item->id,
        ]);

        $response = $this->getJson("/api/likes/{$item->id}");
        $response->assertStatus(200);

        // お気に入り数を確認
        $response->assertJson([
            'like_count' => $likeCount,
        ]);
    }


    // 特定のitemに紐づくコメントが取得できるかテスト
    public function test_it_returns_comments_for_specific_item()
    {
        // 商品作成
        $item = Item::factory()->create();

        // ユーザー作成
        [$user1, $user2] = User::factory()->count(2)->create();

        // commentレコード2つ作成
        $commentCount = 2;
        [$comment1, $comment2] = Comment::factory()->count($commentCount)->sequence(
            ['user_id' => $user1->id, 'item_id' => $item->id,],
            ['user_id' => $user2->id, 'item_id' => $item->id,],
        )->create();

        $response = $this->getJson("/api/comments/{$item->id}");
        $response->assertStatus(200);

        // 環境設定からユーザーアイコンのベースURLを取得
        $baseUrl = config('app.url') . '/storage/user-icons/';

        // レスポンスのプロパティ名、データが一致するか確認
        $response->assertJson([
            'comments' => [
                [
                    'user_id' => $user1->id,
                    'item_id' => $item->id,
                    'comment' => $comment1->comment,
                    'user' => [
                        'id' => $user1->id,
                        'name' => $user1->name,
                        'image_path' => $baseUrl . $user1->image_path,
                    ],
                ],
                [
                    'user_id' => $user2->id,
                    'item_id' => $item->id,
                    'comment' => $comment2->comment,
                    'user' => [
                        'id' => $user2->id,
                        'name' => $user2->name,
                        'image_path' => $baseUrl . $user2->image_path,
                    ],
                ],
            ],
            'comment_count' => $commentCount,
        ]);
    }
}
