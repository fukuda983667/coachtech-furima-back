<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;


class MyPageTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        // テスト開始前にマイグレーションをリフレッシュし、シーディングを実行 少し時間がかかる
        Artisan::call('migrate:refresh --seed --env=testing');
    }


    // ユーザデータ取得
    public function test_user_can_get_profile()
    {
        $testUser = User::where('email', 'test-taro@mail.com')->first();
        $this->actingAs($testUser);

        // 商品購入リクエスト送信
        $response = $this->getJson('/api/user');

        // ステータスコード200を確認
        $response->assertStatus(200);

        // 環境設定からベースURLを取得 config('app.url')は.env.testingのAPP_URLを参照している
        $baseUrl = config('app.url') . '/storage/user-icons/';

        // レスポンスのプロパティ名、データがSeederで設定したデータと一致するか確認
        $response->assertJson([
            'id' => 1,
            'name' => 'テスト太郎',
            'email' => 'test-taro@mail.com',
            'image_path' => $baseUrl . "default-user.jpeg",
            'postal_code' => '123-4567',
            'address' => '東京都',
            'building_name' => '某オフィスビル',
        ]);
    }


    // ユーザが購入した商品と出品した商品が正しく取得できるかテスト
    public function test_user_can_get_purchased_and_listed_items()
    {
        // 環境設定からベースURLを取得 config('app.url')は.env.testingのAPP_URLを参照している
        $baseUrl = config('app.url') . '/storage/items/';

        // テストユーザを取得
        $testUser = User::where('email', 'test-taro@mail.com')->first();
        $this->actingAs($testUser);

        // 商品情報を取得
        $response = $this->getJson('/api/user/my-page');
        $response->assertStatus(200);


        // レスポンスデータ(出品商品)を取得
        $listedItems = $response->json('listed_items');

        // 各 item レコードの user_id が testUser の id と一致することを確認(ユーザが出品した商品のみ取得できているか)
        foreach ($listedItems as $item) {
            // user_idプロパティが存在しているか
            $this->assertArrayHasKey('user_id', $item);
            $this->assertEquals($testUser->id, $item['user_id']);
            $this->assertArrayHasKey('is_sold', $item);
            $this->assertEquals($item->purchase()->exists(), $item['is_sold']);
        }


        // レスポンスデータを検証
        collect($response->json('listed_items'))->each(function ($listedItem) use ($baseUrl) {
            $this->assertArrayHasKey('id', $responseItem);
            $this->assertEquals($testUser->id, $listedItem['user_id']);
            $this->assertArrayHasKey('is_sold', $listedItem);
            $this->assertEquals(Item::find($item['id'])->purchase()->exists(), $item['is_sold']);

            $this->assertArrayHasKey('image_path', $listedItem);

            // image_pathが加工されていることを確認
            $expectedImagePath = $baseUrl . $dbItem->image_path;
            $this->assertEquals($expectedImagePath, $responseItem['image_path']);
        });


        // レスポンスデータ(購入商品)を取得
        $purchasedItems = $response->json('purchased_items');


    }
}
