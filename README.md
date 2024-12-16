# coachtech-furima-back

docker-compose exec php bash

cat storage/logs/laravel.log

php artisan route:clear
php artisan route:cache
php artisan migrate:refresh --seed

php artisan migrate:refresh --seed --env=testing

./vendor/bin/phpunit --filter RegisterTest
./vendor/bin/phpunit --filter LoginTest
./vendor/bin/phpunit --filter LogoutTest
./vendor/bin/phpunit --filter ItemsTest
./vendor/bin/phpunit --filter MyListTest
./vendor/bin/phpunit --filter ItemDetailTest
./vendor/bin/phpunit --filter CommentTest
./vendor/bin/phpunit --filter PurchaseTest
./vendor/bin/phpunit --filter MyPageTest

./vendor/bin/phpunit --filter GetItemImageTest
curl http://host.docker.internal:8080/storage/items/1.jpg --output 1.jpg



factoryでダミーデータを作成できるようにして、テストケースを一から実装しなおせば、追いつける。
テーブル設計が変更されたから、ER図の書き直し、基本設計書の作成。

時間があれば、stripeの実装。
さらに時間があれば、バリデーションメッセージをバックから受け取る方法に変更。