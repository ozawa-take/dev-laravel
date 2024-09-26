<?php

namespace Tests\Feature\admin\information;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Information;
use App\Models\Group;

class AdminInformationsTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $information;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@admin.com',
        ]);

        // ログイン
        $this->actingAs($this->admin, 'admin');

        $this->information = Information::factory()->create([
            'title' => 'testtitle',
            'text'  => 'testtext',
            'admin_id' => '120001',
        ]);
    }

    /**
     * @test
     * 未ログイン時にログイン画面へ遷移するか確認
     */
    public function
    test_unauthenticated_user_redirected_to_login()
    {
        // ログアウトして未ログイン状態にする
        auth()->logout();

        // 未ログインの状態でアクセス
        $response = $this->get('/admin/informations');

        // ログイン画面にリダイレクトされることを確認
        $response->assertRedirect('/admin/login');
    }

    /**
     * @test
     * お知らせ一覧画面へのアクセスが正常に行われるか確認
     */
    public function
    test_admin_informations_get_ok()
    {
        $response = $this->get('/admin/informations');
        $response->assertOk();
        $response->assertViewIs('admin.informations.index');
    }

    /**
     * @test
     * お知らせの新規作成がエラーなく成功するか確認
     */
    public function test_admin_informations_post_ok()
    {
        $information = [
            'title' => 'newtestTitle',
            'text' => 'newtestText',
            'group' => ['180001'],
        ];
        $response = $this->post('/admin/informations', $information);
        $this->assertDatabaseHas('information', [
            'title' => $information['title'],
            'text' => $information['text'],
        ]);
        $response->assertRedirect('/admin/informations/');
        $response->assertSessionHas('message', 'お知らせを登録しました');
    }

    /**
     * @test
     * お知らせの新規作成画面が正常に表示されているか確認
     */
    public function test_admin_informations_create_get_ok()
    {
        $response = $this->get('/admin/informations/create');
        $response->assertOk();
        $response->assertViewIs('admin.informations.create');
    }

    /**
     * @test
     * 作成したお知らせが表示されるか確認
     */
    public function test_admin_informations_show_get_ok()
    {
        $response = $this->get("/admin/informations/{$this->information->id}");
        $response->assertOk();
        $response->assertViewIs('admin.informations.show');
    }

    /**
     * @test
     * お知らせの更新が正常に行われるか確認
     */
    public function test_admin_informations_update_patch_ok()
    {
        $newTitle = 'NewTitle';
        $newText = 'NewText';
        $newGroup = ['180001'];
        $response = $this->patch("/admin/informations/{$this->information->id}", [
            'title' => $newTitle,
            'text' => $newText,
            'group' => $newGroup,
        ]);
        $response->assertRedirect('/admin/informations/');
        $this->assertDatabaseHas('information', [
            'id' => $this->information->id,
            'title' => $newTitle,
            'text' => $newText,
        ]);
        $this->assertNotNull(session('message'));
    }

    /**
     * @test
     * お知らせの削除が正常に行われるか確認
     */
    public function test_admin_informations_destroy_delete_ok()
    {
        $response = $this->delete("/admin/informations/{$this->information->id}", [
            'id' => $this->information->id
        ]);
        $response->assertRedirect('/admin/informations/');
        $this->assertSoftDeleted('information', ['id' => $this->information->id]);
        $this->assertNotNull($this->information->fresh()->deleted_at);
        $this->assertNotNull(session('danger'));
    }

    /**
     * @test
     * お知らせの編集画面が正常に表示されるか確認
     */
    public function test_admin_informations_edit_get_ok()
    {
        $response = $this->get("/admin/informations/{$this->information->id}/edit");
        $response->assertOk();
        $response->assertViewIs('admin.informations.edit');
    }

    /**
     * お知らせ新規作成 & 更新_正常系バリデーションチェック
     */
    public static function data_admin_informations_create_post_and_patch_ok_validation_ok()
    {
        $groupId = ['180001'];
        return [
            //基本系
            'Case: basic' => [
                'data' => [
                    'title' => 'Validation Test',
                    'text' => 'basic',
                    'group' => $groupId,
                ],
            ],
            //最小
            'Case: min' => [
                'data' => [
                    'title' => 'a',
                    'text' => 'b',
                    'group' => $groupId,
                ],
            ],
            //最大
            'Case: max' => [
                'data' => [
                    'title' => str_repeat('a', 255),
                    'text' => str_repeat('b', 500),
                    'group' => $groupId,
                ],
            ],
            //最大(日本語)
            'Case: max_ja' => [
                'data' => [
                    'title' => str_repeat('あ', 255),
                    'text' => str_repeat('い', 500),
                    'group' => $groupId,
                ],
            ],
            //必須のみ
            'Case: required_only' => [
                'data' => [
                    'title' => 'Validation Test',
                    'group' => $groupId,
                ],
            ]
        ];
    }

    /**
     * 新規作成 & 更新_正常系エラーバリデーションチェック
     */
    public static function data_admin_informations_create_post_and_patch_ok_validation_normal_error()
    {
        return [
            //必須チェック
            'Case: missing_field' => [
                'data' => [],
                'expectedErrors' => [
                    'title' => 'お知らせタイトルは必ず指定してください。',
                    'group' => '対象グループは必ず指定してください。',
                ]
            ],
            //必須項目が空文字
            'Case: null_required_field' => [
                'data' => [
                    'title' => '',
                    'group' => '',
                ],
                'expectedErrors' => [
                    'title' => 'お知らせタイトルは必ず指定してください。',
                    'group' => '対象グループは必ず指定してください。',
                ]
            ],
            //string指定のフィールドの値が文字列ではない
            'Case: not_string' => [
                'data' => [
                    'title' => 1,
                    'text' => 2,
                    'group' => ['180001'],
                ],
                'expectedErrors' => [
                    'title' => 'お知らせタイトルは文字列を指定してください。',
                    'text' => '本文は文字列を指定してください。',
                ]
            ],
            //最大文字数超過
            'Case: over_max_words' => [
                'data' => [
                    'title' => str_repeat('a', 256),
                    'text' => str_repeat('b', 501),
                    'group' => ['180001'],
                ],
                'expectedErrors' => [
                    'title' => 'お知らせタイトルは、255文字以下で指定してください。',
                    'text' => '本文は、500文字以下で指定してください。',
                ]
            ],
            //groupに渡す値が配列ではない
            'Case: not_array' => [
                'data' => [
                    'title' => 'Validation Test',
                    'text' => 'Validation Test',
                    'group' => 180001,
                ],
                'expectedErrors' => [
                    'group' => '対象グループは配列でなくてはなりません。'
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider data_admin_informations_create_post_and_patch_ok_validation_ok
     * お知らせ新規作成時のバリデーションチェック(正常系)
     */
    public function test_admin_informations_create_post_ok_validation_ok($data)
    {
        $this->actingAs($this->admin, 'admin');

        //新規作成画面に移動し、データをpost
        $this->get("/admin/informations/create");
        $response = $this->post("/admin/informations", $data);

        $response->assertStatus(302)->assertRedirect("/admin/informations");
        $response->assertSessionHasNoErrors();
    }

    /**
     * @test
     * @dataProvider data_admin_informations_create_post_and_patch_ok_validation_normal_error
     * お知らせ新規作成のバリデーションチェック(正常系エラー)
     */
    public function test_admin_informations_create_post_ok_validation_normal_error($data, $expectedErrors)
    {
        $this->actingAs($this->admin, 'admin');

        //新規作成画面に移動し、データをpost
        $this->get("admin/informations/create");
        $response = $this->post("admin/informations", $data);

        $response->assertStatus(302)->assertRedirect("/admin/informations/create");
        $response->assertSessionHasErrors($expectedErrors);
    }

    /**
     * @test
     * @dataProvider data_admin_informations_create_post_and_patch_ok_validation_ok
     * コンテンツ更新時のバリデーションチェック(正常系)
     */
    public function test_admin_informations_edit_patch_ok_validation_ok($data)
    {
        $this->actingAs($this->admin, 'admin');

        //編集画面に移動し、データをpatch
        $this->get("/admin/informations/{$this->information->id}/edit");
        $response = $this->patch("/admin/informations/{$this->information->id}", $data);

        $response->assertStatus(302)->assertRedirect("/admin/informations");
        $response->assertSessionHasNoErrors();
    }

    /**
     * @test
     * @dataProvider data_admin_informations_create_post_and_patch_ok_validation_normal_error
     * コンテンツ更新時のバリデーションチェック(正常系エラー)
     */
    public function test_admin_informations_patch_ok_validation_normal_errors($data, $expectedErrors)
    {
        $this->actingAs($this->admin, 'admin');

        //編集画面に移動し、データをpatch
        $this->get("admin/informations/{$this->information->id}/edit");
        $response = $this->patch("/admin/informations/{$this->information->id}", $data);

        $response->assertStatus(302)->assertRedirect("admin/informations/{$this->information->id}/edit");
        $response->assertSessionHasErrors($expectedErrors);
    }
}
