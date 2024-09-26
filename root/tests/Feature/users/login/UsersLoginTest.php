<?php

namespace Tests\Feature\Users\Login;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersLoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'username' => 'testUser',
            'password' => Hash::make('testUser'),
            'mail_address' => 'testUser@user.com',
        ]);
    }


    private function login()
    {
        $response = $this->post('/users/login', [
            'username' => 'testUser',
            'password' => 'testUser',
        ]);
        return $response;
    }

    private function logout()
    {
        $response = $this->delete('/users/login');
        return $response;
    }

    /**
     * @test
     */
    public function test_users_login_get_ok()
    {
        $response = $this->get('/users/login');
        $response->assertOk();
    }

    /**
     * @test
     */
    public function test_users_login_post_ok_redirect()
    {
        $response = $this->login();

        // リダイレクトの確認
        $response->assertRedirect('users/');
    }

    /**
     * @test
     */
    public function test_users_login_post_ok_authenticated_as_expected_user()
    {
        $this->login();

        // 認証されたユーザーが期待するユーザー名を持っているか確認
        $this->assertAuthenticatedAs(User::where('username', 'testUser')->first(), 'web');
    }

    /**
     * @test
     */
    public function test_users_login_post_ok_session_regenerated()
    {
        // セッションIDの保持
        $sessionIDBeforeLogin = session()->getId();

        $this->login();

        // セッションIDを再取得し、変化を確認
        $sessionIDAfterLogin = session()->getId();
        $this->assertNotSame($sessionIDBeforeLogin, $sessionIDAfterLogin);
    }

    /**
     * @test
     */
    public function test_users_login_post_ok_users_log()
    {
        $this->login();

        // ログが記録されているか確認
        $loginUser = Auth::guard('web')->user();
        $this->assertDatabaseHas('user_logs', ['user_id' => $loginUser->id]);
    }

    /**
     * @test
     */
    public function test_users_login_delete_ok()
    {
        $this->login();
        $this->logout();

        //ユーザーがゲスト状態（ログアウト状態）であるか確認
        $this->assertGuest('web');
    }

    /**
     * @test
     */
    public function test_users_login_delete_ok_redirect()
    {
        $this->login();
        $response = $this->logout();

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/users/login');
    }

    /**
     * @test
     */
    public function test_users_login_delete_ok_session_invalidated()
    {
        $this->login();

        //セッションに値を設定
        $this->withSession(['key' => 'value']);

        $response = $this->logout();

        // セッションデータが破棄されていることを確認
        $response->assertSessionMissing('key');
    }

    /**
     * @test
     */
    public function test_users_login_delete_ok_session_regenerate()
    {
        $this->login();

        // セッショントークンの取得
        $tokenBeforeLogout = session('_token');

        $this->logout();

        // セッショントークンが再生成されていることを確認
        $tokenAfterLogout = session('_token');
        $this->assertNotSame($tokenBeforeLogout, $tokenAfterLogout);
    }
}