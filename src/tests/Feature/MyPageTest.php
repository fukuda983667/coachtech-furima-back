<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use App\Models\Item;
use App\Models\Purchase;


class MyPageTest extends TestCase
{
    use RefreshDatabase;


    // ユーザデータ取得
    public function test_user_can_get_profile()
    {
        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        $testUserImagePath = $testUser->image_path;

        // ユーザ情報取得
        $response = $this->getJson('/api/user');
        $response->assertStatus(200);

        // 環境設定からベースURLを取得 config('app.url')は.env.testingのAPP_URLを参照している
        $baseUrl = config('app.url') . '/storage/user-icons/';

        // レスポンス確認
        $response->assertJson([
            'id' => $testUser->id,
            'name' => $testUser->name,
            'email' => $testUser->email,
            'image_path' => $baseUrl . $testUserImagePath,
        ]);
    }


    // マイページで設定した住所情報取得
    public function test_user_can_get_default_address()
    {
        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        // デフォルトのアドレス作成
        $defaultAddress = Address::factory()->create([
            'user_id' => $testUser->id,
            'is_default' => true,
        ]);

        // その他アドレス作成
        Address::factory()->count(3)->create([
            'user_id' => $testUser->id,
            'is_default' => false,
        ]);

        // ユーザ情報取得
        $response = $this->getJson('/api/user/address/default');
        $response->assertStatus(200);

        // レスポンス確認
        $response->assertJson([
            'address' => [
                'user_id' => $testUser->id,
                'postal_code' => $defaultAddress->postal_code,
                'address' => $defaultAddress->address,
                'building_name' => $defaultAddress->building_name,
                'is_default' => true,
            ]
        ]);
    }


    // ユーザが購入した商品と出品した商品が正しく取得できるかテスト
    public function test_user_can_get_purchased_and_listed_items()
    {
        // テストユーザを取得
        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        $purchasedItem = Item::factory()->create();
        Purchase::factory()->create([
            'user_id' => $testUser->id,
            'item_id' => $purchasedItem->id,
        ]);

        $listedItem = Item::factory()->create([
            'user_id' => $testUser->id,
        ]);

        // 商品情報を取得
        $response = $this->getJson('/api/user/my-page');
        $response->assertStatus(200);

        // 環境設定からベースURLを取得 config('app.url')は.env.testingのAPP_URLを参照している
        $baseUrl = config('app.url') . '/storage/items/';

        // 作成しておいたレコードを取得できているか確認
        $response->assertJson([
            'purchased_items' => [
                [
                    'id' => $purchasedItem->id,
                    'name' => $purchasedItem->name,
                    'is_sold' => true,
                    'image_path' => $baseUrl . $purchasedItem->image_path,
                ],
            ],
            'listed_items' => [
                [
                    'id' => $listedItem->id,
                    'name' => $listedItem->name,
                    'image_path' => $baseUrl . $listedItem->image_path,
                ],
            ],
        ]);
    }


    // マイページ情報更新テスト
    public function test_user_can_up_date_profile()
    {
        $testUser = User::factory()->create();
        $this->actingAs($testUser);

        // デフォルトのアドレスを作成
        $defaultAddress = Address::factory()->create([
            'user_id' => $testUser->id,
            'is_default' => true,
        ]);

        // 画像ファイル作成
        $imageFile = UploadedFile::fake()->image('profile.jpeg');

        // 更新用のデータを準備
        $updatedData = [
            'name' => 'Updated Name',
            'postal_code' => '123-4567',
            'address' => 'New Address',
            'building_name' => 'New Building',
            'image_path' => $imageFile,
        ];

        // プロファイル更新リクエストを送信
        $response = $this->postJson('/api/user/profile', $updatedData);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'プロフィールが更新されました。']);


        // レスポンスから user オブジェクトを取得
        $responseData = $response->json();
        $updatedUser = $responseData['user'];

        // 画像ファイル名が更新されていることを確認(コントローラーで画像ファイル名が一意になるように加工されるため)
        $this->assertNotEquals($updatedUser['image_path'], 'profile.jpeg');


        // データベースを確認
        $this->assertDatabaseHas('users', [
            'id' => $testUser->id,
            'name' => $updatedData['name'],
            'image_path' => $updatedUser['image_path'],
        ]);

        // 新しいデフォルト住所が設定されていることを確認
        $this->assertDatabaseHas('addresses', [
            'user_id' => $testUser->id,
            'postal_code' => $updatedData['postal_code'],
            'address' => $updatedData['address'],
            'building_name' => $updatedData['building_name'],
            'is_default' => true,
        ]);

        // 画像が正しいディレクトリに保存されていることを確認
        Storage::disk('public')->assertExists('user-icons/' . $updatedUser['image_path']);

        // 画像の削除
        Storage::disk('public')->delete('user-icons/' . $updatedUser['image_path']);
    }
}
