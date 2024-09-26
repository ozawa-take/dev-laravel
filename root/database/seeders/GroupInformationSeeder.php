<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GroupInformation;

class GroupInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->isLocal()) {
            GroupInformation::factory()
                ->count(10)
                ->sequence(function ($sequence) {
                    return [
                        'group_id' => config('project.database.auto_increment.groups'),
                        'information_id' => $sequence->index + config('project.database.auto_increment.information'),
                    ];
                })
                ->create();
        }
    }
}
