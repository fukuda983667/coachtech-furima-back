<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // phpunitテスト(.env.testing環境(APP_ENV=testing))実行時、毎回シーディングが実行されます。その際、.env環境(APP_ENV=loacal)で保存した画像は消去されます。
        // `storage/app/public/items` ディレクトリを削除
        File::deleteDirectory(storage_path('app/public/items'));
        // `public/img/items` を `storage/app/public/item` にコピー
        File::copyDirectory(public_path('img/items'), storage_path('app/public/items'));

        // `storage/app/public/items` ディレクトリを削除
        File::deleteDirectory(storage_path('app/public/user-icons'));
        // `public/img/items` を `storage/app/public/item` にコピー
        File::copyDirectory(public_path('img/user-icons'), storage_path('app/public/user-icons'));

        // 各シーダークラスを呼び出し
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ItemConditionSeeder::class,
            ItemSeeder::class,
            CategoryItemSeeder::class,
            LikeSeeder::class,
            CommentSeeder::class,
            PurchaseSeeder::class,
        ]);
    }
}
