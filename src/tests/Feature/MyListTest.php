<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Factories\ItemConditionFactory;
use App\Models\ItemCondition;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;


class MyListTest extends TestCase
{
    use RefreshDatabase;


    // お気に入り商品はisLikedプロパティがtrueである。→フロントエンドでお気に入りか否かを判断するためのプロパティがある。
    public function test_items_have_correct_isLiked_property()
    {
        // テストユーザ作成
        $testUser = User::factory()->create();

        // アイテムを生成
        $itemCount = 5;
        $items = Item::factory()->count($itemCount)->create();

        // 最初の3つの商品をお気に入り登録
        $likedItems = $items->take(3);
        $likedItems->each(function ($item) use ($testUser) {
            Like::factory()->create([
                'user_id' => $testUser->id,
                'item_id' => $item->id,
            ]);
        });

        // ログインしてリクエスト送信
        $this->actingAs($testUser);
        $response = $this->getJson('/api/items');
        $response->assertStatus(200);

        // 取得したitemsを確認
        fwrite(STDOUT, "\n" . "お気に入り商品のisLikedプロパティがtrueであることを確認" . "\n");
        collect($response->json('items'))->each(function ($item) use ($testUser) {

            // isLikedプロパティが存在していることを確認
            $this->assertArrayHasKey('is_liked', $item);

            // likeレコードの存在をboolean型で取得
            $expectedIsLiked = Like::where('user_id', $testUser->id)->where('item_id', $item['id'])->exists();

            // ログ出力
            fwrite(STDOUT, "Item ID: {$item['id']} | is_likedプロパティ "
                . var_export($item['is_liked'], true)
                . " | likeレコードの有無: "
                . var_export($expectedIsLiked, true)
                . "\n"
            );

            // isLikedの値が正しいことを確認
            $this->assertEquals($expectedIsLiked, $item['is_liked'], "Item ID: {$item['id']} のis_likedが正しくありません。");
        });
    }


    // ゲストユーザはis_likedプロパティを持たない。→お気に入り機能がない
    public function test_guest_items_dont_have_is_liked_property()
    {
        $response = $this->getJson('/api/items');
        $response->assertStatus(200);

        // isLikedプロパティが存在しないことを確認
        collect($response->json('items'))->each(function ($item) {
            $this->assertArrayNotHasKey('is_liked', $item);
        });
    }
}
