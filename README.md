# coachtech-furima-back

docker-compose exec php bash

php artisan migrate:refresh --seed

php artisan migrate:refresh --seed --env=testing

./vendor/bin/phpunit --filter RegisterTest
./vendor/bin/phpunit --filter LoginTest
./vendor/bin/phpunit --filter LogoutTest
./vendor/bin/phpunit --filter ItemsTest
./vendor/bin/phpunit --filter MyListTest