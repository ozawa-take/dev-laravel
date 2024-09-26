<?php

namespace App\Http\Controllers;

use App\Http\Requests\SelectCourseRequest;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class SelectCourseController extends Controller
{
    public const YES = 'yes';
    public const NO = 'no';
    public const QUESTION = [
        # q_id 質問固有の番号
        # q_order 何問目の質問か
        # text 質問文
        # yes はいと回答した場合の次回の質問
        # no いいえと回答した場合の次回の質問
        [
            'q_id' => 0,
            'q_order' => 1,
            'text' => "最初の質問です。0ですか？",
            self::YES => 1,
            self::NO => 2
        ], [
            'q_id' => 1,
            'q_order' => 2,
            'text' => "2問目です。1ですか？",
            self::YES => 3,
            self::NO => 4
        ], [
            'q_id' => 2,
            'q_order' => 2,
            'text' => "2問目です。2ですか？",
            self::YES => 5,
            self::NO => 6
        ], [
            'q_id' => 3,
            'q_order' => 3,
            'text' => "3問目です。3ですか？",
            self::YES => null,
            self::NO => null,
            'yes_course_id' => 190001,
            'no_course_id' => 190002,
        ], [
            'q_id' => 4,
            'q_order' => 3,
            'text' => "3問目です。4ですか？",
            self::YES => null,
            self::NO => null,
            'yes_course_id' => 190001,
            'no_course_id' => 190002,
        ], [
            'q_id' => 5,
            'q_order' => 3,
            'text' => "3問目です。5ですか？",
            self::YES => null,
            self::NO => null,
            'yes_course_id' => 190001,
            'no_course_id' => 190002,
        ], [
            'q_id' => 6,
            'q_order' => 3,
            'text' => "3問目です。6ですか？",
            self::YES => null,
            self::NO => null,
            'yes_course_id' => 190001,
            'no_course_id' => 190002,
        ],
    ];

    private int $q_id;
    private string $answer;
    private ?int $next_q_id = null;
    private ?Course $course = null;

    public function index(SelectCourseRequest $request): view
    {
        $user = Auth::user();
        $input_check = $request->has('q_id') && $request->has('answer');
        if ($input_check) {
            $this->q_id = (int)$request->input('q_id');
            $this->answer = (string)$request->input('answer');
        } else {
            $q = self::QUESTION[0]; //1問目の質問をviewに渡す
            return view('users.recommend.index', compact('q', 'user'));
        }

        if (!isset(self::QUESTION[$this->q_id])) {
            abort(400);
        }

        $this->next_q_id = self::QUESTION[$this->q_id][$this->answer] ?? null;

        //次の質問が存在するなら$qとしてviewに渡す
        if ($this->next_q_id) {
            $q = self::QUESTION[$this->next_q_id];
            return view('users.recommend.index', compact('q', 'user'));
        }

        $this->course = null;
        if ($this->answer === self::YES) {
            $this->course = Course::find(self::QUESTION[$this->q_id]['yes_course_id']) ?? null;
        }
        if ($this->answer === self::NO) {
            $this->course = Course::find(self::QUESTION[$this->q_id]['no_course_id']) ?? null;
        }
        if (isset($this->course)) { //回答に対応するCourseが正常に取得できていれば結果表示画面に$courseとして渡す
            return view('users.recommend.answer', ['course' => $this->course, 'user' => $user]);
        } else {
            abort(400);
        }
    }
}
