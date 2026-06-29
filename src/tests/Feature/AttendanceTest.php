<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;


class AttendanceTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;
    
    public function test_新規登録画面（一般）にて名前が未入力の場合「お名前を入力してください」()
    {
        $response = $this->post('/register', [
            'name' => '', 
            'email' => 'test@example.com',
            'password' => 'Test1234',
            'password_confirmation' => 'Test1234',
        ]);

        $response->assertSessionHasErrors([
        'name' => 'お名前を入力してください'
        ]);
    
        $response->assertStatus(302); 
    }

    public function test_新規登録画面にてメールアドレスが未入力の場合「メールアドレスを入力してください」というバリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎', 
            'email' => '',
            'password' => 'Test1234',
            'password_confirmation' => 'Test1234',
        ]);

        $response->assertSessionHasErrors([
        'email' => 'メールアドレスを入力してください'
        ]);
    
        $response->assertStatus(302); 
    }

    public function test_新規登録画面にてパスワードが未入力の場合「パスワードを入力してください」というバリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎', 
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',

        ]);

        $response->assertSessionHasErrors([
        'password' => 'パスワードを入力してください'
        ]);
    
        $response->assertStatus(302); 
    }

    public function test_新規登録画面にてパスワードが7文字以下の場合「パスワードは8文字以上で入力してください」というバリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎', 
            'email' => 'test@example.com',
            'password' => 'Test123',
            'password_confirmation' => 'Test123',

        ]);

        $response->assertSessionHasErrors([
        'password' => 'パスワードは8文字以上で入力してください'
        ]);
    
        $response->assertStatus(302); 
    }

    public function test_新規登録画面にてパスワードが確認用パスワードと一致しない場合「パスワードと一致しません」というバリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎', 
            'email' => 'test@example.com',
            'password' => 'Test1234',
            'password_confirmation' => '1234Test',

        ]);

        $response->assertSessionHasErrors([
        'password' => 'パスワードと一致しません'
        ]);
    
        $response->assertStatus(302); 
    }

    public function test_全ての項目が入力されている場合、会員情報が登録され、登録されたメールアドレスに確認メールが送信される()
    {
        $this->withoutExceptionHandling();
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テスト太郎', 
            'email' => 'test@example.com',
            'password' => 'Test1234',
            'password_confirmation' => 'Test1234',
        ]);

        if ($response->status() !== 302) {
            $response->dump();
        }

        $response->assertStatus(200);
    
        $this->assertDatabaseHas('users', [
            'name' => 'テスト太郎', 
            'email' => 'test@example.com',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );

    }

    public function test_メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/attendance');
    
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_メール認証サイトのメール認証を完了すると、勤怠記録画面に遷移する()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create(['email_verified_at' => null]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($url);

        $this->assertNotNull($user->fresh()->email_verified_at);

        $response->assertRedirect('/attendance');
    }

    public function test_ログイン画面にてメールアドレスが未入力の場合「メールアドレスを入力してください」というバリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'Test1234',

        ]);

        $response->assertSessionHasErrors([
        'email' => 'メールアドレスを入力してください'
        ]);
    
        $response->assertStatus(302); 
    }

    public function test_ログイン画面にてパスワードが未入力の場合「パスワードを入力してください」というバリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',

        ]);

        $response->assertSessionHasErrors([
        'password' => 'パスワードを入力してください'
        ]);
    
        $response->assertStatus(302); 
    }

    public function test_ログイン画面にて誤った情報を入力した場合「ログイン情報が登録されていません」というバリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrong1234',
        ]);

        $response->assertSessionHasErrors([
        'email' => 'ログイン情報が登録されていません'
        ]);
    
        $response->assertStatus(302); 
    }

    public function test_ログイン画面にて正しい情報が入力された場合、ログイン処理が実行される()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('test1234'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'test1234',
        ]);

        $this->assertAuthenticatedAs($user);
    
        $response->assertRedirect('/attendance');
    }

    public function test_ログアウト処理が実行される()
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();

        $response->assertRedirect('/login');
    }

    
    
}
