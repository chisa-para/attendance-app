# attendance-app

# COACHTECH勤怠管理アプリ

## 1. 概要

Laravelを使用した、勤怠管理システムのアプリケーションです。

## 2. 環境構築の手順

Dockerビルド
1. `git clone git@github.com:chisa-para/attendance-app.git`
2. `docker-compose up -d --build`

Laravel環境構築
1. `cp src/.env.example src/.env`
2. `docker-compose exec php bash`
3. `composer install` 
4. `php artisan key:generate`
5. `php artisan migrate --seed`<br>
   ※エラー等でもし途中でやり直したい場合は、下記を実行してください<br>
   `php artisan migrate:fresh --seed`

## 3. 開発環境

- 新規登録ページ(一般):http://localhost/register
- ログインページ(一般):http://localhost/login
- ログインページ（管理者）:http://localhost/admin/login

下記ユーザーのアカウントと出品商品が登録されています。
- 西怜奈（メールアドレス＝user1@example.com、パスワード＝password1）
- 山田太郎（メールアドレス＝user2@example.com、パスワード＝password2）
- 管理秀一（メールアドレス＝user3@example.com、パスワード＝password3）

- phpMyAdmin:http://localhost:8080/

## 4. メール認証について
本プロジェクトは新規ユーザー登録の際メール認証システムを使用します。
- MailHog:http://localhost:8025/

## 5.テストの実行について
1. テスト用環境ファイルの作成
```
cp .env.testing.example .env.testing
docker-compose exec php bash
php artisan key:generate --env=testing
```

2. テスト用データのマイグレーション
`php artisan migrate --env=testing`

3. テストの実行
`php phpunit`

※もし .env の変更が反映されない場合や、挙動がおかしい場合は、一度以下のコマンドで設定キャッシュをクリアしてください。<br>
`php artisan config:clear`

※テストの実装は要件シートのID1～7、16までしか実装できませんでした。

## 6. 提出にあたっての備考

- 勤怠記録機能において、出勤日から日をまたいでの退勤は想定していません。

- 勤怠一覧表示画面にて、出勤していない日の「詳細」ボタンはページ遷移しません。

- 勤怠一覧取得APIについて、パラメータにdate、monthを同時に指定した場合はdateかつmonth両方を満たすデータが検索されます。

## 7. 使用技術（実行環境）

- フレームワーク：Laravel 11.52.0
- 言語/ランタイム：PHP 8.4.21
- 管理ツール: Composer 2.9.8
- データベース：MySQL 8.0.26
- Webサーバー：nginx 1.30.1

## 8.ER図
<img width="1360" height="882" alt="index drawio" src="https://github.com/user-attachments/assets/8a92a99d-bfca-4f80-bb31-3ad0059b886c" />
