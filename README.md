# coachtech-furima-back

フリマアプリです。
※フロントとバック両方の環境構築が必要です。

backはRESTfull APIの提供をします。

## 前提条件
- Dockerがインストールされていること
- Docker Composeがインストールされていること

## 環境構築

1. coachtech-furima-frontの環境構築から始めてください。https://github.com/fukuda983667/coachtech-furima-front

1. リポジトリをクローンしたい任意のディレクトリで以下のコマンドを実行してください。

    ```bash
    git clone https://github.com/fukuda983667/coachtech-furima-back
    ```

2. クローンしたcoachtech-furima-backディレクトリに移動

    ```bash
    cd coachtech-furima-back
    ```

3. Docker Composeを使用してコンテナを作成・起動します。※Docker Descktop起動時に実行してください。

    ```bash
    docker-compose up -d --build
    ```

4. phpコンテナにログイン→`composer`をインストールします。

    ```bash
    docker-compose exec php bash
    ```
    ```
    composer install
    ```

5. `.env.example`ファイルをコピーして`.env`ファイルを作成します。

    ```bash
    cp .env.example .env
    ```

6. `.env`ファイルを編集し、必要な環境変数を設定します（5行目）。

   ```
   APP_URL=http://localhost:8080
   ```

6. `.env`ファイルを編集し、必要な環境変数を設定します（11～16行目）。

   ```
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=laravel_db
   DB_USERNAME=laravel_user
   DB_PASSWORD=laravel_pass
   ```

3. Mailtrapでメール認証機能をテストするため、アカウントを作成してください。

    https://mailtrap.io/

3. 番号の手順に従って環境変数をコピーしてください。画像と異なりますが「Laravel 9+」を選択してください

    ![env](/img/Mailtrap_env.png)

3. `.env`ファイルの31～35行目に先ほどコピーした値を貼り付け。36,37,38行目は追記してください。

   ```
   MAIL_MAILER=smtp
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=**************
   MAIL_PASSWORD=**************
   MAIL_ENCRYPTION=tls　# null→tlsに変更してください。
   MAIL_FROM_ADDRESS=Rese@example.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

7. アプリケーションキーを生成します。

    ```bash
    php artisan key:generate
    ```

8. データベースのマイグレーションを実行します。

    ```bash
    php artisan migrate
    ```

9. データベースのシーディングを実行します。

    ```bash
    php artisan db:seed
    ```

9. ストレージのシンボリックリンク作成します。
    ```bash
    php artisan storage:link
    ```

10. アプリケーションがhttp://localhost:3000 で利用可能になります。
   ※Rese-frontの環境構築が必要です。

9. ユーザー登録後、MailtrapのInboxに認証メールが届くので、Verify Email Addressをクリックして認証を完了してください。

![認証メール](/img/認証メール.png)

9. QRコードの読み取りはスクリーンショットでQRコード部分のみ切り取って、https://qrcode.red で読み取ってください。

## 仕様技術(実行環境)

- PHP : 8.1.18
- Laravel : 10.48.17
- MySQL : 8.0.32
- NGINX : 1.26.1
- docker-compose.yml : 3.8

## ER図

![ER図](/img/ER.svg)

## ローカルリポジトリの削除  
`git clone`したローカルリポジトリを完全に削除します。  
```
sudo rm -rf Rese-back
```
