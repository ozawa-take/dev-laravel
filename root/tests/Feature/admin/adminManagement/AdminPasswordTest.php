<?php

namespace Tests\Feature\Admin\Login;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminPasswordTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
    }

    private function createAdmin()
    {
        Admin::factory()->create([
            'id' => 120001,
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
        ]);
    }

    private function postNewPassword()
    {
        $response = $this->post('/admin/admin-management/120001/password', [
            'password' => 'testAdmin',
            'new_password' => 'changedPassword',
            'new_password_confirmation' => 'changedPassword'
        ]);
        return $response;
    }

    private function login()
    {
        $this->post('/admin/login', [
            'username' => 'testAdmin',
            'password' => 'testAdmin',
        ]);
    }

    /**
     * @test
     */
    public function test_admin_admin_management_password_get_ok()
    {
        //パスワード変更画面にアクセスできる
        $this->login();
        $response = $this->get('/admin/admin-management/120001/password');
        $response->assertOk();
    }

    /**
     * @test
     */
    public function test_admin_admin_management_password_get_ok_unauthenticated()
    {
        //ログアウト時にパスワード変更画面にアクセスしたときログインページにリダイレクトする
        $response = $this->get('/admin/admin-management/120001/password');
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     */
    public function test_admin_admin_management_password_post_ok()
    {
        //パスワード変更後にアドミン管理画面にリダイレクトされる
        $this->login();
        $response = $this->postNewPassword();
        $response->assertRedirect('admin/admin-management');
    }

    /**
     * @test
     */
    public function test_admin_admin_management_password_post_ok_db_change()
    {
        //データベース内のデータが変更されている
        $this->login();
        $this->postNewPassword();
        $hashedPassword = Admin::find(120001)->password;
        $this->assertTrue(Hash::check('changedPassword', $hashedPassword));
    }

    /**
     * @test
     */
    public function test_admin_admin_management_password_post_ok_login()
    {
        //新しいパスワードでログインし、管理者ログイン画面にアクセスできる
        $this->login();
        $response = $this->postNewPassword();
        $this->delete('admin/login'); //ログアウト
        $this->post('/admin/login', [
            'username' => 'testAdmin',
            'password' => 'changedPassword',
        ]); //新しいパスワードでログイン
        $response = $this->get('/admin/admin-management');
        $response->assertOk(); //アドミン管理画面にアクセスできる
    }

    /**
     *
     *  管理者アカウントのパスワード更新後の送信リクエスト_正常系バリデーションチェック
     */
    public static function data_admin_password_post_ok_update_validation_ok()
    {
        return [
            //基本系
            'Case: basic' => [
                'data' => [
                    'password' => 'testAdmin',
                    'new_password' => 'changedPassword',
                    'new_password_confirmation' => 'changedPassword'
                ]
            ],
            // //最小
            'Case: min' => [
                'data' => [
                    'password' => 'testAdmin',
                    'new_password' => 'b',
                    'new_password_confirmation' => 'b'
                ]
            ],
            //最大
            'Case: max' => [
                'data' => [
                    'password' => 'testAdmin',
                    'new_password'     => str_repeat('b', 255),
                    'new_password_confirmation' => str_repeat('b', 255),
                ]
            ],
        ];
    }

     /**
     *
     * 管理者アカウントのパスワード更新後の送信リクエスト_正常系エラーバリデーションチェック
     */
    public static function data_admin_password_post_ok_update_validation_normal_error()
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
                    'password' => 'testAdmin',
                    'new_password' => 'changedPassword',
                    'new_password_confirmation' => 'missingChangedPassword'
                ],
                'expectedErrors' => [
                    'new_password' => '新しいパスワードと、パスワードの確認が、一致していません。',
                ]
            ],
            //現在のパスワードと異なる
            'Case: not_current_password' => [
                'data' => [
                    'password' => 'a',
                    'new_password' => 'changedPassword',
                    'new_password_confirmation' => 'changedPassword'
                ],
                'expectedErrors' => [
                    'password' => 'パスワードが正しくありません。',
                ]
            ],
        ];
    }

        /**
     * @test
     * @dataProvider data_admin_password_post_ok_update_validation_ok
     */
    public function test_admin_password_post_ok_update_validation_ok($data)
    {
        //パスワード変更画面にアクセスできる
        $this->login();
        $response = $this->get('/admin/admin-management/120001/password');
        //パスワード変更後にアドミン管理画面にリダイレクトされる
        $response = $this->post('/admin/admin-management/120001/password',$data);
        $response->assertRedirect('admin/admin-management');
        $response->assertSessionHasNoErrors();
    }

     /**
     * @test
     * @dataProvider data_admin_password_post_ok_update_validation_normal_error
     */
    public function test_admin_password_post_ok_update_validation_normal_error($data,$expectedErrors)
    {
        //パスワード変更画面にアクセスできる
        $this->login();
        $response = $this->get('/admin/admin-management/120001/password');
        //パスワード変更後にパスワード変更画面にリダイレクトされる
        $response = $this->post('/admin/admin-management/120001/password',$data);
        $response->assertStatus(302)->assertRedirect('/admin/admin-management/120001/password');
        $response->assertSessionHasErrors($expectedErrors);
    }
}
