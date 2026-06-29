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
- phpMyAdmin:http://localhost:8080/

下記ユーザーのアカウントと出品商品が登録されています。
- 西怜奈（メールアドレス＝user1@example.com、パスワード＝password1）
- 山田太郎（メールアドレス＝user2@example.com、パスワード＝password2）
- 管理秀一（メールアドレス＝user3@example.com、パスワード＝password3）

シーディングされている勤怠データ
- 西怜奈<br>
過去5カ月:各月平日15日(全75日)の通常出勤+当月17日出勤(内訳:通常10日/残業3/遅刻2/早退1/長時間労働1)
- 山田太郎<br>
当月を含む6カ月間に各月20日(全120日)の出勤(8:00〜22:00の間で、7時間〜12時間のランダムな勤務時間)


## 4. メール認証について
本プロジェクトは新規ユーザー登録の際メール認証システムを使用します。
- MailHog:http://localhost:8025/

## 5. APIの利用方法・動作確認

本システムはAPIを提供しています。以下の手順で動作確認を行うことができます。

### 前提条件・ベースURL
APIを叩く際は、事前にローカル環境（Docker等）が起動している必要があります。
* **ベースURL:** `http://localhost/api/v1`
* **共通ヘッダー:**
    ```http
    Accept: application/json
    Content-Type: application/json
    ```

---

### 認証について
 **Laravel Sanctum** による認証が必要です。
1. `POST /login` または `POST /register` でユーザー認証を行い、レスポンスから `token` を取得してください。
2. 以降のリクエストでは、ヘッダーに以下を設定する必要があります。
    * **Key:** `Authorization`
    * **Value:** `Bearer {取得したトークン}`

---

### 主要なエンドポイント一覧

| メソッド | エンドポイント | 認証 | 説明 |<br>
| `GET` | `/attendance-records` | 不要 | 勤怠一覧の取得 |<br>
| `POST` | `/attendance-records` | Sanctum認証必須 | 勤怠の新規登録 |<br>
| `GET` | `/attendance-records/{attendanceId}` | 不要 | 勤怠詳細の取得 |<br>
| `PUT/PATCH` | `/attendance-records/{attendanceRecord}` | Sanctum認証必須 | 勤怠情報の更新（本人/管理者のみ） |<br>
| `DELETE` | `/attendance-records/{attendanceRecord}` | Sanctum認証必須 | 勤怠情報の削除 |<br>

### Postmanでの確認方法

開発効率化のため、Postmanを使って簡単にテストできるようにしています。

プロジェクトのルート直下に、Postman用の設定ファイル（コレクション）を書き出して保存しています。
1. Postmanを開き、画面左上の **[Import]** ボタンをクリックします。
2. 本プロジェクトの `tests/Postman/attendance_api_collection.json` を選択してインポートします。
3. 自動でフォルダとリクエスト一覧が生成されます。

Postmanでのトークン設定
1. ログインAPIを実行し、返ってきた `token` をコピーします。
2. 叩きたいAPI（例：勤怠登録）を開き、**[Authorization]** タブをクリックします。
3. Typeで **[Bearer Token]** を選択し、**Token** 欄にコピーしたトークンを貼り付けて [Send] を押してください。

## 6.テストの実行について
1. テスト用環境ファイルの作成
```
cp .env.testing.example .env.testing
docker-compose exec php bash
```

2. テストの実行
`php artisan test tests/Feature/AttendanceTest.php`

※もし .env の変更が反映されない場合や、挙動がおかしい場合は、一度以下のコマンドで設定キャッシュをクリアしてください。<br>
`php artisan config:clear`

※テストの実装は要件シートのID1～2、16までしか実装できませんでした。

## 7. 提出にあたっての備考

- 勤怠記録機能において、出勤日から日をまたいでの退勤は想定していません。

- 勤怠一覧表示画面にて、出勤していない日の「詳細」ボタンはページ遷移しません。

- 勤怠一覧取得APIについて、パラメータにdate、monthを同時に指定した場合はdateかつmonth両方を満たすデータが検索されます。

## 8. 使用技術（実行環境）

- フレームワーク：Laravel 11.55.0
- 言語/ランタイム：PHP 8.4.21
- 管理ツール: Composer 2.9.8
- データベース：MySQL 8.0.26
- Webサーバー：nginx 1.30.1

## 9.ER図
<img width="1360" height="882" alt="index drawio" src="https://github.com/user-attachments/assets/8a92a99d-bfca-4f80-bb31-3ad0059b886c" />
