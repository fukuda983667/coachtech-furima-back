<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use GuzzleHttp\Client;


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
    public function test_it_returns_correct_item_details()
    {
        // Seederで作成した"腕時計"(カテゴリ2つ持ち)の商品詳細情報を取得する。
        $response = $this->getJson('/api/items/1');

        // ステータスコード 200 を確認
        $response->assertStatus(200);

        // 環境設定からベースURLを取得 config('app.url')は.env.testingのAPP_URLを参照している
        $baseUrl = config('app.url') . '/storage/items/';

        // レスポンスのプロパティ名、データが一致するか確認
        $response->assertJson([
            'item' => [
                'id' => 1,
                'name' => '腕時計',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'user_id' => 1,
                'image_path' => $baseUrl . "1.jpg",
                'condition_id' => 1,
                'brand' => "テスト太郎商会",
                'categories' => [
                    ['id' => 5, 'name' => 'メンズ'],
                    ['id' => 15, 'name' => '時計']
                ],
                'condition' => [
                    'id' => 1,
                    'name' => '良好'
                ]
            ]
        ]);
    }


    // 特定のitemのお気に入り数を取得できるかテスト
    public function test_it_returns_like_count_for_item()
    {
        // APIリクエストを送信
        $response = $this->getJson('/api/likes/1');

        // ステータスコード200を確認
        $response->assertStatus(200);

        // お気に入り数が1であることを確認(LikeSeeder.phpで1にしてある)
        $response->assertJson([
            'like_count' => 1,
        ]);
    }


    // 特定のitemに紐づくコメントが取得できるかテスト
    public function test_it_returns_comments_for_specific_item()
    {
        // APIリクエストを送信
        $response = $this->getJson('/api/comments/1');

        // ステータスコード200を確認
        $response->assertStatus(200);

        // 環境設定からユーザーアイコンのベースURLを取得
        $baseUrl = config('app.url') . '/storage/user-icons/';

        // レスポンスのプロパティ名、データがSeederで設定したデータと一致するか確認
        $response->assertJson([
            'comments' => [
                [
                    'user_id' => 2,
                    'item_id' => 1,
                    'comment' => '何年使用していましたか？',
                    'user' => [
                        'id' => 2,
                        'name' => 'テスト花子',
                        'image_path' => $baseUrl . 'default-user.jpeg',
                    ],
                ]
            ],
            'comment_count' => 1,
        ]);
    }


    // storageに保存されている商品画像がブラウザで表示できるかをテスト
    public function test_can_get_item_image()
    {
        // $this->get()メソッドは実際にhttpリクエストを送信しているわけではなく、リクエストURLに
        // 対応すると思われるweb.phpとapi.phpで設定したrouteを参照する。そのためstorageへのアクセスとはみなされず404エラーになる。
        // docker descktopでnginxのログを確認すると、リクエストが届いていないことが確認できる。
        // GuzzleHttpライブラリ使用すればブラウザと同じようにnginx経由でリクエスト送信することになるからstorageにアクセスできる。

        $client = new Client();

        // 単に'http://localhost:8080/storage/items/1.jpg'とするとコンテナ
        $response = $client->get('http://host.docker.internal:8080/storage/items/1.jpg');

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('image/jpeg', $response->getHeaderLine('Content-Type'));
    }
}
