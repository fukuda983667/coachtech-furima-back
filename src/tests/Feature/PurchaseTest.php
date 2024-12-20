<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use App\Models\Item;


class PurchaseTest extends TestCase
{
    use RefreshDatabase;


    // 商品購入ヘルパーメソッド
    private function purchaseItem(User $user, Item $item, Address $address, int $paymentMethod)
    {
        $this->actingAs($user);

        $data = [
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => $paymentMethod,
        ];

        return $response = $this->postJson('/api/purchases', $data);
    }


    // 商品購入テスト 正常系
    public function test_user_can_purchase_item()
    {
        $item = Item::factory()->create();
        $testUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $testUser->id]);
        $paymentMethod = 1;

        // 購入処理関数実行
        $response = $this->purchaseItem($testUser, $item, $address, $paymentMethod);

        // ステータスコード201を確認
        $response->assertStatus(201);
        $response->assertJson(['message' => '購入が完了しました']);

        // データベースに購入レコードが保存されていることを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $testUser->id,
            'item_id' => $item->id,
            'payment_method' => $paymentMethod,
            'address_id' => $address->id,
        ]);
    }


    // 商品購入後、商品一覧取得時にis_soldプロパティがtrueになるかテスト
    public function test_is_sold_property_updates_correctly_after_purchase()
    {
        $item = Item::factory()->create();
        $testUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $testUser->id]);
        $paymentMethod = 1;

        // 購入前に商品一覧取得、itemのレスポンスにis_soldプロパティが含まれていてfalseか確認
        $responseBefore = $this->getJson('/api/items');
        $responseBefore->assertStatus(200);
        $responseBefore->assertJsonFragment([
            'id' => $item->id,
            'is_sold' => false,
        ]);

        // 購入処理
        $this->purchaseItem($testUser, $item, $address, $paymentMethod);

        // 購入後に商品一覧取得、itemのレスポンスにis_soldプロパティが含まれていてtrueか確認
        $response = $this->getJson('/api/items');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $item->id,
            'is_sold' => true,
        ]);
    }


    // 商品購入後、マイページ取得時にitemが含まれていてis_soldプロパティがtrueかテスト
    public function test_item_appears_in_my_page_with_is_sold_true_after_purchase()
    {
        $item = Item::factory()->create();
        $testUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $testUser->id]);
        $paymentMethod = 1;

        // 購入前に商品一覧取得、itemのレスポンスにis_soldプロパティが含まれていてfalseか確認
        $this->actingAs($testUser);
        $responseBefore = $this->getJson('/api/user/my-page');
        $responseBefore->assertStatus(200);

        // 購入前のpurchased_itemsにitemが含まれていないことを確認
        $responseBefore->assertJson([
            'purchased_items' => [],
            'listed_items' => [],
        ]);

        // 購入処理
        $this->purchaseItem($testUser, $item, $address, $paymentMethod);

        // 購入後に商品一覧取得、itemのレスポンスにis_soldプロパティが含まれていてtrueか確認
        $response = $this->getJson('/api/user/my-page');
        $response->assertStatus(200);

        // 購入後のpurchased_itemsにitemが含まれていること、かつis_soldがtrueであることを確認
        $response->assertJson([
            'purchased_items' => [
                [
                    'id' => $item->id,
                    'is_sold' => true,
                ],
            ],
            'listed_items' => [],
        ]);
    }
}
