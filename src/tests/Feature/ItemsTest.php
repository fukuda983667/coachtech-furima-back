<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;


class ItemsTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        // テスト開始前にマイグレーションをリフレッシュし、シーディングを実行 少し時間がかかる
        Artisan::call('migrate:refresh --seed --env=testing');
    }


    // ゲストユーザは全itemを取得できる。→フロントエンドで一覧画面を表示するために必要なデータを取得できる。
    public function test_guest_can_get_all_items()
    {
        // 未認証ユーザでAPIリクエストを送信
        $response = $this->getJson('/api/items');

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // itemsテーブルのレコード数を取得
        $expectedCount = Item::count();

        // ログ出力
        fwrite(STDOUT, "itemsテーブルのレコード数: $expectedCount\n");
        fwrite(STDOUT, "レスポンスのアイテム数: " . count($response->json('items')) . "\n");

        // レスポンスで取得したitemsの数を確認
        $response->assertJsonCount($expectedCount, 'items');


        // 商品一覧の表示には最低限'id', 'name', 'image_path'が必要
        collect($response->json('items'))->each(function ($item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('name', $item);
            $this->assertArrayHasKey('image_path', $item);
        });
    }


    // 購入済み商品はisSoldプロパティがtrueである。→フロントエンドで購入済みか否かを判断するためのプロパティがある。
    public function test_items_have_correct_isSold_property()
    {
        // 未認証ユーザでAPIリクエストを送信(全item取得)
        $response = $this->getJson('/api/items');
        $response->assertStatus(200);

        // 取得したitemsを確認
        fwrite(STDOUT, "\n" . "購入済み商品のisSoldプロパティがtrueであることを確認" . "\n");
        collect($response->json('items'))->each(function ($item) {

            // isSoldプロパティが存在していることを確認
            $this->assertArrayHasKey('isSold', $item);

            // Itemモデルのpurchase()リレーションを利用してitemに紐づく購入レコードの存在をboolean型で取得
            $expectedIsSold = Item::find($item['id'])->purchase()->exists();

            // ログ出力
            fwrite(STDOUT, "Item ID: {$item['id']} | isSoldプロパティ "
                . var_export($item['isSold'], true)
                . " | purchaseレコードの有無: "
                . var_export($expectedIsSold, true)
                . "\n"
            );

            // isSoldの値が正しいことを確認
            $this->assertEquals($expectedIsSold, $item['isSold'], "Item ID: {$item['id']} のisSoldが正しくありません。");
        });
    }


    // 自分が出品したitemを取得できない。→フロントエンドで一覧に表示されない。
    public function test_logged_in_user_cannot_get_their_own_items()
    {
        // テストユーザ情報
        $testUser = User::where('email', 'test-taro@mail.com')->first();
        $this->assertNotNull($testUser, "テストユーザ 'test-taro@mail.com' が存在しません。");

        // ログインしてリクエスト送信
        $this->actingAs($testUser);
        $response = $this->getJson('/api/items');
        $response->assertStatus(200);

        // レスポンス内の全itemsを取得
        $items = $response->json('items');

        // 自分が出品した商品のIDを取得
        $userItemIds = Item::where('user_id', $testUser->id)->pluck('id')->toArray();
        // レスポンスに含まれる商品IDを抽出
        $responseItemIds = collect($items)->pluck('id')->toArray();

        // ログ出力
        fwrite(STDOUT, "自分が出品した商品ID: " . implode(', ', $userItemIds) . "\n");
        fwrite(STDOUT, "レスポンスに含まれる商品ID: " . implode(', ', $responseItemIds) . "\n");

        // 自分が出品した商品がレスポンスに含まれていないことを確認
        foreach ($items as $item) {
            $this->assertNotContains($item['id'], $userItemIds, "自分が出品した商品が含まれています。Item ID: {$item['id']}");
        }
    }


    // ゲストユーザはisLikedプロパティを持たない = お気に入り機能がない
    public function test_guest_items_dont_have_isLiked_property()
    {
        $response = $this->getJson('/api/items');
        $response->assertStatus(200);

        // isLikedプロパティが存在しないことを確認
        collect($response->json('items'))->each(function ($item) {
            $this->assertArrayNotHasKey('isLiked', $item);
        });
    }
}
