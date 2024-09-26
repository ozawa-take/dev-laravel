<?php

namespace Tests\Feature\Admin\Group;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;

class AdminGroupsTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $group;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'username' => 'testAdmin',
            'password' => Hash::make('testAdmin'),
            'mail_address' => 'testAdmin@test.com',
        ]);
    }

    // ユーザーのテストデータを作成
    private function createTestUsers()
    {
        return User::factory()->create([
            'username' => 'testUser',
            'password' => Hash::make('testUser'),
            'mail_address' => 'testUser@test.com',
        ]);
    }

    // グループのテストデータを作成
    private function createTestGroups()
    {
        return Group::factory()->create([
            'group_name' => 'Group',
            'remarks' => 'GroupRemark',
        ]);
    }

    //コンテンツのテストデータを作成
    private function createTestCourses()
    {
        return Course::factory()->create([
            'title' => 'Course',
        ]);
    }

    // グループとの関連付け
    private function createGroupWithRelations(): array
    {
        $user = $this->createTestUsers();
        $group = $this->createTestGroups();
        $course = $this->createTestCourses();

        $group->courses()->attach($course);
        $group->users()->attach($user);

        return compact('user', 'group', 'course');
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok()
    {
        // 管理者としてログインし、一覧画面を取得する
        $this->actingAs($this->admin, 'admin');

        $response = $this->get('/admin/groups/');
        $response->assertStatus(200)->assertViewIs('admin.groups.index');
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok_unauthenticated()
    {
        // ユーザーがゲスト状態（ログアウトの状態）で一覧を取得する
        $response = $this->get('/admin/groups/');

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect('/admin/login');
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok_get_sort_created()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        // 3つのグループを作成し、作成日時を設定する
        for ($i = 1; $i <= 3; $i++) {
            Group::factory()->create([
                'group_name' => 'Group' . $i,
                'remarks' => 'GroupRemark',
                'created_at' => now()->subDays($i),
            ]);
        }

        // グループ一覧を取得し、作成日時の順にグループが並んでいるか確認する
        $response = $this->get('/admin/groups/');
        $response->assertSeeInOrder(['Group1', 'Group2', 'Group3']);
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok_get_sort_updated()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        // 3つのグループを作成し、更新日時を設定する
        for ($i = 1; $i <= 3; $i++) {
            Group::factory()->create([
                'group_name' => 'Group' . $i,
                'remarks' => 'GroupRemark',
                'updated_at' => now()->subDays($i),
            ]);
        }

        // グループ一覧を取得し、更新日時の順にグループが並んでいるか確認する
        $response = $this->get('/admin/groups/');
        $response->assertSeeInOrder(['Group1', 'Group2', 'Group3']);
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok_details()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $relations = $this->createGroupWithRelations();

        // グループ詳細を取得し、特定のグループ、ユーザー、コースが表示されていることを確認する
        $response = $this->get("/admin/groups/{$relations['group']->id}/");
        $response
            ->assertStatus(200)
            ->assertViewIs('admin.groups.show')
            ->assertSee(['Group', 'GroupRemark', 'testUser', 'Course']);
    }

    /**
     * @test
     */
    public function test_admin_groups_get_ok_unauthenticated_details()
    {
        $relations = $this->createGroupWithRelations();

        // ユーザーがゲスト状態（ログアウトの状態）で詳細を取得する
        $response = $this->get("/admin/groups/{$relations['group']->id}/");

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect('/admin/login');
    }

    /**
     * @test
     */
    public function test_admin_groups_create_get_ok()
    {
        // 管理者としてログインし、新規作成画面を取得する
        $this->actingAs($this->admin, 'admin');

        $response = $this->get('/admin/groups/create/');
        $response->assertStatus(200)->assertViewIs('admin.groups.create');
    }

    /**
     * @test
     */
    public function test_admin_groups_create_get_ok_unauthenticated()
    {
        // ユーザーがゲスト状態（ログアウトの状態）で新規作成画面を取得する
        $response = $this->get('/admin/groups/create/');

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect('/admin/login');
    }

    /**
     * @test
     */
    public function test_admin_groups_post_ok_groups_store()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        // グループを新規作成
        $response = $this->post('/admin/groups/', [
            'group_name' => 'newGroup',
            'remarks' => 'newGroupRemark',
        ]);

        // 作成されたデータがデータベース内に存在することを確認する
        $this->assertDatabaseHas('groups', [
            'group_name' => 'newGroup',
            'remarks' => 'newGroupRemark',
        ]);

        // 新規作成後に正しいリダイレクトが行われていることを確認する。
        $response->assertStatus(302)->assertRedirect('admin/groups');

        // 新規作成された際に適切なメッセージが表示されている確認する。
        $response->assertSessionHas('message', 'newGroup' . 'を登録しました');
    }


    /**
     * @test
     */
    public function test_admin_groups_post_ok_store()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $user = $this->createTestUsers();
        $course = $this->createTestCourses();

        // グループを新規作成
        $response = $this->post('/admin/groups/', [
            'group_name' => 'newGroupName',
            'remarks' => 'newGroupRemark',
            'user' => [$user->id],
            'course' => [$course->id],
        ]);

        $newGroup = Group::where('group_name', 'newGroupName')->first();

        // 作成されたデータがデータベース内に存在することを確認する
        $this->assertDatabaseHas('groups', [
            'group_name' => 'newGroupName',
            'remarks' => 'newGroupRemark',
        ]);
        $this->assertDatabaseHas('groups_courses', [
            'group_id' => $newGroup->id,
            'course_id' => $course->id,
        ]);
        $this->assertDatabaseHas('users_groups', [
            'group_id' => $newGroup->id,
            'user_id' => $user->id,
        ]);

        // 新規作成後に正しいリダイレクトが行われていることを確認する。
        $response->assertStatus(302)->assertRedirect('admin/groups');

        // 新規作成された際に適切なメッセージが表示されている確認する。
        $response->assertSessionHas('message', 'newGroupName' . 'を登録しました');
    }

    /**
     * @test
     */
    public function test_admin_groups_edit_get_ok()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $group = $this->createTestGroups();

        // 編集画面を取得する
        $response = $this->get("/admin/groups/{$group->id}/edit");
        $response->assertStatus(200)->assertViewIs('admin.groups.edit');
    }

    /**
     * @test
     */
    public function test_admin_groups_edit_get_ok_unauthenticated()
    {
        $group = $this->createTestGroups();

        // ユーザーがゲスト状態（ログアウトの状態）で編集画面を取得する
        $response = $this->get("/admin/groups/{$group->id}/edit");

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect('/admin/login');
    }

    /**
     * @test
     */
    public function test_admin_groups_edit_get_ok_back_show()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $group = $this->createTestGroups();

        // showの場合、特定のグループへの戻るボタンへのリンクを生成する
        $response = $this->get("/admin/groups/{$group->id}/edit?show=show");
        $response->assertViewHas('backBtn', "http://localhost/admin/groups/{$group->id}");
    }

    /**
     * @test
     */
    public function test_admin_groups_edit_get_ok_back_index()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $group = $this->createTestGroups();

        // showでない場合、デフォルトの戻るボタンへのリンクを生成する
        $response = $this->get("/admin/groups/{$group->id}/edit");
        $response->assertViewHas('backBtn', 'http://localhost/admin/groups');
    }

    /**
     * @test
     */
    public function test_admin_groups_patch_ok_groups_update()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $group = $this->createTestGroups();

        // 新しいグループを作成
        $updateGroupName = 'updateGroupName';
        $updateGroupRemarks = 'updateGroupRemark';

        // グループを編集
        $response = $this->patch("/admin/groups/{$group->id}", [
            'group_name' => $updateGroupName,
            'remarks' => $updateGroupRemarks,
        ]);

        // 編集されたデータがデータベース内に存在することを確認する
        $this->assertDatabaseHas('groups', [
            'group_name' => $updateGroupName,
            'remarks' => $updateGroupRemarks,
        ]);

        // 編集後に正しいリダイレクトが行われていることを確認する。
        $response->assertStatus(302)->assertRedirect('admin/groups');

        // 編集された際に適切なメッセージが表示されている確認する。
        $response->assertSessionHas('message', $updateGroupName . 'を編集しました');
    }

    /**
     * @test
     */
    public function test_admin_groups_patch_ok_update()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $group = $this->createGroupWithRelations();

        // 新しいグループ、ユーザー、コースを作成
        $updateGroupName = 'updateGroupName';
        $updateGroupRemarks = 'updateGroupRemark';

        $updateUser = User::factory()->create([
            'username' => 'updateTestUser',
            'password' => Hash::make('updateTestUser'),
            'mail_address' => 'updateTestUser@test.com',
        ]);
        $updateCourse = Course::factory()->create([
            'title' => 'updateCourse',
        ]);

        // グループを編集
        $response = $this->patch("/admin/groups/{$group['group']->id}", [
            'group_name' => $updateGroupName,
            'remarks' => $updateGroupRemarks,
            'user' => $updateUser->id,
            'course' => $updateCourse->id,
        ]);

        $updateGroup = Group::where('group_name', $updateGroupName)->first();

        // 編集されたデータがデータベース内に存在することを確認する
        $this->assertDatabaseHas('groups', [
            'group_name' => $updateGroupName,
            'remarks' => $updateGroupRemarks,
        ]);
        $this->assertDatabaseHas('groups_courses', [
            'group_id' => $updateGroup->id,
            'course_id' => $updateCourse->id,
        ]);
        $this->assertDatabaseHas('users_groups', [
            'group_id' => $updateGroup->id,
            'user_id' => $updateUser->id,
        ]);

        // 編集後に正しいリダイレクトが行われていることを確認する。
        $response->assertStatus(302)->assertRedirect('admin/groups');

        // 編集された際に適切なメッセージが表示されている確認する。
        $response->assertSessionHas('message', $updateGroupName . 'を編集しました');
    }

    /**
     * @test
     */
    public function test_admin_groups_delete_ok_destroy()
    {
        // 管理者としてログイン
        $this->actingAs($this->admin, 'admin');

        $relations = $this->createGroupWithRelations();

        // 正しいリダイレクトが行われていることを確認
        $response = $this->delete("admin/groups/{$relations['group']->id}");
        $response->assertStatus(302)->assertRedirect('admin/groups');

        // 削除を実行し、グループが論理削除されているか確認
        $relations['group']->delete();
        $this->assertSoftDeleted($relations['group']);

        // deleted_at カラムが適切に設定されていることを確認
        $this->assertNotNull($relations['group']->fresh()->deleted_at);

        // 削除メッセージがセッションに存在することを確認
        $this->assertNotNull(session('danger'));
    }

    /**
     * グループ新規作成　& 更新_正常系バリデーションチェック
     */
    public static function data_admin_groups_create_post_and_patch_ok_validation()
    {
        return [
            //基本型
            'Case: basic' => [
                'data' => [
                    'group_name' => 'Validation Test',
                    'remarks' => 'basic',
                ]
            ],
            //最小
            'Case: min' => [
                'data' => [
                    'group_name' => 'a',
                    'remarks' => '',
                ]
            ],
            //最大
            'Case: max' => [
                'data' => [
                    'group_name' => str_repeat('a', 255),
                    'remarks' => str_repeat('b', 500),
                ]
            ],
            //最大（日本語）
            'Case: max_ja' => [
                'data' => [
                    'group_name' => str_repeat('あ', 255),
                    'remarks' => str_repeat('い', 500),
                ]
            ],
            //必須のみ
            'Case: required_only' => [
                'data' => [
                    'group_name' => 'Validation Test',
                ]
            ]
        ];
    }

    /**
     * グループ新規作成　& 更新_正常系エラーバリデーションチェック
     */
    public static function data_admin_groups_create_post_and_patch_ok_validation_normal_error()
    {
        $validRemarks = 'Valid value';

        return [
            //必須チェック
            'Case: missing_field' => [
                'data' => [],
                'expectedErrors' => [
                    'group_name' => 'グループ名は必ず指定してください。',
                ]
            ],
            //必須項目が空文字
            'Case: null_required_field' => [
                'data' => [
                    'group_name' => '',
                    'remarks' => $validRemarks,
                ],
                'expectedErrors' => [
                    'group_name' => 'グループ名は必ず指定してください。',
                ]
            ],
            //string指定のフィールドの値が文字列ではない
            'Case: not_string' => [
                'data' => [
                    'group_name' => 1,
                    'remarks' => 2,
                ],
                'expectedErrors' => [
                    'group_name' => 'グループ名は文字列を指定してください。',
                    'remarks' => '備考は文字列を指定してください。',
                ]
            ],
            //最大文字数超過
            'Case: over_max_words' => [
                'data' => [
                    'group_name' => str_repeat('a', 256),
                    'remarks' => str_repeat('b', 501),
                ],
                'expectedErrors' => [
                    'group_name' => 'グループ名は、255文字以下で指定してください。',
                    'remarks' => '備考は、500文字以下で指定してください。'
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider data_admin_groups_create_post_and_patch_ok_validation
     * グループ新規作成時のバリデーションチェック（正常系）
     */
    public function test_admin_groups_create_post_ok_validation_ok($data)
    {
        $this->actingAs($this->admin, 'admin');

        //新規作成画面に移動し、データをpost
        $this->get("/admin/groups/create");
        $response = $this->post("/admin/groups", $data);

        $response->assertStatus(302)->assertRedirect("/admin/groups");
        $response->assertSessionHasNoErrors();
    }

    /**
     * @test
     * @dataProvider data_admin_groups_create_post_and_patch_ok_validation_normal_error
     * グループ新規作成時のバリデーションチェック（正常系エラー）
     */
    public function test_admin_groups_create_post_ok_validation_normal_error($data, $expectedErrors)
    {
        $this->actingAs($this->admin, 'admin');

        //新規作成画面に移動し、データをpost
        $this->get("/admin/groups/create");
        $response = $this->post("/admin/groups", $data);

        $response->assertStatus(302)->assertRedirect("/admin/groups/create/");
        $response->assertSessionHasErrors($expectedErrors);
    }

    /**
     * @test
     * @dataProvider data_admin_groups_create_post_and_patch_ok_validation
     * コンテンツ更新時のバリデーションチェック（正常系）
     */
    public function test_admin_groups_edit_patch_ok_validation_ok($data)
    {
        $this->actingAs($this->admin, 'admin');

        $group = $this->createTestGroups();

        //編集画面に移動し、データをpatch
        $this->get("/admin/groups/{$group->id}/edit");
        $response = $this->patch("/admin/groups/{$group->id}", $data);

        $response->assertStatus(302)->assertRedirect("/admin/groups");
        $response->assertSessionHasNoErrors();
    }

    /**
     * @test
     * @dataProvider data_admin_groups_create_post_and_patch_ok_validation_normal_error
     * コンテンツ更新時のバリデーションチェック（正常系エラー）
     */
    public function test_admin_groups_edit_patch_ok_validation_normal_error($data, $expectedErrors)
    {
        $this->actingAs($this->admin, 'admin');

        $group = $this->createTestGroups();

        //編集画面に移動し、データをpatch
        $this->get("/admin/groups/{$group->id}/edit");
        $response = $this->patch("/admin/groups/{$group->id}", $data);

        $response->assertStatus(302)->assertRedirect("/admin/groups/{$group->id}/edit");
        $response->assertSessionHasErrors($expectedErrors);
    }
}
