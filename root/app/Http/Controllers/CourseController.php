<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Http\Requests\SortRequest;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $courses = Course::orderby('position')->get();
        $adminUser = Auth::user();

        return view('admin.courses.index', compact('courses', 'adminUser'));
    }

    public function sort(SortRequest $request): JsonResponse
    {
        $positions = $request->input('positions');

        DB::transaction(function () use ($positions) {
            foreach ($positions as $index => $id) {
                Course::where('id', $id)->update(['position' => $index + 1]);
            }
        });

        return response()->json(['message' => '並び替えを保存しました。']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $adminUser = Auth::user();
        return view('admin.courses.create', compact('adminUser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseRequest $request): RedirectResponse
    {

        Course::create([
            'title'       => $request->title,
        ]);

        return redirect()->route('admin.courses.index')->with('message', 'コースを登録しました');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course): View
    {
        $adminUser = Auth::user();
        return view('admin.courses.edit', compact('course', 'adminUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        $course->update([
            'title'       => $request->title,
        ]);

        return redirect()->route('admin.courses.index')->with('message', $course->title . 'を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('danger', $course->title . 'を削除しました');
    }
}
