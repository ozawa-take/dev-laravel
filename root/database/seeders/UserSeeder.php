<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->isLocal()) {
            User::factory()
                ->count(10)
                ->sequence(function ($sequence) {
                    return [
                        'username' => sprintf('test_%02d', $sequence->index + 1),
                        'password' => Hash::make('test'),
                        'mail_address' => sprintf('test_%02d@test', $sequence->index + 1),
                        'deleted_at' => null,
                        'created_at' => '2023-06-01 01:23:45',
                        'updated_at' => '2023-06-30 21:58:59',
                    ];
                })
                ->create();
        }
    }
}
