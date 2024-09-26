<?php

namespace Tests\Feature\users;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersPasswordTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createTestUser();
    }

    private function createTestUser()
    {
        // ユーザーのテストデータを作成
        return User::factory()->create([
            'id' => 110001,
            'username' => 'testUser',
            'password' => Hash::make('testUser'),
            'mail_address' => 'testUsers@User.com'
        ]);
    }

    private function updatePassword()
    {
        // パスワードを変更するPOSTリクエストを送信
        $response = $this->post("/users/password/{$this->user->id}", [
            'password' => 'testUser',
            'new_password' => 'new_testUser',
            'new_password_confirmation' => 'new_testUser',
        ]);
        return $response;
    }

    /**
     * @test
     */
    public function test_users_password_get_ok_redirect_without_login()
    {
        // 未ログイン時のリダイレクトの確認
        $response = $this->get('/users/password');
        $response->assertRedirect('/users/login');
    }

    /**
     * @test
     */
    public function test_users_password_get_ok_view()
    {
        // ログイン状態での、パスワード変更画面へのアクセスの確認
        $this->actingAs($this->user);
        $response = $this->get('/users/password');
        $response->assertViewIs('users.passwordChange.index');
    }

    /**
     * @test
     */
    public function test_users_password_post_ok_Redirect()
    {
        // パスワード変更後のリダイレクト先の確認
        $this->actingAs($this->user);
        $response = $this->updatePassword();
        $response->assertRedirect('/users');
    }

    /**
     * @test
     */
    public function test_users_password_post_ok_change_success()
    {
        // パスワードが正しく変更されたか確認
        $this->actingAs($this->user);
        $response = $this->updatePassword();
        $this->assertTrue(Hash::check('new_testUser', $this->user->fresh()->password));
    }

    /**
     * @test
     */
    public function test_users_password_post_ok_display_success_message()
    {
        // 「パスワードが変更されました」が表示されるか確認
        $this->actingAs($this->user);
        $response = $this->updatePassword();
        $response->assertSessionHas('message', 'パスワードが変更されました');
    }
}
