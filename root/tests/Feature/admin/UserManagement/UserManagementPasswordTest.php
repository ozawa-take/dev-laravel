<?php

namespace Tests\Feature\Admin\UserManagement;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;

class UserManagementPasswordTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $admin;

    /**
     * テスト用のユーザー作成
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
        ]);

        $this->user = User::factory()->create([
            'username' => 'testUser',
            'password' => Hash::make('old_password'),
            'mail_address' => 'testUser1@user.com',
        ]);
    }

    /**
     * パスワード変更メソッド
     */
    private function changePassword()
    {
        return $this->post("/admin/user-management/{$this->user->id}/password", [
            'password' => 'old_password',
            'new_password' => 'new_password',
            'new_password_confirmation' => 'new_password',
        ]);
    }

    /**
     * @test
     * 管理者がユーザーのパスワード管理画面に正常にアクセスできることを確認する
     */
    public function test_admin_user_management_password_get_ok()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->get("/admin/user-management/{$this->user->id}/password");
        $response->assertOk();
    }

    /**
     * 管理者が未ログイン時にユーザーのパスワード管理画面にアクセスできないことを確認する
     * @test
     */
    public function test_admin_user_management_password_get_ok_redirect_without_login()
    {
        $response = $this->get("/admin/user-management/{$this->user->id}/password");

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * ユーザーのパスワードが正常に変更されることを確認する
     */
    public function test_admin_user_management_password_post_ok_change_password()
    {
        $this->actingAs($this->admin, 'admin');
        $this->changePassword();

        //変更後のパスワードが期待される値になっているかの確認
        $this->assertTrue(Hash::check('new_password', $this->user->fresh()->password));
    }

    /**
     * @test
     * パスワード変更処理が完了するとユーザー管理画面へとリダイレクトすることを確認する
     */
    public function test_admin_user_management_password_post_ok_redirect()
    {
        $this->actingAs($this->admin, 'admin');
        $response = $this->changePassword();

        $response->assertRedirect('/admin/user-management/');
    }

    /**
     * @test
     * リダイレクト先のユーザー管理画面で「パスワードが変更されました」の表示が出力されることを確認する
     */
    public function test_admin_user_management_password_post_ok_redirect_message()
    {
        $this->actingAs($this->admin, 'admin');
        $response = $this->changePassword();

        $response->assertSessionHas('message', 'パスワードが変更されました');
    }

    /**
     *
     *  管理者がユーザーアカウントのパスワード更新後の送信リクエスト_正常系バリデーションチェック
     */
    public static function data_admin_user_password_post_ok_update_validation_ok()
    {
        return [
            //基本系
            'Case: basic' => [
                'data' => [
                    'password' => 'old_password',
                    'new_password' => 'new_password',
                    'new_password_confirmation' => 'new_password',
                ]
            ],
            // //最小
            'Case: min' => [
                'data' => [
                    'password' => 'old_password',
                    'new_password' => 'b',
                    'new_password_confirmation' => 'b'
                ]
            ],
            //最大
            'Case: max' => [
                'data' => [
                    'password' => 'old_password',
                    'new_password'     => str_repeat('b', 255),
                    'new_password_confirmation' => str_repeat('b', 255),
                ]
            ],
        ];
    }

     /**
     *
     * 管理者がユーザーアカウントのパスワード更新後の送信リクエスト_正常系エラーバリデーションチェック
     */
    public static function data_admin_user_password_post_ok_update_validation_normal_error()
    {
        return [
            //必須チェック
            'Case: missing_field' => [
                'data' => [],
                'expectedErrors' => [
                    'password' => 'パスワードは必ず指定してください。',
                    'new_password' => '新しいパスワードは必ず指定してください。',
                ]
            ],
            //必須項目が空文字
            'Case: null_required_field' => [
                'data' => [
                    'password' => '',
                    'new_password' => '',
                    'new_password_confirmation' => ''
                ],
                'expectedErrors' => [
                    'password' => 'パスワードは必ず指定してください。',
                    'new_password' => '新しいパスワードは必ず指定してください。',
                ]
            ],
            //最大文字数超過
            'Case: over_max_words' => [
                'data' => [
                    'password'    => str_repeat('a', 256),
                    'new_password'     => str_repeat('b', 256),
                    'new_password_confirmation' => str_repeat('b', 256),
                ],
                'expectedErrors' => [
                    'password' => 'パスワードは、255文字以下で指定してください。',
                    'new_password'  => '新しいパスワードは、255文字以下で指定してください。',
                ]
            ],
            //新しいパスワードとの確認が不一致
            'Case: not_confirm_password' => [
                'data' => [
                    'password' => 'old_password',
                    'new_password' => 'new_password',
                    'new_password_confirmation' => 'missing_new_password',
                ],
                'expectedErrors' => [
                    'new_password' => '新しいパスワードと、パスワードの確認が、一致していません。',
                ]
            ],
        ];
    }

        /**
     * @test
     * @dataProvider data_admin_user_password_post_ok_update_validation_ok
     */
    public function test_admin_user_password_post_ok_update_validation_ok($data)
    {
        //管理者がユーザーのパスワード変更画面にアクセスできる
        $this->actingAs($this->admin, 'admin');
        $response = $this->get("/admin/user-management/{$this->user->id}/password");
        //パスワード変更後にアドミン管理画面にリダイレクトされる
        $response =  $this->post("/admin/user-management/{$this->user->id}/password", $data);
        $response->assertRedirect('admin/user-management');
        $response->assertSessionHasNoErrors();
    }

     /**
     * @test
     * @dataProvider data_admin_user_password_post_ok_update_validation_normal_error
     */
    public function test_admin_user_password_post_ok_update_validation_normal_error($data,$expectedErrors)
    {
        //管理者がユーザーのパスワード変更画面にアクセスできる
        $this->actingAs($this->admin, 'admin');
        $response = $this->get("/admin/user-management/{$this->user->id}/password");
        //パスワード変更後にパスワード変更画面にリダイレクトされる
        $response =  $this->post("/admin/user-management/{$this->user->id}/password",$data);
        $response->assertStatus(302)->assertRedirect("/admin/user-management/{$this->user->id}/password");
        $response->assertSessionHasErrors($expectedErrors);
    }
}
