<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CourseSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            GroupSeeder::class,
            ContentSeeder::class,
            GroupsCourseSeeder::class,
            UsersGroupSeeder::class,
            UserMessageSeeder::class,
            AdminMessageSeeder::class,
            InformationSeeder::class,
            GroupInformationSeeder::class,
        ]);
    }
}
