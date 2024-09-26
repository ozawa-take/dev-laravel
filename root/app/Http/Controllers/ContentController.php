<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Course;
use App\Models\ContentsLog;
use App\Http\Requests\ContentRequest;
use App\Http\Requests\SortRequest;
use App\Http\Requests\StoreContentLogRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course): View
    {
        $contents = Content::where('course_id', $course->id)->orderby('position')->get();
        $adminUser = Auth::user();
        return view('admin.contents.index', compact('contents', 'course', 'adminUser'));
    }

    /**
     * 並び替え
     */
    public function sort(SortRequest $request): JsonResponse
    {
        $positions = $request->input('positions');

        DB::transaction(function () use ($positions) {
            foreach ($positions as $index => $id) {
                Content::where('id', $id)->update(['position' => $index + 1]);
            }
        });

        return response()->json(['message' => '並び替えを保存しました。']);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course): View
    {
        $adminUser = Auth::user();
        return view('admin.contents.create', compact('course', 'adminUser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContentRequest $request, Course $course): RedirectResponse
    {
        $user = Auth::user();

        $data = [
            'course_id'        => $course->id,
            'admin_id'         => $user->id,
            'title'            => $request->title,
            'youtube_video_id' => $request->youtube_video_id,
            'remarks'          => $request->remarks,
        ];

        Content::create($data);

        return redirect()->route('admin.contents.index', compact('course'))->with('message', 'コンテンツを登録しました');
    }

    /**
     * show
     */
    public function show(Content $content): View
    {
        $admin = $content->admin;
        $adminUser = Auth::user();

        return view('admin.contents.show', compact('content', 'admin', 'adminUser'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $content): View
    {
        $course = $content->course;
        $adminUser = Auth::user();
        return view('admin.contents.edit', compact('content', 'course', 'adminUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContentRequest $request, Content $content): RedirectResponse
    {
        $course = $content->course;
        $user = Auth::user();

        $data = [
            'course_id'          => $course->id,
            'admin_id'           => $user->id,
            'title'              => $request->title,
            'youtube_video_id'   => $request->youtube_video_id,
            'remarks'            => $request->remarks,
        ];

        $content->update($data);

        return redirect()->route('admin.contents.index', compact('course'))->with('message', 'コンテンツを変更しました');
    }

    /**
     * 複製
     */
    public function duplicate(Content $content): RedirectResponse
    {
        $newContent = new Content();
        $newContent->fill($content->toArray())->save();
        $course = $newContent->course;

        return redirect()->route('admin.contents.index', compact('course'))->with('message', 'コンテンツを複製しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content): RedirectResponse
    {
        $course = $content->course;
        $content->delete();
        return redirect()->route('admin.contents.index', compact('course'))
            ->with('danger', $content->title . 'を削除しました');
    }

    public function list(Course $course): View
    {
        //コンテンツ一覧画面
        $user = Auth::user();
        $course_title = $course->title;
        $contents = Content::where('course_id', $course->id)->get();

        return view('users.contents.index', compact('contents', 'user', 'course_title'));
    }

    public function view(Content $content): View
    {
        //コンテンツ詳細画面
        $user = Auth::user();
        $title = $content->title;
        return view('users.contents.show', compact('content', 'user', 'title'));
    }

    public function record(StoreContentLogRequest $request, Content $content): RedirectResponse
    {
        $user = Auth::user();
        $completed = $request->input('log');
        $checkExists = ContentsLog::where('user_id', $user->id)->where('content_id', $content->id)->exists();
        if ($checkExists) {
            $contentsLog = ContentsLog::where('user_id', $user->id)->where('content_id', $content->id)->first();
            $contentsLog->update([
                'completed' => $completed,
                'updated_at' => now()
            ]);
            $contentsLog->touch();
        } else {
            ContentsLog::create([
                'user_id' => $user->id,
                'content_id' => $content->id,
                'completed' => $completed
            ]);
        }

        $course = $content->course;
        return to_route('users.contents.index', $course);
    }
}
