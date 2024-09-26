<?php

namespace Tests\Feature\users;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Group;
use App\Models\Information;
use App\Models\Course;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $groups;
    private $informations;
    private $courses;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createTestUser();
        $this->groups = $this->createTestGroups();
        $this->informations = $this->createTestInformations();
        $this->courses = $this->createTestCourses();

        $this->attachGroupsToUser();
        $this->attachInformationsToGroups();
        $this->attachCoursesToGroups();
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

    private function createTestGroups()
    {
        // グループのテストデータを作成
        $groupInstances = [];

        for ($i = 1; $i <= 2; $i++) {
            $Group = Group::factory()->create([
                'id' => 180000 + $i,
                'group_name' => "testGroup_$i"
            ]);
            $groupInstances[] = $Group;
        }
        return $groupInstances;
    }

    private function createTestInformations()
    {
        // お知らせのテストデータを作成
        $infoInstances = [];

        for ($i = 1; $i <= 6; $i++) {
            $info = Information::factory()->create([
                'id' => 170000 + $i,
                'title' => "testInfo_$i",
                'text' => "testInfo_$i",
                'admin_id' => 120001,
                'updated_at' => now()->subDays($i),
            ]);
            $infoInstances[] = $info;
        }
        return $infoInstances;
    }

    private function createTestCourses()
    {
        // コースのテストデータを作成
        $CourseInstances = [];

        for ($i = 1; $i <= 3; $i++) {
            $course = Course::factory()->create([
                'id' => 190000 + $i,
                'title' => "testCourse_$i",
            ]);
            $CourseInstances[] = $course;
        }
        return $CourseInstances;
    }

    private function attachGroupsToUser()
    {
        // グループをユーザーにアタッチ
        foreach ($this->groups as $group) {
            $this->user->groups()->attach($group);
        }
    }

    private function attachInformationsToGroups()
    {
        // お知らせをグループにアタッチ
        foreach ($this->groups as $group) {
            $group->informations()->attach(collect($this->informations)->pluck('id'));
        }
    }

    private function attachCoursesToGroups()
    {
        // コースをグループにアタッチ
        foreach ($this->groups as $group) {
            $group->courses()->attach(collect($this->courses)->pluck('id'));
        }
    }

    /**
     * @test
     */
    public function test_users_get_ok_redirect_without_login()
    {
        // 未ログイン時のリダイレクトの確認
        $response = $this->get('/users');
        $response->assertRedirect('/users/login');
    }

    /**
     * @test
     */
    public function test_users_get_ok_top_page_view()
    {
        // ログイン状態での、ユーザーページへのアクセスの確認
        $this->actingAs($this->user);
        $response = $this->get('/users');
        $response->assertViewIs('users.index');
    }

    /**
     * @test
     */
    public function test_users_get_ok_select_courses_view()
    {
        // おすすめ動画診断への画面遷移の確認
        $this->actingAs($this->user);
        $response = $this->get('/users/select-courses');
        $response->assertViewIs('users.recommend.index');
    }

    /**
     * @test
     */
    public function test_users_get_ok_informations_view()
    {
        // お知らせ一覧への画面遷移の確認
        $this->actingAs($this->user);
        $response = $this->get('/users/informations');
        $response->assertViewIs('users.informations.index');
    }

    /**
     * @test
     */
    public function test_users_get_ok_information_detailed_view()
    {
        // お知らせ詳細への画面遷移の確認
        $this->actingAs($this->user);
        for ($i = 1; $i <= 5; $i++) {
            $response = $this->get("/users/informations/" . 170000 + $i);
            $response->assertSee("testInfo_$i");
        }
    }

    /**
     * @test
     */
    public function test_users_get_ok_informations_display_limited_to_five()
    {
        // ５つまでお知らせが表示されているか確認
        $this->actingAs($this->user);
        $response = $this->get('/users');
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee("testInfo_$i");
        }
    }

    /**
     * @test
     */
    public function test_users_get_informations_sixth_not_displayed()
    {
        // ６つ目以降のお知らせが表示されていないか確認
        $this->actingAs($this->user);
        $response = $this->get('/users');
        $response->assertDontSee('testInfo_6');
    }

    /**
     * @test
     */
    public function test_users_get_ok_informations_display_without_duplicates()
    {
        // お知らせの表示に重複がないか確認
        $this->actingAs($this->user);
        $response = $this->get('/users');
        $contents = $response->getContent();
        for ($i = 1; $i <= 5; $i++) {
            $count = substr_count($contents, "testInfo_$i");
            $this->assertEquals($count, 1);
        }
    }

    /**
     * @test
     */
    public function test_users_get_ok_informations_sorted_by_updated_at_desc()
    {
        // お知らせが降順でソートされているか確認
        $this->actingAs($this->user);
        for ($i = 1; $i <= 5; $i++) {
            $expectedInfoTitles[$i] = "testInfo_$i";
        }
        $response = $this->get('/users');
        $response->assertSeeInOrder($expectedInfoTitles);
    }

    /**
     * @test
     */
    public function test_users_get_ok_courses_display()
    {
        // コース一覧にコースが表示されているか確認
        $this->actingAs($this->user);
        $response = $this->get('/users');
        for ($i = 1; $i <= 3; $i++) {
            $response->assertSee("testCourse_$i");
        }
    }

    /**
     * @test
     */
    public function test_users_get_ok_courses_detailed_view()
    {
        // コース詳細への画面遷移の確認
        $this->actingAs($this->user);
        for ($i = 1; $i <= 3; $i++) {
            $response = $this->get("/users/contents/" . 190000 + $i);
            $response->assertSee("testCourse_$i");
        }
    }

    /**
     * @test
     */
    public function test_users_get_ok_courses_display_without_duplicates()
    {
        // コース一覧に重複がないか確認
        $this->actingAs($this->user);
        $response = $this->get('/users');
        $contents = $response->getContent();
        for ($i = 1; $i <= 3; $i++) {
            $count = substr_count($contents, "testCourse_$i");
            $this->assertEquals($count, 1);
        }
    }

    /**
     * @test
     */
    public function test_users_get_ok_courses_sorted_by_updated_at_desc()
    {
        // コースが昇順でソートされているか確認
        $this->actingAs($this->user);
        for ($i = 1; $i <= 3; $i++) {
            $expectedInfoTitles[$i] = "testCourse_$i";
        }
        $response = $this->get('/users');
        $response->assertSeeInOrder($expectedInfoTitles);
    }
}
