<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\Category;


class ItemCreateTest extends TestCase
{
    use RefreshDatabase;


    // 商品出品テスト
    public function test_user_can_create_an_item()
    {
        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        // conditionレコード作成
        $conditions = ItemCondition::factory()->createMany([
            ['id' => 1, 'name' => '良好', ],
            ['id' => 2, 'name' => '目立った傷や汚れなし', ],
            ['id' => 3, 'name' => 'やや傷や汚れあり', ],
            ['id' => 4, 'name' => '状態が悪い', ],
        ]);

        // categoryレコード作成
        $categories = Category::factory()->createMany([
            ['id' => 1, 'name' => '腕時計'],
            ['id' => 2, 'name' => 'メンズ'],
        ]);

        // 画像ファイル作成
        $imageFile = UploadedFile::fake()->image('item.jpeg');

        // 更新用のデータを準備
        $data = [
            'name' => '腕時計',
            'condition_id' => 1,
            'brand' => '愛知時計',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'price' => 15000,
            'categories' => [1, 2],  // 商品のカテゴリを設定（腕時計とメンズ）
            'image_path' => $imageFile,
        ];

        // プロファイル更新リクエストを送信
        $response = $this->postJson('/api/items', $data);

        $response->assertStatus(201);
        $response->assertJson(['message' => '商品作成処理に成功しました']);


        // レスポンスからitemオブジェクトを取得
        $responseData = $response->json();
        $item = $responseData['item'];

        // 画像ファイル名が更新されていることを確認(コントローラーで画像ファイル名が一意になるように加工されるため)
        $this->assertNotEquals($item['image_path'], 'item.jpeg');


        // データベースを確認
        $this->assertDatabaseHas('items', [
            'id' => $testUser->id,
            'name' => $data['name'],
            'brand' => $data['brand'],
            'price' => $data['price'],
            'condition_id' => $data['condition_id'],
            'description' => $data['description'],
            'image_path' => $item['image_path'],
        ]);

        // 中間テーブルにデータが存在することを確認（カテゴリの紐付け）
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item['id'], // アイテムIDを確認
            'category_id' => 1,       // カテゴリ「腕時計」
        ]);

        $this->assertDatabaseHas('category_item', [
            'item_id' => $item['id'], // アイテムIDを確認
            'category_id' => 2,       // カテゴリ「メンズ」
        ]);

        // 画像が正しいディレクトリに保存されていることを確認
        Storage::disk('public')->assertExists('items/' . $item['image_path']);

        // 画像の削除
        Storage::disk('public')->delete('items/' . $item['image_path']);
    }


    // カテゴリ取得テスト
    public function test_user_can_get_categories()
    {
        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        // categoryレコード作成
        $categories = Category::factory()->createMany([
            ['id' => 1, 'name' => '腕時計'],
            ['id' => 2, 'name' => 'メンズ'],
        ]);

        $response = $this->getJson('/api/categories');
        $response->assertStatus(200);

        // レスポンス内にカテゴリーのデータが含まれていることを確認
        $response->assertJson([
            'categories' => [
                ['id' => 1, 'name' => '腕時計'],
                ['id' => 2, 'name' => 'メンズ'],
            ]
        ]);
    }


    // コンディション取得テスト
    public function test_user_can_get_conditions()
    {
        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        // conditionレコード作成
        $conditions = ItemCondition::factory()->createMany([
            ['id' => 1, 'name' => '良好', ],
            ['id' => 2, 'name' => '目立った傷や汚れなし', ],
            ['id' => 3, 'name' => 'やや傷や汚れあり', ],
            ['id' => 4, 'name' => '状態が悪い', ],
        ]);

        $response = $this->getJson('/api/conditions');
        $response->assertStatus(200);

        // レスポンス内にコンディションのデータが含まれていることを確認
        $response->assertJson([
            'conditions' => [
                ['id' => 1, 'name' => '良好'],
                ['id' => 2, 'name' => '目立った傷や汚れなし'],
                ['id' => 3, 'name' => 'やや傷や汚れあり'],
                ['id' => 4, 'name' => '状態が悪い'],
            ]
        ]);
    }
}
