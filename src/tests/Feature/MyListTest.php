<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;


class MyListTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        // テスト開始前にマイグレーションをリフレッシュし、シーディングを実行 少し時間がかかる
        Artisan::call('migrate:refresh --seed --env=testing');
    }


    // お気に入り商品はisLikedプロパティがtrueである。→フロントエンドでお気に入りか否かを判断するためのプロパティがある。
    public function test_items_have_correct_isLiked_property()
    {
        // テストユーザ情報
        $testUser = User::where('email', 'test-taro@mail.com')->first();
        $this->assertNotNull($testUser, "テストユーザ 'test-taro@mail.com' が存在しません。");

        // ログインしてリクエスト送信
        $this->actingAs($testUser);
        $response = $this->getJson('/api/items');
        $response->assertStatus(200);

        // 取得したitemsを確認
        fwrite(STDOUT, "\n" . "お気に入り商品のisLikedプロパティがtrueであることを確認" . "\n");
        collect($response->json('items'))->each(function ($item) use ($testUser) {

            // isLikedプロパティが存在していることを確認
            $this->assertArrayHasKey('is_liked', $item);

            // ユーザモデルのlikesリレーションをitemに紐づくlikeレコードの存在をboolean型で取得
            $expectedIsLiked = $testUser->likes()->where('item_id', $item['id'])->exists();

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


    // ゲストユーザはisLikedプロパティを持たない。→お気に入り機能がない
    public function test_guest_items_dont_have_isLiked_property()
    {
        $response = $this->getJson('/api/items');
        $response->assertStatus(200);

        // isLikedプロパティが存在しないことを確認
        collect($response->json('items'))->each(function ($item) {
            $this->assertArrayNotHasKey('is_liked', $item);
        });
    }
}
