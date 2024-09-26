<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Collection;

class UserHomeController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $informations = $this->getUserInformations($user);
        $courses = $this->getUserCourses($user);

        foreach ($courses as $course) {
            $course->residue = $course->getResidue($user);
            $course->logFirst = $course->getLogFirst($user);
            $course->logLast = $course->getLogLast($user);
        }
        return view('users.index', compact('user', 'informations', 'courses'));
    }
    // コースの取得
    private function getUserCourses($user): Collection
    {
        return $user->groups()
            ->with('courses')
            ->get()
            ->pluck('courses')
            ->collapse()
            ->unique('id')
            ->sortBy('id');
    }

    // お知らせ5件の取得(重複は除外)
    private function getUserInformations($user): Collection
    {
        return $user->groups()
            ->with('informations')
            ->get()
            ->pluck('informations')
            ->collapse()
            ->unique('id')
            ->sortByDesc('updated_at')
            ->take(5);
    }
}
