<?php

namespace Database\Seeders;

use App\Models\Information;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->isLocal()) {
            Information::factory()
                ->count(10)
                ->sequence(function ($sequence) {
                    return [
                        'title' => sprintf('お知らせ_%02d', $sequence->index + 1),
                        'text' => sprintf('お知らせ_%02d', $sequence->index + 1),
                        'admin_id' => config('project.database.auto_increment.admins'),
                        'deleted_at' => null,
                        'created_at' => '2023-06-01 01:23:45',
                        'updated_at' => '2023-06-30 21:58:59',
                    ];
                })
                ->create();
        }
    }
}
