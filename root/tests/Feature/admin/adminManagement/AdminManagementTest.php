<?php

namespace Tests\Feature\Admin\AdminManagementTest;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use DOMDocument;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->createAdmin();
    }

    private function createAdmin()
    {
        Admin::factory()->create([
            'username' => 'testSystemAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testSystemAdmin@admin.com',
            'is_system_admin' => True,
        ]);
        for ($i = 1; $i <= 10; $i++) {
            Admin::factory()->create([
                'username' => 'testAdmin' . $i,
                'password' => Hash::make('testAdmin'),
                'mail_address' => 'testAdmin@admin.com',
                'is_system_admin' => False,
            ]);
        }
    }

    private function loginAdmin()
    {
        $this->post('/admin/login', [
            'username' => 'testAdmin1',
            'password' => 'testAdmin',
        ]);
    }

    private function loginSystemAdmin()
    {
        $this->post('/admin/login', [
            'username' => 'testSystemAdmin',
            'password' => 'testAdmin',
        ]);
    }

    private function postNewAdmin()
    {
        $response = $this->post('/admin/admin-management', [
            'username' => 'createdAdmin',
            'password' => 'testAdmin',
            'mail_address' => 'createdAdmin@admin.com',
        ]);
        return $response;
    }
    private function patchEditAdmin()
    {
        $updateAdmin = Admin::where('username', 'testAdmin1')->first();
        $response = $this->patch("/admin/admin-management/{$updateAdmin->id}", [
            'username' => 'updatedAdmin',
            'mail_address' => 'updatedAdmin@admin.com',
        ]);
        return $response;
    }

    /**
     * @test
     */
    public function test_admin_admin_management_get_ok()
    {
        //アドミン管理画面にアクセスできる
        $this->loginAdmin();
        $response = $this->get('/admin/admin-management');
        $response->assertOk();
    }

    /**
     * @test
     */
    public function test_admin_admin_management_get_ok_unauthenticated()
    {
        //ログアウト中のアクセス時、ログインページにリダイレクトする
        $response = $this->get('/admin/admin-management');
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     */
    public function test_admin_admin_management_get_ok_no_button()
    {
        //通常の管理者としてログイン中、追加ボタンと削除ボタンが表示されない
        $this->loginAdmin();
        $response = $this->get('/admin/admin-management');
        $response->assertDontSee('追加');
        $response->assertDontSee('削除');
    }

    /**
     * @test
     */
    public function test_admin_admin_management_get_ok_system_see_button()
    {
        //システム管理者としてログイン中、追加ボタンと削除ボタン(通常管理者の人数分)が表示される
        $this->loginSystemAdmin();
        $response = $this->get('/admin/admin-management');
        $response->assertSee('追加');
        $response->assertSee('削除', 10);
    }

    /**
     * @test
     */
    public function test_admin_admin_management_get_ok_button_action()
    {
        //アドミン管理画面に存在するaタグがすべて正常に遷移する
        $this->loginSystemAdmin();
        $response = $this->get('/admin/admin-management');
        $html = $response->getContent();
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $urls = [];
        foreach ($dom->getElementsByTagName('a') as $a) {
            $urls[] = $a->getAttribute('href');
        }
        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertOk();
        }
    }

    /**
     * @test
     */
    public function test_admin_admin_management_create_get_ok()
    {
        //管理者新規追加画面にアクセスできる
        $this->loginSystemAdmin();
        $response = $this->get('/admin/admin-management/create');
        $response->assertOk();
    }

    /**
     * @test
     */
    public function test_admin_admin_management_create_get_ok_unauthenticated()
    {
        //ログアウト中のアクセス時、ログインページにリダイレクトする
        $response = $this->get('/admin/admin-management/create');
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     */
    public function test_admin_admin_management_post_ok()
    {
        //管理者の新規追加時、アドミン管理画面にリダイレクトする
        $this->loginSystemAdmin();
        $response = $this->postNewAdmin();
        $response->assertRedirect('/admin/admin-management');
    }

    /**
     * @test
     */
    public function test_admin_admin_management_post_ok_db_add()
    {
        //管理者追加機能で追加したデータがDBに反映されている
        $this->loginSystemAdmin();
        $this->postNewAdmin();
        $this->assertDatabaseHas('admins', [
            'username' => 'createdAdmin',
        ]);
    }


    /**
     * @test
     */
    public function test_admin_admin_management_edit_get_ok()
    {
        //アドミン情報変更画面にアクセスできる
        $this->loginAdmin();
        $response = $this->get('/admin/admin-management/edit');
        $response->assertOk();
    }

    /**
     * @test
     */
    public function test_admin_admin_management_edit_get_ok_unauthenticated()
    {
        //ログアウト中のアクセス時、ログインページにリダイレクトする
        $response = $this->get('/admin/admin-management/edit');
        $response->assertRedirect('/admin/login');
    }


    /**
     * @test
     */
    public function test_admin_admin_management_patch_ok()
    {
        //アドミン情報変更処理実行後、アドミン管理画面にリダイレクトする
        $this->loginAdmin();
        $response = $this->patchEditAdmin();
        $response->assertRedirect('/admin/admin-management');
    }


    /**
     * @test
     */
    public function test_admin_admin_management_patch_ok_db_change()
    {
        //変更されたデータがDBに反映されている
        $this->loginAdmin();
        $this->patchEditAdmin();
        $this->assertDatabaseHas('admins', [
            'username' => 'updatedAdmin',
        ]);
    }


    /**
     * @test
     */
    public function test_admin_admin_management_delete_ok()
    {
        //アドミン削除機能実行後、アドミン管理画面にリダイレクトする
        $this->loginSystemAdmin();
        $deleteAdmin = Admin::where('username', 'testAdmin1')->first();
        $response = $this->delete("/admin/admin-management/{$deleteAdmin->id}");
        $response->assertRedirect('/admin/admin-management');
    }


    /**
     * @test
     */
    public function test_admin_admin_management_delete_ok_db_delete()
    {
        //アドミン削除機能実行後、該当のデータがDB内で論理削除されている
        $this->loginSystemAdmin();
        $deleteAdmin = Admin::where('username', 'testAdmin1')->first();
        $this->delete("/admin/admin-management/{$deleteAdmin->id}");
        $this->assertSoftDeleted('admins', [
            'id' => $deleteAdmin->id,
        ]);
    }


    /**
     * @test
     */
    public function test_admin_admin_management_search_post_ok()
    {
        //検索機能実行時、jsonデータが正しい構造で返ってくる
        $this->loginSystemAdmin();
        $response = $this->post('/admin/admin-management/search', ['name' => 'testSystemAdmin']);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'username',
                'mail_address',
                'is_system_admin',
                'login_at',
                'deleted_at',
                'created_at',
                'updated_at',
            ],
        ]);
    }


    /**
     * @test
     */
    public function test_admin_admin_management_search_post_ok_just()
    {
        //部分一致検索を実行したとき、該当するデータがすべて含まれている
        $this->loginSystemAdmin();
        $response = $this->post('/admin/admin-management/search', ['name' => 'testAdmin']);
        $results = Admin::where('username', 'like', '%testAdmin%')->get()->toArray();
        foreach ($results as $result) {
            $response->assertJsonFragment([
                'id' => $result['id'],
                'username' => $result['username'],
                'mail_address' => $result['mail_address'],
                'is_system_admin' => $result['is_system_admin'],
            ]);
        }
    }

        /**
     *
     *  管理者アカウントの更新後の送信リクエスト_正常系バリデーションチェック
     */
    public static function data_admin_management_patch_ok_update_validation_ok()
    {
        return [
            //基本系
            'Case: basic' => [
                'data' => [
                    'username' => 'createdAdmin',
                    'mail_address' => 'createdAdmin@admin.com',
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
                    'mail_address'     => 'createdAdmin@admin.com',
                ]
            ],
        ];
    }

     /**
     *
     * 管理者アカウントの更新後の送信リクエスト_正常系エラーバリデーションチェック
     */
    public static function data_admin_management_patch_ok_update_validation_normal_error()
    {
        return [
            //必須チェック
            'Case: missing_field' => [
                'data' => [],
                'expectedErrors' => [
                    'username'    => '管理者IDは必ず指定してください。',
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
                    'username'    => '管理者IDは必ず指定してください。',
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
                    'username' => '管理者IDは、255文字以下で指定してください。',
                    'mail_address'  => 'メールアドレスは、255文字以下で指定してください。',
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider data_admin_management_patch_ok_update_validation_ok
     */
    public function test_admin_management_patch_ok_validation($data)
    {

        $this->loginAdmin();
        //アドミン情報変更画面にアクセス
        $response = $this->get('/admin/admin-management/edit');

        //アドミン情報変更処理実行後、アドミン管理画面にリダイレクトする
        $updateAdmin = Admin::where('username', 'testAdmin1')->first();
        $response = $this->patch("/admin/admin-management/{$updateAdmin->id}",$data);
        $response->assertRedirect('/admin/admin-management');
        $response->assertSessionHasNoErrors();
    }

    /**
     * @test
     * @dataProvider data_admin_management_patch_ok_update_validation_normal_error
     */
    public function test_admin_admin_management_patch_ok_validation_normal_error($data,$expectedErrors)
    {
        $this->loginAdmin();
        //アドミン情報変更画面にアクセス
        $response = $this->get('/admin/admin-management/edit');

        //アドミン情報変更処理実行後、アドミン情報変更画面にリダイレクトする
        $updateAdmin = Admin::where('username', 'testAdmin1')->first();
        $response = $this->patch("/admin/admin-management/{$updateAdmin->id}",$data);
        $response->assertStatus(302)->assertRedirect('/admin/admin-management/edit');
        $response->assertSessionHasErrors($expectedErrors);
    }

    /**
     *
     *  管理者アカウント新規作成の送信リクエスト_正常系バリデーションチェック
     */
    public static function data_admin_management_post_ok_store_validation_ok()
    {
        return [
            //基本系
            'Case: basic' => [
                'data' => [
                    'username' => 'createdAdmin',
                    'password' => 'testAdmin',
                    'mail_address' => 'createdAdmin@admin.com',
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
                    'password' => 'testAdmin',
                    'mail_address'     => 'createdAdmin@admin.com',
                ]
            ],
        ];
    }

     /**
     *
     * 管理者アカウント新規作成の送信リクエスト_正常系エラーバリデーションチェック
     */
    public static function data_admin_management_post_ok_store_validation_normal_error()
    {
        return [
            //必須チェック
            'Case: missing_field' => [
                'data' => [],
                'expectedErrors' => [
                    'username'    => '管理者IDは必ず指定してください。',
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
                    'username'    => '管理者IDは必ず指定してください。',
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
                    'username' => '管理者IDは、255文字以下で指定してください。',
                    'password' => 'パスワードは、255文字以下で指定してください。',
                    'mail_address'  => 'メールアドレスは、255文字以下で指定してください。',
                ]
            ],
            //管理者IDが重複
            'Case: not_unique_username' => [
                'data' => [
                    'username'    => 'testAdmin1',
                    'password' => 'testAdmin',
                    'mail_address' => 'createdAdmin@admin.com',
                ],
                'expectedErrors' => [
                    'username'    => '管理者IDの値は既に存在しています。',
                ]
            ],

        ];
    }

    /**
     * @test
     * @dataProvider data_admin_management_post_ok_store_validation_ok
     */
    public function test_admin_management_post_ok_store_validation_ok($data)
    {

        $this->loginSystemAdmin();
        //管理者新規追加画面にアクセス
        $response = $this->get('/admin/admin-management/create');

        //管理者の新規追加時、アドミン管理画面にリダイレクトする
        $response = $this->post('/admin/admin-management',$data);
        $response->assertRedirect('/admin/admin-management');
        $response->assertSessionHasNoErrors();
    }

    /**
     * @test
     * @dataProvider data_admin_management_post_ok_store_validation_normal_error
     */
    public function test_admin_management_post_ok_store_validation_normal_error($data,$expectedErrors)
    {
        $this->loginSystemAdmin();
        //管理者新規追加画面にアクセス
        $response = $this->get('/admin/admin-management/create');

        //管理者の新規追加時、管理者新規追加画面にリダイレクトする
        $response = $this->post('/admin/admin-management',$data);
        $response->assertStatus(302)->assertRedirect('/admin/admin-management/create');
        $response->assertSessionHasErrors($expectedErrors);
    }
}
