<?php

namespace Tests\Feature\Users\Information;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Group;
use App\Models\Information;


class UsersInformationsTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'username' => 'testInfo',
            'password' => Hash::make('testInfo'),
            'mail_address' => 'testInfo@test.com',
        ]);
    }

    // お知らせのテストデータを作成
    private function createTestInformations()
    {
        return Information::factory()->create([
            'title' => 'Information',
            'text' => 'InformationText',
            'admin_id' => 120001,
        ]);
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok()
    {
        // ユーザーがログインして一覧を取得する
        $response = $this->actingAs($this->user)->get('/users/informations/');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_unauthenticated()
    {
        // ユーザーがゲスト状態（ログアウトの状態）で一覧を取得する
        $response = $this->get('/users/informations/');

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect('/users/login');
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_get_informations()
    {
        // テスト用のグループを作成し、ユーザーと関連付ける
        $group = Group::factory()->create(['group_name' => 'testGroup']);
        $this->user->groups()->attach($group);

        // お知らせとグループを関連付ける
        $information = $this->createTestInformations();
        $group->informations()->attach($information);

        // お知らせの一覧を取得し、特定のお知らせが表示されていることを確認する
        $response = $this->actingAs($this->user)->get('/users/informations/');
        $response->assertStatus(200)->assertSee('Information');
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_sort()
    {
        // テスト用のグループを作成し、ユーザーと関連付ける
        $group = Group::factory()->create(['group_name' => 'testGroup']);
        $this->user->groups()->attach($group);

         // 3つのお知らせを作成し、更新日時を設定してグループと関連付ける
        for ($i = 1; $i <= 3; $i++)
        {
            $information = Information::factory()->create([
                'title' => 'Information' . $i,
                'text' => 'InformationText',
                'admin_id' => 120001,
                'updated_at' => now()->subDays($i),
            ]);
            $group->informations()->attach($information->id);
        }

        // お知らせの一覧を取得し、更新日時の順にお知らせが並んでいるか確認
        $response = $this->actingAs($this->user)->get('/users/informations/');
        $response->assertSeeInOrder(['Information1', 'Information2' ,'Information3']);
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_no_duplicates()
    {
        $information = $this->createTestInformations();

        // 2つのグループを作成し、ユーザー・お知らせに関連付ける
        for ($i = 1; $i <= 2; $i++)
        {
            $group = Group::factory()->create(['group_name' => 'testGroup' . $i]);
            $this->user->groups()->attach($group->id);
            $group->informations()->syncWithoutDetaching([$information->id]);
        }

        // お知らせの一覧を取得し、重複しないことを確認
        $response = $this->actingAs($this->user)->get('/users/informations/');
        $this->assertEquals(1, substr_count($response->getContent(), 'Information'));
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_details()
    {
        // テスト用のグループを作成し、ユーザーと関連付ける
        $group = Group::factory()->create(['group_name' => 'testGroup']);
        $this->user->groups()->attach($group);

        // お知らせとグループを関連付ける
        $information = $this->createTestInformations();
        $group->informations()->attach($information);

        // ユーザーがログインして詳細を取得する
        $response = $this->actingAs($this->user)->get("users/informations/{$information->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_informations_details()
    {
        // テスト用のグループを作成し、ユーザーと関連付ける
        $group = Group::factory()->create(['group_name' => 'testGroup']);
        $this->user->groups()->attach($group);

        // お知らせとグループを関連付ける
        $information = $this->createTestInformations();
        $group->informations()->attach($information);

        // お知らせの詳細を取得し、特定のお知らせが表示されていることを確認する
        $response = $this->actingAs($this->user)->get("users/informations/{$information->id}");
        $response->assertSee(['Information', 'InformationText']);
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_unauthenticated_details()
    {
        // テスト用のグループを作成し、ユーザーと関連付ける
        $group = Group::factory()->create(['group_name' => 'testGroup']);
        $this->user->groups()->attach($group);

        // お知らせとグループを関連付ける
        $information = $this->createTestInformations();
        $group->informations()->attach($information);

        // ユーザーがゲスト状態（ログアウトの状態）で詳細を取得する
        $response = $this->get("users/informations/{$information->id}");

        // ログイン画面にリダイレクトされるか確認
        $response->assertStatus(302)->assertRedirect('/users/login');
    }

    /**
     * @test
     */
    public function test_users_informations_get_ok_information_detailed_view()
    {
        // テスト用のグループを作成し、ユーザーと関連付ける
        $group = Group::factory()->create(['group_name' => 'testGroup']);
        $this->user->groups()->attach($group);

        // お知らせとグループを関連付ける
        $information = $this->createTestInformations();
        $group->informations()->attach($information);

        // お知らせの詳細を取得し、viewが正しく表示されることを確認する
        $response = $this->actingAs($this->user)->get("users/informations/{$information->id}");
        $response->assertViewIs('users.informations.show')->assertSee(['Information', 'InformationText']);
    }

}
