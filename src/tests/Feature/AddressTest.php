<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use App\Models\Item;


class AddressTest extends TestCase
{
    use RefreshDatabase;


    // 送付先住所登録テスト
    public function test_user_can_create_address()
    {
        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        $data = [
            'postal_code' => '123-4567',
            'address' => '東京都千代田区',
            'building_name' => '某オフィスビル',
        ];

        // 配送先住所登録リクエスト
        $response = $this->postJson('/api/user/address', $data);

        // ステータスコード201を確認
        $response->assertStatus(201);
        $response->assertJson([
            'message' => '送付先住所を登録しました',
            'address' => [
                'id' => true, // 'id' が含まれていることを確認
            ],
        ]);

        // レスポンスから登録された住所の ID を取得
        $addressId = $response->json('address.id');

        // データベースに購入レコードが保存されていることを確認
        $this->assertDatabaseHas('addresses', [
            'id' => $addressId,
            'user_id' => $testUser->id,
            'postal_code' => $data['postal_code'],
            'address' => $data['address'],
            'building_name' => $data['building_name'],
            'is_default' => false,
        ]);
    }


    // 指定した住所取得テスト
    public function test_user_can_get_specified_address()
    {
        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        $addresses = Address::factory()->count(3)->create([
            'user_id' => $testUser->id,
        ]);

        $selectedAddress = $addresses->first();

        // 住所登録後、idを指定して住所情報を取得できるかテスト
        $response = $this->getJson("/api/user/address/{$selectedAddress->id}");
        $response->assertStatus(200);

        $response->assertJson([
            'address' => [
                'id' => $selectedAddress->id,
                'user_id' => $testUser->id,
                'postal_code' => $selectedAddress->postal_code,
                'address' => $selectedAddress->address,
                'building_name' => $selectedAddress->building_name,
                'is_default' => $selectedAddress->is_default,
            ],
        ]);
    }


    // 送付先住所を指定して商品購入
    public function test_user_can_purchase_with_specified_address()
    {
        $item = Item::factory()->create();

        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        $addresses = Address::factory()->count(3)->create([
            'user_id' => $testUser->id,
        ]);

        // 使用する送付先住所選択
        $selectedAddress = $addresses->first();

        // 支払い方法選択
        $paymentMethod = 1;

        $data = [
            'item_id' => $item->id,
            'address_id' => $selectedAddress->id,
            'payment_method' => $paymentMethod,
        ];

        $response = $this->postJson('/api/purchases', $data);

        // ステータスコード201を確認
        $response->assertStatus(201);
        $response->assertJson(['message' => '購入が完了しました']);

        // 購入テーブルにレコードが正しく保存されていることを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $testUser->id,
            'item_id' => $item->id,
            'address_id' => $selectedAddress->id, // 指定した住所IDが紐づいているか確認
            'payment_method' => $paymentMethod,
        ]);
    }
}
