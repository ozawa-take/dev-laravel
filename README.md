# イロハボードLaravel化

## 資料

<https://github.com/epkotsoftware/dev-laravel-learning-docs/blob/main/README.md>

## 環境構築手順

### 1. .env の編集

[.env](./.env)ファイルはDockerの環境ファイルです。  
基本的にはそのまま使用可能ですが、IPとポートが重複するとコンテナが起動しないので  
自身の環境に合わせて設定を変えてください。

### 2. コマンド実行

```sh
# リポジトリをクローン
git clone https://github.com/epkotsoftware/dev-laravel-learning.git
# 2. リポジトリに移動
cd dev-laravel-learning
# 3. コンテナ起動
docker-compose up -d
# 4. 対象コンテナ全てのステータスが、UP である事を確認
docker-compose ps
```

### 3. Laravel

#### Webサーバー（コンテナ）に入る

Laravel関連のコマンドはDockerで用意した、Webサーバー（コンテナ）上で行います。

```bash
# ターミナルで実行
docker exec -it dev-laravel-learning-web bash
```

VSCodeの[Docker拡張機能](https://marketplace.visualstudio.com/items?itemName=ms-azuretools.vscode-docker)が入っている場合、対象コンテナの「Attach Shell」でも開けます。  

#### composer install

```bash
# ■ Webサーバーで入力
# 「composer.json」、「composer.lock」に記載されているパッケージをvendorディレクトリにインストール
#   ※ 時間がかかるので注意。
composer install
```

`composer install` 実行後に「`Exception`」が出ていると失敗しているので  
[root/vendor/](./root/vendor/)ディレクトリを削除して、再実行してみましょう。  

#### Laravel初期設定

```bash
# ■ Webサーバーで入力
cd /var/www/root
# 「.env」ファイル
## 「.env.dev」ファイルを「.env」にコピー
cp .env.dev .env
# storage ディレクトリに読み取り・書き込み権限を与える（bootstrap, storage内に書き込み（ログ出力時等）に「Permission denied」のエラーが発生する）
chmod -R 777 bootstrap/cache/
chmod -R 777 storage/
```

#### データベースの初期化

[Laravel初期設定](#laravel初期設定)後に実行してください。  

```bash
# ■ Webサーバーで入力
cd /var/www/root
# テーブルの再作成＆初期データを挿入
php artisan migrate:fresh --seed
```

#### マルチログインについて

- 管理者側へログイン
  - <http://127.0.0.1/admin/login> ログイン画面
  - ログインID：`admin_01` 〜 `admin_10` （管理者10人分）
  - パスワード：`admin`
- ユーザー側へログイン
  - <http://127.0.0.1/users/login> ログイン画面
  - ログインID：`test_01` 〜 `test_10` （ユーザー10人分）
  - パスワード：`test`

## 環境構築の確認

- Web ※ **IP・ポート番号は [`.env`](./.env) の `IP`・`PORT_WEB` を参照**
  - <http://127.0.0.1:80/> （デフォルト設定のURL）  
    [routes/web.php](./root/routes/web.php)のURI「`'/'`」の実行結果が画面に表示されます。  
    VSCodeの[Docker拡張機能](https://marketplace.visualstudio.com/items?itemName=ms-azuretools.vscode-docker)が入っている場合、対象コンテナの「Open in Browser」でも開けます。  
- phpMyAdmin ※ **IP・ポート番号は [`.env`](./.env) の `IP`・`PORT_PHPMYADMIN` を参照**
  - <http://127.0.0.1:8080> （デフォルト設定のURL）  
    VSCodeの[Docker拡張機能](https://marketplace.visualstudio.com/items?itemName=ms-azuretools.vscode-docker)が入っている場合、対象コンテナの「Open in Browser」でも開けます。  

### SQLクライアント

こちらは任意です。

- `DBeaver`
  - <https://dbeaver.io/>
  - 接続情報 ※ [`.env`](./.env) の情報にあわせて設定すること
    - ドライバ名: `MySQL`
    - ServerHost: `localhost`  ～  `IP` 参照 (localhost = 127.0.0.1)
    - Port: `3306`  ～  `PORT_DB` 参照
    - Database: ※ 未入力でOK
    - ユーザー名: `root`
    - パスワード: `root`  ～  `DB_ROOT_PASSWORD` 参照
- `A5:SQL Mk-2`
  - <https://a5m2.mmatsubara.com/>
  - 接続情報 ※ [`.env`](./.env) の情報にあわせて設定すること
    - ホスト名: `localhost`  ～  `IP` 参照 (localhost = 127.0.0.1)
    - ユーザーID: `root`
    - パスワード: `root`  ～  `DB_ROOT_PASSWORD` 参照
    - ポート番号: `3306`  ～  `PORT_DB` 参照

### PHP_CodeSnifferの使用

コミット・プッシュ前にPHP_CodeSnifferを活用してコーディング規約違反がないかチェックすること。

```bash
# ■ Webサーバーで入力
# 全体チェック
composer sniffer ./
# 単一ファイルチェック(例としてAdminLoginControllerをチェックする場合)
composer sniffer ./app/Http/Controllers/AdminLoginController.php
```

### PHPunit

#### テストの実施

```bash
# ■ Webサーバーで入力
# 全体チェック
php artisan test
# 単一ファイルチェック(例としてAdminLoginTest.phpを実施する場合)
php artisan test --filter AdminLoginTest
```

#### 運用ルール

- メソッド名はキャメルケースで`'test' ＋ URI ＋ HTTPメソッド ＋ok(正常系) or error(異常系) ＋ テスト観点`
  - 例） test_admin_login_delete_ok_session_regenerate()
- URI毎にテストファイルを作成する
- 1ケース1メソッドを意識してテストを作成する（1メソッドにテストをまとめない）
