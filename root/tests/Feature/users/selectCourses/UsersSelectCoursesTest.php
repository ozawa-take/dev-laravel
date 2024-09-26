<?php

namespace Tests\Feature\Users\selectCourses;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Http\Controllers\SelectCourseController;

class UsersSelectCoursesTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    /**
     * テスト用データを作成
     */
    public function setUp(): void
    {
        parent::setUp();

        //ユーザーを作成
        $this->user = User::factory()->create([
            'username' => 'testUser',
            'password' => Hash::make('password1'),
            'mail_address' => 'testUser1@user.com',
        ]);

        //ログインする
        $this->actingAs($this->user);

        //診断結果用のコースを作成
        Course::factory()->create([
            'id' => 190001,
            'title' => 'test_course_yes',
        ]);

        Course::factory()->create([
            'id' => 190002,
            'title' => 'test_course_no',
        ]);
    }

    /**
     * @test
     * ユーザーがおすすめ動画診断画面に正常にアクセスできることを確認する
     */
    public function test_users_select_courses_get_ok()
    {
        $response = $this->get('/users/select-courses');
        $response->assertOk();

        //最初の質問が表示されているか確認
        $response->assertSee(SelectCourseController::QUESTION[0]['text']);
    }

    /**
     * @test
     * ユーザーが未ログイン時におすすめ動画診断画面にアクセスできないことを確認する
     */
    public function test_users_select_courses_get_ok_redirect_without_login()
    {
        //ログアウトする
        auth()->logout();

        $response = $this->get('/users/select-courses');

        //ログイン画面にリダイレクトされるか確認
        $response->assertRedirect('/users/login');
    }

    /**
     * データプロバイダ: 1問目と2問目の質問に対するテストデータ
     */
    public static function data_users_select_courses_get_ok_answer_question()
    {
        return [
            [SelectCourseController::QUESTION[0]['q_id'], SelectCourseController::YES, SelectCourseController::QUESTION[1]['text']],
            [SelectCourseController::QUESTION[0]['q_id'], SelectCourseController::NO, SelectCourseController::QUESTION[2]['text']],
            [SelectCourseController::QUESTION[1]['q_id'], SelectCourseController::YES, SelectCourseController::QUESTION[3]['text']],
            [SelectCourseController::QUESTION[1]['q_id'], SelectCourseController::NO, SelectCourseController::QUESTION[4]['text']],
            [SelectCourseController::QUESTION[2]['q_id'], SelectCourseController::YES, SelectCourseController::QUESTION[5]['text']],
            [SelectCourseController::QUESTION[2]['q_id'], SelectCourseController::NO, SelectCourseController::QUESTION[6]['text']],
        ];
    }

    /**
     * @test
     * ユーザーが1問目または2問目の質問に対して回答をした場合、次の質問が正しく表示されていることを確認する
     * @dataProvider data_users_select_courses_get_ok_answer_question
     */
    public function test_users_select_courses_get_ok_answer_question($q_id, $answer, $NextQuestionText)
    {
        $response = $this->get("/users/select-courses?q_id=$q_id&answer=$answer");

        //次の質問が表示されているか確認
        $response->assertSee($NextQuestionText);
    }

    /**
     * データプロバイダ: 3問目の質問に対するテストデータ
     */
    public static function data_users_select_courses_get_ok_answer_third_question()
    {
        return [
            [SelectCourseController::QUESTION[3]['q_id'], SelectCourseController::YES, SelectCourseController::QUESTION[3]['yes_course_id']],
            [SelectCourseController::QUESTION[3]['q_id'], SelectCourseController::NO, SelectCourseController::QUESTION[3]['no_course_id']],
            [SelectCourseController::QUESTION[4]['q_id'], SelectCourseController::YES, SelectCourseController::QUESTION[4]['yes_course_id']],
            [SelectCourseController::QUESTION[4]['q_id'], SelectCourseController::NO, SelectCourseController::QUESTION[4]['no_course_id']],
            [SelectCourseController::QUESTION[5]['q_id'], SelectCourseController::YES, SelectCourseController::QUESTION[5]['yes_course_id']],
            [SelectCourseController::QUESTION[5]['q_id'], SelectCourseController::NO, SelectCourseController::QUESTION[5]['no_course_id']],
            [SelectCourseController::QUESTION[6]['q_id'], SelectCourseController::YES, SelectCourseController::QUESTION[6]['yes_course_id']],
            [SelectCourseController::QUESTION[6]['q_id'], SelectCourseController::NO, SelectCourseController::QUESTION[6]['no_course_id']],
        ];
    }

    /**
     * @test
     * ユーザーが3問目の質問に対して回答をした場合、回答に対応したコースが正しく表示されていることを確認する
     * @dataProvider data_users_select_courses_get_ok_answer_third_question
     */
    public function test_users_select_courses_get_ok_answer_third_question($q_id, $answer, $expectedCourseId)
    {
        $response = $this->get("/users/select-courses?q_id=$q_id&answer=$answer");

        //ビューが正常に表示されているか確認
        $response->assertViewIs('users.recommend.answer');

        //ビューに正しいコースが表示されているか確認
        $response->assertViewHas('course', Course::find($expectedCourseId));

        //期待されるテキストが表示されているか確認
        $expectedTitle = Course::find($expectedCourseId)['title'];
        $response->assertSee("あなたへのおすすめ動画は{$expectedTitle}です。");

        //コースのコンテンツへのリンクが正しく表示されているか確認
        $response->assertSee("/users/contents/$expectedCourseId");
    }

    /**
     * データプロバイダ: 正常系エラーバリデーションチェックに対するテストデータ
     */
    public static function data_users_select_courses_get_ok_validation_normal_error()
    {
        return [
            //q_idの値が数値でない場合
            ['a', SelectCourseController::YES],
            //answerの値が文字列でない場合
            [SelectCourseController::QUESTION[0]['q_id'], 123],
        ];
    }

    /**
     * @test
     * おすすめ動画診断時の正常系エラーバリデーションチェック
     * @dataProvider data_users_select_courses_get_ok_validation_normal_error
     */
    public function test_users_select_courses_get_ok_validation_normal_error($q_id, $answer)
    {
        $response = $this->get("/users/select-courses?q_id=$q_id&answer=$answer");

        //abort(400)が呼び出されることを確認
        $response->assertStatus(400);
    }
}
