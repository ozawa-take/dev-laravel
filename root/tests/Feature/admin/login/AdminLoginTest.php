<?php

namespace Tests\Feature\Admin\Login;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Admin::factory()->create([
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
        ]);
    }


    private function login()
    {
        $response = $this->post('/admin/login', [
            'username' => 'testAdmin',
            'password' => 'testAdmin',
        ]);
        return $response;
    }

    private function logout()
    {
        $response = $this->delete('/admin/login');
        return $response;
    }

    /**
     * @test
     */
    public function test_admin_login_get_ok()
    {
        $response = $this->get('/admin/login');
        $response->assertOk();
    }

    /**
     * @test
     */
    public function test_admin_login_post_ok_redirect()
    {
        $response = $this->login();

        // リダイレクトの確認
        $response->assertRedirect('/admin/admin-management');
    }

    /**
     * @test
     */
    public function test_admin_login_post_ok_authenticated_as_expected_user()
    {
        $this->login();

        // 認証されたユーザーが期待するユーザー名を持っているか確認
        $this->assertAuthenticatedAs(Admin::where('username', 'testAdmin')->first(), 'admin');
    }

    /**
     * @test
     */
    public function test_admin_login_post_ok_session_regenerated()
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
    public function test_admin_login_post_ok_admin_log()
    {
        $this->login();

        // ログが記録されているか確認
        $loginUser = Auth::guard('admin')->user();
        $this->assertDatabaseHas('admin_logs', ['admin_id' => $loginUser->id]);
    }

    /**
     * @test
     */
    public function test_admin_login_delete_ok()
    {
        $this->login();
        $this->logout();

        //ユーザーがゲスト状態（ログアウト状態）であるか確認
        $this->assertGuest('admin');
    }

    /**
     * @test
     */
    public function test_admin_login_delete_ok_redirect()
    {
        $this->login();
        $response = $this->logout();

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     */
    public function test_admin_login_delete_ok_session_invalidated()
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
    public function test_admin_login_delete_ok_session_regenerate()
    {
        $this->login();

        // セッショントークンの取得
        $tokenBeforeLogout = session('_token');

        $this->logout();

        // セッショントークンが再生成されていることを確認
        $tokenAfterLogout = session('_token');
        $this->assertNotSame($tokenBeforeLogout, $tokenAfterLogout);
    }


    /**
     * ログイン時の正常系バリデーションチェック
     */
    public static function data_admin_login_post_ok_validation_ok()
    {
        return [
            //必須チェック
            'Case: basic' => [
                'data' => [
                    'username' => 'testAdmin',
                    'password' => 'testAdmin',
                ]
            ]
        ];
    }

    /**
     * ログイン時の正常系エラーバリデーションチェック
     */
    public static function data_admin_login_post_ok_validation_normal_error()
    {
        $incorrectValue = 'a';

        return [
            //必須チェック
            'Case: missing_field' => [
                'data' => [],
                'expectedErrors' => [
                    'username' => 'ユーザー名は必ず指定してください。',
                    'password' => 'パスワードは必ず指定してください。',
                ]
            ],
            //必須項目が空文字
            'Case: null_required_field' => [
                'data' => [
                    'username' => '',
                    'password' => '',
                ],
                'expectedErrors' => [
                    'username' => 'ユーザー名は必ず指定してください。',
                    'password' => 'パスワードは必ず指定してください。',
                ]
            ],
            //ユーザー名は正しいが、パスワードが誤っている
            'Case: correct_username_and_incorrect_password' => [
                'data' => [
                    'username' => 'testAdmin',
                    'password' => $incorrectValue,
                ],
                'expectedErrors' => [
                    'failed' => 'ログイン情報が登録されていません。'
                ]
            ],
            //パスワードは正しいが、ユーザー名が誤っている
            'Case: correct_password_and_incorrect_username' => [
                'data' => [
                    'username' => $incorrectValue,
                    'password' => 'testAdmin',
                ],
                'expectedErrors' => [
                    'failed' => 'ログイン情報が登録されていません。'
                ]
            ],
            //ユーザー名とパスワード両方誤っている
            'Case: incorrect_username_and_password' => [
                'data' => [
                    'username' => $incorrectValue,
                    'password' => $incorrectValue,
                ],
                'expectedErrors' => [
                    'failed' => 'ログイン情報が登録されていません。'
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider data_admin_login_post_ok_validation_ok
     * ログイン時のバリデーションチェック(正常系)
     */
    public function test_admin_login_post_ok_validation_ok($data)
    {
        $this->get("/admin/login");
        $response = $this->post("/admin/login", $data);

        $response->assertStatus(302)->assertRedirect("/admin/admin-management");
        $response->assertSessionHasNoErrors();
    }

    /**
     * @test
     * @dataProvider data_admin_login_post_ok_validation_normal_error
     * ログイン時のバリデーションチェック（正常系エラー）
     */
    public function test_admin_post_ok_validation_normal_error($data, $expectedErrors)
    {
        $this->get("/admin/login");
        $response = $this->post("/admin/login", $data);

        $response->assertStatus(302)->assertRedirect("/admin/login");
        $response->assertSessionHasErrors($expectedErrors);
    }
}
