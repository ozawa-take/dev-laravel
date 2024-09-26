<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create(
            [
                'username' => config('project.database.system_admin.name'),
                'password' => Hash::make(config('project.database.system_admin.password')),
                'mail_address' => config('project.database.system_admin.email'),
                'is_system_admin' => true,
                'deleted_at' => null,
                'created_at' => '2023-06-01 01:23:47',
                'updated_at' => '2023-06-30 21:58:59',
            ]
        );
        if (app()->isLocal()) {
            Admin::factory()
                ->count(10)
                ->sequence(function ($sequence) {
                    return [
                        'username' => sprintf('admin_%02d', $sequence->index + 1),
                        'password' => Hash::make('admin'),
                        'mail_address' => sprintf('admin_%02d@admin', $sequence->index + 1),
                        'deleted_at' => null,
                        'created_at' => '2023-06-01 01:23:47',
                        'updated_at' => '2023-06-30 21:58:59',
                    ];
                })
                ->create();
        }
    }
}
