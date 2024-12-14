<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;


class PurchaseTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        // テスト開始前にマイグレーションをリフレッシュし、シーディングを実行 少し時間がかかる
        Artisan::call('migrate:refresh --seed --env=testing');
    }


    // 商品購入テスト 正常系
    public function test_user_can_purchase_item()
    {
        $testUser = User::where('email', 'test-taro@mail.com')->first();
        $this->actingAs($testUser);

        $data = [
            'item_id' => 6,
            'payment_method_id' => 1,
            'postal_code' => $testUser->postal_code,
            'address' => $testUser->address,
            'building_name' => $testUser->building_name,
        ];

        // 商品購入リクエスト送信
        $response = $this->postJson('/api/purchases', $data);

        // ステータスコード201を確認
        $response->assertStatus(201);
        $response->assertJson(['message' => '購入が完了しました']);

        // データベースに購入レコードが保存されていることを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $testUser->id,
            'item_id' => $data['item_id'],
            'payment_method_id' => $data['payment_method_id'],
            'postal_code' => $data['postal_code'],
            'address' => $data['address'],
            'building_name' => $data['building_name'],
        ]);
    }
}
