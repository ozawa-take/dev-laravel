<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(app()->isLocal()) {
            Group::factory()
                ->count(10)
                ->sequence(function($sequence) {
                    return [
                        'group_name' => sprintf('グループ%d', $sequence->index + 1),
                        'remarks' => sprintf('備考%d', $sequence->index + 1),
                        'created_at' => '2023-09-01 10:05:00',
                        'updated_at' => '2023-09-01 10:05:00',
                    ];
                })->create();
        }
    }
}
