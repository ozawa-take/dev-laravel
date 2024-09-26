<?php

namespace Tests\Feature\Admin\UserManagement;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $adminUser = Admin::factory()->create([
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
        ]);

        // Adminユーザーとしてログインする
        $this->actingAs($adminUser, 'admin');

        $this->user = User::factory()->create([
            'username' => 'testUser',
            'password' => Hash::make('password1'),
            'mail_address' => 'testUser1@user.com',
        ]);
    }

    /**
     * @test
     * 未ログイン時ログイン画面にリダイレクトされることを確認
     **/
    public function test_unauthenticated_user_redirected_to_login()
    {
        // ログアウトして未ログイン状態にする
        auth()->logout();

        // 未ログインの状態でアクセス
        $response = $this->get('/admin/user-management');

        // ログイン画面にリダイレクトされることを確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * ユーザー管理画面へのアクセスが正常に行われることを確認
     **/
    public function test_admin_user_management_index_ok()
    {
        $response = $this->get('/admin/user-management');
        $response->assertStatus(200);
        $response->assertViewIs('admin.user-management.index');
    }

    /**
     * @test
     * ユーザーの新規作成がエラーなく成功することを確認
     */
    public function test_admin_user_management_post_ok()
    {
        //モックデータを使用する
        $userData = [
            'username' => 'newtestUser',
            'password' => 'newpassword1',
            'mail_address' => 'newtestUser1@user.com',
        ];

        // storeメソッドを呼び出してユーザーを作成
        $response = $this->post('/admin/user-management', $userData);

        // データベースにユーザーが作成されたか確認
        $this->assertDatabaseHas('users', [
            'username' => $userData['username'],
            'mail_address' => $userData['mail_address'],
        ]);

        // パスワードがHash化されているか確認
        $user = User::where('username', $userData['username'])->first();
        $this->assertTrue(Hash::check($userData['password'], $user->password));

        $response->assertRedirect('/admin/user-management')->assertStatus(302);

        // セッションにメッセージが保存されているか確認
        $this->assertNotNull(session('message'));

        // セッションのメッセージが期待通りのものか確認
        $this->assertEquals($userData['username'] . 'を登録しました', session('message'));
    }

    /**
     * @test
     * ユーザーの新規作成画面が正常に表示されることを確認
     */
    public function test_admin_user_management_create_get_ok()
    {
        $response = $this->get('/admin/user-management/create');
        $response->assertStatus(200);
        $response->assertViewIs('admin.user-management.create');
    }

    /**
     * @test
     * 検索機能が正常に行われることを確認
     */
    public function test_admin_user_management_search_post_ok()
    {
        $response = $this->post('/admin/user-management/search', ['name' => $this->user->username]);

        // 正しいJSON構造を持っていることを確認
        $response->assertJsonStructure([
            '*' => [
                'id',
                'username',
                'mail_address',
                'deleted_at',
                'created_at',
                'updated_at',
            ],
        ]);

        // 正しいデータが含まれていることを確認
        $response->assertJsonFragment([
            'id' => $this->user->id,
            'username' => $this->user->username,
            'mail_address' => $this->user->mail_address,
        ]);
        $response->assertStatus(200);
    }

    /**
     * @test
     * 更新が正常に行われることを確認
     */
    public function test_admin_user_management_update_patch_ok()
    {
        $newUsername = 'NewUsername';
        $newMailAddress = 'newtestUser1@user.com';

        $response = $this->patch("/admin/user-management/{$this->user->id}", [
            'username' => $newUsername,
            'mail_address' => $newMailAddress,
        ]);
        $response->assertRedirect('/admin/user-management')->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'username' => $newUsername,
            'mail_address' => $newMailAddress,
        ]);

        // 更新メッセージがセッションに存在することを確認
        $this->assertNotNull(session('message'));
    }

    /**
     * @test
     * 削除が正常に行われることを確認
     */
    public function test_admin_user_management_destroy_delete_ok()
    {
        $response = $this->delete("/admin/user-management/{$this->user->id}", ['id' => $this->user->id]);
        $response->assertRedirect('/admin/user-management')->assertStatus(302);

        // 論理削除の確認
        $this->assertSoftDeleted('users', ['id' => $this->user->id]);

        // deleted_at カラムが適切に設定されていることを確認
        $this->assertNotNull($this->user->fresh()->deleted_at);

        // 削除メッセージがセッションに存在することを確認
        $this->assertNotNull(session('danger'));
    }

    /**
     * @test
     * ユーザーの編集画面が正常に表示されることを確認
     */
    public function test_admin_user_management_edit_get_ok()
    {
        $response = $this->get("/admin/user-management/{$this->user->id}/edit");
        $response->assertStatus(200);
    }

    /**
     *
     *  ユーザーアカウントの更新後の送信リクエスト_正常系バリデーションチェック
     */
    public static function data_admin_user_management_patch_ok_update_validation_ok()
    {
        return [
            //基本系
            'Case: basic' => [
                'data' => [
                    'username' => 'testUser',
                    'mail_address' => 'testUser1@user.com',
                ]
            ],
            // //最小
            'Case: min' => [
                'data' => [
                    'username'    => 'a',
                    'mail_address'     => 'b',
                ]
            ],
            //最大
            'Case: max' => [
                'data' => [
                    'username'    => str_repeat('a', 255),
                    'mail_address'     => str_repeat('b', 255),
                ]
            ],
            //文字数最大(日本語)
            'Case: max_ja' => [
                'data' => [
                    'username'    => str_repeat('あ', 255),
                    'mail_address' => 'testUser1@user.com',
                ]
            ],
        ];
    }

     /**
     *
     * ユーザーアカウントの更新後の送信リクエスト_正常系エラーバリデーションチェック
     */
    public static function data_admin_user_management_patch_ok_update_validation_normal_error()
    {
        return [
            //必須チェック
            'Case: missing_field' => [
                'data' => [],
                'expectedErrors' => [
                    'username'    => 'ユーザーIDは必ず指定してください。',
                    'mail_address'     => 'メールアドレスは必ず指定してください。',
                ]
            ],
            //必須項目が空文字
            'Case: null_required_field' => [
                'data' => [
                    'username'    => '',
                    'mail_address'     => '',
                ],
                'expectedErrors' => [
                    'username'    => 'ユーザーIDは必ず指定してください。',
                    'mail_address'     => 'メールアドレスは必ず指定してください。',
                ]
            ],
            //最大文字数超過
            'Case: over_max_words' => [
                'data' => [
                    'username'    => str_repeat('a', 256),
                    'mail_address'     => str_repeat('b', 256),
                ],
                'expectedErrors' => [
                    'username' => 'ユーザーIDは、255文字以下で指定してください。',
                    'mail_address'  => 'メールアドレスは、255文字以下で指定してください。',
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider data_admin_user_management_patch_ok_update_validation_ok
     */
    public function test_admin_user_management_patch_ok_validation($data)
    {

        //ユーザー情報変更画面にアクセス
        $response = $this->get("/admin/user-management/{$this->user->id}/edit");

        //ユーザー情報変更処理実行後、ユーザー管理画面にリダイレクトする
        $response = $this->patch("/admin/user-management/{$this->user->id}",$data);
        $response->assertRedirect('/admin/user-management');
        $response->assertSessionHasNoErrors();
    }

    /**
     * @test
     * @dataProvider data_admin_user_management_patch_ok_update_validation_normal_error
     */
    public function test_admin_user_management_patch_ok_validation_normal_error($data,$expectedErrors)
    {
        //ユーザー情報変更画面にアクセス
        $response = $this->get("/admin/user-management/{$this->user->id}/edit");

        //ユーザー情報変更処理実行後、ユーザー情報変更画面にリダイレクトする
        $response = $this->patch("/admin/user-management/{$this->user->id}",$data);
        $response->assertStatus(302)->assertRedirect("/admin/user-management/{$this->user->id}/edit");
        $response->assertSessionHasErrors($expectedErrors);
    }

    /**
     *
     *  ユーザーアカウント新規作成の送信リクエスト_正常系バリデーションチェック
     */
    public static function data_admin_user_management_post_ok_store_validation_ok()
    {
        return [
            //基本系
            'Case: basic' => [
                'data' => [
                    'username' => 'testNewUser',
                    'password' => 'testUser',
                    'mail_address' => 'testUser1@user.com',
                ]
            ],
            // //最小
            'Case: min' => [
                'data' => [
                    'username'    => 'a',
                    'password' => 'c',
                    'mail_address'     => 'b',
                ]
            ],
            //最大
            'Case: max' => [
                'data' => [
                    'username'    => str_repeat('a', 255),
                    'password' => str_repeat('c', 255),
                    'mail_address'     => str_repeat('b', 255),
                ]
            ],
            //文字数最大(日本語)
            'Case: max_ja' => [
                'data' => [
                    'username'    => str_repeat('あ', 255),
                    'password' => 'testUser',
                    'mail_address' => 'testUser1@user.com',
                ]
            ],
        ];
    }

     /**
     *
     * ユーザーアカウント新規作成の送信リクエスト_正常系エラーバリデーションチェック
     */
    public static function data_admin_user_management_post_ok_store_validation_normal_error()
    {
        return [
            //必須チェック
            'Case: missing_field' => [
                'data' => [],
                'expectedErrors' => [
                    'username'    => 'ユーザーIDは必ず指定してください。',
                    'password' => 'パスワードは必ず指定してください。',
                    'mail_address'     => 'メールアドレスは必ず指定してください。',
                ]
            ],
            //必須項目が空文字
            'Case: null_required_field' => [
                'data' => [
                    'username'    => '',
                    'mail_address'     => '',
                ],
                'expectedErrors' => [
                    'username'    => 'ユーザーIDは必ず指定してください。',
                    'password' => 'パスワードは必ず指定してください。',
                    'mail_address'     => 'メールアドレスは必ず指定してください。',
                ]
            ],
            //最大文字数超過
            'Case: over_max_words' => [
                'data' => [
                    'username'    => str_repeat('a', 256),
                    'password' => str_repeat('c', 256),
                    'mail_address'     => str_repeat('b', 256).'@a',
                ],
                'expectedErrors' => [
                    'username' => 'ユーザーIDは、255文字以下で指定してください。',
                    'password' => 'パスワードは、255文字以下で指定してください。',
                    'mail_address'  => 'メールアドレスは、255文字以下で指定してください。',
                ]
            ],
            //ユーザーIDが重複
            'Case: not_unique_username' => [
                'data' => [
                    'username'    => 'testUser',
                    'password' => 'testUSer',
                    'mail_address' => 'testUser1@user.com',
                ],
                'expectedErrors' => [
                    'username'    => 'ユーザーIDの値は既に存在しています。',
                ]
            ],

        ];
    }

    /**
     * @test
     * @dataProvider data_admin_user_management_post_ok_store_validation_ok
     */
    public function test_admin_user_management_post_ok_store_validation_ok($data)
    {

        //ユーザー新規追加画面にアクセス
        $response = $this->get('/admin/user-management/create');

        //ユーザーの新規追加時、ユーザー管理画面にリダイレクトする
        $response = $this->post('/admin/user-management',$data);
        $response->assertRedirect('/admin/user-management');
        $response->assertSessionHasNoErrors();
    }

    /**
     * @test
     * @dataProvider data_admin_user_management_post_ok_store_validation_normal_error
     */
    public function test_admin_user_management_post_ok_store_validation_normal_error($data,$expectedErrors)
    {
        //ユーザー新規追加画面にアクセス
        $response = $this->get('/admin/user-management/create');

        //ユーザーの新規追加時、ユーザー新規追加画面にリダイレクトする
        $response = $this->post('/admin/user-management',$data);
        $response->assertStatus(302)->assertRedirect('/admin/user-management/create');
        $response->assertSessionHasErrors($expectedErrors);
    }
}
