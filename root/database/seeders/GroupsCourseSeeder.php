<?php

namespace Database\Seeders;

use App\Models\GroupsCourse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupsCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(app()->isLocal()) {
            GroupsCourse::factory()
                ->count(10)
                ->sequence(function($sequence) {
                    return [
                        'group_id'   => $sequence->index + config('project.database.auto_increment.groups'),
                        'course_id'  => $sequence->index + config('project.database.auto_increment.courses'),
                        'deleted_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->create();
        }
    }
}
