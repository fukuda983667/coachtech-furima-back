<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;


class ItemDetailTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        // テスト開始前にマイグレーションをリフレッシュし、シーディングを実行 少し時間がかかる
        Artisan::call('migrate:refresh --seed --env=testing');
    }


    // 商品詳細情報を取得できる。→フロントエンドで商品詳細画面を表示するために必要なデータを取得できる。
    public function it_returns_correct_item_details()
    {
        // API リクエストを送信
        $response = $this->getJson('/api/item/1');

        // ステータスコード 200 を確認
        $response->assertStatus(200);

        // レスポンスの JSON 構造を確認
        $response->assertJsonStructure([
            'item' => [
                'id',
                'name',
                'description',
                'price',
                'image_path',
                'isSold',
                'brand',
                'categories',
                'condition',
                'condition_id',
                'user_id',
                'created_at',
                'updated_at',
            ],
        ]);

        // ログ出力（デバッグ用）
        fwrite(STDOUT, $response->getContent() . PHP_EOL);
    }

}
