<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;


class LikeTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        // テスト開始前にマイグレーションをリフレッシュし、シーディングを実行 少し時間がかかる
        Artisan::call('migrate:refresh --seed --env=testing');
    }


    // ログインユーザは商品をお気に入り登録できる

}
