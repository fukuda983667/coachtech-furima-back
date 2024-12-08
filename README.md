# coachtech-furima-back

docker-compose exec php bash

php artisan migrate:refresh --seed

php artisan migrate:refresh --seed --env=testing

./vendor/bin/phpunit --filter RegisterTest
./vendor/bin/phpunit --filter LoginTest
