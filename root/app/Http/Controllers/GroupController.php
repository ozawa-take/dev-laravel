<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $groups = Group::all();
        $adminUser = Auth::user();
        return view('admin.groups.index', compact('groups', 'adminUser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $courses = Course::all();
        $users = User::all();
        $adminUser = Auth::user();
        return view('admin.groups.create', compact('courses', 'users', 'adminUser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupRequest $request): RedirectResponse
    {
        Group::create([
            'group_name' => $request->group_name,
            'remarks'    => $request->remarks,
        ]);

        $group = Group::orderByDesc('id')->first();
        $courses = $request->input('course', []);
        $users = $request->input('user', []);

        $group->courses()->attach(Course::findMany($courses));
        $group->users()->attach(User::findMany($users));

        return redirect()->route('admin.groups.index')->with('message', $request->group_name . 'を登録しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group): View
    {
        $adminUser = Auth::user();
        return view('admin.groups.show', compact('group', 'adminUser'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group, Request $request): View
    {
        $courses = Course::all();
        $users = User::all();
        $adminUser = Auth::user();
        $show = $request->input('show');

        if ($show === 'show') {
            $backBtn = route('admin.groups.show', $group);
        } else {
            $backBtn = route('admin.groups.index');
        }

        return view('admin.groups.edit', compact('courses', 'users', 'adminUser', 'group', 'backBtn'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GroupRequest $request, Group $group): RedirectResponse
    {
        $group->update([
            'group_name' => $request->group_name,
            'remarks'    => $request->remarks,
        ]);

        $courses = $request->input('course', []);
        $users = $request->input('user', []);

        $group->courses()->sync($courses);
        $group->users()->sync($users);

        return redirect()->route('admin.groups.index')->with('message', $request->group_name . 'を編集しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group): RedirectResponse
    {
        $group->delete();
        return redirect()->route('admin.groups.index')->with('danger', $group->group_name . 'を削除しました');
    }
}
