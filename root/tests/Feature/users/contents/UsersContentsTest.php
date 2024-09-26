<?php

namespace Tests\Feature\Users\Contents;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Content;
use App\Models\Course;

class UsersContentsTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->create_user();
        $this->create_course();
        $this->create_content();
    }

    private function create_user()
    {
        return User::factory()->create([
            'username' => 'testUser',
            'password' => Hash::make('testUser'),
            'mail_address' => 'testUser@user.com',
        ]);
    }

    private function create_course()
    {
        Course::factory()->create([
            'id' => 190001,
            'title' => 'test_course',
        ]);
    }

    private function create_content()
    {
        for ($i = 1; $i <= 5; $i++) {
            Content::factory()->create([
                'id' => 200000 + $i,
                'title' => ('content_' . $i),
                'admin_id' => 120001,
                'course_id' => 190001,
                'youtube_video_id' => '1q8VtH2zxYE',
                'position' => $i,
            ]);
        }
    }

    /**
     * @test
     */
    public function test_users_contents_get_ok()
    {
        // users/contents/{course}にアクセスできる
        $this->actingAs($this->user);
        $response = $this->get('/users/contents/190001');
        $response->assertOk();
    }

    /**
     * @test
     */
    public function test_users_contents_get_ok_unauthenticated()
    {
        // users/contents/{course}にアクセス時、ログアウト中ならログインページにリダイレクトする
        $response = $this->get('/users/contents/190001');

        $response->assertRedirect('/users/login');
    }

    /**
     * @test
     */
    public function test_users_contents_get_ok_all_title()
    {
        // 対応している公開コンテンツのタイトルがすべて表示されている
        $this->actingAs($this->user);
        $response = $this->get('/users/contents/190001');
        $course = Course::find(190001);
        $course->load('contents');
        $contentTitles = $course->contents->pluck('title');
        foreach ($contentTitles as $title) {
            $response->assertSee($title);
        }
    }

    /**
     * @test
     */
    public function test_users_contents_get_ok_sorted()
    {
        // 各ソート番号(positionカラムの値)に適した順に表示されている
        $course = Course::find(190001);

        $this->actingAs($this->user);
        $contents = Content::where('course_id', $course->id)->orderBy('position')->get();

        $response = $this->get('/users/contents/190001');
        $response->assertSeeInOrder($contents->pluck('title')->toArray());
    }

    /**
     * @test
     */
    public function test_users_contents_view_get_ok()
    {
        // users/contents/view/{content}にアクセスできる
        $this->actingAs($this->user);
        $response = $this->get('/users/contents/view/200001');
        $response->assertOk();
    }

    /**
     * @test
     */
    public function test_users_contents_view_get_ok_unauthenticated()
    {
        // users/contents/view/{content}にアクセス時、ログアウト中ならログインページにリダイレクトする
        $response = $this->get('/users/contents/view/200001');
        $response->assertRedirect('/users/login');
    }
}
