<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;


class ItemsTest extends TestCase
{
    use RefreshDatabase;

    // ゲストユーザは全itemを取得できる。→フロントエンドで一覧画面を表示するために必要なデータを取得できる。
    public function test_guest_can_get_all_items()
    {
        // アイテムを5件作成
        $itemCount = 5;
        Item::factory()->count($itemCount)->create();

        // 未認証ユーザでAPIリクエストを送信
        $response = $this->getJson('/api/items');

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // レスポンスで取得したitemsの数を確認
        $response->assertJsonCount($itemCount, 'items');


        // 環境設定からベースURLを取得 config('app.url')は.env.testingのAPP_URLを参照している
        $baseUrl = config('app.url') . '/storage/items/';

        // itemsテーブルのデータを取得
        $items = Item::all();

        // レスポンスデータを検証
        collect($response->json('items'))->each(function ($responseItem) use ($items, $baseUrl) {
            // 商品一覧の表示には最低限'id', 'name', 'image_path'のプロパティが必要
            $this->assertArrayHasKey('id', $responseItem);
            $this->assertArrayHasKey('name', $responseItem);
            $this->assertArrayHasKey('image_path', $responseItem);

            // レスポンスのitemと対応するitemをitemsテーブル($items)から取得
            $dbItem = $items->firstWhere('id', $responseItem['id']);

            // image_pathが加工されていることを確認
            $expectedImagePath = $baseUrl . $dbItem->image_path;
            $this->assertEquals($expectedImagePath, $responseItem['image_path']);
        });
    }


    // 購入済み商品はis_soldプロパティがtrueである。→フロントエンドで購入済みか否かを判断するためのプロパティがある。
    public function test_items_have_correct_isSold_property()
    {
        // アイテムを5件作成
        $itemCount = 5;
        $items = Item::factory()->count($itemCount)->create();

        // 1件の購入レコードを作成（ランダムにアイテムを選んで購入を紐付け）
        $purchasedItem = $items->random();
        Purchase::factory()->create([
            'item_id' => $purchasedItem->id,
        ]);

        // 未認証ユーザでAPIリクエストを送信(全item取得)
        $response = $this->getJson('/api/items');
        $response->assertStatus(200);

        // 取得したitemsを確認
        fwrite(STDOUT, "\n" . "購入済み商品のisSoldプロパティがtrueであることを確認" . "\n");
        collect($response->json('items'))->each(function ($item) use ($items) {

            // isSoldプロパティが存在していることを確認
            $this->assertArrayHasKey('is_sold', $item);

            // is_soldが購入レコードの有無に基づいて設定されているかを確認
            $expectedIsSold = Purchase::where('item_id', $item['id'])->exists();

            // ログ出力
            fwrite(STDOUT, "Item ID: {$item['id']} | is_soldプロパティ "
                . var_export($item['is_sold'], true)
                . " | purchaseレコードの有無: "
                . var_export($expectedIsSold, true)
                . "\n"
            );

            // isSoldの値が正しいことを確認
            $this->assertEquals($expectedIsSold, $item['is_sold'], "Item ID: {$item['id']} のis_soldが正しくありません。");
        });
    }


    // 自分が出品したitemを取得できない。→フロントエンドで一覧に表示されない。
    public function test_logged_in_user_cannot_get_their_own_items()
    {
        // テストユーザ作成
        $testUser = User::factory()->create();

        // 自分が出品したアイテムの数と他のユーザーのアイテムの数を設定
        $ownItemCount = 2;
        $otherItemCount = 3;

        $ownItems = Item::factory()->count($ownItemCount)->create(['user_id' => $testUser->id,]);
        $otherItems = Item::factory()->count($otherItemCount)->create();

        // ログインしてリクエスト送信
        $this->actingAs($testUser);
        $response = $this->getJson('/api/items');
        $response->assertStatus(200);

        // レスポンス内の全itemsを取得
        $responseItems = $response->json('items');


        // 件数の確認
        $responseItemCount = count($responseItems);
        $this->assertEquals($otherItemCount, $responseItemCount, "レスポンスのアイテム件数が期待値と一致しません。");


        // 自分が出品した商品IDを取得
        $ownItemIds = $ownItems->pluck('id')->toArray();
        // レスポンスに含まれる商品IDを抽出
        $responseItemIds = collect($responseItems)->pluck('id')->toArray();

        // 自分が出品した商品がレスポンスに含まれていないことを確認
        foreach ($ownItemIds as $ownItemId) {
            $this->assertNotContains($ownItemId, $responseItemIds, "自分が出品した商品が含まれています。Item ID: {$ownItemId}");
        }
    }
}
